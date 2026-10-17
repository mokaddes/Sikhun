<?php

namespace App\Services;

use App\Contracts\PdfParsable;
use App\Models\Student;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Turns one page of a private PDF into a watermarked JPEG — the ONLY way
 * book content is ever served to a browser. The PDF path itself never
 * appears in any response; callers only ever see rendered page bytes
 * behind a short-lived signed URL (see Student\ReaderController).
 *
 * Requires the PHP Imagick extension + Ghostscript for PDF rasterization.
 * If a book has no pdf_path yet (e.g. fresh demo data before an admin
 * uploads the real file), a clearly-labelled placeholder image is
 * generated instead of a 500 error, so the reader flow stays testable
 * end-to-end without requiring real content first.
 */
class BookReaderService
{
    // Rasterize once per page (not per student) and cache globally; the PDF→JPEG
    // step is the expensive one (Ghostscript). The per-student watermark is a
    // cheap annotate pass on top of that shared raster, so every student still
    // gets their own watermarked bytes without re-running the raster.
    private const RASTER_TTL = 86400 * 7;
    private const WATERMARK_TTL = 86400 * 7;

    // Display-appropriate size: 120 DPI (≈992×1403 A4) is crisp on high-DPI
    // screens while keeping file sizes small (~120KB-200KB per page).
    private const RASTER_DPI = 120;
    private const JPEG_QUALITY = 82;

    public function renderPage(PdfParsable $book, int $page, Student $student): string
    {
        $source = $book->pdfSourceId();
        $renderedRelPath = "books/rendered/{$source}/{$page}.jpg";
        $watermarkedRelPath = "books/watermarked/{$source}/{$student->id}/{$page}.jpg";

        // 1. Ultra-fast path: already watermarked for this student on disk
        if (Storage::disk('private')->exists($watermarkedRelPath)) {
            return Storage::disk('private')->get($watermarkedRelPath);
        }

        // 2. Check if raw raster is already on disk (e.g. pre-rendered)
        $raw = null;
        if (Storage::disk('private')->exists($renderedRelPath)) {
            $raw = Storage::disk('private')->get($renderedRelPath);
        }

        // 3. Check memory/cache fallback
        if ($raw === null) {
            $raw = Cache::get("book_page:raw:{$source}:{$page}");
        }

        // 4. If not available on disk or in cache, render on-demand
        if ($raw === null) {
            $exists = $book->pdfFilePath() && Storage::disk('private')->exists($book->pdfFilePath());
            if (! $exists) {
                return $this->placeholderImage($book, $page, $student, 'No PDF uploaded yet for this book.');
            }

            $pdfPath = Storage::disk('private')->path($book->pdfFilePath());
            $raw = $this->rasterizePage($pdfPath, $page);

            if ($raw !== null) {
                // Permanently persist to disk so this page NEVER needs rasterizing again!
                Storage::disk('private')->put($renderedRelPath, $raw);
                Cache::put("book_page:raw:{$source}:{$page}", $raw, self::RASTER_TTL);
            }
        }

        if ($raw === null) {
            return $this->placeholderImage($book, $page, $student, 'Could not render this page.');
        }

        // 5. Watermark pass
        $watermarked = $this->applyWatermark($raw, $student);

        // Cache watermarked version to disk for sub-millisecond future reads
        try {
            Storage::disk('private')->put($watermarkedRelPath, $watermarked);
        } catch (\Throwable $e) {
            // Ignore write errors to avoid blocking the user
        }

        return $watermarked;
    }

