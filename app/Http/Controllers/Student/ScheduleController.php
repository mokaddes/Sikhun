<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\GenerateScheduleRequest;
use App\Models\StudySchedule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ScheduleController extends Controller
{
    public function index(): Response
    {
        $student = auth('web')->user();

        return Inertia::render('Student/Schedules/Index', [
            'schedules' => $student->studySchedules()->latest()->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Student/Schedules/Create');
    }

    public function store(GenerateScheduleRequest $request): RedirectResponse
    {
        $student = auth('web')->user();

        $schedule = $student->studySchedules()->create([
            'exam_date' => $request->exam_date,
            'config' => [
                'subjects' => $request->subjects,
                'weak_subjects' => $request->weak_subjects ?? [],
                'hours_per_day' => $request->hours_per_day,
                'style' => $request->style,
                'include_weekends' => $request->boolean('include_weekends'),
            ],
            'status' => 'generating',
        ]);

        \App\Jobs\GenerateStudySchedule::dispatch($schedule->id);
        $student->increment('ai_trial_minutes_used');

        return redirect()->route('schedules.show', $schedule);
    }

    public function show(StudySchedule $schedule): Response
    {
        $this->authorizeSchedule($schedule);

        return Inertia::render('Student/Schedules/Show', ['schedule' => $schedule]);
    }

    public function status(StudySchedule $schedule): JsonResponse
    {
        $this->authorizeSchedule($schedule);

        return response()->json(['status' => $schedule->status, 'schedule_data' => $schedule->schedule_data]);
    }

    public function markProgress(Request $request, StudySchedule $schedule): RedirectResponse
    {
        $this->authorizeSchedule($schedule);
        $request->validate(['date' => 'required|date']);

        $days = collect($schedule->schedule_data)->map(function ($day) use ($request) {
            if ($day['date'] === $request->date) {
                $day['completed'] = ! ($day['completed'] ?? false);
            }

            return $day;
        })->all();

        $schedule->update(['schedule_data' => $days]);

        return back();
    }

    public function pdf(StudySchedule $schedule)
    {
        $this->authorizeSchedule($schedule);
        $pdf = Pdf::loadView('pdf.study-schedule', ['schedule' => $schedule]);

        return $pdf->download("study-schedule-{$schedule->id}.pdf");
    }

    private function authorizeSchedule(StudySchedule $schedule): void
    {
        abort_unless($schedule->student_id === Auth::guard('web')->id(), 403);
    }
}
