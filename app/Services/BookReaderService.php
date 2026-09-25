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
    private const RASTER_TTL = 86400;
    private const WATERMARK_TTL = 86400;

    // Display-appropriate size: the reader shows pages ~380×537 CSS px, so 120
    // DPI (≈992×1403 A4) is sharp enough on real devices while cutting payload
    // ~45% vs 150 DPI. Raise back to 150 if clients want crisper high-DPI text.
    private const RASTER_DPI = 120;
    private const JPEG_QUALITY = 82;

    public function renderPage(PdfParsable $book, int $page, Student $student): string
    {
        $source = $book->pdfSourceId();

        $exists = $book->pdfFilePath() && Storage::disk('private')->exists($book->pdfFilePath());
        if (! $exists || ! extension_loaded('imagick')) {
            $note = null;
            if ($exists && ! extension_loaded('imagick')) {
                $note = 'Imagick extension not installed on this server.';
            }
            // Placeholder contains the student's name, so keep it per-student
            // (it's tiny SVG and cheap to rebuild).
            return Cache::remember("book_page:pl:{$source}:{$page}:{$student->id}", self::RASTER_TTL, function () use ($book, $page, $student, $note) {
                return $this->placeholderImage($book, $page, $student, $note);
            });
        }

        $pdfPath = Storage::disk('private')->path($book->pdfFilePath());

        // Expensive step — shared by every student, so it runs once per page.
        $raw = Cache::remember("book_page:raw:{$source}:{$page}", self::RASTER_TTL, function () use ($pdfPath, $page) {
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
        });

        if ($raw === null) {
            return Cache::remember("book_page:pl:{$source}:{$page}:{$student->id}", self::RASTER_TTL, function () use ($book, $page, $student) {
                return $this->placeholderImage($book, $page, $student, 'Could not render this page.');
            });
        }

        // Cheap step — overlay this student's watermark on the shared raster.
        return Cache::remember("book_page:wm:{$source}:{$page}:{$student->id}", self::WATERMARK_TTL, function () use ($raw, $student) {
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
                // Unwatermarked fallback beats a broken reader; the signed URL
                // still gates access per student.
                return $raw;
            }
        });
    }

    /**
     * SVG placeholder (converted to nothing extra needed — served with
     * image/svg+xml content type) so the reader UI works before real PDFs
     * exist, and so a render failure never surfaces a raw stack trace.
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
        return ! $book->pdfFilePath() || ! Storage::disk('private')->exists($book->pdfFilePath()) || ! extension_loaded('imagick');
    }
}
