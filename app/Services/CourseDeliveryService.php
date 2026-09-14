<?php

namespace App\Services;

use App\Mail\CourseLinkMail;
use App\Models\Course;
use App\Models\Order;
use App\Models\Student;
use Illuminate\Support\Facades\Mail;

/**
 * Handles what a student receives once they own a course.
 *
 * Video courses deliver their content through the lesson pages, so there is
 * nothing to push. Link-based courses (enrollment invite / file download) sell
 * the link itself, so the student gets it by email — the mail is also the only
 * durable record if they lose the page.
 */
class CourseDeliveryService
{
    public function deliver(Student $student, Course $course, ?Order $order = null): void
    {
        if (! $course->hasDeliveryLink()) {
            return;
        }

        Mail::to($student->email)->send(new CourseLinkMail($course, $student, $order));
    }
}
