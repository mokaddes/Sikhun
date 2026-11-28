<?php

namespace App\Services\Ai;

use App\Models\Book;
use App\Models\MyBook;
use App\Models\QuestionBank;
use App\Models\QuestionSubject;
use App\Models\Student;
use App\Services\BookAccessService;
use Illuminate\Validation\ValidationException;

class FlashcardSourceService
{
    public function __construct(
        private BookAccessService $access,
        private BookChunkRetrievalService $retrieval,
    ) {}

    /**
     * Source flashcard material from an academic book.
     */
    public function fromBook(Book $book, Student $student, array $selectedChapterIds = []): array
    {
        abort_unless($this->access->hasAccess($student, $book)
            || $this->access->accessibleChapterIds($student, $book) !== null, 403);

        $accessibleChapterIds = $this->access->accessibleChapterIds($student, $book) ?? [];
        $selectedChapterIds = array_values(array_unique(array_map('intval', $selectedChapterIds)));
        abort_if(array_diff($selectedChapterIds, $accessibleChapterIds), 403);

        if ($book->processing_status !== 'completed') {
            abort(422, 'The selected book is not fully processed yet.');
        }

        if ($book->chunks()->count() === 0 && $book->chapters()->count() === 0) {
            abort(422, 'The selected book has no searchable text chunks.');
        }

        $chunks = [];
        if ($book->chunks()->count() > 0) {
            $chunks = $this->retrieval->coverageChunks($book, $student, $selectedChapterIds);
        }

        // If coverage chunks could not be retrieved, fall back to chapter text
        if (empty($chunks)) {
            $chapterQuery = $book->chapters();
            if (! empty($selectedChapterIds)) {
                $chapterQuery->whereIn('id', $selectedChapterIds);
            }
            $chapters = $chapterQuery->orderBy('chapter_number')->limit(5)->get();

            if ($chapters->isEmpty()) {
                abort(422, 'নির্বাচিত বইটি প্রক্রিয়াকরণ সম্পন্ন হয়নি অথবা কোনো কনটেন্ট পাওয়া যায়নি। অন্য সোর্স বা টপিক নির্বাচন করুন।');
            }

            $content = $chapters->map(function ($ch) {
                return "অধ্যায়: {$ch->title}\n" . ($ch->description ?: $ch->title);
            })->implode("\n\n");

            return [
                'content' => $content,
                'source_label' => $book->title,
                'references' => [],
            ];
        }

        $references = [];
        $content = collect($chunks)->map(function (array $chunk) use (&$references) {
            $references[$chunk['chunk_id']] = [
                'chapter_id' => $chunk['chapter_id'],
                'chapter' => $chunk['chapter'],
                'page' => $chunk['page'],
            ];

            $label = '[chunk '.$chunk['chunk_id']
                .($chunk['chapter'] ? ' | chapter '.$chunk['chapter'] : '')
                .($chunk['page'] ? ' | page '.$chunk['page'] : '')
                .']';

            return $label."\n".$chunk['content'];
        })->implode("\n\n");

        return [
            'content' => $content,
            'source_label' => $book->title,
            'references' => $references,
        ];
    }

    /**
     * Source flashcard material from a student's own uploaded MyBook.
     */
    public function fromMyBook(MyBook $myBook, Student $student): array
    {
        abort_unless($myBook->student_id === $student->id, 403);

        if ($myBook->processing_status !== 'completed') {
            abort(422, 'The selected book is not fully processed yet.');
        }

        $pages = $myBook->pages()
            ->whereNotNull('extracted_text')
            ->where('extracted_text', '!=', '')
            ->inRandomOrder()
            ->limit(15)
            ->get(['page_number', 'extracted_text'])
            ->sortBy('page_number');

        if ($pages->isEmpty()) {
            abort(422, 'নির্বাচিত বইটিতে কোনো পাঠ্য কনটেন্ট পাওয়া যায়নি।');
        }

        $content = $pages->map(fn ($p) => "[Page {$p->page_number}]\n" . mb_substr($p->extracted_text, 0, 1200))
            ->implode("\n\n");

        return [
            'content' => mb_substr($content, 0, 8000),
            'source_label' => $myBook->title,
            'references' => [],
        ];
    }

