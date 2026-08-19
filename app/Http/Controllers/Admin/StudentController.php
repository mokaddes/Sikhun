<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignSubscriptionRequest;
use App\Http\Requests\Admin\GrantAccessRequest;
use App\Http\Requests\Admin\WalletAdjustRequest;
use App\Models\Coupon;
use App\Models\Plan;
use App\Models\Student;
use App\Services\AccessGrantService;
use App\Services\SubscriptionService;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    public function index(Request $request): Response
    {
        $students = Student::query()
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Students/Index', [
            'students' => $students,
            'filters' => $request->only('search', 'type', 'status'),
        ]);
    }

    public function show(Student $student, AccessGrantService $grants): Response
    {
        return Inertia::render('Admin/Students/Show', [
            'student' => $student->load(['activeSubscription.plan', 'walletTransactions' => fn ($q) => $q->latest()->limit(20)]),
            'plans' => Plan::where('is_active', true)->get(['id', 'name', 'price_monthly']),
            'access' => $grants->accessSummary($student),
        ]);
    }

    public function toggleStatus(Student $student): RedirectResponse
    {
        $student->update(['status' => $student->status === 'active' ? 'inactive' : 'active']);

        return back()->with('success', 'Student status updated.');
    }

    public function adjustWallet(WalletAdjustRequest $request, Student $student, WalletService $wallet): RedirectResponse
    {
        $method = $request->type === 'credit' ? 'credit' : 'debit';

        try {
            $wallet->{$method}($student, (float) $request->amount, 'admin_'.$method, null, $request->notes);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Wallet updated.');
    }

    public function assignSubscription(AssignSubscriptionRequest $request, Student $student, SubscriptionService $subscriptions): RedirectResponse
    {
        $plan = Plan::findOrFail($request->plan_id);
        $subscriptions->assign($student, $plan, (int) $request->months);

        return back()->with('success', 'Subscription assigned.');
    }

    /**
     * Quick "give this student full access" — creates a direct-assign
     * coupon so books, courses and AI are unlocked without a package
     * (or beyond it) for the requested duration.
     */
    public function grantAccess(GrantAccessRequest $request, Student $student, AccessGrantService $grants): RedirectResponse
    {
        $coupon = Coupon::create([
            'name' => $request->name ?: "Access grant for {$student->name}",
            'description' => $request->notes,
            'student_id' => $student->id,
            'duration_days' => $request->duration_days,
            'is_active' => true,
        ]);

        $grants->assign($student, $coupon);

        return back()->with('success', 'Full access granted to student.');
    }
}
