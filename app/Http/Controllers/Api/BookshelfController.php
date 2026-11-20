<?php

namespace App\Http\Controllers\Api;

use App\Models\ReadingSession;
use App\Models\Student;
use Illuminate\Http\JsonResponse;

class BookshelfController extends BaseApiController
{
    public function index(): JsonResponse
    {
        /** @var Student $student */
        $student = auth('sanctum')->user();

        $shelves = $student->bookShelf()
            ->with('book:id,title,slug,cover_image,level,subject,total_pages')
            ->latest('added_at')
            ->get();

        $bookIds = $shelves->pluck('book_id')->all();
        $lastPages = ReadingSession::where('student_id', $student->id)
            ->whereIn('book_id', $bookIds)
            ->whereNotNull('last_page')
            ->orderBy('id', 'desc')
            ->get(['book_id', 'last_page'])
            ->unique('book_id')
            ->pluck('last_page', 'book_id');

        $shelves->each(function ($shelf) use ($lastPages) {
            $shelf->last_read_page = $lastPages[$shelf->book_id] ?? 1;
        });

        return $this->success([
            'shelves' => $shelves,
            'my_books' => $student->myBooks()->latest()->get(),
            'upload_quota' => [
                'current_count' => $student->myBooks()->count(),
                'max_slots' => $student->maxUploadBookSlots(),
                'reuploads_remaining' => $student->getMonthlyReuploadsRemaining(),
            ],
        ]);
    }
}
