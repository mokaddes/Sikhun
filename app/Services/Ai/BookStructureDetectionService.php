<?php

namespace App\Services\Ai;

use App\Contracts\ParsedDocument;
use Illuminate\Support\Facades\Log;

/** Detects headings in small page batches, eliminates running headers, and maps them back to exact PDF pages. */
class BookStructureDetectionService
{
    public function detect(ParsedDocument $doc): void
    {
        if (! $doc->pages) {
            return;
        }

        $batches = array_chunk($doc->pages, 10);
        $detected = [];
        try {
            $provider = AiProviderFactory::default('book_structure');
            foreach ($batches as $pages) {
                try {
                    $lines = [];
                    foreach ($pages as $page) {
                        $text = trim((string) ($page['content'] ?? ''));
                        if ($text === '') {
                            continue;
                        }
                        $text = mb_substr($text, 0, 3500);
                        $lines[] = 'PAGE '.(int) $page['page_number']."\n".$text;
                    }
                    if (! $lines) {
                        continue;
                    }

                    $prompt = "You are an expert textbook and document structure analyzer. "
                        ."Identify ONLY genuine major CHAPTER (অধ্যায় / Chapter) starting points in these extracted PDF pages.\n\n"
                        ."CRITICAL RULES:\n"
                        ."1. ONLY identify main top-level chapters (e.g. 'প্রথম অধ্যায়', 'দ্বিতীয় অধ্যায়' or 'অধ্যায় ১', 'অধ্যায় ২' or 'Chapter 1', 'Chapter 2').\n"
                        ."2. NEVER extract sections, sub-sections, topics, or অনুচ্ছেদ (e.g. '১.১', '১.২', '২.১.১', 'পাঠ', 'পরিচ্ছেদ', 'অনুচ্ছেদ')! Only main chapters.\n"
                        ."3. DO NOT extract repeating running headers/footers (textbooks repeat the chapter title on almost every page — you must ONLY identify the single page where a chapter actually BEGINS!).\n"
                        ."4. Strip printed page numbers from titles (e.g. if text says 'পরিসংখ্যান ৩৩২' or 'পরিমিতি ৩২৪', the title is 'পরিসংখ্যান' or 'পরিমিতি', do NOT include the page numbers in the title).\n"
                        ."5. Strip leading punctuation or noise (e.g. '. পরিসংখ্যান' -> 'পরিসংখ্যান').\n"
                        ."6. Return valid JSON only: {\"headings\":[{\"title\":string,\"chapter_number\":string|null,\"level\":1,\"page\":integer}]}. Every item MUST have level: 1.\n\n"
                        .implode("\n\n", $lines);

                    $raw = $provider->chat([['role' => 'user', 'content' => $prompt]], ['temperature' => 0]);
                    $json = json_decode(trim(preg_replace('/^```(?:json)?|```$/m', '', $raw)), true, 512, JSON_THROW_ON_ERROR);
                    foreach (($json['headings'] ?? []) as $heading) {
                        $pageNumbers = array_column($pages, 'page_number');
                        if (! in_array((int) ($heading['page'] ?? 0), $pageNumbers, true)) {
                            continue;
                        }
                        $level = (int) ($heading['level'] ?? 1);
                        $title = trim((string) ($heading['title'] ?? ''));
                        if ($level !== 1 || $title === '') {
                            continue;
                        }
                        // Skip sub-section numbers like 1.1, ১.১, or words like অনুচ্ছেদ, পাঠ, পরিচ্ছেদ
                        if (preg_match('/[০-৯0-9]+\.[০-৯0-9]+/u', $title) || preg_match('/(?:অনুচ্ছেদ|পাঠ|পরিচ্ছেদ|টপিক|section|topic|lesson|উপঅধ্যায়|উপ-অধ্যায়)/iu', $title)) {
                            continue;
                        }
                        $detected[] = [
                            'title' => mb_substr($title, 0, 255),
                            'chapter_number' => isset($heading['chapter_number']) ? (string) $heading['chapter_number'] : null,
                            'level' => 1,
                            'start_page' => (int) $heading['page'],
                        ];
                    }
                } catch (\Throwable $batchErr) {
                    Log::warning('Book structure detection batch skipped: '.$batchErr->getMessage());
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Book structure AI detection unavailable, using heuristic fallback: '.$e->getMessage());
        }

        if (! $detected) {
            $detected = $this->detectHeuristicHeadings($doc);
        }

        // Apply strict deduplication & running header filtering on all detected headings
        $detected = $this->deduplicateAndCleanHeadings($detected);

        if (! $detected) {
            if (! $doc->chapters) {
                $lastPage = max(array_map(fn ($p) => (int) $p['page_number'], $doc->pages));
                $doc->chapters = [[
                    'chapter_number' => '1',
                    'title' => 'সম্পূর্ণ বই (Full Book)',
                    'level' => 1,
                    'start_page' => 1,
                    'end_page' => max(1, $lastPage),
                    'parent_index' => null,
                    'metadata' => ['generated_by' => 'fallback', 'structure_uncertain' => true],
                    'content' => $this->pageContent($doc, 1, $lastPage),
                ]];

                return;
            }

            foreach ($doc->chapters as &$chapter) {
                $chapter['metadata'] = array_merge($chapter['metadata'] ?? [], ['structure_uncertain' => true]);
                $chapter['content'] = $this->pageContent($doc, $chapter['start_page'] ?? 1, $chapter['end_page'] ?? null);
            }
            unset($chapter);

            return;
        }

        usort($detected, fn ($a, $b) => [$a['start_page'], $a['level']] <=> [$b['start_page'], $b['level']]);
        $lastPage = max(array_map(fn ($p) => (int) $p['page_number'], $doc->pages));
        $flat = [];
        $stack = [];
        foreach ($detected as $i => $heading) {
            $level = $heading['level'];
            $end = $lastPage;
            for ($j = $i + 1; $j < count($detected); $j++) {
                if ($detected[$j]['level'] <= $level) {
                    $end = $detected[$j]['start_page'] - 1;
                    break;
                }
            }
            $stack = array_slice($stack, 0, $level - 1);
            $parentIndex = $level === 1 ? null : ($stack[$level - 2] ?? null);
            $content = $this->pageContent($doc, $heading['start_page'], $end);
            $flat[] = [
                'chapter_number' => $heading['chapter_number'],
                'title' => $heading['title'],
                'level' => $level,
                'start_page' => $heading['start_page'],
                'end_page' => max($heading['start_page'], $end),
                'parent_index' => $parentIndex,
                'metadata' => ['generated_by' => 'book_structure', 'structure_uncertain' => false],
                'content' => $content,
            ];
            $stack[$level - 1] = count($flat) - 1;
        }
        $doc->chapters = $flat;
    }

    /**
     * Clean up titles, strip running headers & trailing page numbers, and eliminate duplicate chapters.
     */
    public function deduplicateAndCleanHeadings(array $headings): array
    {
        if (empty($headings)) {
            return [];
        }

        // Sort primarily by page number, then by level
        usort($headings, fn ($a, $b) => [$a['start_page'], $a['level']] <=> [$b['start_page'], $b['level']]);

        $cleaned = [];
        $seenNumbers = [];
        $seenTitles = [];

        foreach ($headings as $h) {
            $level = (int) ($h['level'] ?? 1);
            if ($level !== 1) {
                continue; // Strictly enforce top-level chapters only, no sections/sub-sections
            }

            $rawTitle = trim((string) ($h['title'] ?? ''));
            $num = isset($h['chapter_number']) ? trim((string) $h['chapter_number']) : null;
            $startPage = (int) ($h['start_page'] ?? 1);

            // Skip sub-section notation (e.g. 1.1, ২.১, 2.1.3, অনুচ্ছেদ, পাঠ, পরিচ্ছেদ)
            if (preg_match('/[০-৯0-9]+\.[০-৯0-9]+/u', $rawTitle) || preg_match('/[০-৯0-9]+\.[০-৯0-9]+/u', $num ?? '')) {
                continue;
            }
            if (preg_match('/(?:অনুচ্ছেদ|পাঠ|পরিচ্ছেদ|টপিক|section|topic|lesson|উপঅধ্যায়|উপ-অধ্যায়)/iu', $rawTitle)) {
                continue;
            }

            // Strip trailing printed page numbers and common OCR artifacts (e.g., '৩৩২', '৩৩৪', '332', '©', 'wo')
            $title = preg_replace('/\s+[০-৯0-9©®wo]+(?:\.)?$/u', '', $rawTitle);
            // Strip leading dot, colon, dash noise (e.g. '. বাস্তব সংখ্যা' -> 'বাস্তব সংখ্যা')
            $title = preg_replace('/^[\s\.\:\-–—]+/', '', $title);

            // Extract chapter number from title if missing (e.g. 'অধ্যায় ১৭: পরিসংখ্যান')
            if (! $num && preg_match('/(?:অধ্যায়|অধ্যায়|Chapter)\s*([০-৯0-9IVXivx]+)/iu', $title, $m)) {
                $num = $m[1];
            }

            // Clean title of repetitive 'অধ্যায় ১৭:' prefix if chapter_number is already set
            if ($num) {
                $title = preg_replace('/^(?:অধ্যায়|অধ্যায়|Chapter)\s*[০-৯0-9IVXivx]+\s*[:\-–—\.]*\s*/iu', '', $title);
            }
            $title = trim($title);

            if ($title === '') {
                $title = $num ? "অধ্যায় {$num}" : 'অধ্যায়';
            }

            // Normalize for deduplication comparison
            $normNum = $num !== null ? $this->normalizeNumeral($num) : null;
            $normTitle = mb_strtolower(preg_replace('/[^\p{L}\p{N}]+/u', '', $title));

            // Check if this is a duplicate of an already seen chapter
            if ($level === 1 && $normNum !== null) {
                if (isset($seenNumbers[$normNum])) {
                    // Running header duplicate! If the previous entry had a shorter/noisier title, upgrade its title
                    $prevIdx = $seenNumbers[$normNum];
                    if (mb_strlen($title) > mb_strlen($cleaned[$prevIdx]['title']) && ! preg_match('/[০-৯0-9]$/u', $title)) {
                        $cleaned[$prevIdx]['title'] = $title;
                    }
                    continue; // Skip creating a new chapter
                }
            }

            // If same level and identical normalized title starts within 6 pages, it's a running header repetition
            if (isset($seenTitles[$normTitle])) {
                $prevIdx = $seenTitles[$normTitle];
                if ($level === $cleaned[$prevIdx]['level'] && abs($startPage - $cleaned[$prevIdx]['start_page']) <= 12) {
                    continue; // Skip running header duplicate
                }
            }

            $entryIndex = count($cleaned);
            $cleaned[] = [
                'title' => $title,
                'chapter_number' => $num,
                'level' => $level,
                'start_page' => $startPage,
            ];

            if ($level === 1 && $normNum !== null) {
                $seenNumbers[$normNum] = $entryIndex;
            }
            $seenTitles[$normTitle] = $entryIndex;
        }

        return $cleaned;
    }

    private function detectHeuristicHeadings(ParsedDocument $doc): array
    {
        $detected = [];
        $ordinalMap = [
            'প্রথম' => '১', 'দ্বিতীয়' => '২', 'দ্বিতীয়' => '২', 'তৃতীয়' => '৩', 'তৃতীয়' => '৩',
            'চতুর্থ' => '৪', 'পঞ্চম' => '৫', 'ষষ্ঠ' => '৬', 'সপ্তম' => '৭', 'অষ্টম' => '৮',
            'নবম' => '৯', 'দশম' => '১০', 'একাদশ' => '১১', 'দ্বাদশ' => '১২',
            'ত্রয়োদশ' => '১৩', 'ত্রয়োদশ' => '১৩', 'চতুর্দশ' => '১৪', 'পঞ্চদশ' => '১৫',
            'ষোড়শ' => '১৬', 'ষোড়শ' => '১৬', 'সপ্তদশ' => '১৭', 'অষ্টাদশ' => '১৮',
            'ঊনবিংশ' => '১৯', 'বিংশ' => '২০', 'একবিংশ' => '২১', 'দ্বাবিংশ' => '২২',
            'ত্রয়োবিংশ' => '২৩', 'চতুর্বিংশ' => '২৪', 'পঞ্চবিংশ' => '২৫',
        ];
        $ordinalRegex = '/^(প্রথম|দ্বিতীয়|দ্বিতীয়|তৃতীয়|তৃতীয়|চতুর্থ|পঞ্চম|ষষ্ঠ|সপ্তম|অষ্টম|নবম|দশম|একাদশ|দ্বাদশ|ত্রয়োদশ|ত্রয়োদশ|চতুর্দশ|পঞ্চদশ|ষোড়শ|ষোড়শ|সপ্তদশ|অষ্টাদশ|ঊনবিংশ|বিংশ|একবিংশ|দ্বাবিংশ|ত্রয়োবিংশ|চতুর্বিংশ|পঞ্চবিংশ)\s+(অধ্যায়|অধ্যায়|Chapter)\s*[:\-–—]?\s*(.*)$/u';
        $prefixRegex = '/^(অধ্যায়|অধ্যায়|Chapter)\s*[:\-–—]?\s*([০-৯0-9IVXivx]+)\s*[:\-–—]?\s*(.*)$/iu';

        $lastSeenNum = null;
        $lastSeenPage = 0;

        foreach ($doc->pages as $page) {
            $pageNum = (int) ($page['page_number'] ?? 0);
            if ($pageNum <= 0) {
                continue;
            }
            $text = trim((string) ($page['content'] ?? ''));
            if ($text === '') {
                continue;
            }

            $lines = array_slice(array_values(array_filter(array_map('trim', explode("\n", $text)))), 0, 8);
            // Skip Table of Contents (সূচিপত্র) pages
            $topSample = implode(' ', array_slice($lines, 0, 4));
            if (preg_match('/(সূচিপত্র|শিরোনাম\s+পৃষ্ঠা|table\s+of\s+contents)/iu', $topSample)) {
                continue;
            }

            foreach ($lines as $idx => $line) {
                if (mb_strlen($line) > 120) {
                    continue;
                }

                // Check for running header pattern: contains current page number at the end
                if (preg_match('/(?:[০-৯0-9]+)\s*$/u', $line) && $idx <= 1) {
                    continue;
                }

                // Skip sub-sections (e.g. 1.1, ২.১, অনুচ্ছেদ, পাঠ, পরিচ্ছেদ)
                if (preg_match('/[০-৯0-9]+\.[০-৯0-9]+/u', $line) || preg_match('/(?:অনুচ্ছেদ|পাঠ|পরিচ্ছেদ|টপিক|section|topic|lesson|উপঅধ্যায়|উপ-অধ্যায়)/iu', $line)) {
                    continue;
                }

                if (preg_match($ordinalRegex, $line, $m)) {
                    $ordWord = trim($m[1]);
                    $kind = trim($m[2]);
                    $rest = trim($m[3] ?? '');
                    if ($rest === '' && isset($lines[$idx + 1]) && mb_strlen($lines[$idx + 1]) <= 100) {
                        $rest = $lines[$idx + 1];
                    }
                    $num = $ordinalMap[$ordWord] ?? $ordWord;

                    // If same chapter number was already seen within 50 pages, it is a running header
                    if ($lastSeenNum === $this->normalizeNumeral($num)) {
                        continue;
                    }

                    $title = trim($rest !== '' ? $rest : "{$ordWord} {$kind}");
                    $detected[] = [
                        'title' => mb_substr($title, 0, 255),
                        'chapter_number' => $num,
                        'level' => 1,
                        'start_page' => $pageNum,
                    ];
                    $lastSeenNum = $this->normalizeNumeral($num);
                    $lastSeenPage = $pageNum;
                    break;
                }

                if (preg_match($prefixRegex, $line, $m)) {
                    $prefix = trim($m[1]);
                    $num = trim($m[2]);
                    $rest = trim($m[3] ?? '');
                    if ($rest === '' && isset($lines[$idx + 1]) && mb_strlen($lines[$idx + 1]) <= 100) {
                        $rest = $lines[$idx + 1];
                    }

                    // If same chapter number was already seen, it is a running header
                    if ($lastSeenNum === $this->normalizeNumeral($num)) {
                        continue;
                    }

                    $title = trim($rest !== '' ? $rest : "{$prefix} {$num}");
                    $detected[] = [
                        'title' => mb_substr($title, 0, 255),
                        'chapter_number' => $num,
                        'level' => 1,
                        'start_page' => $pageNum,
                    ];
                    $lastSeenNum = $this->normalizeNumeral($num);
                    $lastSeenPage = $pageNum;
                    break;
                }
            }
        }

        return $detected;
    }

    private function normalizeNumeral(string $str): string
    {
        $bn = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return str_replace($bn, $en, trim($str));
    }

    private function pageContent(ParsedDocument $doc, int $start, ?int $end): string
    {
        $combined = collect($doc->pages)
            ->filter(fn ($page) => (int) $page['page_number'] >= $start && ($end === null || (int) $page['page_number'] <= $end))
            ->map(fn ($page) => trim((string) ($page['content'] ?? '')))
            ->filter()
            ->implode("\n\n");

        return mb_substr($combined, 0, 16000);
    }
}
