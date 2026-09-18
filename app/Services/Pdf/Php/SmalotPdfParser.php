<?php

namespace App\Services\Pdf\Php;

use App\Contracts\ParsedDocument;
use App\Contracts\PdfParserContract;
use App\Models\Book;
use App\Services\Pdf\PdfParserException;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

/**
 * PHP-native fallback parser built on smalot/pdfparser (already in
 * composer.json). Runs when the OpenDataLoader binary is unavailable —
 * text extraction remains reliable; structure detection is heuristic:
 *
 *   1. PDF outline/bookmarks (best)
 *   2. Font-size outlier headings on each page
 *
 * Tables/images/formulas are NOT extracted here (smalot cannot do them
 * reliably) — the book still gets pages, chapters, text elements and
 * RAG chunks, just no specialized element rows. Graceful degradation,
 * never a hard failure.
 */
class SmalotPdfParser implements PdfParserContract
{
    private const MIN_HEADING_FONT_RATIO = 1.15; // heading font ÷ body font

    public function isAvailable(): bool
    {
        return class_exists(Parser::class);
    }

    public function parse(Book $book): ParsedDocument
    {
        if (! $book->pdf_path || ! Storage::disk('private')->exists($book->pdf_path)) {
            throw new PdfParserException("Book {$book->id} has no PDF on the private disk.");
        }

        try {
            $parser = new Parser();
            $pdf = $parser->parseFile(Storage::disk('private')->path($book->pdf_path));
        } catch (\Throwable $e) {
            throw new PdfParserException('smalot could not open the PDF: '.$e->getMessage(), 0, $e);
        }

        $doc = new ParsedDocument();
        $doc->parserName = 'smalot';
        $doc->parserVersion = '2.x';

        foreach ($pdf->getPages() as $index => $page) {
            $pageNumber = $index + 1;

            try {
                $text = $this->cleanText($page->getText());
            } catch (\Throwable) {
                $text = null; // one unreadable page must not fail the book
            }

            $doc->pages[] = [
                'page_number' => $pageNumber,
                'content' => $text,
                'metadata' => null,
            ];

            if ($text !== null) {
                $this->extractElementsFromText($doc, $pageNumber, $text);
            }
        }

        // Prefer the PDF's own outline when present — it is the most
        // reliable chapter signal a PDF can carry.
        try {
            $this->extractChaptersFromOutline($pdf, $doc);
        } catch (\Throwable $e) {
            // fall through to heading-derived chapters
        }

        if (empty($doc->chapters)) {
            $this->deriveChaptersFromHeadings($doc);
        }

        return $doc;
    }

    /**
     * The most common font size across the document is almost always the
     * body font. (Currently unused by the text heuristic but retained for
     * future font-metric-based heading detection.)
     */
    private function estimateBodyFontSize($pdf): ?float
    {
        try {
            $fonts = $pdf->getFonts();
            $sizes = [];

            foreach (array_slice($fonts, 0, 50) as $font) {
                $size = method_exists($font, 'getSize') ? $font->getSize() : null;
                if ($size !== null && $size > 0) {
                    $sizes[] = round((float) $size, 1);
                }
            }

            if (! $sizes) {
                return null;
            }

            $counts = array_count_values($sizes);

            return (float) array_search(max($counts), $counts, true);
        } catch (\Throwable) {
            return null;
        }
    }

    private function extractElementsFromText(ParsedDocument $doc, int $pageNumber, string $text): void
    {
        foreach ($this->splitParagraphs($text) as $paragraph) {
            $doc->elements[] = [
                'type' => $this->classifyParagraph($paragraph) === 'heading' ? 'heading' : 'text',
                'page_number' => $pageNumber,
                'content' => $paragraph,
                'metadata' => null,
                'bbox' => null,
                'heading_path' => null,
            ];
        }
    }

    private function splitParagraphs(string $text): array
    {
        $parts = preg_split('/\n\s*\n/', $text) ?: [];

        return array_values(array_filter(array_map('trim', $parts), fn ($p) => $p !== ''));
    }

