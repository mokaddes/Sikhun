<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Student\PurchaseRequest;
use App\Models\Book;
use App\Services\BookAccessService;
use App\Services\Payment\ZinipayService;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LibraryController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $books = Book::published()
            ->with(['author:id,name', 'category:id,name'])
            ->when($request->level, fn ($q) => $q->where('level', $request->level))
            ->when($request->subject, fn ($q) => $q->where('subject', $request->subject))
            ->when($request->free, fn ($q) => $q->where('is_free', true))
            ->when($request->search, fn ($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(20);

        return $this->success($books);
    }

    public function show(Book $book, BookAccessService $access): JsonResponse
    {
        abort_unless($book->is_published, 404);

        $book->load(['author', 'publication', 'category']);
        $data = $book->toArray();
        $data['access_type'] = $access->accessType(auth('sanctum')->user(), $book);
        $data['has_access'] = $access->hasAccess(auth('sanctum')->user(), $book);

        return $this->success($data);
    }

    public function purchase(PurchaseRequest $request, Book $book, PurchaseService $purchases, ZinipayService $zinipay): JsonResponse
    {
        try {
            $result = $purchases->purchaseBook(
                auth('sanctum')->user(),
                $book,
                $request->payment_method,
                $request->payment_method === 'zinipay' ? $zinipay : null
            );
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), [], 422);
        }

        return $this->success($result, $result['redirect_url'] ? 'Redirect to complete payment' : 'Purchased successfully');
    }
}
