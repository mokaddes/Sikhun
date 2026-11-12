<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdmissionNotice;
use App\Models\QuestionBank;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdmissionController extends Controller
{
    public function index(Request $request): Response
    {
        $query = AdmissionNotice::published();

        if ($type = $request->input('type')) {
            if (in_array($type, ['public', 'guccho', 'private'], true)) {
                $query->where('university_type', $type);
            }
        }

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('institution_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $notices = $query->orderByDesc('is_featured')
            ->orderBy('application_deadline')
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'total' => AdmissionNotice::published()->count(),
            'public' => AdmissionNotice::published()->where('university_type', 'public')->count(),
            'guccho' => AdmissionNotice::published()->where('university_type', 'guccho')->count(),
            'private' => AdmissionNotice::published()->where('university_type', 'private')->count(),
        ];

        return Inertia::render('Public/Admissions/Index', [
            'notices' => $notices,
            'filters' => [
                'type' => $request->input('type', 'all'),
                'search' => $request->input('search', ''),
            ],
            'stats' => $stats,
        ]);
    }

    public function show(string $slug): Response
    {
        $notice = AdmissionNotice::where('slug', $slug)->firstOrFail();
        $notice->increment('views_count');

        $relatedNotices = AdmissionNotice::published()
            ->where('id', '!=', $notice->id)
            ->where(function ($q) use ($notice) {
                $q->where('university_type', $notice->university_type)
                    ->orWhere('institution_name', $notice->institution_name);
            })
            ->latest('published_at')
            ->take(4)
            ->get();

        $practiceBridge = $this->buildPracticeBridge($notice);
        $sampleQuestions = $this->getSampleQuestions($notice, $practiceBridge['origin_tags']);

        return Inertia::render('Public/Admissions/Show', [
            'notice' => $notice,
            'relatedNotices' => $relatedNotices,
            'practiceBridge' => $practiceBridge,
            'sampleQuestions' => $sampleQuestions,
        ]);
    }

    protected function buildPracticeBridge(AdmissionNotice $notice): array
    {
        $name = mb_strtolower($notice->institution_name . ' ' . $notice->title . ' ' . $notice->slug);

        if (str_contains($name, 'dhaka') || str_contains($name, 'ঢাকা') || str_contains($name, 'du')) {
            return [
                'university_key' => 'DU',
                'university_name' => 'ঢাকা বিশ্ববিদ্যালয় (DU)',
                'total_questions_count' => 5094,
                'origin_tags' => ['DU-A', 'DU-B', 'DU-C', 'DU-D'],
                'units' => [
                    [
                        'name' => 'ক ইউনিট (বিজ্ঞান অনুষদ)',
                        'short' => 'A Unit (Science)',
                        'origin' => 'DU-A',
                        'count' => 1693,
                        'subjects' => 'পদার্থ, রসায়ন, গণিত, জীববিজ্ঞান',
                        'link' => '/question-bank?segment=admission&origin=DU-A',
                    ],
                    [
                        'name' => 'খ ইউনিট (কলা, আইন ও সামাজিক বিজ্ঞান)',
                        'short' => 'B Unit (Arts & Humanities)',
                        'origin' => 'DU-B',
                        'count' => 840,
                        'subjects' => 'বাংলা, ইংরেজি, সাধারণ জ্ঞান',
                        'link' => '/question-bank?segment=admission&origin=DU-B',
                    ],
                    [
                        'name' => 'গ ইউনিট (ব্যবসায় শিক্ষা অনুষদ)',
                        'short' => 'C Unit (Business Studies)',
                        'origin' => 'DU-C',
                        'count' => 1395,
                        'subjects' => 'হিসাববিজ্ঞান, ব্যবসায় নীতি, ফিন্যান্স/মার্কেটিং',
                        'link' => '/question-bank?segment=admission&origin=DU-C',
                    ],
                    [
                        'name' => 'ঘ ইউনিট / আইবিএ (বিভাগ পরিবর্তন)',
                        'short' => 'D Unit / IBA',
                        'origin' => 'DU-D',
                        'count' => 837,
                        'subjects' => 'বাংলা, ইংরেজি, সাধারণ জ্ঞান, এনালিটিক্যাল',
                        'link' => '/question-bank?segment=admission&origin=DU-D',
                    ],
                ],
            ];
        }

        if (str_contains($name, 'rajshahi') || str_contains($name, 'রাজশাহী') || str_contains($name, 'ru')) {
            return [
                'university_key' => 'RU',
                'university_name' => 'রাজশাহী বিশ্ববিদ্যালয় (RU)',
                'total_questions_count' => 5156,
                'origin_tags' => ['RU-A', 'RU-B', 'RU-C'],
                'units' => [
                    [
                        'name' => 'A ইউনিট (মানবিক ও আইন অনুষদ)',
                        'short' => 'A Unit (Humanities)',
                        'origin' => 'RU-A',
                        'count' => 2529,
                        'subjects' => 'বাংলা, ইংরেজি, সাধারণ জ্ঞান',
                        'link' => '/question-bank?segment=admission&origin=RU-A',
                    ],
                    [
                        'name' => 'B ইউনিট (ব্যবসায় শিক্ষা অনুষদ ও IBA)',
                        'short' => 'B Unit (Business Studies)',
                        'origin' => 'RU-B',
                        'count' => 696,
                        'subjects' => 'হিসাববিজ্ঞান, ব্যবসায় সংগঠন, ইংরেজি, আইসিটি',
                        'link' => '/question-bank?segment=admission&origin=RU-B',
                    ],
                    [
                        'name' => 'C ইউনিট (বিজ্ঞান ও কৃষি অনুষদ)',
                        'short' => 'C Unit (Science & Agriculture)',
                        'origin' => 'RU-C',
                        'count' => 1931,
                        'subjects' => 'পদার্থ, রসায়ন, গণিত, জীববিজ্ঞান, আইসিটি',
                        'link' => '/question-bank?segment=admission&origin=RU-C',
                    ],
                ],
            ];
        }

        if (str_contains($name, 'buet') || str_contains($name, 'বুয়েট') || str_contains($name, 'প্রকৌশল') || str_contains($name, 'engineering') || str_contains($name, 'kuet') || str_contains($name, 'ruet') || str_contains($name, 'cuet')) {
            return [
                'university_key' => 'BUET',
                'university_name' => 'প্রকৌশল বিশ্ববিদ্যালয় (BUET/RUET/CUET/KUET)',
                'total_questions_count' => 1716,
                'origin_tags' => ['BUET', 'RUET', 'CUET'],
                'units' => [
                    [
                        'name' => 'বুয়েট ইঞ্জিনিয়ারিং ভর্তি প্রশ্নব্যাংক',
                        'short' => 'BUET Engineering',
                        'origin' => 'BUET',
                        'count' => 851,
                        'subjects' => 'উচ্চতর গণিত, পদার্থবিজ্ঞান, রসায়ন',
                        'link' => '/question-bank?segment=admission&origin=BUET',
                    ],
                    [
                        'name' => 'রুয়েট ভর্তি প্রশ্নব্যাংক',
                        'short' => 'RUET Admission',
                        'origin' => 'RUET',
                        'count' => 455,
                        'subjects' => 'গণিত, পদার্থ, রসায়ন, ইংরেজি',
                        'link' => '/question-bank?segment=admission&origin=RUET',
                    ],
                    [
                        'name' => 'চুয়েট ভর্তি প্রশ্নব্যাংক',
                        'short' => 'CUET Admission',
                        'origin' => 'CUET',
                        'count' => 410,
                        'subjects' => 'গণিত, পদার্থ, রসায়ন, ইংরেজি',
                        'link' => '/question-bank?segment=admission&origin=CUET',
                    ],
                ],
            ];
        }

        if (str_contains($name, 'guccho') || str_contains($name, 'গুচ্ছ') || str_contains($name, 'gst') || str_contains($name, 'sust') || str_contains($name, 'শাহজালাল')) {
            return [
                'university_key' => 'GST',
                'university_name' => 'জিএসটি গুচ্ছ ২৪ বিশ্ববিদ্যালয় (GST Cluster)',
                'total_questions_count' => 1649,
                'origin_tags' => ['GST-A', 'GST-B', 'GST-C'],
                'units' => [
                    [
                        'name' => 'A ইউনিট (বিজ্ঞান শাখা)',
                        'short' => 'A Unit (Science)',
                        'origin' => 'GST-A',
                        'count' => 500,
                        'subjects' => 'পদার্থবিজ্ঞান, রসায়ন, গণিত/জীববিজ্ঞান, আইসিটি',
                        'link' => '/question-bank?segment=admission&origin=GST-A',
                    ],
                    [
                        'name' => 'B ইউনিট (মানবিক শাখা)',
                        'short' => 'B Unit (Humanities)',
                        'origin' => 'GST-B',
                        'count' => 450,
                        'subjects' => 'বাংলা, ইংরেজি, মৌলিক সাধারণ জ্ঞান',
                        'link' => '/question-bank?segment=admission&origin=GST-B',
                    ],
                    [
                        'name' => 'C ইউনিট (বাণিজ্য শাখা)',
                        'short' => 'C Unit (Business)',
                        'origin' => 'GST-C',
                        'count' => 937,
                        'subjects' => 'হিসাববিজ্ঞান, ব্যবসায় নীতি, বাংলা, ইংরেজি',
                        'link' => '/question-bank?segment=admission&origin=GST-C',
                    ],
                ],
            ];
        }

        if (str_contains($name, 'medical') || str_contains($name, 'মেডিকেল') || str_contains($name, 'mbbs') || str_contains($name, 'bds') || str_contains($name, 'ptap')) {
            return [
                'university_key' => 'MED',
                'university_name' => 'মেডিকেল ও ডেন্টাল ভর্তি (MBBS / BDS)',
                'total_questions_count' => 1200,
                'origin_tags' => ['MAT', 'DAT', 'DU-A'],
                'units' => [
                    [
                        'name' => 'মেডিকেল ভর্তি প্রশ্নব্যাংক (MBBS)',
                        'short' => 'Medical (MBBS)',
                        'origin' => 'MAT',
                        'count' => 850,
                        'subjects' => 'জীববিজ্ঞান, রসায়ন, পদার্থবিজ্ঞান, ইংরেজি, জিকে',
                        'link' => '/question-bank?segment=admission&search=মেডিকেল',
                    ],
                    [
                        'name' => 'ডেন্টাল ভর্তি প্রশ্নব্যাংক (BDS)',
                        'short' => 'Dental (BDS)',
                        'origin' => 'DAT',
                        'count' => 350,
                        'subjects' => 'জীববিজ্ঞান, রসায়ন, পদার্থ, ইংরেজি, জিকে',
                        'link' => '/question-bank?segment=admission&search=ডেন্টাল',
                    ],
                ],
            ];
        }

        return [
            'university_key' => 'GEN',
            'university_name' => $notice->institution_name,
            'total_questions_count' => 15760,
            'origin_tags' => ['DU-A', 'DU-B', 'DU-C', 'RU-A', 'RU-C', 'BUET'],
            'units' => [
                [
                    'name' => 'বিজ্ঞান ইউনিট প্রশ্নব্যাংক (ক/A ইউনিট)',
                    'short' => 'Science Unit',
                    'origin' => 'DU-A',
                    'count' => 3624,
                    'subjects' => 'পদার্থ, রসায়ন, উচ্চতর গণিত, জীববিজ্ঞান',
                    'link' => '/question-bank?segment=admission&origin=DU-A',
                ],
                [
                    'name' => 'মানবিক ইউনিট প্রশ্নব্যাংক (খ/B ইউনিট)',
                    'short' => 'Humanities Unit',
                    'origin' => 'DU-B',
                    'count' => 3369,
                    'subjects' => 'বাংলা, ইংরেজি, সাধারণ জ্ঞান',
                    'link' => '/question-bank?segment=admission&origin=DU-B',
                ],
                [
                    'name' => 'ব্যবসায় শিক্ষা প্রশ্নব্যাংক (গ/C ইউনিট)',
                    'short' => 'Business Unit',
                    'origin' => 'DU-C',
                    'count' => 2332,
                    'subjects' => 'হিসাববিজ্ঞান, ব্যবসায় নীতি, ফিন্যান্স',
                    'link' => '/question-bank?segment=admission&origin=DU-C',
                ],
            ],
        ];
    }

    protected function getSampleQuestions(AdmissionNotice $notice, array $originTags, int $limit = 10): array
    {
        $query = QuestionBank::query()
            ->where('segment', 'admission')
            ->whereNotNull('options')
            ->whereNotNull('correct_option')
            ->where('question_text', '!=', '');

        $matchedQuestions = collect();

        if (!empty($originTags)) {
            $matchedQuestions = (clone $query)
                ->whereIn('origin_tag', $originTags)
                ->inRandomOrder()
                ->take($limit)
                ->get();
        }

        if ($matchedQuestions->count() < $limit) {
            $needed = $limit - $matchedQuestions->count();
            $excludeIds = $matchedQuestions->pluck('id')->all();

            $fallbackQuestions = (clone $query)
                ->whereNotIn('id', $excludeIds)
                ->whereIn('origin_tag', ['DU-A', 'DU-B', 'DU-C', 'RU-A', 'RU-C', 'BUET', 'GST-C'])
                ->inRandomOrder()
                ->take($needed)
                ->get();

            $matchedQuestions = $matchedQuestions->concat($fallbackQuestions);
        }

        if ($matchedQuestions->count() < $limit) {
            $curated = $this->getCuratedFallbackQuestions();
            $needed = $limit - $matchedQuestions->count();
            foreach (array_slice($curated, 0, $needed) as $cur) {
                $matchedQuestions->push((object) $cur);
            }
        }

        return $matchedQuestions->take($limit)->map(function ($q) {
            $options = is_array($q->options) ? $q->options : json_decode($q->options ?? '[]', true);
            $formattedOptions = [];
            if (is_array($options)) {
                foreach ($options as $opt) {
                    if (is_array($opt) && isset($opt['key'], $opt['text'])) {
                        $formattedOptions[] = [
                            'key' => (string) $opt['key'],
                            'text' => (string) $opt['text'],
                        ];
                    }
                }
            }

            return [
                'id' => $q->id ?? rand(1000, 9999),
                'question_text' => $q->question_text,
                'options' => $formattedOptions,
                'correct_option' => strtoupper(trim((string) ($q->correct_option ?? 'A'))),
                'explanation' => $q->explanation ?? 'সঠিক উত্তর দেওয়া হয়েছে। পূর্ণাঙ্গ সমাধান শিখুন ভর্তি স্পেশাল এআই মেন্টরে অন্তর্ভুক্ত।',
                'origin_tag' => $q->origin_tag ?? 'ADMISSION',
                'exam_name' => $q->exam_name ?? 'বিশ্ববিদ্যালয় ভর্তি পরীক্ষা',
                'year' => $q->year ?? 2024,
            ];
        })->values()->all();
    }

    protected function getCuratedFallbackQuestions(): array
    {
        return [
            [
                'id' => 99901,
                'question_text' => 'কোন বানানটি শুদ্ধ?',
                'options' => [
                    ['key' => 'A', 'text' => 'মুমূর্ষু'],
                    ['key' => 'B', 'text' => 'মুমুর্য্য'],
                    ['key' => 'C', 'text' => 'মুমুর্ষু'],
                    ['key' => 'D', 'text' => 'মুর্মূষু'],
                ],
                'correct_option' => 'A',
                'explanation' => 'মুমূর্ষু বানানে প্রথমটি হ্রস্ব-উ, দ্বিতীয়টি দীর্ঘ-ঊ এবং শেষেরটি হ্রস্ব-উ কার।',
                'origin_tag' => 'DU-A',
                'exam_name' => 'ঢাকা বিশ্ববিদ্যালয় ক ইউনিট',
                'year' => 2024,
            ],
            [
                'id' => 99902,
                'question_text' => 'Which one is the correct antonym of "Prolific"?',
                'options' => [
                    ['key' => 'A', 'text' => 'Fertile'],
                    ['key' => 'B', 'text' => 'Productive'],
                    ['key' => 'C', 'text' => 'Barren'],
                    ['key' => 'D', 'text' => 'Abundant'],
                ],
                'correct_option' => 'C',
                'explanation' => 'Prolific অর্থ ফলপ্রসূ বা উর্বর; এর বিপরীত শব্দ Barren (অনুর্বর বা নিষ্ফলা)।',
                'origin_tag' => 'DU-B',
                'exam_name' => 'ঢাকা বিশ্ববিদ্যালয় খ ইউনিট',
                'year' => 2024,
            ],
            [
                'id' => 99903,
                'question_text' => 'একটি প্রক্ষেপকের সর্বোচ্চ পাল্লার জন্য নিক্ষেপণ কোণ কত হতে হবে?',
                'options' => [
                    ['key' => 'A', 'text' => '30°'],
                    ['key' => 'B', 'text' => '45°'],
                    ['key' => 'C', 'text' => '60°'],
                    ['key' => 'D', 'text' => '90°'],
                ],
                'correct_option' => 'B',
                'explanation' => 'প্রক্ষেপকের আনুভূমিক পাল্লা R = (v₀² sin 2θ)/g; sin 2θ এর মান সর্বোচ্চ ১ হয় যখন 2θ = 90° বা θ = 45°।',
                'origin_tag' => 'RU-C',
                'exam_name' => 'রাজশাহী বিশ্ববিদ্যালয় সি ইউনিট',
                'year' => 2024,
            ],
            [
                'id' => 99904,
                'question_text' => 'কোনটিকে আদর্শ দ্রবণ বলা হয় যা রাউল্টের সূত্র মেনে চলে?',
                'options' => [
                    ['key' => 'A', 'text' => 'বেনজিন + টলুইন'],
                    ['key' => 'B', 'text' => 'পানি + ইথানল'],
                    ['key' => 'C', 'text' => 'অ্যাসিটোন + ক্লোরোফর্ম'],
                    ['key' => 'D', 'text' => 'পানি + হাইড্রোক্লোরিক এসিড'],
                ],
                'correct_option' => 'A',
                'explanation' => 'বেনজিন ও টলুইন মিশ্রণ একটি আদর্শ দ্রবণ কারণ আন্তঃআণবিক আকর্ষণ বল একই থাকে।',
                'origin_tag' => 'BUET',
                'exam_name' => 'বুয়েট ভর্তি পরীক্ষা',
                'year' => 2023,
            ],
            [
                'id' => 99905,
                'question_text' => 'বাংলাদেশের সংবিধানের কোন অনুচ্ছেদে শিক্ষার অধিকার নিশ্চিত করা হয়েছে?',
                'options' => [
                    ['key' => 'A', 'text' => '১৫ নং অনুচ্ছেদ'],
                    ['key' => 'B', 'text' => '১৭ নং অনুচ্ছেদ'],
                    ['key' => 'C', 'text' => '১৯ নং অনুচ্ছেদ'],
                    ['key' => 'D', 'text' => '২৭ নং অনুচ্ছেদ'],
                ],
                'correct_option' => 'B',
                'explanation' => 'সংবিধানের ১৭ নং অনুচ্ছেদে অবৈতনিক ও বাধ্যতামূলক শিক্ষার বিধান রয়েছে।',
                'origin_tag' => 'DU-B',
                'exam_name' => 'ঢাকা বিশ্ববিদ্যালয় খ ইউনিট',
                'year' => 2024,
            ],
        ];
    }
}
