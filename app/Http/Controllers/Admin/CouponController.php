<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CouponRequest;
use App\Models\Coupon;
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
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('code', 'like', "%{$request->search}%"))
            ->when($request->status, fn ($q) => $q->where('is_active', $request->status === 'active'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Coupons/Index', [
            'coupons' => $coupons,
            'filters' => $request->only('search', 'status'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Coupons/Form', [
            'students' => Student::where('status', 'active')->orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }

    public function store(CouponRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('student_id');

        if ($request->filled('student_id')) {
            $data['student_id'] = $request->student_id;
            $data['code'] = null;
        } elseif (empty($data['code'])) {
            $data['code'] = null;
        }

        Coupon::create($data);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created.');
    }

    public function edit(Coupon $coupon): Response
    {
        return Inertia::render('Admin/Coupons/Form', [
            'coupon' => $coupon,
            'students' => Student::where('status', 'active')->orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }

    public function update(CouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $data = $request->safe()->except('student_id');

        if ($request->filled('student_id')) {
            $data['student_id'] = $request->student_id;
            $data['code'] = null;
        } else {
            $data['student_id'] = null;
            $data['code'] = empty($data['code']) ? null : $data['code'];
        }

        $coupon->update($data);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return back()->with('success', 'Coupon deleted.');
    }
}
