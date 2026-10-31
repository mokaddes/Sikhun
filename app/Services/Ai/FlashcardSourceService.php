<?php

namespace App\Services\Ai;

use App\Models\Book;
use App\Models\Student;
use App\Services\BookAccessService;

class FlashcardSourceService
{
    public function __construct(
        private BookAccessService $access,
        private BookChunkRetrievalService $retrieval,
    ) {}


    public function fromBook(Book $book, Student $student, array $selectedChapterIds = []): array
    {
        abort_unless($this->access->hasAccess($student, $book)
            || $this->access->accessibleChapterIds($student, $book) !== null, 403);

        if ($book->processing_status !== 'completed') {
            abort(422, 'The selected book is not fully processed yet.');
        }

        if ($book->chunks()->count() === 0) {
            abort(422, 'The selected book has no searchable text chunks.');
        }

        $accessibleChapterIds = $this->access->accessibleChapterIds($student, $book) ?? [];
        $selectedChapterIds = array_values(array_unique(array_map('intval', $selectedChapterIds)));
        abort_if(array_diff($selectedChapterIds, $accessibleChapterIds), 403);

        $chunks = $this->retrieval->coverageChunks($book, $student, $selectedChapterIds);
        if (! $chunks) {
            abort(422, 'No processed book content is available within your current access.');
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
}
