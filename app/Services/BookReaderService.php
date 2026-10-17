<?php

namespace App\Services;

use App\Contracts\PdfParsable;
use App\Models\MyBook;
use App\Models\Student;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Turns one page of a private PDF into a watermarked JPEG — the ONLY way
 * book content is ever served to a browser. The PDF path itself never
 * appears in any response; callers only ever see rendered page bytes
 * behind a short-lived signed URL (see Student\ReaderController).
 */
class BookReaderService
{
    private const RASTER_TTL = 86400 * 7;
    private const WATERMARK_TTL = 86400 * 7;

    // Display-appropriate size: 120 DPI (≈992×1403 A4) is crisp on high-DPI
    // screens while keeping file sizes small (~120KB-200KB per page).
    private const RASTER_DPI = 120;
    private const JPEG_QUALITY = 82;

    private function baseNamespace(PdfParsable $book): string
    {
        return ($book instanceof MyBook) ? 'my_books' : 'books';
    }

    private function renderedRelPath(PdfParsable $book, int $page): string
    {
        $ns = $this->baseNamespace($book);
        $source = $book->pdfSourceId();

        return "{$ns}/rendered/{$source}/{$page}.jpg";
    }

    private function watermarkedRelPath(PdfParsable $book, int $page, int $studentId): string
    {
        $ns = $this->baseNamespace($book);
        $source = $book->pdfSourceId();

        return "{$ns}/watermarked/{$source}/{$studentId}/{$page}.jpg";
    }

    /**
     * Returns absolute path to the watermarked page file on disk so Laravel
     * can stream it via zero-copy sendfile() and browser HTTP caching.
     */
    public function renderPageFile(PdfParsable $book, int $page, Student $student): ?string
    {
        $watermarkedRelPath = $this->watermarkedRelPath($book, $page, $student->id);
        $disk = Storage::disk('private');

        if ($disk->exists($watermarkedRelPath)) {
            $path = $disk->path($watermarkedRelPath);
            if (file_exists($path) && filesize($path) > 0) {
                return $path;
            }
        }

        // Generate and cache to disk
        $bytes = $this->renderPage($book, $page, $student);
        if ($disk->exists($watermarkedRelPath)) {
            $path = $disk->path($watermarkedRelPath);
            if (file_exists($path) && filesize($path) > 0) {
                return $path;
            }
        }

        return null;
    }

    public function renderPage(PdfParsable $book, int $page, Student $student): string
    {
        $ns = $this->baseNamespace($book);
        $source = $book->pdfSourceId();
        $renderedRelPath = $this->renderedRelPath($book, $page);
        $watermarkedRelPath = $this->watermarkedRelPath($book, $page, $student->id);

        // 1. Ultra-fast path: already watermarked for this student on disk
        try {
            if (Storage::disk('private')->exists($watermarkedRelPath)) {
                $cachedWm = Storage::disk('private')->get($watermarkedRelPath);
                if ($cachedWm !== null && strlen($cachedWm) > 0) {
                    return $cachedWm;
                }
            }
        } catch (\Throwable) {
            // Continue if unreadable
        }

        // 2. Check if raw raster is already on disk (e.g. pre-rendered)
        $raw = null;
        try {
            if (Storage::disk('private')->exists($renderedRelPath)) {
                $raw = Storage::disk('private')->get($renderedRelPath);
            }
        } catch (\Throwable) {
            $raw = null;
        }

        // 3. Check memory/cache fallback
        if ($raw === null) {
            try {
                $raw = Cache::get("book_page:raw:{$ns}:{$source}:{$page}");
            } catch (\Throwable) {
                $raw = null;
            }
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
                try {
                    Storage::disk('private')->put($renderedRelPath, $raw);
                    @chmod(Storage::disk('private')->path($renderedRelPath), 0666);
                } catch (\Throwable) {
                }

                try {
                    Cache::put("book_page:raw:{$ns}:{$source}:{$page}", $raw, self::RASTER_TTL);
                } catch (\Throwable) {
                }
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
            @chmod(Storage::disk('private')->path($watermarkedRelPath), 0666);
        } catch (\Throwable) {
            // Ignore write errors to avoid blocking the user
        }

        return $watermarked;
    }

    /**
     * Pre-render all pages of a PDF book/myBook into JPEGs on disk using pdftoppm.
     * Returns the number of rendered pages.
     */
    public function preRenderAllPages(PdfParsable $book, bool $force = false): int
    {
        if (! $book->pdfFilePath() || ! Storage::disk('private')->exists($book->pdfFilePath())) {
            return 0;
        }

        $ns = $this->baseNamespace($book);
        $source = $book->pdfSourceId();
        $pdfPath = Storage::disk('private')->path($book->pdfFilePath());
        $renderedDir = Storage::disk('private')->path("{$ns}/rendered/{$source}");
        $watermarkedDir = Storage::disk('private')->path("{$ns}/watermarked/{$source}");

        if ($force) {
            Storage::disk('private')->deleteDirectory("{$ns}/rendered/{$source}");
            Storage::disk('private')->deleteDirectory("{$ns}/watermarked/{$source}");
        }

        if (! is_dir($renderedDir)) {
            @mkdir($renderedDir, 0777, true);
            @chmod($renderedDir, 0777);
        }

        $existingCount = count(glob("{$renderedDir}/*.jpg") ?: []);
        $expectedPages = $this->detectPageCount($pdfPath) ?? (int) ($book->total_pages ?? 0);

        if (! $force && $existingCount > 0 && ($expectedPages > 0 && $existingCount >= $expectedPages)) {
            return $existingCount;
        }

        if (function_exists('exec')) {
            $tempPrefix = "{$renderedDir}/page";
            $cmd = sprintf(
                'pdftoppm -jpeg -r %d %s %s 2>&1',
                self::RASTER_DPI,
                escapeshellarg($pdfPath),
                escapeshellarg($tempPrefix)
            );

            $output = [];
            $code = 0;
            @exec($cmd, $output, $code);

            if ($code === 0) {
                $generated = glob("{$renderedDir}/page-*.jpg") ?: [];
                foreach ($generated as $file) {
                    if (preg_match('/page-0*([0-9]+)\.jpg$/', $file, $matches)) {
                        $pageNum = (int) $matches[1];
                        $target = "{$renderedDir}/{$pageNum}.jpg";
                        @rename($file, $target);
                        @chmod($target, 0666);
                    }
                }
            }
        }

        return count(glob("{$renderedDir}/*.jpg") ?: []);
    }

    /**
     * Fast page count detection via poppler pdfinfo.
     */
    public function detectPageCount(string $pdfPath): ?int
    {
        if (! function_exists('exec') || ! is_file($pdfPath)) {
            return null;
        }

        $output = [];
        $code = 0;
        @exec('pdfinfo ' . escapeshellarg($pdfPath) . ' 2>/dev/null', $output, $code);

        if ($code === 0) {
            foreach ($output as $line) {
                if (preg_match('/^Pages:\s+(\d+)/i', $line, $m)) {
                    return max(1, (int) $m[1]);
                }
            }
        }

        return null;
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
                @mkdir($tempDir, 0777, true);
                @chmod($tempDir, 0777);
            }
            $tempPrefix = $tempDir . '/page_' . bin2hex(random_bytes(6)) . '_' . $page;
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
                $matches = glob($tempPrefix . '-*.jpg') ?: [];
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
        if (Storage::disk('private')->exists($this->renderedRelPath($book, 1))) {
            return false;
        }

        return ! $book->pdfFilePath() || ! Storage::disk('private')->exists($book->pdfFilePath());
    }
}
