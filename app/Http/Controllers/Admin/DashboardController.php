<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Book;
use App\Models\Order;
use App\Models\Student;
use App\Models\StudentSubscription;
use App\Models\SupportConversation;
use App\Models\SupportTicket;
use App\Models\Feedback;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $days = collect(range(13, 0))->map(fn ($i) => Carbon::today()->subDays($i));

        $revenueByDay = Order::where('status', 'completed')
            ->where('created_at', '>=', Carbon::today()->subDays(13))
            ->selectRaw('DATE(created_at) as day, SUM(amount) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $registrationsByDay = Student::where('created_at', '>=', Carbon::today()->subDays(13))
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        return Inertia::render('Admin/Dashboard/Index', [
            'stats' => [
                'total_students' => Student::count(),
                'total_books' => Book::count(),
                'active_subscriptions' => StudentSubscription::where('status', 'active')->count(),
                'revenue_total' => (float) Order::where('status', 'completed')->sum('amount'),
                'revenue_today' => (float) Order::where('status', 'completed')->whereDate('created_at', today())->sum('amount'),
                'draft_blogs' => BlogPost::where('status', 'draft')->count(),
                'open_tickets' => SupportTicket::whereIn('status', ['open', 'pending'])->count(),
                'overdue_tickets' => SupportTicket::whereIn('status', ['open', 'pending'])
                    ->whereNotNull('sla_due_at')->where('sla_due_at', '<', now())->count(),
                'new_feedback' => Feedback::where('status', 'new')->count(),
                'bot_handoffs' => SupportConversation::where('bot_enabled', false)->count(),
            ],
            'charts' => [
                'labels' => $days->map(fn ($d) => $d->format('M j'))->values(),
                'revenue' => $days->map(fn ($d) => (float) ($revenueByDay[$d->toDateString()] ?? 0))->values(),
                'registrations' => $days->map(fn ($d) => (int) ($registrationsByDay[$d->toDateString()] ?? 0))->values(),
            ],
            'recent_orders' => Order::with('student:id,name,email')
                ->latest()
                ->take(5)
                ->get(['id', 'order_number', 'student_id', 'amount', 'payment_method', 'status', 'created_at']),
            'recent_students' => Student::latest()
                ->take(5)
                ->get(['id', 'name', 'email', 'type', 'status', 'created_at']),
        ]);
    }
}
