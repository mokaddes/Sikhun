<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookChapter;
use App\Models\Course;
use App\Models\Invoice;
use App\Models\Plan;
use App\Services\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function apply(Request $request, CouponService $coupons): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:64'],
            'target_type' => ['required', 'in:book,book_chapter,book_chapters,course,plan,subscription,custom_subscription,invoice'],
            'target_id' => ['nullable', 'integer'],
            'chapter_ids' => ['nullable', 'array'],
            'chapter_ids.*' => ['integer'],
            'months' => ['nullable', 'integer', 'min:1', 'max:12'],
            'subtotal' => ['nullable', 'numeric', 'min:0'],
        ]);

        $student = auth('web')->user();
        $targetType = $validated['target_type'];
        $targetId = isset($validated['target_id']) ? (int) $validated['target_id'] : null;
        $months = (int) ($validated['months'] ?? 1);
        $productId = $targetId;
        $subtotal = (float) ($validated['subtotal'] ?? 0);

        switch ($targetType) {
            case 'book':
                $book = Book::findOrFail($targetId);
                $subtotal = (float) $book->price;
                $productId = $book->id;
                break;

            case 'book_chapter':
                $chapter = BookChapter::findOrFail($targetId);
                $subtotal = (float) $chapter->price;
                $productId = (int) $chapter->book_id;
                break;

            case 'book_chapters':
                $book = Book::findOrFail($targetId);
                $ownedIds = $student->ownedChapters()->where('book_id', $book->id)->pluck('chapter_id')->all();
                $chapterIds = array_values(array_diff(array_map('intval', (array) ($validated['chapter_ids'] ?? [])), $ownedIds));
                $subtotal = (float) $book->chapters()->whereIn('id', $chapterIds)->sum('price');
                $productId = $book->id;
                break;

            case 'course':
                $course = Course::findOrFail($targetId);
                $subtotal = (float) $course->price;
                $productId = $course->id;
                break;

            case 'plan':
            case 'subscription':
                $plan = Plan::findOrFail($targetId);
                if ($plan->price_total > 0 && ($months === (int) $plan->duration_months || $months <= 1)) {
                    $subtotal = (float) $plan->price_total;
                } else {
                    $baseAmount = (float) $plan->price_monthly * $months;
                    $factor = match ($months) {
                        3 => 0.90,
                        6 => 0.80,
                        12 => 0.70,
                        default => 1.0,
                    };
                    $subtotal = round($baseAmount * $factor, 2);
                }
                $productId = $plan->id;
                break;

            case 'custom_subscription':
                $productId = null;
                break;

            case 'invoice':
                $invoice = Invoice::findOrFail($targetId);
                abort_unless((int) $invoice->student_id === (int) $student->id, 403);
                $subtotal = (float) $invoice->total;
                $productId = $invoice->id;
                break;
        }

        try {
            $result = $coupons->validateAndCalculate(
                $student,
                $validated['code'],
                $targetType,
                $productId,
                $subtotal
            );
        } catch (\RuntimeException $e) {
            return response()->json([
                'valid' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'valid' => true,
            'message' => sprintf(
                'কুপন "%s" প্রয়োগ করা হয়েছে — ৳%s ছাড়!',
                $result['code'],
                number_format($result['discount_amount'], 0)
            ),
            'coupon' => [
                'id' => $result['coupon_id'],
                'code' => $result['code'],
                'name' => $result['name'],
                'discount_type' => $result['discount_type'],
                'discount_value' => $result['discount_value'],
                'subtotal' => $result['subtotal'],
                'discount_amount' => $result['discount_amount'],
                'final_amount' => $result['final_amount'],
                'is_free' => $result['is_free'],
            ],
        ]);
    }
}