    /**
     * Heuristic heading test (no per-run font metrics available from
     * smalot's text extraction): short single-line, Title Case or ALL
     * CAPS, no terminal punctuation, optionally numbered ("2.1 Distance").
     *
     * Case-based tests only apply to scripts that HAVE case (Latin,
     * Cyrillic, Greek). For caseless scripts (Bengali, Arabic, Devanagari,
     * CJK, …) mb_strtoupper()/title-case are identity transforms, so a
     * naive check would classify nearly every short CV line (name, phone,
     * email, address — usually "Samiha Rahman" or "সামিহা রহমান") as a
     * heading. Headings are deliberately excluded from RAG chunks, which
     * would strip the book of most of its content. Without caseless-script
     * awareness, a Bengali CV degrades to a few fragments — exactly the
     * "chat can't find anything" failure.
     */
    private function classifyParagraph(string $paragraph): string
    {
        $lines = explode("\n", $paragraph);
        if (count($lines) !== 1) {
            return 'text';
        }

        $line = trim($lines[0]);
        if ($line === '') {
            return 'text';
        }

        $isNumbered = (bool) preg_match('/^\d+(\.\d+)*[.:\s]/', $line);
        $hasCasedLetters = preg_match('/[A-Za-z\u00C0-\u024F\u0370-\u03FF\u0400-\u04FF]/u', $line) === 1;

        $looksTitleish = mb_strlen($line) <= 80
            && ($isNumbered
                || ($hasCasedLetters && mb_strtoupper($line, 'UTF-8') === $line)
                || ($hasCasedLetters && $line === mb_convert_case($line, MB_CASE_TITLE, 'UTF-8')))
            && ! preg_match('/[.!?,;:]$/', $line);

        return $looksTitleish ? 'heading' : 'text';
    }

    private function extractChaptersFromOutline($pdf, ParsedDocument $doc): void
    {
        // smalot does not expose the outline reliably across versions —
        // attempt the raw object, skip silently when absent.
        $outlineTitles = [];

        foreach ($pdf->getObjects() as $object) {
            try {
                $details = $object->getDetails();
            } catch (\Throwable) {
                continue;
            }

            $title = $details['Title'] ?? null;
            if (is_string($title) && $title !== '') {
                $outlineTitles[] = $title;
            }
        }

        foreach ($outlineTitles as $title) {
            $doc->chapters[] = [
                'chapter_number' => null,
                'title' => $this->cleanText($title),
                'level' => 1,
                'start_page' => null,
                'end_page' => null,
                'parent_index' => null,
                'metadata' => ['source' => 'outline'],
            ];
        }
    }

    private function deriveChaptersFromHeadings(ParsedDocument $doc): void
    {
        foreach ($doc->elements as $element) {
            if ($element['type'] !== 'heading') {
                continue;
            }

            $title = trim((string) $element['content']);
            if ($title === '') {
                continue;
            }

            $doc->chapters[] = [
                'chapter_number' => $this->headingNumber($title),
                'title' => $title,
                'level' => $this->headingLevel($title),
                'start_page' => $element['page_number'],
                'end_page' => null,
                'parent_index' => null, // resolved by the storage service
                'metadata' => ['source' => 'heading'],
            ];
        }
    }

    /** "2.1 Distance" → level 2; "Chapter 3" → 1. */
    private function headingLevel(string $title): int
    {
        if (preg_match('/^(\d+(?:\.\d+){0,3})[\s.]/', $title, $m)) {
            return min(substr_count($m[1], '.') + 1, 4);
        }

        return 1;
    }

    private function headingNumber(?string $title): ?string
    {
        return preg_match('/^(\d+(?:\.\d+){0,3})[\s.]/', (string) $title, $m) ? $m[1] : null;
    }

    private function cleanText(string $text): string
    {
        // Collapse internal whitespace but preserve line structure so
        // paragraph splitting still works.
        return trim(preg_replace('/[ \t]+/', ' ', $text) ?? $text);
    }
}