    /**
     * Source flashcard material from an academic topic.
     */
    public function fromTopic(string $topic): array
    {
        $cleanTopic = trim(strip_tags($topic));
        abort_if(empty($cleanTopic), 422, 'অনুগ্রহ করে একটি বৈধ টপিক প্রদান করুন।');

        $content = "টপিক: {$cleanTopic}\n\n"
            . "নির্দেশনা: এই টপিকের অন্তর্ভুক্ত মৌলিক সংজ্ঞা, গুরুত্বপূর্ণ সূত্র, নীতি, মূল বৈশিষ্ট্য, পার্থক্য, উদাহরণ এবং পরীক্ষায় বারবার আসা গুরুত্বপূর্ণ তথ্যগুলোর ওপর ভিত্তি করে কার্যকর রিভিশন ফ্ল্যাশকার্ড তৈরি করুন।";

        return [
            'content' => $content,
            'source_label' => "টপিক: {$cleanTopic}",
            'references' => [],
        ];
    }

    /**
     * Source flashcard material from custom paragraph notes.
     */
    public function fromParagraph(string $text): array
    {
        $cleanText = trim(strip_tags($text));
        abort_if(mb_strlen($cleanText) < 10, 422, 'অনুচ্ছেদ বা নোটে অন্তত ১০টি অক্ষর থাকতে হবে।');

        return [
            'content' => $cleanText,
            'source_label' => 'কাস্টম স্টাডি নোট',
            'references' => [],
        ];
    }

    /**
     * Source flashcard material from Question Bank questions.
     */
    public function fromQuestionBank(int $subjectId, ?string $originTag = null, ?string $sector = null, int $sampleLimit = 25): array
    {
        $subject = QuestionSubject::findOrFail($subjectId);

        $query = QuestionBank::published()->where('subject_id', $subjectId);

        if ($sector) {
            $query->where('segment', $sector);
        }

        if ($originTag) {
            $query->where('origin_tag', $originTag);
        }

        $questions = $query->inRandomOrder()->limit($sampleLimit)->get();

        if ($questions->isEmpty()) {
            throw ValidationException::withMessages([
                'origin_tag' => 'নির্বাচিত বিষয় বা পরীক্ষায় কোনো প্রশ্ন পাওয়া যায়নি। অনুগ্রহ করে এই বিষয়ের সংশ্লিষ্ট পরীক্ষা নির্বাচন করুন।',
            ]);
        }

        $sourceLines = [];
        $sourceLines[] = 'বিষয়: ' . ($subject->name_bn ?? $subject->name_en);
        if ($originTag) {
            $sourceLines[] = "পরীক্ষা/ট্যাগ: {$originTag}";
        }
        $sourceLines[] = 'গুরুত্বপূর্ণ নির্দেশনা: প্রতিটি ফ্ল্যাশকার্ডের উত্তর (back) অবশ্যই নিচে দেওয়া পূর্ণাঙ্গ সঠিক উত্তর টেক্সট হতে হবে—কখনোই A, B, C, D বা ক, খ, গ, ঘ অক্ষর ব্যবহার করবেন না।';
        $sourceLines[] = "\nপ্রশ্ন ও সমাধান ব্যাংক:";

        $usableCount = 0;
        foreach ($questions as $q) {
            $qText = trim(strip_tags((string) $q->question_text));
            $ans = $this->resolveCorrectAnswerText($q);
            $exp = trim(strip_tags((string) ($q->explanation ?: ($q->meta['ai_explanation'] ?? ''))));

            if ($qText === '' || $ans === '') {
                continue;
            }

            $usableCount++;
            $sourceLines[] = "{$usableCount}. প্রশ্ন: {$qText}";
            $sourceLines[] = "   সঠিক উত্তর (পূর্ণাঙ্গ টেক্সট): {$ans}";
            if ($exp !== '') {
                $sourceLines[] = "   ব্যাখ্যা: {$exp}";
            }
            $sourceLines[] = '';
        }

        if ($usableCount === 0) {
            throw ValidationException::withMessages([
                'subject_id' => 'নির্বাচিত প্রশ্নগুলোতে পূর্ণাঙ্গ উত্তর টেক্সট পাওয়া যায়নি। অনুগ্রহ করে অন্য বিষয় বা পরীক্ষা নির্বাচন করুন।',
            ]);
        }

        $label = ($subject->name_bn ?? $subject->name_en) . ($originTag ? " ({$originTag})" : ' প্রশ্নব্যাংক');

        return [
            'content' => implode("\n", $sourceLines),
            'source_label' => $label,
            'references' => [],
        ];
    }

