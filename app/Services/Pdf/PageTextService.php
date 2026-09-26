<?php

namespace App\Services\Pdf;

use App\Contracts\PdfParsable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Returns the text of one PDF page, extracting it on demand when the
 * structured-parse pipeline left the page empty (scanned/image-only PDFs,
 * or books parsed before OCR existed). Strategy mirrors the parsing
 * pipeline: text layer first (pdftotext), then rasterize + OCR
 * (pdftoppm + tesseract with the configured language). A successful
 * extraction is persisted to the page row so the very next request —
 * reader chat, RAG rebuild — is instant, and the database improves over
 * time instead of re-OCRing the same page forever.
 */
class PageTextService
{
    // Hard cap so one row never grows unbounded; chat context trims
    // further before it ever reaches the model.
    private const MAX_TEXT_LENGTH = 20000;

    // A page that yields nothing is remembered for an hour so repeated
    // chats don't hammer tesseract on a genuinely unreadable page.
    private const EMPTY_NEGATIVE_TTL = 3600;

    /**
     * @return string|null  the page text, or null when extraction fails
     */
    public function forPage(PdfParsable $book, int $page): ?string
    {
        if (! $book->pdfFilePath() || ! Storage::disk('private')->exists($book->pdfFilePath())) {
            return null;
        }

        $row = $book->pages()->where('page_number', $page)->first();
        $existing = $row ? trim((string) $row->content) : '';
        if ($existing !== '') {
            return $existing;
        }

        $source = $book->pdfSourceId();
        if (Cache::get("page_text:empty:{$source}:{$page}")) {
            return null;
        }

        $text = $this->extract(Storage::disk('private')->path($book->pdfFilePath()), $page);

        if ($text === null || trim($text) === '') {
            Cache::put("page_text:empty:{$source}:{$page}", true, self::EMPTY_NEGATIVE_TTL);

            return null;
        }

        $text = mb_substr(trim($text), 0, self::MAX_TEXT_LENGTH);

        if ($row) {
            $row->update(['content' => $text]);
        }

        return $text;
    }

    private function extract(string $pdfPath, int $page): ?string
    {
        // 1. Text layer (fast, accurate for born-digital PDFs).
        $out = [];
        exec(sprintf('pdftotext -f %d -l %d %s - 2>&1', $page, $page, escapeshellarg($pdfPath)), $out, $code);
        $text = trim(implode("\n", $out));
        if ($code === 0 && $text !== '') {
            return $text;
        }

        // 2. OCR fallback for scanned/image-only pages.
        $ocr = (array) config('pdf-parsing.ocr', []);
        if (! ($ocr['enabled'] ?? true)) {
            return null;
        }

        $dpi = (int) ($ocr['dpi'] ?? 200);
        $lang = $ocr['lang'] ?: config('pdf-parsing.opendataloader.ocr_lang') ?: 'ben+eng';

        $tmp = sys_get_temp_dir().'/pt_'.bin2hex(random_bytes(4));
        $out2 = [];
        exec(sprintf(
            '%s -f %d -l %d -r %d -png %s %s 2>&1',
            escapeshellarg($ocr['pdftoppm_path'] ?? 'pdftoppm'),
            $page,
            $page,
            $dpi,
            escapeshellarg($pdfPath),
            escapeshellarg($tmp),
        ), $out2, $code2);

        $files = glob($tmp.'-*.png');
        $img = $files[0] ?? null;
        if ($code2 !== 0 || ! $img) {
            return null;
        }

        $out3 = [];
        exec(sprintf(
            '%s %s - -l %s --psm 3 2>&1',
            escapeshellarg($ocr['tesseract_path'] ?? 'tesseract'),
            escapeshellarg($img),
            escapeshellarg($lang),
        ), $out3, $code3);

        @unlink($img);
        @unlink($tmp);

        $text = trim(implode("\n", $out3));

        return $text !== '' ? $text : null;
    }
}