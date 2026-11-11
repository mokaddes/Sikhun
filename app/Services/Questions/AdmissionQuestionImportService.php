<?php

namespace App\Services\Questions;

use App\Models\QuestionBank;
use App\Models\QuestionSource;
use App\Models\QuestionSubject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdmissionQuestionImportService
{
    /**
     * In-memory cache for resolved subject IDs.
     * key: slug -> id
     */
    protected array $subjectCache = [];

    /**
     * Canonical subject definitions for admission.
     */
    protected array $canonicalSubjects = [
        'bangla' => ['bn' => 'বাংলা', 'en' => 'Bangla', 'segment' => 'both', 'icon' => 'BookOpenIcon'],
        'english' => ['bn' => 'ইংরেজি', 'en' => 'English', 'segment' => 'both', 'icon' => 'LanguageIcon'],
        'physics' => ['bn' => 'পদার্থবিজ্ঞান', 'en' => 'Physics', 'segment' => 'both', 'icon' => 'BoltIcon'],
        'chemistry' => ['bn' => 'রসায়ন', 'en' => 'Chemistry', 'segment' => 'both', 'icon' => 'BeakerIcon'],
        'higher-mathematics' => ['bn' => 'উচ্চতর গণিত', 'en' => 'Higher Mathematics', 'segment' => 'both', 'icon' => 'CalculatorIcon'],
        'mathematics' => ['bn' => 'গণিত', 'en' => 'Mathematics', 'segment' => 'both', 'icon' => 'CalculatorIcon'],
        'biology' => ['bn' => 'জীববিজ্ঞান', 'en' => 'Biology', 'segment' => 'both', 'icon' => 'HeartIcon'],
        'general-knowledge' => ['bn' => 'সাধারণ জ্ঞান', 'en' => 'General Knowledge', 'segment' => 'both', 'icon' => 'GlobeAltIcon'],
        'ict-computer' => ['bn' => 'তথ্য ও যোগাযোগ প্রযুক্তি', 'en' => 'ICT & Computer', 'segment' => 'both', 'icon' => 'CpuChipIcon'],
        'accounting' => ['bn' => 'হিসাববিজ্ঞান', 'en' => 'Accounting', 'segment' => 'both', 'icon' => 'DocumentChartBarIcon'],
        'business-organization' => ['bn' => 'ব্যবসায় সংগঠন ও ব্যবস্থাপনা', 'en' => 'Business Organization & Management', 'segment' => 'both', 'icon' => 'BriefcaseIcon'],
        'finance-banking' => ['bn' => 'ফিন্যান্স, ব্যাংকিং ও বীমা', 'en' => 'Finance, Banking & Insurance', 'segment' => 'both', 'icon' => 'BanknotesIcon'],
        'marketing' => ['bn' => 'উৎপাদন ব্যবস্থাপনা ও বিপণন', 'en' => 'Production Management & Marketing', 'segment' => 'both', 'icon' => 'PresentationChartLineIcon'],
        'economics' => ['bn' => 'অর্থনীতি', 'en' => 'Economics', 'segment' => 'both', 'icon' => 'ChartBarIcon'],
        'geography-disaster' => ['bn' => 'ভূগোল ও দুর্যোগ ব্যবস্থাপনা', 'en' => 'Geography & Disaster Management', 'segment' => 'both', 'icon' => 'MapIcon'],
        'psychology' => ['bn' => 'মনোবিজ্ঞান', 'en' => 'Psychology', 'segment' => 'both', 'icon' => 'SparklesIcon'],
        'statistics' => ['bn' => 'পরিসংখ্যান', 'en' => 'Statistics', 'segment' => 'both', 'icon' => 'TableCellsIcon'],
        'integrated-science' => ['bn' => 'সমন্বিত বিজ্ঞান ও আইসিটি', 'en' => 'Integrated Science & ICT', 'segment' => 'admission', 'icon' => 'AcademicCapIcon'],
    ];

    public function __construct()
    {
        $this->warmSubjectCache();
    }

    /**
     * Preload and initialize canonical subjects in DB.
     */
    public function warmSubjectCache(): void
    {
        foreach ($this->canonicalSubjects as $slug => $def) {
            $existing = QuestionSubject::where('slug', $slug)->first();
            if (!$existing) {
                // Also check if name_bn matches
                $existing = QuestionSubject::where('name_bn', $def['bn'])->first();
            }

            if ($existing) {
                // Ensure segment allows admission
                if ($existing->segment === 'job') {
                    $existing->update(['segment' => 'both']);
                }
                $this->subjectCache[$slug] = $existing->id;
            } else {
                $newSubject = QuestionSubject::create([
                    'name_bn' => $def['bn'],
                    'name_en' => $def['en'],
                    'slug' => $slug,
                    'segment' => $def['segment'],
                    'icon' => $def['icon'] ?? null,
                    'is_active' => true,
                ]);
                $this->subjectCache[$slug] = $newSubject->id;
            }
        }
    }

    /**
     * Get or create the Chorcha Admission Question Source.
     */
    public function getOrCreateSource(): QuestionSource
    {
        return QuestionSource::firstOrCreate(
            ['slug' => 'chorcha-admission-archive'],
            [
                'name' => 'Chorcha Admission Archive',
                'base_url' => 'https://chorcha.net',
                'source_type' => 'educational_archive',
                'institution' => 'Bangladeshi Public & Engineering Universities',
                'content_category' => 'admission',
                'crawl_frequency' => 'manual',
                'permission_status' => 'fair_use_educational',
                'license_info' => 'Educational Fair Use / Admission Archive',
                'is_active' => true,
            ]
        );
    }

    /**
     * Import admission questions from a JSON file.
     *
     * @param string $filePath
     * @param array $options ['dry_run' => bool, 'limit' => ?int, 'status' => string]
     * @param callable|null $progressCallback
     * @return array
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

        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException("Invalid JSON: " . json_last_error_msg());
        }

        $exams = $data['exams'] ?? (isset($data[0]['questions']) ? $data : []);
        if (empty($exams)) {
            throw new \InvalidArgumentException("No exams found in JSON file.");
        }

        $dryRun = (bool)($options['dry_run'] ?? false);
        $limit = isset($options['limit']) ? (int)$options['limit'] : null;
        $status = $options['status'] ?? 'published';

        if ($limit && $limit > 0) {
            $exams = array_slice($exams, 0, $limit);
        }

        $source = $dryRun ? null : $this->getOrCreateSource();
        $sourceId = $source?->id;

        // Build existing question hashes to prevent duplicates: md5(exam_name . '||' . question_text)
        $existingHashes = [];
        QuestionBank::where('segment', 'admission')
            ->select(['exam_name', 'question_text'])
            ->chunk(5000, function ($chunk) use (&$existingHashes) {
                foreach ($chunk as $q) {
                    $key = md5(trim((string)$q->exam_name) . '||' . trim((string)$q->question_text));
                    $existingHashes[$key] = true;
                }
            });

        $totalExams = count($exams);
        $totalQuestionsInFile = 0;
        $totalInserted = 0;
        $totalSkipped = 0;

        $universityStats = [];
        $subjectStats = [];
        $yearStats = [];
        $originTagStats = [];

        $batchSize = 250;
        $pendingRows = [];

        foreach ($exams as $examIndex => $exam) {
            $rawTitle = trim((string)($exam['title'] ?? ''));
            $series = (string)($exam['series'] ?? '');
            $readUrl = $exam['readUrl'] ?? null;
            $liveExamUrl = $exam['liveExamUrl'] ?? null;
            $questions = $exam['questions'] ?? [];

            $parsedMeta = $this->parseExamMetadata($rawTitle, $series);
            $examName = $parsedMeta['exam_name'];
            $institution = $parsedMeta['institution'];
            $originTag = $parsedMeta['origin_tag'];
            $year = $parsedMeta['year'];
            $session = $parsedMeta['session'];
            $shiftOrSet = $parsedMeta['shift_or_set'];
            $questionType = $parsedMeta['exam_type'];

            $universityStats[$institution] = ($universityStats[$institution] ?? 0) + count($questions);
            $yearStats[$year ?? 'Unknown'] = ($yearStats[$year ?? 'Unknown'] ?? 0) + count($questions);
            $originTagStats[$originTag] = ($originTagStats[$originTag] ?? 0) + count($questions);

            foreach ($questions as $qIndex => $q) {
                $totalQuestionsInFile++;
                $questionText = trim((string)($q['question'] ?? ''));
                if (empty($questionText)) {
                    $totalSkipped++;
                    continue;
                }

                $hashKey = md5($examName . '||' . $questionText);
                if (isset($existingHashes[$hashKey])) {
                    $totalSkipped++;
                    continue;
                }
                $existingHashes[$hashKey] = true;

                $rawSubject = $q['subject'] ?? null;
                $subjectSlug = $this->resolveCanonicalSubjectSlug($rawSubject);
                $subjectId = $this->subjectCache[$subjectSlug] ?? $this->subjectCache['general-knowledge'];

                $subjectStats[$subjectSlug] = ($subjectStats[$subjectSlug] ?? 0) + 1;

                $rawOptions = $q['options'] ?? [];
                $formattedOptions = $this->formatOptions($rawOptions);
                $correctOption = $this->determineCorrectOption($q['answer'] ?? null, $formattedOptions);

                $explanation = !empty($q['explanation']) ? trim((string)$q['explanation']) : null;

                if (!$dryRun) {
                    $pendingRows[] = [
                        'segment' => 'admission',
                        'subject_id' => $subjectId,
                        'topic_id' => null,
                        'topic_name' => $q['topic'] ?? null,
                        'institution' => $institution,
                        'exam_name' => $examName,
                        'origin_tag' => $originTag,
                        'year' => $year,
                        'session' => $session,
                        'shift_or_set' => $shiftOrSet,
                        'question_text' => $questionText,
                        'question_type' => $questionType,
                        'options' => json_encode($formattedOptions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'correct_option' => $correctOption,
                        'explanation' => $explanation,
                        'marks' => 1.00,
                        'negative_marks' => 0.25,
                        'difficulty' => 'medium',
                        'source_id' => $sourceId,
                        'source_url' => $readUrl,
                        'ingestion_type' => 'json_import',
                        'status' => $status,
                        'verification_status' => 'verified',
                        'verified_by_admin_id' => null,
                        'verified_at' => now(),
                        'is_public_to_all' => true,
                        'allowed_student_types' => json_encode(['all', 'admission']),
                        'allowed_plan_ids' => json_encode(['all']),
                        'meta' => json_encode([
                            'chorcha_id' => $exam['id'] ?? null,
                            'live_exam_url' => $liveExamUrl,
                            'series' => $series,
                            'unit' => $parsedMeta['unit'],
                            'original_subject' => $rawSubject,
                            'original_index' => $q['index'] ?? ($qIndex + 1),
                            'imported_at' => now()->toIso8601String(),
                        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (count($pendingRows) >= $batchSize) {
                        DB::table('question_banks')->insert($pendingRows);
                        $totalInserted += count($pendingRows);
                        $pendingRows = [];
                    }
                } else {
                    $totalInserted++;
                }
            }

            if ($progressCallback) {
                $progressCallback($examIndex + 1, $totalExams, $examName, $totalInserted);
            }
        }

        if (!$dryRun && !empty($pendingRows)) {
            DB::table('question_banks')->insert($pendingRows);
            $totalInserted += count($pendingRows);
            $pendingRows = [];
        }

        // Update QuestionSource count if not dry run
        if (!$dryRun && $source) {
            $source->update([
                'questions_imported_count' => QuestionBank::where('source_id', $sourceId)->count(),
                'last_crawl_at' => now(),
                'last_crawl_status' => 'success',
            ]);
        }

        arsort($universityStats);
        arsort($subjectStats);
        krsort($yearStats);
        arsort($originTagStats);

        return [
            'dry_run' => $dryRun,
            'total_exams' => $totalExams,
            'total_questions_in_file' => $totalQuestionsInFile,
            'total_inserted' => $totalInserted,
            'total_skipped' => $totalSkipped,
            'university_stats' => $universityStats,
            'subject_stats' => $subjectStats,
            'year_stats' => $yearStats,
            'origin_tag_stats' => $originTagStats,
        ];
    }

    /**
     * Parse exam metadata from title and series.
     */
    public function parseExamMetadata(string $title, string $series): array
    {
        $rawTitle = trim($title);
        $bnToEnDigits = ['০'=>'0','১'=>'1','২'=>'2','৩'=>'3','৪'=>'4','৫'=>'5','৬'=>'6','৭'=>'7','৮'=>'8','৯'=>'9'];
        $normalizedTitle = strtr($rawTitle, $bnToEnDigits);

        // Clean trailing "Question Paper", "Question", etc.
        $cleanTitle = preg_replace('/\s*Question\s*Paper\s*$/iu', '', $rawTitle);
        $cleanTitle = preg_replace('/\s*Question\s*$/iu', '', $cleanTitle);
        $cleanTitle = trim($cleanTitle);

        $seriesLower = strtolower($series);
        $titleLower = strtolower($normalizedTitle);

        // 1. Institution
        $institution = null;
        $institutionCode = null;

        if (str_contains($titleLower, 'buet') || str_contains($seriesLower, 'buet')) {
            $institution = 'বাংলাদেশ প্রকৌশল বিশ্ববিদ্যালয় (BUET)';
            $institutionCode = 'BUET';
        } elseif (str_contains($titleLower, 'cuet') || str_contains($seriesLower, 'cuet')) {
            $institution = 'চট্টগ্রাম প্রকৌশল ও প্রযুক্তি বিশ্ববিদ্যালয় (CUET)';
            $institutionCode = 'CUET';
        } elseif (str_contains($titleLower, 'ruet') || str_contains($seriesLower, 'ruet')) {
            $institution = 'রাজশাহী প্রকৌশল ও প্রযুক্তি বিশ্ববিদ্যালয় (RUET)';
            $institutionCode = 'RUET';
        } elseif (str_contains($titleLower, 'gst') || str_contains($seriesLower, 'gst') || str_contains($titleLower, 'guccho') || str_contains($titleLower, 'গুচ্ছ')) {
            $institution = 'জিএসটি গুচ্ছ বিশ্ববিদ্যালয় (GST Cluster)';
            $institutionCode = 'GST';
        } elseif (str_contains($titleLower, 'cou') || str_contains($seriesLower, 'comilla') || str_contains($titleLower, 'কুমিল্লা')) {
            $institution = 'কুমিল্লা বিশ্ববিদ্যালয় (CoU)';
            $institutionCode = 'COU';
        } elseif (str_contains($titleLower, 'just') || str_contains($seriesLower, 'just') || str_contains($titleLower, 'যশোর')) {
            $institution = 'যশোর বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (JUST)';
            $institutionCode = 'JUST';
        } elseif (str_contains($titleLower, 'hstu') || str_contains($seriesLower, 'hstu') || str_contains($titleLower, 'হাজী মোহাম্মদ দানেশ')) {
            $institution = 'হাজী মোহাম্মদ দানেশ বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (HSTU)';
            $institutionCode = 'HSTU';
        } elseif (str_contains($titleLower, 'mbstu') || str_contains($seriesLower, 'mbstu') || str_contains($titleLower, 'মাওলানা ভাসানী')) {
            $institution = 'মাওলানা ভাসানী বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (MBSTU)';
            $institutionCode = 'MBSTU';
        } elseif (str_contains($titleLower, 'bsmrstu') || str_contains($seriesLower, 'bsmrstu')) {
            $institution = 'বঙ্গবন্ধু শেখ মুজিবুর রহমান বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (BSMRSTU)';
            $institutionCode = 'BSMRSTU';
        } elseif (str_contains($titleLower, 'pust') || str_contains($seriesLower, 'pust')) {
            $institution = 'পাবনা বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (PUST)';
            $institutionCode = 'PUST';
        } elseif (str_contains($titleLower, 'rmstu') || str_contains($seriesLower, 'rmstu')) {
            $institution = 'রাঙ্গামাটি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (RMSTU)';
            $institutionCode = 'RMSTU';
        } elseif (str_contains($titleLower, 'pstu') || str_contains($seriesLower, 'pstu')) {
            $institution = 'পটুয়াখালী বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (PSTU)';
            $institutionCode = 'PSTU';
        } elseif (str_contains($titleLower, 'nstu') || str_contains($seriesLower, 'nstu')) {
            $institution = 'নোয়াখালী বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (NSTU)';
            $institutionCode = 'NSTU';
        } elseif (preg_match('/\bIU\b/i', $normalizedTitle) || str_contains($seriesLower, 'islamic-university')) {
            $institution = 'ইসলামী বিশ্ববিদ্যালয়, বাংলাদেশ (IU)';
            $institutionCode = 'IU';
        } elseif (str_contains($titleLower, 'ঢাকা বিশ্ববিদ্যালয়') || str_contains($titleLower, 'ঢাবি') || str_contains($seriesLower, 'du-') || preg_match('/\bDU\b/i', $normalizedTitle)) {
            $institution = 'ঢাকা বিশ্ববিদ্যালয় (DU)';
            $institutionCode = 'DU';
        } elseif (str_contains($titleLower, 'রাজশাহী বিশ্ববিদ্যালয়') || str_contains($titleLower, 'রাবি') || str_contains($seriesLower, 'ru-') || preg_match('/\bRU\b/i', $normalizedTitle)) {
            $institution = 'রাজশাহী বিশ্ববিদ্যালয় (RU)';
            $institutionCode = 'RU';
        } else {
            $institution = 'বিশ্ববিদ্যালয় ভর্তি পরীক্ষা';
            $institutionCode = 'UNIV';
        }

        // 2. Unit
        $unit = null;
        $unitCode = null;
        if (preg_match('/(?:Unit|ইউনিট)\s*[:\-]?\s*([A-Za-z])/iu', $normalizedTitle, $m)) {
            $unitCode = strtoupper($m[1]);
            $unit = $unitCode . ' Unit';
        } elseif (preg_match('/\b([A-Da-d])\s*(?:Unit|ইউনিট)/iu', $normalizedTitle, $m)) {
            $unitCode = strtoupper($m[1]);
            $unit = $unitCode . ' Unit';
        } elseif (preg_match('/(?:DU|RU|GST|COU|JUST|HSTU|MBSTU|IU|BSMRSTU|PUST|RMSTU|PSTU|NSTU)\s*[\-]?\s*([A-Da-d])\b/iu', $normalizedTitle, $m)) {
            $unitCode = strtoupper($m[1]);
            $unit = $unitCode . ' Unit';
        } elseif (str_contains($normalizedTitle, 'প্রযুক্তি ইউনিট') || str_contains($seriesLower, 'technology')) {
            $unit = 'Technology Unit';
            $unitCode = 'TECH';
        } elseif (str_contains($normalizedTitle, 'বিজ্ঞান ইউনিট') || str_contains($seriesLower, 'science')) {
            $unit = 'A Unit (Science)';
            $unitCode = 'A';
        } elseif (str_contains($normalizedTitle, 'ব্যবসায় শিক্ষা') || str_contains($normalizedTitle, 'বাণিজ্য')) {
            $unit = 'C Unit (Business)';
            $unitCode = 'C';
        } elseif (str_contains($seriesLower, '-a-')) {
            $unit = 'A Unit';
            $unitCode = 'A';
        } elseif (str_contains($seriesLower, '-b-')) {
            $unit = 'B Unit';
            $unitCode = 'B';
        } elseif (str_contains($seriesLower, '-c-')) {
            $unit = 'C Unit';
            $unitCode = 'C';
        } elseif (str_contains($seriesLower, '-d-')) {
            $unit = 'D Unit';
            $unitCode = 'D';
        }

        // 3. Year & Session
        $year = null;
        $session = null;

        if (preg_match('/(20\d{2})[\s\-–\/]+(\d{2,4})/', $normalizedTitle, $m)) {
            $y1 = (int)$m[1];
            $y2 = (int)$m[2];
            if ($y2 < 100) {
                $y2 = (int)(substr($m[1], 0, 2) . str_pad($m[2], 2, '0', STR_PAD_LEFT));
            }
            $session = $y1 . '-' . substr((string)$y2, -2);
            $year = $y1;
        } elseif (preg_match('/\b(\d{2})[\s\-–\/]+(\d{2})\b/', $normalizedTitle, $m)) {
            $y1 = (int)$m[1];
            $y2 = (int)$m[2];
            $fullY1 = ($y1 >= 50) ? (1900 + $y1) : (2000 + $y1);
            $session = $fullY1 . '-' . str_pad($y2, 2, '0', STR_PAD_LEFT);
            $year = $fullY1;
        } elseif (preg_match('/\b(19\d{2}|20\d{2})\b/', $normalizedTitle, $m)) {
            $year = (int)$m[1];
            $session = (string)$year;
        }

        if (!$year && preg_match('/(19\d{2}|20\d{2})/', $series, $m)) {
            $year = (int)$m[1];
            $session = (string)$year;
        }

        // 4. Shift or Set or Group
        $shiftOrSet = null;
        if (preg_match('/(Shift\s*[-:]?\s*\d+)/i', $normalizedTitle, $m)) {
            $shiftOrSet = trim($m[1]);
        } elseif (preg_match('/(Group\s*[-:]?\s*\d+)/i', $normalizedTitle, $m)) {
            $shiftOrSet = trim($m[1]);
        } elseif (preg_match('/(Set\s*[-:]?\s*[A-Z\d]+)/i', $normalizedTitle, $m)) {
            $shiftOrSet = trim($m[1]);
        } elseif (preg_match('/(সেট\s*[-:]?\s*[\d\w]+)/iu', $rawTitle, $m)) {
            $shiftOrSet = trim($m[1]);
        }

        // 5. Written vs MCQ
        $isWritten = str_contains(strtolower($normalizedTitle), 'written') || str_contains($rawTitle, 'লিখিত');
        $examType = $isWritten ? 'written' : 'mcq';

        // 6. Origin Tag
        $tagParts = [];
        $tagParts[] = $institutionCode;
        if ($unitCode) {
            $tagParts[] = $unitCode;
        }
        $originTag = implode('-', $tagParts);

        // Standardized clean exam name
        $nameParts = [];
        $nameParts[] = $institutionCode;
        if ($unit) {
            $nameParts[] = $unit;
        }
        if ($session) {
            $nameParts[] = $session;
        } elseif ($year) {
            $nameParts[] = (string)$year;
        }
        if ($shiftOrSet) {
            $nameParts[] = '(' . $shiftOrSet . ')';
        }
        if ($isWritten) {
            $nameParts[] = 'Written';
        } else {
            $nameParts[] = 'MCQ';
        }

        $standardExamName = implode(' ', $nameParts);

        return [
            'exam_name' => $cleanTitle ?: $standardExamName,
            'clean_title' => $cleanTitle,
            'standard_exam_name' => $standardExamName,
            'institution' => $institution,
            'institution_code' => $institutionCode,
            'unit' => $unit,
            'unit_code' => $unitCode,
            'year' => $year,
            'session' => $session,
            'shift_or_set' => $shiftOrSet,
            'exam_type' => $examType,
            'origin_tag' => $originTag,
        ];
    }

    /**
     * Map raw subject string to canonical subject slug.
     */
    public function resolveCanonicalSubjectSlug(?string $raw): string
    {
        $raw = trim((string)$raw);
        if (empty($raw)) {
            return 'general-knowledge';
        }
        $lower = mb_strtolower($raw);

        // 1. Bangla
        if (str_contains($lower, 'বাংলা') || str_contains($lower, 'bangla')) {
            return 'bangla';
        }

        // 2. English
        if (str_contains($lower, 'ইংরেজি') || str_contains($lower, 'ইংরেজী') || str_contains($lower, 'english') || str_contains($lower, 'passage')) {
            return 'english';
        }

        // 3. Physics
        if (str_contains($lower, 'পদার্থবিজ্ঞান') || str_contains($lower, 'পদার্থ বিজ্ঞান') || str_contains($lower, 'পদার্থ') || str_contains($lower, 'physics') || str_contains($lower, 'physic')) {
            if (!str_contains($lower, 'রসায়ন') && !str_contains($lower, 'রসায়ন') && !str_contains($lower, 'ict') && !str_contains($lower, 'গণিত')) {
                return 'physics';
            }
        }

        // 4. Chemistry
        if (str_contains($lower, 'রসায়ন') || str_contains($lower, 'রসায়ন') || str_contains($lower, 'chemistry')) {
            if (!str_contains($lower, 'পদার্থ') && !str_contains($lower, 'ict') && !str_contains($lower, 'গণিত')) {
                return 'chemistry';
            }
        }

        // 5. Higher Math / Math
        if (str_contains($lower, 'উচ্চতর গণিত') || str_contains($lower, 'উচ্চতর গনিত') || str_contains($lower, 'higher math')) {
            return 'higher-mathematics';
        }
        if (str_contains($lower, 'গণিত') || str_contains($lower, 'গনিত') || str_contains($lower, 'mathematics') || str_contains($lower, 'math') || str_contains($lower, 'বীজগণিত') || str_contains($lower, 'পাটীগণিত') || str_contains($lower, 'জ্যামিতি')) {
            if (!str_contains($lower, 'পদার্থ') && !str_contains($lower, 'রসায়ন') && !str_contains($lower, 'বিজ্ঞান') && !str_contains($lower, 'জীব')) {
                return 'higher-mathematics';
            }
        }

        // 6. Biology
        if (str_contains($lower, 'জীববিজ্ঞান') || str_contains($lower, 'জীববিদ্যা') || str_contains($lower, 'biology')) {
            if (!str_contains($lower, 'গণিত')) {
                return 'biology';
            }
        }

        // 7. Accounting
        if (str_contains($lower, 'হিসাববিজ্ঞান') || str_contains($lower, 'হিসাব বিজ্ঞান') || str_contains($lower, 'accounting') || str_contains($lower, 'একাউন্টিং')) {
            if (!str_contains($lower, 'ব্যবসায়') && !str_contains($lower, 'ব্যবসা')) {
                return 'accounting';
            }
        }

        // 8. Finance & Banking
        if (str_contains($lower, 'ফিন্যান্স') || str_contains($lower, 'ফিনান্স') || str_contains($lower, 'ফিন্যন্স') || str_contains($lower, 'finance') || str_contains($lower, 'banking') || str_contains($lower, 'ব্যাংকিং')) {
            return 'finance-banking';
        }

        // 9. Marketing & Management
        if (str_contains($lower, 'মার্কেটিং') || str_contains($lower, 'marketing') || str_contains($lower, 'উৎপাদন ব্যবস্থাপনা') || str_contains($lower, 'বিপণন')) {
            return 'marketing';
        }

        // 10. Business Org & Management
        if (str_contains($lower, 'ব্যবসায়') || str_contains($lower, 'ব্যবসায়') || str_contains($lower, 'ব্যাবসায়') || str_contains($lower, 'management') || str_contains($lower, 'ব্যবস্থাপনা') || str_contains($lower, 'business') || str_contains($lower, 'বিজনেস')) {
            return 'business-organization';
        }

        // 11. ICT
        if (str_contains($lower, 'আইসিটি') || str_contains($lower, 'ict') || str_contains($lower, 'তথ্যপ্রযুক্তি') || str_contains($lower, 'তথ্য ও যোগাযোগ প্রযুক্তি') || str_contains($lower, 'তথ্য প্রযুক্তি') || str_contains($lower, 'কম্পিউটার')) {
            if (!str_contains($lower, 'পদার্থ') && !str_contains($lower, 'রসায়ন') && !str_contains($lower, 'জ্ঞান')) {
                return 'ict-computer';
            }
        }

        // 12. GK / Bangladesh / International
        if (str_contains($lower, 'জ্ঞান') || str_contains($lower, 'gk') || str_contains($lower, 'general knowledge') || str_contains($lower, 'বাংলাদেশ') || str_contains($lower, 'আন্তর্জাতিক') || str_contains($lower, 'বিষয়াবলী') || str_contains($lower, 'বিষয়াবলি')) {
            return 'general-knowledge';
        }

        // 13. Economics
        if (str_contains($lower, 'অর্থনীতি') || str_contains($lower, 'economics')) {
            return 'economics';
        }

        // 14. Geography
        if (str_contains($lower, 'ভূগোল') || str_contains($lower, 'geography')) {
            return 'geography-disaster';
        }

        // 15. Psychology
        if (str_contains($lower, 'মনোবিজ্ঞান') || str_contains($lower, 'psychology')) {
            return 'psychology';
        }

        // 16. Statistics
        if (str_contains($lower, 'পরিসংখ্যান') || str_contains($lower, 'statistics')) {
            return 'statistics';
        }

        // 17. Integrated / Combined Science
        if (str_contains($lower, 'পদার্থ') || str_contains($lower, 'রসায়ন') || str_contains($lower, 'রসায়ন') || str_contains($lower, 'বিজ্ঞান') || str_contains($lower, 'ict') || str_contains($lower, 'গণিত')) {
            return 'integrated-science';
        }

        return 'general-knowledge';
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
    public function determineCorrectOption(?string $rawAnswer, array $formattedOptions): string
    {
        if (empty($formattedOptions)) {
            return 'A';
        }

        $validKeys = array_column($formattedOptions, 'key');
        if (empty($rawAnswer)) {
            return $validKeys[0] ?? 'A';
        }

        $rawAnswer = trim($rawAnswer);
        $bnKeys = ['ক' => 'A', 'খ' => 'B', 'গ' => 'C', 'ঘ' => 'D', 'ঙ' => 'E'];

        $foundKey = null;

        // Pattern: "C) বার্লিন" or "C. বার্লিন" or "গ) বার্লিন"
        if (preg_match('/^([A-Fa-fক-ঙ])[\)\.\:\-]/u', $rawAnswer, $m)) {
            $rawKey = $m[1];
            $foundKey = isset($bnKeys[$rawKey]) ? $bnKeys[$rawKey] : strtoupper($rawKey);
        } elseif (preg_match('/^([A-Fa-fক-ঙ])$/u', $rawAnswer, $m)) {
            // Just "C" or "গ"
            $rawKey = $m[1];
            $foundKey = isset($bnKeys[$rawKey]) ? $bnKeys[$rawKey] : strtoupper($rawKey);
        }

        if ($foundKey && in_array($foundKey, $validKeys, true)) {
            return $foundKey;
        }

        // Try matching answer text with options text
        $cleanAns = preg_replace('/^[A-Fa-fক-ঙ][\)\.\:\-]\s*/u', '', $rawAnswer);
        $cleanAns = trim($cleanAns);

        foreach ($formattedOptions as $opt) {
            if (mb_strtolower(trim($opt['text'])) === mb_strtolower($cleanAns)) {
                return $opt['key'];
            }
        }

        // If foundKey exists, even if non-standard, use it if in validKeys, otherwise first valid option
        return in_array($foundKey, $validKeys, true) ? $foundKey : ($validKeys[0] ?? 'A');
    }
}
