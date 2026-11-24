<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\QuestionBank;
use App\Models\QuestionSubject;
use App\Services\PublicQuestionCatalog;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Indexable Programmatic SEO Question Bank (/questions, /questions/{collection}, /questions/subject/{slug}).
 *
 * Exposes curated previews of Sikhun's 38,499+ past admission and job exam MCQs
 * with interactive self-practice, instant explanations, structured Quiz/FAQPage JSON-LD,
 * and server-rendered <noscript> semantic HTML for Search & AI crawlers.
 */
class PublicQuestionBankController extends Controller
{
    public function __construct(
        private readonly SeoService $seo
    ) {}

    public function hub(): Response
    {
        $payload = Cache::remember('public_questions_hub_v1', 1800, function () {
            $totalQuestions = QuestionBank::published()->count();
            $admissionCount = QuestionBank::published()->where('segment', 'admission')->count();
            $jobCount = QuestionBank::published()->where('segment', 'job')->count();

            $collections = [];
            foreach (PublicQuestionCatalog::collections() as $slug => $meta) {
                $count = PublicQuestionCatalog::queryForCollection($meta)->count();
                if ($count > 0) {
                    $collections[] = array_merge($meta, ['count' => $count]);
                }
            }

            $subjects = QuestionSubject::query()
                ->where('is_active', true)
                ->withCount(['questions' => fn ($q) => $q->published()])
                ->having('questions_count', '>', 0)
                ->orderByDesc('questions_count')
                ->take(30)
                ->get(['id', 'name_bn', 'name_en', 'slug', 'segment']);

            return [
                'stats' => [
                    'total' => $totalQuestions,
                    'admission' => $admissionCount,
                    'job' => $jobCount,
                    'collections' => count($collections),
                ],
                'collections' => $collections,
                'subjects' => $subjects,
            ];
        });

        return Inertia::render('Public/Questions/Hub', [
            'stats' => $payload['stats'],
            'collections' => $payload['collections'],
            'subjects' => $payload['subjects'],
            'seo' => $this->seo->forQuestionsHub($payload['stats']['total'], $payload['collections']),
        ]);
    }

    public function collection(Request $request, string $slug): Response
    {
        $collection = PublicQuestionCatalog::find($slug);
        abort_unless($collection !== null, 404);

        $baseQuery = PublicQuestionCatalog::queryForCollection($collection);
        $totalCount = Cache::remember("public_q_col_count_{$slug}", 1800, fn () => (clone $baseQuery)->count());

        $query = (clone $baseQuery)
            ->with('subject:id,name_bn,name_en,slug')
            ->orderByDesc('year')
            ->orderByDesc('id');

        if ($search = trim((string) $request->input('search'))) {
            $query->where('question_text', 'like', "%{$search}%");
        }

        if ($year = $request->input('year')) {
            if ($year !== 'all' && is_numeric($year)) {
                $query->where('year', (int) $year);
            }
        }

        $questions = $query->paginate(15)->withQueryString();
        $questions->getCollection()->transform(fn (QuestionBank $q) => $this->formatQuestion($q));

        $years = Cache::remember("public_q_col_years_{$slug}", 1800, function () use ($baseQuery) {
            return (clone $baseQuery)
                ->whereNotNull('year')
                ->distinct()
                ->orderByDesc('year')
                ->pluck('year')
                ->take(15)
                ->values()
                ->all();
        });

        $relatedCollections = collect(PublicQuestionCatalog::collections())
            ->filter(fn ($c) => $c['slug'] !== $slug && $c['segment'] === $collection['segment'])
            ->take(6)
            ->values()
            ->all();

        $relatedBooks = Book::published()
            ->when($collection['segment'] === 'job', fn ($q) => $q->where('level', 'job'))
            ->latest('id')
            ->take(4)
            ->get(['id', 'title', 'slug', 'subject', 'level', 'cover_image', 'is_free', 'price']);

        return Inertia::render('Public/Questions/Show', [
            'mode' => 'collection',
            'header' => [
                'slug' => $collection['slug'],
                'segment' => $collection['segment'],
                'title_bn' => $collection['title_bn'],
                'title_en' => $collection['title_en'],
                'short_bn' => $collection['short_bn'],
                'institution_bn' => $collection['institution_bn'],
                'description_bn' => $collection['description_bn'],
                'faqs' => $collection['faqs'] ?? [],
            ],
            'totalCount' => $totalCount,
            'questions' => $questions,
            'years' => $years,
            'filters' => [
                'search' => (string) $request->input('search', ''),
                'year' => (string) $request->input('year', 'all'),
            ],
            'relatedCollections' => $relatedCollections,
            'relatedBooks' => $relatedBooks,
            'seo' => $this->seo->forQuestionCollection(
                $collection,
                $totalCount,
                $questions->getCollection()->all()
            ),
        ]);
    }

