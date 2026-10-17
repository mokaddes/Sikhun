<?php

namespace App\Services\Blog;

use App\Contracts\AiProviderContract;
use App\Services\Ai\AiProviderFactory;

class BlogTranslator
{
    private const CHUNK_MAX_CHARS = 3500;

    /**
     * Translate source title and content into Bengali if not already Bengali.
     *
     * @return array{title_bn: string, content_bn: string, translated: bool}
     */
    public function translateToBengali(string $title, string $content, string $detectedLanguage = 'en'): array
    {
        if ($detectedLanguage === 'bn') {
            return [
                'title_bn' => $title,
                'content_bn' => $content,
                'translated' => false,
            ];
        }

        $provider = $this->resolveProvider();

        $chunks = $this->splitIntoChunks($content, self::CHUNK_MAX_CHARS);
        $translatedChunks = [];

        foreach ($chunks as $index => $chunk) {
            $systemPrompt = 'You are an expert English-to-Bengali translator for an educational platform in Bangladesh (Sikhun.com). '
                . 'Translate the provided text accurately into clear, natural, standard Bengali (প্রমিত বাংলা). '
                . 'Keep technical, scientific, programming, or AI terms in Bengali with the English term in parentheses where helpful for students. '
                . 'Output ONLY the translated Bengali text without any extra commentary.';

            $userPrompt = $index === 0
                ? "Title: {$title}\n\nContent Part " . ($index + 1) . ":\n{$chunk}"
                : "Content Part " . ($index + 1) . ":\n{$chunk}";

            $response = trim($provider->chat([
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ]));

            $translatedChunks[] = $response;
        }

        $combined = implode("\n\n", $translatedChunks);
        $titleBn = $title;

        // Extract translated title if the first chunk includes a first line for the title
        $lines = preg_split('/\r\n|\r|\n/', $combined, 2);
        if ($lines && count($lines) === 2) {
            $firstLine = trim(preg_replace('/^(Title|শিরোনাম)\s*:\s*/iu', '', $lines[0]) ?? $lines[0]);
            if ($firstLine !== '' && mb_strlen($firstLine, 'UTF-8') <= 250) {
                $titleBn = $firstLine;
                $combined = trim($lines[1]);
            }
        }

        return [
            'title_bn' => $titleBn,
            'content_bn' => $combined,
            'translated' => true,
        ];
    }

    private function resolveProvider(): AiProviderContract
    {
        foreach (['blog_translate', 'blog_rewrite', 'book_chat'] as $useCase) {
            try {
                return AiProviderFactory::default($useCase);
            } catch (\Throwable) {
                // try next fallback
            }
        }

        throw new \RuntimeException("No active default AI provider configured for 'blog_translate' or 'book_chat'. Configure one in /admin/ai-providers.");
    }

    /**
     * @return array<int, string>
     */
    private function splitIntoChunks(string $text, int $maxChars): array
    {
        $text = trim($text);
        if (mb_strlen($text, 'UTF-8') <= $maxChars) {
            return [$text];
        }

        $paragraphs = preg_split('/\n\s*\n/', $text) ?: [$text];
        $chunks = [];
        $current = '';

        foreach ($paragraphs as $para) {
            $para = trim($para);
            if ($para === '') {
                continue;
            }

            if ($current !== '' && mb_strlen($current . "\n\n" . $para, 'UTF-8') > $maxChars) {
                $chunks[] = $current;
                $current = $para;
            } else {
                $current = $current === '' ? $para : ($current . "\n\n" . $para);
            }
        }

        if ($current !== '') {
            $chunks[] = $current;
        }

        return $chunks ?: [$text];
    }
}
