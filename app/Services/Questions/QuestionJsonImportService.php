<?php

namespace App\Services\Questions;

use App\Models\QuestionBank;
use App\Models\QuestionSubject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class QuestionJsonImportService
{
    /**
     * In-memory cache for resolved subjects to prevent repeated database queries.
     * key: normalized subject name -> subject_id
     */
    protected array $subjectCache = [];

    /**
     * Map of common raw subject strings to standardized Bangla and English names and slugs.
     */
    protected array $subjectMap = [
        'বাংলা' => ['bn' => 'বাংলা', 'en' => 'Bangla', 'slug' => 'bangla'],
        'বাংলা ভাষা' => ['bn' => 'বাংলা', 'en' => 'Bangla', 'slug' => 'bangla'],
        'বাংলা সাহিত্য' => ['bn' => 'বাংলা সাহিত্য', 'en' => 'Bangla Literature', 'slug' => 'bangla-literature'],
        'বাংলা ভাষা ও সাহিত্য' => ['bn' => 'বাংলা ভাষা ও সাহিত্য', 'en' => 'Bangla Language & Literature', 'slug' => 'bangla-language-literature'],
        'বাংলা ভাষা ও ব্যাকরণ' => ['bn' => 'বাংলা ভাষা ও ব্যাকরণ', 'en' => 'Bangla Language & Grammar', 'slug' => 'bangla-language-grammar'],

        'english' => ['bn' => 'ইংরেজি', 'en' => 'English', 'slug' => 'english'],
        'english language' => ['bn' => 'ইংরেজি ভাষা', 'en' => 'English Language', 'slug' => 'english-language'],
        'english literature' => ['bn' => 'ইংরেজি সাহিত্য', 'en' => 'English Literature', 'slug' => 'english-literature'],
        'english language and literature' => ['bn' => 'ইংরেজি ভাষা ও সাহিত্য', 'en' => 'English Language and Literature', 'slug' => 'english-language-and-literature'],
        'english language & literature' => ['bn' => 'ইংরেজি ভাষা ও সাহিত্য', 'en' => 'English Language and Literature', 'slug' => 'english-language-and-literature'],

        'গণিত' => ['bn' => 'গণিত', 'en' => 'Mathematics', 'slug' => 'mathematics'],
        'গণিত (mathematics)' => ['bn' => 'গণিত', 'en' => 'Mathematics', 'slug' => 'mathematics'],
        'গাণিতিক যুক্তি' => ['bn' => 'গাণিতিক যুক্তি', 'en' => 'Mathematical Reasoning', 'slug' => 'mathematical-reasoning'],
        'উচ্চতর গণিত' => ['bn' => 'উচ্চতর গণিত', 'en' => 'Higher Mathematics', 'slug' => 'higher-mathematics'],

        'সাধারণ জ্ঞান' => ['bn' => 'সাধারণ জ্ঞান', 'en' => 'General Knowledge', 'slug' => 'general-knowledge'],
        'বাংলাদেশ বিষয়াবলি' => ['bn' => 'বাংলাদেশ বিষয়াবলি', 'en' => 'Bangladesh Affairs', 'slug' => 'bangladesh-affairs'],
        'বাংলাদেশ বিষয়াবলি' => ['bn' => 'বাংলাদেশ বিষয়াবলি', 'en' => 'Bangladesh Affairs', 'slug' => 'bangladesh-affairs'],
        'আন্তর্জাতিক বিষয়াবলি' => ['bn' => 'আন্তর্জাতিক বিষয়াবলি', 'en' => 'International Affairs', 'slug' => 'international-affairs'],
        'আন্তর্জাতিক বিষয়াবলি' => ['bn' => 'আন্তর্জাতিক বিষয়াবলি', 'en' => 'International Affairs', 'slug' => 'international-affairs'],

        'সাধারণ বিজ্ঞান' => ['bn' => 'সাধারণ বিজ্ঞান', 'en' => 'General Science', 'slug' => 'general-science'],
        'সাধারণ জ্ঞান ও বিজ্ঞান' => ['bn' => 'সাধারণ জ্ঞান ও বিজ্ঞান', 'en' => 'GK & General Science', 'slug' => 'gk-general-science'],
        'জীববিজ্ঞান' => ['bn' => 'জীববিজ্ঞান', 'en' => 'Biology', 'slug' => 'biology'],

        'কম্পিউটার ও তথ্যপ্রযুক্তি' => ['bn' => 'তথ্য ও যোগাযোগ প্রযুক্তি', 'en' => 'ICT & Computer', 'slug' => 'ict-computer'],
        'কম্পিউটার ও তথ্য প্রযুক্তি' => ['bn' => 'তথ্য ও যোগাযোগ প্রযুক্তি', 'en' => 'ICT & Computer', 'slug' => 'ict-computer'],
        'তথ্য ও যোগাযোগ প্রযুক্তি' => ['bn' => 'তথ্য ও যোগাযোগ প্রযুক্তি', 'en' => 'ICT & Computer', 'slug' => 'ict-computer'],

        'মানসিক দক্ষতা' => ['bn' => 'মানসিক দক্ষতা', 'en' => 'Mental Ability', 'slug' => 'mental-ability'],
        'নৈতিকতা, মূল্যবোধ ও সুশাসন' => ['bn' => 'নৈতিকতা, মূল্যবোধ ও সুশাসন', 'en' => 'Ethics, Values & Governance', 'slug' => 'ethics-values-governance'],
        'নৈতিকতা, মূল্যবোধ ও সু-শাসন' => ['bn' => 'নৈতিকতা, মূল্যবোধ ও সুশাসন', 'en' => 'Ethics, Values & Governance', 'slug' => 'ethics-values-governance'],
        'ভূগোল ও দুর্যোগ ব্যবস্থাপনা' => ['bn' => 'ভূগোল ও দুর্যোগ ব্যবস্থাপনা', 'en' => 'Geography & Disaster Management', 'slug' => 'geography-disaster'],
        'ভূগোল (বাংলাদেশ ও বিশ্ব), পরিবেশ ও দুর্যোগ ব্যবস্থাপনা' => ['bn' => 'ভূগোল ও দুর্যোগ ব্যবস্থাপনা', 'en' => 'Geography & Disaster Management', 'slug' => 'geography-disaster'],
    ];

    public function __construct()
    {
        $this->warmSubjectCache();
    }

    /**
     * Preload all existing subjects from DB into cache.
     */
    protected function warmSubjectCache(): void
    {
        $subjects = QuestionSubject::all();
        foreach ($subjects as $sub) {
            $this->subjectCache[mb_strtolower(trim($sub->name_bn))] = $sub->id;
            $this->subjectCache[mb_strtolower(trim($sub->name_en))] = $sub->id;
            $this->subjectCache[$sub->slug] = $sub->id;
        }
    }

    /**
     * Import a JSON file from disk.
     */
    public function importFile(string $filePath, array $options = [], ?callable $progressCallback = null): array
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("File not found: {$filePath}");
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new \RuntimeException("Unable to read file: {$filePath}");
        }

        return $this->importJsonContent($content, array_merge(['source_filename' => basename($filePath)], $options), $progressCallback);
    }

    /**
     * Import raw JSON string content.
     */
    public function importJsonContent(string $jsonString, array $options = [], ?callable $progressCallback = null): array
    {
        $data = json_decode($jsonString, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException("Invalid JSON: " . json_last_error_msg());
        }

        $exams = [];
        if (isset($data['exams']) && is_array($data['exams'])) {
            $exams = $data['exams'];
        } elseif (is_array($data) && isset($data[0]['questions'])) {
            $exams = $data;
        } elseif (is_array($data) && isset($data[0]['question'])) {
            // Flat question array
            $exams = [[
                'title' => $options['exam_name'] ?? 'Imported Exam',
                'series' => $options['series'] ?? 'bulk_import',
                'questions' => $data,
            ]];
        }

        $totalExams = count($exams);
        $totalQuestionsInFile = 0;
        $totalInserted = 0;
        $totalSkipped = 0;

        $batchSize = 250;
        $pendingRows = [];

        // Preload recent question hashes to avoid inserting exact duplicates:
        // Hash = md5(exam_name . '||' . question_text)
        $existingHashes = [];
        QuestionBank::select(['exam_name', 'question_text'])
            ->chunk(5000, function ($chunk) use (&$existingHashes) {
                foreach ($chunk as $q) {
                    $key = md5(trim((string)$q->exam_name) . '||' . trim((string)$q->question_text));
                    $existingHashes[$key] = true;
                }
            });

        foreach ($exams as $examIndex => $exam) {
            $rawTitle = trim((string)($exam['title'] ?? ''));
            $examName = $this->cleanExamName($rawTitle);
            $series = $exam['series'] ?? $options['series'] ?? null;
            $segment = $options['segment'] ?? $this->detectSegment($examName, $series);
            $year = $this->extractYear($rawTitle) ?? $this->extractYear($series);
            $shiftOrSet = $this->extractSet($rawTitle);
            $originTag = $this->generateOriginTag($examName, $series, $year);
            $readUrl = $exam['readUrl'] ?? $options['source_url'] ?? null;

            $questions = $exam['questions'] ?? [];
            $totalQuestionsInFile += count($questions);

            foreach ($questions as $qIndex => $q) {
                $questionText = trim((string)($q['question'] ?? ''));
                if (empty($questionText)) {
                    $totalSkipped++;
                    continue;
                }

                // Check duplicate hash
                $hashKey = md5($examName . '||' . $questionText);
                if (isset($existingHashes[$hashKey])) {
                    $totalSkipped++;
                    continue;
                }
                $existingHashes[$hashKey] = true;

                $rawOptions = $q['options'] ?? [];
                $formattedOptions = $this->formatOptions($rawOptions);
                $correctOption = $this->determineCorrectOption($q['answer'] ?? null, $formattedOptions);
                $subjectId = $this->resolveSubject($q['subject'] ?? null, $segment);

                $pendingRows[] = [
                    'segment' => $segment,
                    'subject_id' => $subjectId,
                    'topic_id' => null,
                    'topic_name' => $q['topic'] ?? null,
                    'institution' => $this->extractInstitution($examName),
                    'exam_name' => $examName,
                    'origin_tag' => $originTag,
                    'year' => $year,
                    'session' => null,
                    'shift_or_set' => $shiftOrSet,
                    'question_text' => $questionText,
                    'question_type' => 'mcq',
                    'options' => json_encode($formattedOptions, JSON_UNESCAPED_UNICODE),
                    'correct_option' => $correctOption ?: 'A',
                    'explanation' => !empty($q['explanation']) ? trim((string)$q['explanation']) : null,
                    'marks' => 1.00,
                    'negative_marks' => 0.25,
                    'difficulty' => 'medium',
                    'source_id' => $options['source_id'] ?? null,
                    'source_url' => $readUrl,
                    'ingestion_type' => 'json_import',
                    'status' => $options['status'] ?? 'active',
                    'verification_status' => 'verified',
                    'verified_by_admin_id' => $options['admin_id'] ?? null,
                    'verified_at' => now(),
                    'is_public_to_all' => true,
                    'allowed_student_types' => json_encode(['all']),
                    'allowed_plan_ids' => json_encode(['all']),
                    'meta' => json_encode([
                        'chorcha_id' => $exam['id'] ?? null,
                        'live_exam_url' => $exam['liveExamUrl'] ?? null,
                        'original_subject' => $q['subject'] ?? null,
                        'original_index' => $q['index'] ?? ($qIndex + 1),
                        'imported_at' => now()->toIso8601String(),
                    ], JSON_UNESCAPED_UNICODE),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($pendingRows) >= $batchSize) {
                    DB::table('question_banks')->insert($pendingRows);
                    $totalInserted += count($pendingRows);
                    $pendingRows = [];
                }
            }

            if ($progressCallback) {
                $progressCallback($examIndex + 1, $totalExams, $examName, $totalInserted);
            }
        }

        // Insert remaining rows
        if (!empty($pendingRows)) {
            DB::table('question_banks')->insert($pendingRows);
            $totalInserted += count($pendingRows);
            $pendingRows = [];
        }

        return [
            'total_exams' => $totalExams,
            'total_questions_in_file' => $totalQuestionsInFile,
            'total_inserted' => $totalInserted,
            'total_skipped' => $totalSkipped,
            'subjects_count' => count($this->subjectCache),
        ];
    }

    /**
     * Clean up raw exam title (e.g. remove trailing 'Question Paper').
     */
    public function cleanExamName(string $title): string
    {
        $clean = trim($title);
        $clean = preg_replace('/\s*Question Paper\s*$/i', '', $clean);
        $clean = preg_replace('/\s*Question\s*$/i', '', $clean);
        return trim($clean);
    }

    /**
     * Extract 4-digit Gregorian year from title (handles Bengali digits 0-9).
     */
    public function extractYear(?string $text): ?int
    {
        if (empty($text)) {
            return null;
        }

        $bnToEn = ['০'=>'0','১'=>'1','২'=>'2','৩'=>'3','৪'=>'4','৫'=>'5','৬'=>'6','৭'=>'7','৮'=>'8','৯'=>'9'];
        $converted = strtr($text, $bnToEn);

        if (preg_match('/(?:19|20)\d{2}/', $converted, $matches)) {
            $year = (int)$matches[0];
            if ($year >= 1971 && $year <= 2035) {
                return $year;
            }
        }

        // BCS specific year deductions if title has "XXth BCS"
        if (preg_match('/(\d+)(?:th|st|nd|rd)?\s*(?:বিসিএস|BCS)/iu', $converted, $matches)) {
            $bcsNum = (int)$matches[1];
            $bcsYearMap = [
                50 => 2026, 49 => 2025, 48 => 2025, 47 => 2025,
                46 => 2024, 45 => 2023, 44 => 2022, 43 => 2021,
                42 => 2021, 41 => 2021, 40 => 2019, 39 => 2018,
                38 => 2017, 37 => 2016, 36 => 2016, 35 => 2015,
                34 => 2013, 33 => 2012, 32 => 2011, 31 => 2011,
                30 => 2010, 29 => 2009, 28 => 2008, 27 => 2007,
            ];
            if (isset($bcsYearMap[$bcsNum])) {
                return $bcsYearMap[$bcsNum];
            }
        }

        return null;
    }

    /**
     * Extract set / shift from title (e.g. সেট নম্বরঃ 1054).
     */
    public function extractSet(?string $title): ?string
    {
        if (empty($title)) {
            return null;
        }

        if (preg_match('/(সেট(?:\s*নম্বর)?[:ঃ]?\s*[\d\w\-]+)/iu', $title, $m)) {
            return trim($m[1]);
        }
        if (preg_match('/(গ্রুপ-?\d+)/iu', $title, $m)) {
            return trim($m[1]);
        }
        if (preg_match('/(Set\s*[-:]?\s*[A-Z\d]+)/i', $title, $m)) {
            return trim($m[1]);
        }

        return null;
    }

    /**
     * Detect segment (job, admission, hsc, ssc).
     */
    public function detectSegment(?string $examName, ?string $series): string
    {
        $haystack = mb_strtolower(($examName ?? '') . ' ' . ($series ?? ''));

        if (str_contains($haystack, 'বিশ্ববিদ্যালয়') ||
            str_contains($haystack, 'ভর্তি') ||
            str_contains($haystack, 'admission') ||
            str_contains($haystack, 'university') ||
            str_contains($haystack, 'মেডিকেল') ||
            str_contains($haystack, 'buet') ||
            str_contains($haystack, 'বুয়েট') ||
            str_contains($haystack, 'du-') ||
            str_contains($haystack, 'guccho')) {
            return 'admission';
        }

        if (str_contains($haystack, 'hsc') || str_contains($haystack, 'এইচএসসি')) {
            return 'hsc';
        }

        if (str_contains($haystack, 'ssc') || str_contains($haystack, 'এসএসসি')) {
            return 'ssc';
        }

        return 'job'; // Default for BCS, Primary Teacher, Bank, PSC, Ministry
    }

    /**
     * Generate origin tag for fast querying (e.g. BCS-45, PRI-TEACHER-24, PSC-NONCADRE).
     */
    public function generateOriginTag(string $examName, ?string $series, ?int $year): string
    {
        if (!empty($series)) {
            $base = Str::slug($series);
            return strtoupper(substr($base, 0, 30));
        }

        if (preg_match('/(\d+)(?:th|st|nd|rd)?\s*BCS/i', $examName, $m)) {
            return 'BCS-' . $m[1];
        }

        if (str_contains($examName, 'প্রাথমিক সহকারী শিক্ষক')) {
            return 'PRIMARY-TEACHER' . ($year ? '-' . $year : '');
        }

        $slug = Str::slug($examName);
        return strtoupper(substr($slug, 0, 30));
    }

    /**
     * Extract organization or institution from exam name.
     */
    public function extractInstitution(string $examName): ?string
    {
        if (str_contains($examName, 'BCS') || str_contains($examName, 'বিসিএস') || str_contains($examName, 'পিএসসি')) {
            return 'বাংলাদেশ সরকারি কর্ম কমিশন (BPSC)';
        }
        if (str_contains($examName, 'প্রাথমিক সহকারী শিক্ষক') || str_contains($examName, 'ডিপিই')) {
            return 'প্রাথমিক শিক্ষা অধিদপ্তর (DPE)';
        }
        if (str_contains($examName, 'বাংলাদেশ ব্যাংক')) {
            return 'বাংলাদেশ ব্যাংক (Bangladesh Bank)';
        }
        if (str_contains($examName, 'মন্ত্রণালয়') || str_contains($examName, 'মন্ত্রণালয়')) {
            if (preg_match('/([^,(\-]+মন্ত্রণাল[য়য])/u', $examName, $m)) {
                return trim($m[1]);
            }
        }
        return null;
    }

    /**
     * Standardize options format into:
     * [{"key": "A", "text": "কোপেনহেগেন"}, {"key": "B", "text": "প্যারিস"}, ...]
     */
    public function formatOptions(array $rawOptions): array
    {
        $keys = ['A', 'B', 'C', 'D', 'E', 'F'];
        $bnKeys = ['ক' => 'A', 'খ' => 'B', 'গ' => 'C', 'ঘ' => 'D', 'ঙ' => 'E'];

        $formatted = [];
        foreach ($rawOptions as $index => $opt) {
            if (is_array($opt) && isset($opt['key'], $opt['text'])) {
                $formatted[] = [
                    'key' => strtoupper(trim((string)$opt['key'])),
                    'text' => trim((string)$opt['text']),
                ];
                continue;
            }

            $optStr = trim((string)$opt);
            // Match pattern "A) text" or "A. text" or "ক) text"
            if (preg_match('/^([A-Fa-fক-ঙ])[\)\.\:\-]\s*(.*)$/u', $optStr, $m)) {
                $rawKey = $m[1];
                $key = isset($bnKeys[$rawKey]) ? $bnKeys[$rawKey] : strtoupper($rawKey);
                $formatted[] = [
                    'key' => $key,
                    'text' => trim($m[2]),
                ];
            } else {
                $formatted[] = [
                    'key' => $keys[$index] ?? (string)($index + 1),
                    'text' => $optStr,
                ];
            }
        }

        return $formatted;
    }

    /**
     * Determine the correct option key (e.g. 'A', 'B', 'C', 'D').
     */
    public function determineCorrectOption(?string $rawAnswer, array $formattedOptions): ?string
    {
        if (empty($rawAnswer)) {
            return null;
        }

        $rawAnswer = trim($rawAnswer);
        $bnKeys = ['ক' => 'A', 'খ' => 'B', 'গ' => 'C', 'ঘ' => 'D', 'ঙ' => 'E'];

        // Pattern: "C) বার্লিন" or "C. বার্লিন" or "গ) বার্লিন"
        if (preg_match('/^([A-Fa-fক-ঙ])[\)\.\:\-]/u', $rawAnswer, $m)) {
            $rawKey = $m[1];
            return isset($bnKeys[$rawKey]) ? $bnKeys[$rawKey] : strtoupper($rawKey);
        }

        // Just "C" or "গ"
        if (preg_match('/^([A-Fa-fক-ঙ])$/u', $rawAnswer, $m)) {
            $rawKey = $m[1];
            return isset($bnKeys[$rawKey]) ? $bnKeys[$rawKey] : strtoupper($rawKey);
        }

        // Match answer text with options text
        $cleanAns = preg_replace('/^[A-Fa-fক-ঙ][\)\.\:\-]\s*/u', '', $rawAnswer);
        $cleanAns = trim($cleanAns);

        foreach ($formattedOptions as $opt) {
            if (mb_strtolower(trim($opt['text'])) === mb_strtolower($cleanAns)) {
                return $opt['key'];
            }
        }

        return $formattedOptions[0]['key'] ?? 'A';
    }

    /**
     * Resolve or auto-create QuestionSubject.
     */
    public function resolveSubject(?string $rawSubject, string $segment = 'job'): int
    {
        $raw = trim((string)($rawSubject ?: 'সাধারণ জ্ঞান'));
        $lower = mb_strtolower($raw);

        // Check in-memory cache
        if (isset($this->subjectCache[$lower])) {
            return $this->subjectCache[$lower];
        }

        // Check alias mapping
        $info = $this->subjectMap[$lower] ?? null;
        if ($info) {
            $slug = $info['slug'];
            if (isset($this->subjectCache[$slug])) {
                return $this->subjectCache[$slug];
            }
            $existing = QuestionSubject::where('slug', $slug)->first();
            if ($existing) {
                $this->subjectCache[$lower] = $existing->id;
                $this->subjectCache[$slug] = $existing->id;
                return $existing->id;
            }

            $newSubject = QuestionSubject::create([
                'name_bn' => $info['bn'],
                'name_en' => $info['en'],
                'slug' => $slug,
                'segment' => 'both',
                'is_active' => true,
            ]);

            $this->subjectCache[$lower] = $newSubject->id;
            $this->subjectCache[$slug] = $newSubject->id;
            return $newSubject->id;
        }

        // Create new dynamic subject
        $baseSlug = Str::slug($raw) ?: 'subject';
        
        // Check if a subject already exists with this slug or name
        $existing = QuestionSubject::where('name_bn', $raw)
            ->orWhere('slug', $baseSlug)
            ->first();

        if ($existing) {
            $this->subjectCache[$lower] = $existing->id;
            $this->subjectCache[$existing->slug] = $existing->id;
            return $existing->id;
        }

        $slug = $baseSlug;
        $counter = 1;
        while (QuestionSubject::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $newSubject = QuestionSubject::create([
            'name_bn' => $raw,
            'name_en' => $raw,
            'slug' => $slug,
            'segment' => $segment === 'admission' ? 'admission' : 'job',
            'is_active' => true,
        ]);

        $this->subjectCache[$lower] = $newSubject->id;
        $this->subjectCache[$newSubject->slug] = $newSubject->id;
        return $newSubject->id;
    }
}
