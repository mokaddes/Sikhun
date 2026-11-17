<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignSubscriptionRequest;
use App\Http\Requests\Admin\GrantAccessRequest;
use App\Http\Requests\Admin\WalletAdjustRequest;
use App\Mail\AdminStudentActionMail;
use App\Models\Coupon;
use App\Models\Plan;
use App\Models\Student;
use App\Services\AccessGrantService;
use App\Services\SubscriptionService;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
        $newStatus = $student->status === 'active' ? 'inactive' : 'active';
        $student->update(['status' => $newStatus]);

        $this->notifyStudent(
            $student,
            $newStatus === 'active' ? 'Your Sikhun account has been activated' : 'Your Sikhun account status was updated',
            $newStatus === 'active' ? 'Account Activated' : 'Account Status Updated',
            $newStatus === 'active'
                ? 'An administrator has activated your Sikhun student account. You can now access all your learning tools.'
                : 'An administrator has changed your Sikhun student account status to inactive.',
            ['Account Status' => ucfirst($newStatus)]
        );

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

        $student->refresh();
        $actionLabel = $method === 'credit' ? 'Credited' : 'Debited';
        $details = [
            'Action' => $actionLabel,
            'Amount' => '৳'.number_format((float) $request->amount, 2),
            'New Wallet Balance' => '৳'.number_format((float) $student->wallet_balance, 2),
        ];
        if ($request->filled('notes')) {
            $details['Reason'] = (string) $request->notes;
        }

        $this->notifyStudent(
            $student,
            "Wallet {$actionLabel}: ৳".number_format((float) $request->amount, 2),
            "Wallet {$actionLabel}",
            "An administrator has {$actionLabel} your Sikhun wallet.",
            $details
        );

        return back()->with('success', 'Wallet updated.');
    }

    public function assignSubscription(AssignSubscriptionRequest $request, Student $student, SubscriptionService $subscriptions): RedirectResponse
    {
        $plan = Plan::findOrFail($request->plan_id);
        $subscription = $subscriptions->assign($student, $plan, (int) $request->months);

        $this->notifyStudent(
            $student,
            "Subscription Assigned: {$plan->name}",
            'Subscription Updated',
            "An administrator has assigned the {$plan->name} subscription to your account.",
            [
                'Plan' => $plan->name,
                'Duration' => ((int) $request->months).' month(s)',
                'Expires At' => $subscription->expires_at?->format('M d, Y') ?? 'Active',
            ]
        );

        return back()->with('success', 'Subscription assigned.');
    }

    /**
     * Quick "give this student full access" — creates a direct-assign
     * coupon so books, courses and AI are unlocked without a package
     * (or beyond it) for the requested duration.
     */
    public function grantAccess(GrantAccessRequest $request, Student $student, AccessGrantService $grants): RedirectResponse
    {
        $label = $request->name ?: "Access grant for {$student->name}";
        $coupon = Coupon::create([
            'name' => $label,
            'description' => $request->notes,
            'student_id' => $student->id,
            'duration_days' => $request->duration_days,
            'is_active' => true,
        ]);

        $redemption = $grants->assign($student, $coupon);

        $details = [
            'Access Pass' => $label,
            'Duration' => ((int) $request->duration_days).' day(s)',
            'Expires At' => $redemption->expires_at?->format('M d, Y') ?? 'Active',
        ];
        if ($request->filled('notes')) {
            $details['Notes'] = (string) $request->notes;
        }

        $this->notifyStudent(
            $student,
            "Free Full Access Granted ({$request->duration_days} days)",
            'Full Access Granted',
            'An administrator has granted you full access to Sikhun books, courses, and AI tools.',
            $details
        );

        return back()->with('success', 'Full access granted to student.');
    }

    /**
     * Send an email notification to the student without blocking the admin action on mail errors.
     *
     * @param  array<string, string>  $details
     */
    private function notifyStudent(Student $student, string $subject, string $heading, string $summary, array $details = []): void
    {
        if (empty($student->email)) {
            return;
        }

        try {
            Mail::to($student->email)->send(new AdminStudentActionMail($student, $subject, $heading, $summary, $details));
        } catch (\Throwable $e) {
            Log::warning('Failed to send admin student action email', [
                'student_id' => $student->id,
                'email' => $student->email,
                'subject' => $subject,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
