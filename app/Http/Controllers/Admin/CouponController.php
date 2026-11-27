<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CouponRequest;
use App\Models\Book;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CouponController extends Controller
{
    public function index(Request $request): Response
    {
        $coupons = Coupon::query()
            ->with('student:id,name,email')
            ->when($request->search, fn ($q) => $q->where(function ($sub) use ($request) {
                $sub->where('name', 'like', "%{$request->search}%")
                    ->orWhere('code', 'like', "%{$request->search}%");
            }))
            ->when($request->status, fn ($q) => $q->where('is_active', $request->status === 'active'))
            ->when($request->kind, fn ($q) => $q->where('coupon_kind', $request->kind))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Coupons/Index', [
            'coupons' => $coupons,
            'filters' => $request->only('search', 'status', 'kind'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Coupons/Form', $this->formOptions());
    }

    public function store(CouponRequest $request): RedirectResponse
    {
        $data = $this->preparePayload($request);
        Coupon::create($data);

        return redirect()->route('admin.coupons.index')->with('success', 'কুপন সফলভাবে তৈরি করা হয়েছে।');
    }

    public function edit(Coupon $coupon): Response
    {
        return Inertia::render('Admin/Coupons/Form', array_merge([
            'coupon' => $coupon,
        ], $this->formOptions()));
    }

    public function update(CouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $data = $this->preparePayload($request);
        $coupon->update($data);

        return redirect()->route('admin.coupons.index')->with('success', 'কুপন সফলভাবে আপডেট করা হয়েছে।');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return back()->with('success', 'কুপন মুছে ফেলা হয়েছে।');
    }

    private function preparePayload(CouponRequest $request): array
    {
        $data = $request->safe()->except(['student_id', 'allowed_student_ids', 'applicable_types', 'applicable_product_ids']);
        $kind = $data['coupon_kind'] ?? 'discount';
        $data['coupon_kind'] = $kind;

        if ($kind === 'access_grant') {
            $data['discount_type'] = $data['discount_type'] ?? 'percent';
            $data['discount_value'] = (float) ($data['discount_value'] ?? 0);
            if ($request->filled('student_id')) {
                $data['student_id'] = $request->integer('student_id');
                $data['code'] = null;
            } else {
                $data['student_id'] = null;
                $data['code'] = empty($data['code']) ? null : mb_strtoupper(trim((string) $data['code']));
            }
            $data['allowed_student_ids'] = null;
            $data['applicable_types'] = null;
            $data['applicable_product_ids'] = null;

            return $data;
        }

        // Discount coupon: ALWAYS keep normalized uppercase code even when targeted to specific user(s)
        $data['code'] = mb_strtoupper(trim((string) ($data['code'] ?? '')));
        $data['discount_type'] = $data['discount_type'] ?? 'percent';
        $data['discount_value'] = (float) ($data['discount_value'] ?? 0);

        $allowedUsers = array_values(array_unique(array_filter(array_map('intval', (array) $request->input('allowed_student_ids', [])))));
        if ($request->filled('student_id')) {
            $allowedUsers = array_values(array_unique(array_merge([$request->integer('student_id')], $allowedUsers)));
        }
        $data['student_id'] = count($allowedUsers) === 1 ? $allowedUsers[0] : null;
        $data['allowed_student_ids'] = $allowedUsers !== [] ? $allowedUsers : null;

        $types = array_values(array_unique(array_filter((array) $request->input('applicable_types', ['all']))));
        if (in_array('all', $types, true) || $types === []) {
            $types = ['all'];
        }
        $data['applicable_types'] = $types;

        $rawProducts = (array) $request->input('applicable_product_ids', []);
        $cleanProducts = [];
        foreach (['book', 'course', 'plan', 'invoice'] as $category) {
            $ids = array_values(array_unique(array_filter(array_map('intval', (array) ($rawProducts[$category] ?? [])))));
            if ($ids !== [] && ($types === ['all'] || in_array($category, $types, true))) {
                $cleanProducts[$category] = $ids;
            }
        }
        $data['applicable_product_ids'] = $cleanProducts !== [] ? $cleanProducts : null;

        return $data;
    }

    private function formOptions(): array
    {
        return [
            'students' => Student::where('status', 'active')->orderBy('name')->get(['id', 'name', 'email']),
            'books' => Book::where('is_published', true)->orderBy('title')->get(['id', 'title', 'price']),
            'courses' => Course::where('is_active', true)->orderBy('title')->get(['id', 'title', 'price']),
            'plans' => Plan::where('is_active', true)->orderBy('name')->get(['id', 'name', 'price_monthly', 'price_total']),
            'invoices' => Invoice::with('student:id,name')->whereIn('status', ['draft', 'sent', 'overdue'])->latest()->take(100)->get(['id', 'invoice_number', 'student_id', 'total', 'status']),
        ];
    }
}