    /**
     * Rasterize a single PDF page using pdftoppm if available, falling back to Imagick.
     */
    protected function rasterizePage(string $pdfPath, int $page): ?string
    {
        // 1. Fast path: pdftoppm via Poppler
        if (function_exists('exec')) {
            $tempDir = storage_path('app/temp');
            if (! is_dir($tempDir)) {
                @mkdir($tempDir, 0775, true);
            }
            $tempPrefix = $tempDir . '/page_' . uniqid('', true) . '_' . $page;
            $cmd = sprintf(
                'pdftoppm -jpeg -r %d -f %d -l %d %s %s 2>&1',
                self::RASTER_DPI,
                $page,
                $page,
                escapeshellarg($pdfPath),
                escapeshellarg($tempPrefix)
            );

            @exec($cmd, $output, $code);
            if ($code === 0) {
                $matches = glob($tempPrefix . '-*.jpg');
                if (! empty($matches) && file_exists($matches[0])) {
                    $bytes = file_get_contents($matches[0]);
                    @unlink($matches[0]);
                    if ($bytes && strlen($bytes) > 0) {
                        return $bytes;
                    }
                }
            }
        }

        // 2. Fallback: PHP Imagick
        if (extension_loaded('imagick')) {
            try {
                $imagick = new \Imagick();
                $imagick->setResolution(self::RASTER_DPI, self::RASTER_DPI);
                $imagick->readImage("{$pdfPath}[".($page - 1).']');
                $imagick->setImageFormat('jpg');
                $imagick->setImageCompressionQuality(self::JPEG_QUALITY);
                $blob = $imagick->getImageBlob();
                $imagick->clear();

                return $blob;
            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * Cheap step: overlay student watermark onto the JPEG raster.
     */
    protected function applyWatermark(string $raw, Student $student): string
    {
        if (! extension_loaded('imagick')) {
            return $raw;
        }

        try {
            $imagick = new \Imagick();
            $imagick->readImageBlob($raw);

            $draw = new \ImagickDraw();
            $draw->setFillColor(new \ImagickPixel('rgba(0,0,0,0.28)'));
            $draw->setFontSize(16);
            $draw->setTextAntialias(true);
            $watermark = "{$student->name} · ID:{$student->id} · sikhun.com";
            $imagick->annotateImage($draw, 16, 28, 0, $watermark);

            $blob = $imagick->getImageBlob();
            $imagick->clear();

            return $blob;
        } catch (\Throwable $e) {
            return $raw;
        }
    }

    /**
     * SVG placeholder so the reader UI works before real PDFs exist,
     * and so a render failure never surfaces a raw 500 error.
     */
    private function placeholderImage(PdfParsable $book, int $page, Student $student, ?string $note = null): string
    {
        $title = htmlspecialchars($book->title, ENT_QUOTES);
        $note = htmlspecialchars($note ?? 'No PDF uploaded yet for this book.', ENT_QUOTES);

        return <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" width="800" height="1120" viewBox="0 0 800 1120">
            <rect width="800" height="1120" fill="#f0f0fa"/>
            <rect x="40" y="40" width="720" height="1040" fill="none" stroke="#e2e2ee" stroke-width="2" stroke-dasharray="8 8"/>
            <text x="400" y="520" font-family="sans-serif" font-size="28" fill="#6b6b8a" text-anchor="middle">{$title}</text>
            <text x="400" y="560" font-family="sans-serif" font-size="18" fill="#9a9ab8" text-anchor="middle">Page {$page}</text>
            <text x="400" y="600" font-family="sans-serif" font-size="14" fill="#9a9ab8" text-anchor="middle">{$note}</text>
            <text x="400" y="1080" font-family="sans-serif" font-size="12" fill="#c0c0d0" text-anchor="middle">{$student->name} · sikhun.com</text>
        </svg>
        SVG;
    }

    public function isPlaceholder(PdfParsable $book): bool
    {
        $source = $book->pdfSourceId();
        // If pre-rendered page exists on disk, it is definitely not a placeholder!
        if (Storage::disk('private')->exists("books/rendered/{$source}/1.jpg")) {
            return false;
        }

        return ! $book->pdfFilePath() || ! Storage::disk('private')->exists($book->pdfFilePath());
    }
}
