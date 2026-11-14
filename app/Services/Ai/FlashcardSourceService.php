<?php

namespace App\Services\Ai;

use App\Models\Book;
use App\Models\QuestionBank;
use App\Models\QuestionSubject;
use App\Models\Student;
use App\Services\BookAccessService;

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

        abort_if($questions->isEmpty(), 422, 'নির্বাচিত বিষয় বা পরীক্ষায় কোনো প্রশ্ন পাওয়া যায়নি।');

        $sourceLines = [];
        $sourceLines[] = "বিষয়: " . ($subject->name_bn ?? $subject->name_en);
        if ($originTag) {
            $sourceLines[] = "পরীক্ষা/ট্যাগ: {$originTag}";
        }
        $sourceLines[] = "\nপ্রশ্ন ও সমাধান ব্যাংক:";

        foreach ($questions as $idx => $q) {
            $num = $idx + 1;
            $qText = trim(strip_tags($q->question_text));
            $ans = trim((string) ($q->correct_answer ?: $q->correct_option));
            $exp = trim(strip_tags((string) $q->explanation));

            $sourceLines[] = "{$num}. প্রশ্ন: {$qText}";
            if ($ans !== '') {
                $sourceLines[] = "   সঠিক উত্তর: {$ans}";
            }
            if ($exp !== '') {
                $sourceLines[] = "   ব্যাখ্যা: {$exp}";
            }
            $sourceLines[] = "";
        }

        $label = ($subject->name_bn ?? $subject->name_en) . ($originTag ? " ({$originTag})" : " প্রশ্নব্যাংক");

        return [
            'content' => implode("\n", $sourceLines),
            'source_label' => $label,
            'references' => [],
        ];
    }
}
