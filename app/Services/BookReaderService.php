<?php

namespace App\Services;

use App\Models\Book;
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
    public function renderPage(Book $book, int $page, Student $student): string
    {
        $cacheKey = "book_page:{$book->id}:{$page}:{$student->id}";

        return Cache::remember($cacheKey, 900, function () use ($book, $page, $student) {
            if (! $book->pdf_path || ! Storage::disk('private')->exists($book->pdf_path)) {
                return $this->placeholderImage($book, $page, $student);
            }

            if (! extension_loaded('imagick')) {
                return $this->placeholderImage($book, $page, $student, 'Imagick extension not installed on this server.');
            }

            try {
                return $this->renderFromPdf($book, $page, $student);
            } catch (\Throwable $e) {
                return $this->placeholderImage($book, $page, $student, 'Could not render this page: '.$e->getMessage());
            }
        });
    }

    private function renderFromPdf(Book $book, int $page, Student $student): string
    {
        $pdfPath = Storage::disk('private')->path($book->pdf_path);

        $imagick = new \Imagick();
        $imagick->setResolution(150, 150);
        $imagick->readImage("{$pdfPath}[".($page - 1).']');
        $imagick->setImageFormat('jpg');
        $imagick->setImageCompressionQuality(85);

        $draw = new \ImagickDraw();
        $draw->setFillColor(new \ImagickPixel('rgba(0,0,0,0.28)'));
        $draw->setFontSize(16);
        $draw->setTextAntialias(true);
        $watermark = "{$student->name} · ID:{$student->id} · sikhun.com";
        $imagick->annotateImage($draw, 16, 28, 0, $watermark);

        $blob = $imagick->getImageBlob();
        $imagick->clear();

        return $blob;
    }

    /**
     * SVG placeholder (converted to nothing extra needed — served with
     * image/svg+xml content type) so the reader UI works before real PDFs
     * exist, and so a render failure never surfaces a raw stack trace.
     */
    private function placeholderImage(Book $book, int $page, Student $student, ?string $note = null): string
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

    public function isPlaceholder(Book $book): bool
    {
        return ! $book->pdf_path || ! Storage::disk('private')->exists($book->pdf_path) || ! extension_loaded('imagick');
    }
}
