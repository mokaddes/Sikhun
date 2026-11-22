<?php

namespace App\Services;

use App\Events\NewNotificationBroadcast;
use App\Jobs\SendPushNotificationJob;
use App\Mail\AdminStudentActionMail;
use App\Models\AdmissionNotice;
use App\Models\BlogPost;
use App\Models\Book;
use App\Models\JobCircular;
use App\Models\Student;
use App\Models\StudentNotificationPreference;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * The fixed set of notification types the platform sends. Used to
     * drive both the student preferences page and the admin broadcast
     * form — one source of truth so the two never drift apart.
     */
    public const TYPES = [
        'general_knowledge' => 'Daily educational fact',
        'exam_reminder' => 'Upcoming exam reminders',
        'subscription_expiry' => 'Subscription expiry & renewal invoices',
        'wallet_low_balance' => 'Low wallet balance alerts',
        'wallet_transaction' => 'Wallet credits, deposits & payments',
        'plan_update' => 'Plan purchases & admin assignments',
        'referral_reward' => 'Referral reward credited',
        'book_recommendation' => 'New book releases & recommendations',
        'admission_notice' => 'Admission circulars (mandatory for Admission students)',
        'job_news' => 'Job circulars (mandatory for Job students)',
        'blog_post' => 'New blog articles & guides',
        'leaderboard_rank_change' => 'Leaderboard rank changes',
        'course_new_lesson' => 'New course lessons available',
        'support_reply' => 'Support conversation replies',
        'special_offer' => 'Special offers & promotions',
        'admin_broadcast' => 'Announcements from Sikhun.com',
    ];

    /**
     * Mandatory types bypass the daily cap and opt-out preferences so critical
     * transactional, billing, admission, and job alerts always reach students.
     */
    private const MANDATORY_TYPES = [
        'admission_notice',
        'job_news',
        'wallet_transaction',
        'plan_update',
        'subscription_expiry',
        'special_offer',
        'support_reply',
    ];

    private const MAX_PER_DAY = 5;

    /**
     * Creates the notification row + broadcasts it live over Reverb + dispatches
     * Firebase Push Notification (with optional clickable link in $data['url']).
     */
    public function send(
        Student $student,
        string $type,
        string $title,
        string $body,
        array $data = [],
        bool $mandatory = false
    ): bool {
        $isMandatory = $mandatory || in_array($type, self::MANDATORY_TYPES, true);

        if (! $isMandatory) {
            if (! $this->isEnabled($student, $type)) {
                return false;
            }

            if ($student->notifications()->whereDate('created_at', today())->count() >= self::MAX_PER_DAY) {
                return false;
            }
        }

        // Normalize optional clickable link
        $actionUrl = $data['url'] ?? $data['action_url'] ?? null;
        if (is_string($actionUrl) && trim($actionUrl) !== '') {
            $actionUrl = trim($actionUrl);
            $data['url'] = $actionUrl;
            $data['action_url'] = $actionUrl;
        }

        $notification = $student->notifications()->create([
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ]);

        try {
            broadcast(new NewNotificationBroadcast($notification))->toOthers();
        } catch (\Throwable $e) {
            Log::debug('Reverb broadcast skipped: '.$e->getMessage());
        }

        if (app(PushNotificationService::class)->isEnabled()) {
            SendPushNotificationJob::dispatch(
                $student->id,
                $title,
                $body,
                array_merge($data, [
                    'type' => $type,
                    'notification_id' => $notification->id,
                ])
            );
        }

        return true;
    }

    /**
     * Sends a notification (and optionally an email) to an audience segment:
     * 'all', 'admission', 'job', 'hsc', 'ssc', 'university'.
     */
    public function sendToAudience(
        string $audience,
        string $type,
        string $title,
        string $body,
        array $data = [],
        bool $mandatory = false,
        bool $sendEmail = false
    ): int {
        $query = Student::query()->where('status', 'active');

        if ($audience !== 'all') {
            $query->where('type', $audience);
        }

        $actionUrl = $data['url'] ?? $data['action_url'] ?? null;
        $sent = 0;

        $query->chunkById(100, function ($students) use ($type, $title, $body, $data, $mandatory, $sendEmail, $actionUrl, &$sent) {
            foreach ($students as $student) {
                if ($this->send($student, $type, $title, $body, $data, $mandatory)) {
                    $sent++;
                }

                if ($sendEmail && ! empty($student->email)) {
                    try {
                        Mail::to($student->email)->queue(new AdminStudentActionMail(
                            student: $student,
                            subjectLine: $title,
                            heading: $title,
                            intro: $body,
                            details: [],
                            actionUrl: $actionUrl ? (str_starts_with($actionUrl, 'http') ? $actionUrl : url($actionUrl)) : url('/dashboard'),
                            actionText: 'View Details'
                        ));
                    } catch (\Throwable $e) {
                        Log::warning('Audience offer email failed', ['student_id' => $student->id, 'error' => $e->getMessage()]);
                    }
                }
            }
        });

        return $sent;
    }

    /**
     * Mandatory alert to all Admission-level students when an admission circular is published.
     */
    public function notifyAdmissionPublished(AdmissionNotice $notice): int
    {
        $url = '/admission-notice/'.$notice->slug;
        $uni = $notice->institution_name ? " ({$notice->institution_name})" : '';

        return $this->sendToAudience(
            audience: 'admission',
            type: 'admission_notice',
            title: "নতুন ভর্তি বিজ্ঞপ্তি: {$notice->title}",
            body: "নতুন ভর্তি বিজ্ঞপ্তি প্রকাশিত হয়েছে{$uni}। বিস্তারিত ও আবেদনের সময়সীমা দেখতে ক্লিক করুন।",
            data: ['url' => $url, 'admission_notice_id' => $notice->id],
            mandatory: true
        );
    }

    /**
     * Mandatory alert to all Job-level students when a job circular is published.
     */
    public function notifyJobPublished(JobCircular $job): int
    {
        $url = '/job-news/'.$job->slug;
        $org = $job->organization ? " — {$job->organization}" : '';

        return $this->sendToAudience(
            audience: 'job',
            type: 'job_news',
            title: "নতুন চাকরির বিজ্ঞপ্তি: {$job->title}",
            body: "নতুন নিয়োগ বিজ্ঞপ্তি প্রকাশিত হয়েছে{$org}। বিস্তারিত ও আবেদনের শেষ তারিখ দেখতে ক্লিক করুন।",
            data: ['url' => $url, 'job_circular_id' => $job->id],
            mandatory: true
        );
    }

    /**
     * Notify all students when a blog post is published.
     */
    public function notifyBlogPublished(BlogPost $post): int
    {
        $url = '/blog/'.$post->slug;
        $excerpt = $post->excerpt ? mb_substr(strip_tags($post->excerpt), 0, 120) : 'নতুন আর্টিকেল ও স্টাডি গাইড প্রকাশিত হয়েছে। পড়তে ক্লিক করুন।';

        return $this->sendToAudience(
            audience: 'all',
            type: 'blog_post',
            title: "নতুন ব্লগ: {$post->title}",
            body: $excerpt,
            data: ['url' => $url, 'blog_post_id' => $post->id],
            mandatory: false
        );
    }

    /**
     * Notify matching students (or all students) when a new book is published.
     */
    public function notifyBookPublished(Book $book): int
    {
        $url = '/library/'.$book->slug;
        $audience = in_array($book->education_level, ['ssc', 'hsc', 'admission', 'university', 'job'], true)
            ? $book->education_level
            : 'all';

        return $this->sendToAudience(
            audience: $audience,
            type: 'book_recommendation',
            title: "নতুন বই যুক্ত হয়েছে: {$book->title}",
            body: "শিখুন লাইব্রেরিতে নতুন বই প্রকাশিত হয়েছে। এখনই পড়ুন ও AI টিউটরের সাথে অনুশীলন করুন!",
            data: ['url' => $url, 'book_id' => $book->id],
            mandatory: false
        );
    }

    private function isEnabled(Student $student, string $type): bool
    {
        $pref = StudentNotificationPreference::where('student_id', $student->id)->where('type', $type)->first();

        // No row yet = default enabled (matches the DB column default).
        return $pref?->is_enabled ?? true;
    }
}
