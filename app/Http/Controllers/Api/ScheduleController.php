<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Student\GenerateScheduleRequest;
use App\Jobs\GenerateStudySchedule;
use App\Models\StudySchedule;
use App\Services\AccessGrantService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;

class ScheduleController extends BaseApiController
{
    public function index(): JsonResponse
    {
        return $this->success(auth('sanctum')->user()->studySchedules()->latest()->get());
    }

    public function store(GenerateScheduleRequest $request): JsonResponse
    {
        $student = auth('sanctum')->user();
        abort_unless($student->hasActiveAiAccess(), 403, 'AI trial exhausted.');

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

        GenerateStudySchedule::dispatch($schedule->id);

        if (! app(AccessGrantService::class)->hasActiveAccess($student)) {
            $student->increment('ai_trial_minutes_used');
        }

        return $this->success($schedule, 'Schedule generation started', 201);
    }

    public function show(StudySchedule $schedule): JsonResponse
    {
        abort_unless($schedule->student_id === auth('sanctum')->id(), 403);

        return $this->success($schedule);
    }

    public function status(StudySchedule $schedule): JsonResponse
    {
        abort_unless($schedule->student_id === auth('sanctum')->id(), 403);

        return $this->success(['id' => $schedule->id, 'status' => $schedule->status]);
    }

    public function retry(StudySchedule $schedule): JsonResponse
    {
        abort_unless($schedule->student_id === auth('sanctum')->id(), 403);
        abort_unless($schedule->status === 'failed', 409, 'Only failed schedules can be retried.');

        $schedule->update(['status' => 'generating']);
        GenerateStudySchedule::dispatch($schedule->id);

        return $this->success($schedule, 'Schedule generation retried');
    }

    public function pdf(StudySchedule $schedule): HttpResponse
    {
        abort_unless($schedule->student_id === auth('sanctum')->id(), 403);
        $pdf = Pdf::loadView('pdf.schedule', ['schedule' => $schedule, 'student' => auth('sanctum')->user()]);

        return $pdf->download("study-schedule-{$schedule->id}.pdf");
    }

    public function progress(Request $request, StudySchedule $schedule): JsonResponse
    {
        abort_unless($schedule->student_id === auth('sanctum')->id(), 403);
        $request->validate(['date' => 'required|date']);

        $days = collect($schedule->schedule_data)->map(function ($day) use ($request) {
            if ($day['date'] === $request->date) {
                $day['completed'] = ! ($day['completed'] ?? false);
            }

            return $day;
        })->all();

        $schedule->update(['schedule_data' => $days]);

        return $this->success($schedule);
    }
}
