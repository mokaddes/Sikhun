<?php

namespace App\Services\Ai;

use App\Contracts\ParsedDocument;
use Illuminate\Support\Facades\Log;

/** Detects headings in small page batches and maps them back to exact PDF pages. */
class BookStructureDetectionService
{
    public function detect(ParsedDocument $doc): void
    {
        if (! $doc->pages) return;

        $batches = array_chunk($doc->pages, 5);
        $detected = [];
        try {
            $provider = AiProviderFactory::default('book_structure');
            foreach ($batches as $pages) {
                $lines = [];
                foreach ($pages as $page) {
                    $text = trim((string) ($page['content'] ?? ''));
                    $text = mb_substr($text, 0, 6500);
                    $lines[] = 'PAGE '.(int) $page['page_number']."\n".$text;
                }
                $prompt = "Identify only explicit chapter, section, and subsection headings in these extracted PDF pages. Return valid JSON only: {\"headings\":[{\"title\":string,\"chapter_number\":string|null,\"level\":1|2|3,\"page\":integer}]}. Page values must exactly match a supplied PAGE label. Do not invent headings or pages; omit uncertain candidates. Preserve original titles.\n\n".implode("\n\n", $lines);
                $raw = $provider->chat([['role' => 'user', 'content' => $prompt]], ['temperature' => 0]);
                $json = json_decode(trim(preg_replace('/^```(?:json)?|```$/m', '', $raw)), true, 512, JSON_THROW_ON_ERROR);
                foreach (($json['headings'] ?? []) as $heading) {
                    $pageNumbers = array_column($pages, 'page_number');
                    if (! in_array((int) ($heading['page'] ?? 0), $pageNumbers, true)) continue;
                    $level = (int) ($heading['level'] ?? 0);
                    $title = trim((string) ($heading['title'] ?? ''));
                    if ($level < 1 || $level > 3 || $title === '') continue;
                    $detected[] = ['title' => mb_substr($title, 0, 255), 'chapter_number' => isset($heading['chapter_number']) ? (string) $heading['chapter_number'] : null, 'level' => $level, 'start_page' => (int) $heading['page']];
                }
            }
        } catch (\Throwable $e) {
            Log::error('Book structure detection failed.', ['error' => $e->getMessage()]);
            throw $e;
        }

        if (! $detected) {
            foreach ($doc->chapters as &$chapter) {
                $chapter['metadata'] = array_merge($chapter['metadata'] ?? [], ['structure_uncertain' => true]);
                $chapter['content'] = $this->pageContent($doc, $chapter['start_page'] ?? 1, $chapter['end_page'] ?? null);
            }
            unset($chapter);
            return;
        }

        usort($detected, fn ($a, $b) => [$a['start_page'], $a['level']] <=> [$b['start_page'], $b['level']]);
        $lastPage = max(array_map(fn ($p) => (int) $p['page_number'], $doc->pages));
        $flat = []; $stack = [];
        foreach ($detected as $i => $heading) {
            $level = $heading['level'];
            $end = $lastPage;
            for ($j = $i + 1; $j < count($detected); $j++) {
                if ($detected[$j]['level'] <= $level) { $end = $detected[$j]['start_page'] - 1; break; }
            }
            $stack = array_slice($stack, 0, $level - 1);
            $parentIndex = $level === 1 ? null : ($stack[$level - 2] ?? null);
            $content = $this->pageContent($doc, $heading['start_page'], $end);
            $flat[] = ['chapter_number' => $heading['chapter_number'], 'title' => $heading['title'], 'level' => $level, 'start_page' => $heading['start_page'], 'end_page' => max($heading['start_page'], $end), 'parent_index' => $parentIndex, 'metadata' => ['generated_by' => 'book_structure', 'structure_uncertain' => false], 'content' => $content];
            $stack[$level - 1] = count($flat) - 1;
        }
        $doc->chapters = $flat;
    }

    private function pageContent(ParsedDocument $doc, int $start, ?int $end): string
    {
        return collect($doc->pages)
            ->filter(fn ($page) => (int) $page['page_number'] >= $start && ($end === null || (int) $page['page_number'] <= $end))
            ->map(fn ($page) => trim((string) ($page['content'] ?? '')))
            ->filter()
            ->implode("\n\n");
    }
}