    public function subject(Request $request, string $slug): Response
    {
        $subject = QuestionSubject::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $baseQuery = QuestionBank::query()
            ->published()
            ->where('subject_id', $subject->id)
            ->where('question_text', '!=', '');

        $totalCount = Cache::remember("public_q_sub_count_{$subject->id}", 1800, fn () => (clone $baseQuery)->count());

        $query = (clone $baseQuery)
            ->with('subject:id,name_bn,name_en,slug')
            ->orderByDesc('year')
            ->orderByDesc('id');

        if ($search = trim((string) $request->input('search'))) {
            $query->where('question_text', 'like', "%{$search}%");
        }

        if ($year = $request->input('year')) {
            if ($year !== 'all' && is_numeric($year)) {
                $query->where('year', (int) $year);
            }
        }

        $questions = $query->paginate(15)->withQueryString();
        $questions->getCollection()->transform(fn (QuestionBank $q) => $this->formatQuestion($q));

        $years = Cache::remember("public_q_sub_years_{$subject->id}", 1800, function () use ($baseQuery) {
            return (clone $baseQuery)
                ->whereNotNull('year')
                ->distinct()
                ->orderByDesc('year')
                ->pluck('year')
                ->take(15)
                ->values()
                ->all();
        });

        $relatedCollections = collect(PublicQuestionCatalog::collections())->take(6)->values()->all();

        return Inertia::render('Public/Questions/Show', [
            'mode' => 'subject',
            'header' => [
                'slug' => $subject->slug,
                'segment' => $subject->segment ?? 'both',
                'title_bn' => ($subject->name_bn ?: $subject->name_en).' MCQ প্রশ্নব্যাংক ও সমাধান',
                'title_en' => ($subject->name_en ?: $subject->name_bn).' MCQ Question Bank',
                'short_bn' => $subject->name_bn ?: $subject->name_en,
                'institution_bn' => 'সকল বিশ্ববিদ্যালয় ভর্তি, বিসিএস ও সরকারি চাকরি',
                'description_bn' => ($subject->name_bn ?: $subject->name_en).' বিষয়ের '.number_format($totalCount).'টি বিগত বছরের আসল MCQ প্রশ্ন, উত্তর ও ব্যাখ্যা ফ্রিতে প্র্যাকটিস করুন।',
                'faqs' => [],
            ],
            'totalCount' => $totalCount,
            'questions' => $questions,
            'years' => $years,
            'filters' => [
                'search' => (string) $request->input('search', ''),
                'year' => (string) $request->input('year', 'all'),
            ],
            'relatedCollections' => $relatedCollections,
            'relatedBooks' => [],
            'seo' => $this->seo->forQuestionSubject(
                $subject,
                $totalCount,
                $questions->getCollection()->all()
            ),
        ]);
    }

    private function formatQuestion(QuestionBank $q): array
    {
        $options = is_array($q->options) ? $q->options : json_decode((string) ($q->options ?? '[]'), true);
        $formattedOptions = [];
        if (is_array($options)) {
            foreach ($options as $opt) {
                if (is_array($opt) && isset($opt['key'], $opt['text'])) {
                    $formattedOptions[] = [
                        'key' => strtoupper(trim((string) $opt['key'])),
                        'text' => (string) $opt['text'],
                    ];
                }
            }
        }

        $correctKey = strtoupper(trim((string) ($q->correct_option ?? '')));
        $correctText = collect($formattedOptions)->firstWhere('key', $correctKey)['text'] ?? $correctKey;

        return [
            'id' => $q->id,
            'question_text' => (string) $q->question_text,
            'options' => $formattedOptions,
            'correct_option' => $correctKey,
            'correct_option_text' => $correctText,
            'explanation' => $q->explanation,
            'origin_tag' => $q->origin_tag,
            'institution' => $q->institution,
            'exam_name' => $q->exam_name,
            'year' => $q->year,
            'subject' => $q->subject ? [
                'name_bn' => $q->subject->name_bn,
                'name_en' => $q->subject->name_en,
                'slug' => $q->subject->slug,
            ] : null,
        ];
    }
}
