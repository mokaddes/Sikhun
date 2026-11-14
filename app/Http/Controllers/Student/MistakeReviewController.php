<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\ExamMistakeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MistakeReviewController extends Controller
{
    /**
     * Display student's mistake review (Revision Book).
     */
    public function index(Request $request, ExamMistakeService $mistakeService): Response
    {
        $student = auth('web')->user();

        $filters = $request->only(['status', 'subject_id', 'institution', 'search']);
        $data = $mistakeService->getMistakesData($student, $filters);

        return Inertia::render('Student/Mistakes/Index', [
            'mistakes' => $data['mistakes'],
            'subjects' => $data['subjects'],
            'institutions' => $data['institutions'],
            'stats' => $data['stats'],
            'filters' => [
                'status' => $filters['status'] ?? 'unresolved',
                'subject_id' => $filters['subject_id'] ?? '',
                'institution' => $filters['institution'] ?? '',
                'search' => $filters['search'] ?? '',
            ],
        ]);
    }

    /**
     * Create a revision exam session with mistakes to re-test until mastered.
     */
    public function practice(Request $request, ExamMistakeService $mistakeService): JsonResponse|RedirectResponse
    {
        $student = auth('web')->user();

        $validated = $request->validate([
            'subject_id' => ['nullable', 'integer'],
            'subject_name' => ['nullable', 'string'],
            'institution' => ['nullable', 'string'],
            'limit' => ['nullable', 'integer', 'min:5', 'max:50'],
        ]);

        $limit = (int) ($validated['limit'] ?? 20);
        $session = $mistakeService->createMistakePracticeExam($student, $validated, $limit);

        if (! $session) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'অনুশীলন করার মতো কোনো ভুল প্রশ্ন পাওয়া যায়নি। দারুণ অগ্রগতি!',
                ], 422);
            }

            return back()->with('error', 'অনুশীলন করার মতো কোনো ভুল প্রশ্ন পাওয়া যায়নি। দারুণ অগ্রগতি!');
        }

        $redirectUrl = route('exams.show', $session->id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'exam_id' => $session->id,
                'redirect_url' => $redirectUrl,
            ]);
        }

        return redirect()->to($redirectUrl);
    }

    /**
     * Reset student's mistake questions (delete or resolve mistake entries).
     */
    public function reset(Request $request): RedirectResponse|JsonResponse
    {
        $student = auth('web')->user();
        $query = \App\Models\ExamAnswer::where('student_id', $student->id)->where('is_correct', false);

        if ($request->filled('subject_id')) {
            $query->where('subject_id', (int) $request->input('subject_id'));
        }

        if ($request->filled('institution')) {
            $query->where('institution', $request->input('institution'));
        }

        $count = $query->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "ভুল প্রশ্নের তালিকা থেকে {$count}টি প্রশ্ন সফলভাবে রিসেট করা হয়েছে।",
            ]);
        }

        return back()->with('success', "ভুল প্রশ্নের তালিকা থেকে {$count}টি প্রশ্ন সফলভাবে রিসেট করা হয়েছে।");
    }
}
