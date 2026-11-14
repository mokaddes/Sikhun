<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiLog;
use App\Models\AiProvider;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AiLogController extends Controller
{
    public function index(Request $request): Response
    {
        $query = AiLog::with(['student:id,name,email', 'admin:id,name,email', 'provider:id,name,type']);

        // Filter: according use type
        if ($request->filled('use_case')) {
            $query->where('use_case', $request->input('use_case'));
        }

        // Filter: according user type
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->input('user_type'));
        }

        // Filter: according specific student
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }

        // Filter: according provider
        if ($request->filled('provider_id')) {
            $query->where('provider_id', $request->input('provider_id'));
        }

        // Filter: status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter: Search across user, model, prompt, response
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                    ->orWhere('user_email', 'like', "%{$search}%")
                    ->orWhere('model_name', 'like', "%{$search}%")
                    ->orWhere('use_case', 'like', "%{$search}%")
                    ->orWhere('prompt', 'like', "%{$search}%")
                    ->orWhere('response', 'like', "%{$search}%");
            });
        }

        // Date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $logs = $query->latest('id')->paginate(20)->withQueryString();

        // Calculate overview stats for admin dashboard
        $totalCost = (float) AiLog::sum('cost');
        $stats = [
            'total_calls' => AiLog::count(),
            'successful_calls' => AiLog::where('status', 'success')->count(),
            'failed_calls' => AiLog::where('status', 'failed')->count(),
            'total_tokens' => (int) AiLog::sum('total_tokens'),
            'avg_latency_ms' => (int) round((float) AiLog::where('status', 'success')->avg('latency_ms')),
            'total_cost' => round($totalCost, 4),
            'total_cost_formatted' => '$' . number_format($totalCost, 4),
            'total_cost_bdt' => round($totalCost * 122, 2),
        ];

        // Available use cases for dropdown filter
        $availableUseCases = [
            'book_chat' => 'AI Chat with Books',
            'exam_gen' => 'Exam Generation',
            'question_analysis' => 'Question Analysis & Explanation',
            'question_structure' => 'Question PDF/Text Structuring',
            'question_gen' => 'Question Bank Batch Generation',
            'flashcard_gen' => 'Flashcard Generation',
            'essay_grade' => 'Essay Grading',
            'schedule_gen' => 'Study Schedule Generation',
            'notification_gen' => 'Daily AI Notifications',
            'support_bot' => 'Support Chat Bot',
            'book_ocr' => 'Book OCR / Document Processing',
            'book_retrieval' => 'Book RAG / Retrieval',
            'book_structure' => 'Book Chapter / Structure Detection',
            'blog_translate' => 'Blog Translation',
            'blog_rewrite' => 'Blog AI Rewriting',
        ];

        return Inertia::render('Admin/AiLogs/Index', [
            'logs' => $logs,
            'filters' => $request->only(['use_case', 'user_type', 'student_id', 'provider_id', 'status', 'search', 'date_from', 'date_to']),
            'stats' => $stats,
            'useCases' => $availableUseCases,
            'providers' => AiProvider::orderBy('name')->get(['id', 'name', 'type']),
        ]);
    }

    public function show(AiLog $aiLog): \Illuminate\Http\JsonResponse
    {
        $aiLog->load(['student', 'admin', 'provider']);
        return response()->json($aiLog);
    }

    public function clear(Request $request): RedirectResponse
    {
        if ($request->boolean('all') || $request->input('days') === 'all') {
            AiLog::truncate();
            return back()->with('success', 'সকল এআই লগ সফলভাবে মুছে ফেলা হয়েছে।');
        }

        if ($request->filled('days')) {
            $days = (int) $request->input('days');
            if ($days > 0) {
                $deleted = AiLog::where('created_at', '<', now()->subDays($days))->delete();
                return back()->with('success', "বিগত {$days} দিনের পূর্বের {$deleted}টি এআই লগ মুছে ফেলা হয়েছে।");
            }
        }

        return back()->with('error', 'সঠিক ক্লিয়ার অপশন নির্বাচন করুন।');
    }
}
