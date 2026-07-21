<?php

namespace App\Services;

use App\Models\CourseEnrollment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificateService
{
    /**
     * Generates the completion certificate PDF and stores it on the
     * public disk (safe to be public — a certificate is meant to be shared).
     */
    public function generate(CourseEnrollment $enrollment): string
    {
        $pdf = Pdf::loadView('pdf.certificate', [
            'student' => $enrollment->student,
            'course' => $enrollment->course,
            'completedAt' => $enrollment->completed_at,
        ])->setPaper('a4', 'landscape');

        $path = "certificates/{$enrollment->id}.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }
}
