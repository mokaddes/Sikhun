<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Models\QuestionSubject;
use App\Services\Questions\QuestionJsonImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class QuestionJsonImportController extends Controller
{
    public function __construct(
        protected QuestionJsonImportService $importService
    ) {}

    /**
     * Show JSON bulk import page with stats and available server repository files.
     */
    public function index(): Response
    {
        $stats = [
            'total_questions' => QuestionBank::count(),
            'distinct_exams' => QuestionBank::distinct('exam_name')->count('exam_name'),
            'total_subjects' => QuestionSubject::count(),
            'job_questions' => QuestionBank::where('segment', 'job')->count(),
            'admission_questions' => QuestionBank::where('segment', 'admission')->count(),
        ];

        // Scan qsn-json directory on server
        $serverFiles = [];
        $serverDir = base_path('qsn-json');
        if (is_dir($serverDir)) {
            $files = glob($serverDir . DIRECTORY_SEPARATOR . '*.json');
            foreach ($files as $f) {
                $base = basename($f);
                $sizeMb = round(filesize($f) / 1024 / 1024, 2);
                $examsCount = 0;
                $questionsCount = 0;

                // Quick metadata peek (read first 500KB or parse)
                try {
                    $jsonSample = json_decode(file_get_contents($f), true);
                    if (isset($jsonSample['totalExams'])) {
                        $examsCount = (int)$jsonSample['totalExams'];
                    } elseif (isset($jsonSample['exams'])) {
                        $examsCount = count($jsonSample['exams']);
                    }
                    if (isset($jsonSample['totalQuestions'])) {
                        $questionsCount = (int)$jsonSample['totalQuestions'];
                    }
                } catch (\Throwable $e) {
                    // ignore
                }

                $serverFiles[] = [
                    'name' => $base,
                    'path' => $f,
                    'size_mb' => $sizeMb,
                    'exams_count' => $examsCount,
                    'questions_count' => $questionsCount,
                ];
            }
        }

        // Recent distinct exams
        $recentExams = QuestionBank::select(['exam_name', 'segment', 'origin_tag', 'year', 'created_at'])
            ->whereNotNull('exam_name')
            ->distinct('exam_name')
            ->latest('id')
            ->take(10)
            ->get();

        return Inertia::render('Admin/Questions/Import/Json', [
            'stats' => $stats,
            'serverFiles' => $serverFiles,
            'recentExams' => $recentExams,
        ]);
    }

    /**
     * Handle user uploaded JSON file.
     */
    public function upload(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'json_file' => ['required', 'file', 'mimes:json,txt', 'max:51200'], // 50MB max
            'segment' => ['nullable', 'string', 'in:auto,job,admission,hsc,ssc'],
            'status' => ['nullable', 'string', 'in:active,draft'],
        ]);

        $file = $request->file('json_file');
        $options = [
            'segment' => ($validated['segment'] ?? 'auto') === 'auto' ? null : $validated['segment'],
            'status' => $validated['status'] ?? 'active',
            'admin_id' => $request->user('admin')?->id,
            'source_filename' => $file->getClientOriginalName(),
        ];

        try {
            $realPath = $file->getRealPath();
            $result = $this->importService->importFile($realPath, $options);

            $msg = "সফলভাবে {$result['total_exams']}টি পরীক্ষার মোট {$result['total_inserted']}টি প্রশ্ন ইমপোর্ট করা হয়েছে! " .
                   "({$result['total_skipped']}টি ডুপ্লিকেট প্রশ্ন বাদ দেওয়া হয়েছে)";

            return redirect()->route('question-import.json')->with('success', $msg);
        } catch (\Throwable $e) {
            return redirect()->route('question-import.json')->with('error', 'ইমপোর্ট ব্যর্থ হয়েছে: ' . $e->getMessage());
        }
    }

    /**
     * 1-Click Import of a server-side JSON file from qsn-json directory.
     */
    public function importServerFile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'filename' => ['required', 'string'],
            'segment' => ['nullable', 'string', 'in:auto,job,admission,hsc,ssc'],
            'status' => ['nullable', 'string', 'in:active,draft'],
        ]);

        $filePath = base_path('qsn-json' . DIRECTORY_SEPARATOR . basename($validated['filename']));
        if (!file_exists($filePath)) {
            return redirect()->route('question-import.json')->with('error', 'সার্ভারে ফাইলটি পাওয়া যায়নি: ' . $validated['filename']);
        }

        $options = [
            'segment' => ($validated['segment'] ?? 'auto') === 'auto' ? null : $validated['segment'],
            'status' => $validated['status'] ?? 'active',
            'admin_id' => $request->user('admin')?->id,
            'source_filename' => basename($filePath),
        ];

        try {
            $result = $this->importService->importFile($filePath, $options);

            $msg = "সার্ভার ফাইল '{$validated['filename']}' থেকে {$result['total_exams']}টি পরীক্ষার {$result['total_inserted']}টি নতুন প্রশ্ন ইমপোর্ট হয়েছে! ({$result['total_skipped']}টি ডুপ্লিকেট প্রশ্ন বাদ পড়েছে)";

            return redirect()->route('question-import.json')->with('success', $msg);
        } catch (\Throwable $e) {
            return redirect()->route('question-import.json')->with('error', 'ইমপোর্ট ত্রুটি: ' . $e->getMessage());
        }
    }
}
