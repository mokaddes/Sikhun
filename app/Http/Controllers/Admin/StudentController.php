<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignSubscriptionRequest;
use App\Http\Requests\Admin\GrantAccessRequest;
use App\Http\Requests\Admin\WalletAdjustRequest;
use App\Mail\AdminStudentActionMail;
use App\Models\Book;
use App\Models\BookShelf;
use App\Models\Coupon;
use App\Models\MyBook;
use App\Models\Plan;
use App\Models\Student;
use App\Services\AccessGrantService;
use App\Services\NotificationService;
use App\Services\SubscriptionService;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    public function index(Request $request): Response
    {
        $isTrashed = $request->status === 'trashed';

        $query = $isTrashed ? Student::onlyTrashed() : Student::query();

        $students = $query
            ->when($request->search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->when(! $isTrashed && $request->status === 'unverified', fn ($q) => $q->whereNull('email_verified_at'))
            ->when(! $isTrashed && in_array($request->status, ['active', 'inactive'], true), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Students/Index', [
            'students' => $students,
            'filters' => $request->only('search', 'type', 'status'),
            'trashedCount' => Student::onlyTrashed()->count(),
            'unverifiedCount' => Student::whereNull('email_verified_at')->count(),
        ]);
    }

    public function show(Student $student, AccessGrantService $grants): Response
    {
        $student->load([
            'activeSubscription.plan',
            'subscriptions' => fn ($q) => $q->with('plan:id,name,price_monthly')->latest()->limit(20),
            'walletTransactions' => fn ($q) => $q->latest()->limit(50),
            'orders' => fn ($q) => $q->latest()->limit(50),
            'invoices' => fn ($q) => $q->latest()->limit(30),
            'bookShelf' => fn ($q) => $q->with('book:id,title,slug,price,is_free')->latest('added_at'),
            'myBooks' => fn ($q) => $q->latest(),
            'courseEnrollments' => fn ($q) => $q->with('course:id,title,slug,price')->latest(),
            'ownedChapters' => fn ($q) => $q->with('chapter:id,book_id,title,price', 'chapter.book:id,title,slug')->latest(),
        ]);

        return Inertia::render('Admin/Students/Show', [
            'student' => array_merge($student->toArray(), [
                'is_trashed' => $student->trashed(),
                'days_until_purge' => $student->trashed() ? $student->daysUntilPurge() : null,
            ]),
            'plans' => Plan::where('is_active', true)->get(['id', 'name', 'price_monthly']),
            'allBooks' => Book::orderBy('title')->get(['id', 'title', 'slug', 'price']),
            'access' => $grants->accessSummary($student),
        ]);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('students', 'email')->ignore($student->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'type' => ['required', 'in:ssc,hsc,admission,university,job'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $student->update($validated);

        return back()->with('success', 'Student profile updated successfully.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->tokens()->delete();
        $student->pushSubscriptions()->delete();
        $student->scheduleDeletion(30);

        $this->notifyStudent(
            $student,
            'Your Sikhun account is scheduled for deletion',
            'Account Scheduled for Deletion (30-Day Recovery)',
            'Your Sikhun account has been deactivated and scheduled for permanent deletion in 30 days. You can log back in within 30 days to restore your account.',
            ['Recovery Window' => '30 days']
        );

        return redirect()->route('admin.students.index')->with('success', 'Student moved to deleted accounts (recoverable for 30 days).');
    }

    public function restore(Student $student): RedirectResponse
    {
        $student->restoreFromDeletion();

        $this->notifyStudent(
            $student,
            'Your Sikhun account has been restored',
            'Account Restored',
            'An administrator has restored your Sikhun student account. You can now log in normally.',
            ['Account Status' => 'Active']
        );

        return back()->with('success', 'Student account restored successfully.');
    }

    public function verifyEmail(Student $student, NotificationService $notifications): RedirectResponse
    {
        if (! $student->hasVerifiedEmail()) {
            $student->forceFill(['email_verified_at' => now()])->save();
        }

        $notifications->send(
            student: $student,
            type: 'admin_broadcast',
            title: 'ইমেইল ভেরিফিকেশন সম্পন্ন হয়েছে',
            body: 'অ্যাডমিন আপনার ইমেইল সফলভাবে অনুমোদন করেছেন। এখন আপনি সব ফিচার ব্যবহার করতে পারবেন।',
            data: ['url' => '/dashboard'],
            mandatory: true
        );

        $this->notifyStudent(
            $student,
            'Your Sikhun email has been verified',
            'Email Approved & Verified',
            'An administrator has manually verified your email address on Sikhun.com. Your account is now fully active.',
            ['Email' => (string) $student->email]
        );

        return back()->with('success', 'Student email manually verified & approved.');
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

    public function grantAccess(GrantAccessRequest $request, Student $student, AccessGrantService $grants, NotificationService $notifications): RedirectResponse
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

        $notifications->send(
            student: $student,
            type: 'plan_update',
            title: "ফ্রি ফুল অ্যাক্সেস যুক্ত হয়েছে ({$request->duration_days} দিন)",
            body: "অ্যাডমিন আপনার অ্যাকাউন্টে {$request->duration_days} দিনের জন্য সব বই, কোর্স ও AI টুল আনলক করেছেন।",
            data: ['url' => '/dashboard'],
            mandatory: true
        );

        $this->notifyStudent(
            $student,
            "Free Full Access Granted ({$request->duration_days} days)",
            'Full Access Granted',
            'An administrator has granted you full access to Sikhun books, courses, and AI tools.',
            $details
        );

        return back()->with('success', 'Full access granted to student.');
    }

    public function sendOffer(Request $request, Student $student, NotificationService $notifications): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:1500'],
            'action_url' => ['nullable', 'string', 'max:500'],
            'send_email' => ['nullable', 'boolean'],
        ]);

        $actionUrl = trim((string) ($validated['action_url'] ?? '')) ?: '/dashboard';

        $notifications->send(
            student: $student,
            type: 'special_offer',
            title: $validated['title'],
            body: $validated['body'],
            data: ['url' => $actionUrl],
            mandatory: true
        );

        if ($request->boolean('send_email', true)) {
            $this->notifyStudent(
                $student,
                $validated['title'],
                $validated['title'],
                $validated['body'],
                ['Link' => str_starts_with($actionUrl, 'http') ? $actionUrl : url($actionUrl)]
            );
        }

        return back()->with('success', 'Personal offer sent via Notification, Firebase Push'.($request->boolean('send_email', true) ? ' & Email.' : '.'));
    }

    public function addBookToShelf(Request $request, Student $student, NotificationService $notifications): RedirectResponse
    {
        $validated = $request->validate([
            'book_id' => ['required', 'exists:books,id'],
        ]);

        $book = Book::findOrFail($validated['book_id']);

        BookShelf::updateOrCreate(
            ['student_id' => $student->id, 'book_id' => $book->id],
            ['source' => 'admin', 'added_at' => now()]
        );

        $notifications->send(
            student: $student,
            type: 'plan_update',
            title: "নতুন বই বুকশেলফে যুক্ত হয়েছে: {$book->title}",
            body: "অ্যাডমিন '{$book->title}' বইটি আপনার বুকশেলফে যুক্ত করেছেন। এখনই পড়া শুরু করুন!",
            data: ['url' => '/library/'.$book->slug, 'book_id' => $book->id],
            mandatory: true
        );

        return back()->with('success', "Book '{$book->title}' added to student's bookshelf.");
    }

    public function removeBookFromShelf(Student $student, Book $book): RedirectResponse
    {
        BookShelf::where('student_id', $student->id)->where('book_id', $book->id)->delete();

        return back()->with('success', "Book '{$book->title}' removed from student's bookshelf.");
    }

    public function deleteUploadedBook(Student $student, MyBook $myBook): RedirectResponse
    {
        abort_unless((int) $myBook->student_id === (int) $student->id, 404);
        $myBook->delete();

        return back()->with('success', 'Uploaded personal book removed.');
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