    /**
     * Resolve the human-readable full answer text from a QuestionBank MCQ,
     * rejecting bare option letters like A, B, C, D or ক, খ, গ, ঘ.
     */
    private function resolveCorrectAnswerText(QuestionBank $q): string
    {
        $stripPrefix = static fn (string $text): string => trim(
            (string) preg_replace('/^(?:[A-Da-d]|[ক-ঘ])[\)\.\:\-]\s*/u', '', trim(strip_tags($text)))
        );

        $isBareLetter = static fn (string $text): bool => preg_match('/^(?:option\s*)?(?:[A-Da-d]|[ক-ঘ])[\)\.\s]*$/iu', trim($text)) === 1;

        $candidate = trim((string) ($q->correct_option_text ?? ''));
        if ($candidate !== '') {
            $cleaned = $stripPrefix($candidate);
            if ($cleaned !== '' && ! $isBareLetter($cleaned)) {
                return $cleaned;
            }
        }

        $targetRaw = trim((string) ($q->correct_option ?? ''));
        $targetUpper = strtoupper($targetRaw);
        $letterIndexMap = [
            'A' => 0, 'B' => 1, 'C' => 2, 'D' => 3,
            'ক' => 0, 'খ' => 1, 'গ' => 2, 'ঘ' => 3,
        ];
        $indexToAliases = [
            0 => ['A', 'ক'],
            1 => ['B', 'খ'],
            2 => ['C', 'গ'],
            3 => ['D', 'ঘ'],
        ];
        $targetIndex = $letterIndexMap[$targetUpper] ?? $letterIndexMap[$targetRaw] ?? null;
        $acceptedKeys = $targetIndex !== null ? $indexToAliases[$targetIndex] : array_values(array_unique(array_filter([$targetRaw, $targetUpper])));

        $options = is_array($q->options) ? $q->options : json_decode((string) ($q->options ?? '[]'), true);
        if (is_array($options)) {
            foreach ($options as $idx => $opt) {
                $matchedText = null;
                if (is_array($opt)) {
                    $key = trim((string) ($opt['key'] ?? ''));
                    if ($key !== '' && in_array(strtoupper($key), array_map('strtoupper', $acceptedKeys), true)) {
                        $matchedText = (string) ($opt['text'] ?? '');
                    } elseif ($key === '' && $targetIndex !== null && (int) $idx === $targetIndex) {
                        $matchedText = (string) ($opt['text'] ?? '');
                    }
                } elseif (is_string($idx) && in_array(strtoupper(trim($idx)), array_map('strtoupper', $acceptedKeys), true)) {
                    $matchedText = (string) $opt;
                } elseif (is_string($opt)) {
                    if ($targetIndex !== null && (int) $idx === $targetIndex) {
                        $matchedText = $opt;
                    } elseif (preg_match('/^([A-Da-d]|[ক-ঘ])[\)\.\:\-]\s*(.+)$/us', trim($opt), $m)) {
                        if (in_array(strtoupper($m[1]), array_map('strtoupper', $acceptedKeys), true)) {
                            $matchedText = $m[2];
                        }
                    }
                }

                if ($matchedText !== null) {
                    $cleaned = $stripPrefix($matchedText);
                    if ($cleaned !== '' && ! $isBareLetter($cleaned)) {
                        return $cleaned;
                    }
                }
            }
        }

        if ($targetRaw !== '' && ! $isBareLetter($targetRaw)) {
            $cleaned = $stripPrefix($targetRaw);
            if ($cleaned !== '' && ! $isBareLetter($cleaned)) {
                return $cleaned;
            }
        }

        return '';
    }
}
