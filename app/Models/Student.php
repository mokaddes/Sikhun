<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use App\Services\AccessGrantService;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\HasApiTokens;

class Student extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, MustVerifyEmailTrait, Notifiable;

    protected $guard = 'web';

    protected $fillable = [
        'name', 'email', 'phone_hash', 'password', 'type', 'status', 'trial_ends_at',
        'wallet_balance', 'ai_trial_minutes_used', 'ai_trial_vision_used',
        'trial_reuploads_count', 'trial_reuploads_month', 'referral_code',
        'referred_by_student_id', 'avatar', 'theme_mode', 'leaderboard_opt_out',
        'onboarding_progress', 'onboarding_completed_at', 'first_ai_chat_at', 'activated_at',
    ];

    protected $hidden = ['password', 'remember_token', 'phone_hash'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'password' => 'hashed',
            'wallet_balance' => 'decimal:2',
            'ai_trial_minutes_used' => 'integer',
            'ai_trial_vision_used' => 'integer',
            'trial_reuploads_count' => 'integer',
            'referred_by_student_id' => 'integer',
            'leaderboard_opt_out' => 'boolean',
            'onboarding_progress' => 'array',
            'onboarding_completed_at' => 'datetime',
            'first_ai_chat_at' => 'datetime',
            'activated_at' => 'datetime',
        ];
    }

    // Relationships
    public function referredBy() { return $this->belongsTo(Student::class, 'referred_by_student_id'); }
    public function referredStudents() { return $this->hasMany(Student::class, 'referred_by_student_id'); }
    public function bookShelf() { return $this->hasMany(BookShelf::class); }
    public function books() { return $this->belongsToMany(Book::class, 'book_shelves')->withPivot('source', 'added_at'); }
    public function ownedChapters() { return $this->hasMany(StudentBookChapter::class); }
    public function subscriptions() { return $this->hasMany(StudentSubscription::class); }
    public function activeSubscription() { return $this->hasOne(StudentSubscription::class)->where('status', 'active')->latest(); }
    public function walletTransactions() { return $this->hasMany(WalletTransaction::class); }
    public function orders() { return $this->hasMany(Order::class); }
    public function aiSessions() { return $this->hasMany(AiSession::class); }
    public function myBooks() { return $this->hasMany(MyBook::class); }
    public function examSessions() { return $this->hasMany(ExamSession::class); }
    public function flashcardSets() { return $this->hasMany(FlashcardSet::class); }
    public function essaySubmissions() { return $this->hasMany(EssaySubmission::class); }
    public function studySchedules() { return $this->hasMany(StudySchedule::class); }
    public function leaderboardEntries() { return $this->hasMany(LeaderboardEntry::class); }
    public function courseEnrollments() { return $this->hasMany(CourseEnrollment::class); }
    public function notifications() { return $this->hasMany(Notification::class); }
    public function notificationPreferences() { return $this->hasMany(StudentNotificationPreference::class); }
    public function referralsMade() { return $this->hasMany(Referral::class, 'referrer_student_id'); }
    public function referralsReceived() { return $this->hasMany(Referral::class, 'referee_student_id'); }

    public function hasActiveAiAccess(): bool
    {
        if (app(AccessGrantService::class)->hasActiveAccess($this)) {
            return true;
        }

        $sub = $this->activeSubscription;
        if ($sub && $sub->expires_at->isFuture()) {
            return $sub->plan->is_free
                ? $sub->ai_chat_minutes_remaining > 0 || $sub->ai_exam_count_remaining > 0
                : true;
        }

        if ($this->isInTrial()) {
            return true;
        }

        if (Plan::where('is_free', true)->exists()) {
            return false;
        }

        $trialLimit = Plan::where('is_active', true)->max('trial_ai_minutes') ?: 10;

        return $this->ai_trial_minutes_used < $trialLimit;
    }

    public function hasActiveAiChatAccess(): bool
    {
        if (app(AccessGrantService::class)->hasActiveAccess($this)) {
            return true;
        }

        $sub = $this->activeSubscription;
        if ($sub && $sub->expires_at->isFuture()) {
            return $sub->plan->is_free
                ? $sub->ai_chat_minutes_remaining > 0
                : true;
        }

        return $this->isInTrial() || ! Plan::where('is_free', true)->exists()
            && $this->hasActiveAiAccess();
    }

    public function hasActiveAiExamAccess(): bool
    {
        if (app(AccessGrantService::class)->hasActiveAccess($this)) {
            return true;
        }

        $sub = $this->activeSubscription;
        if ($sub && $sub->expires_at->isFuture()) {
            return $sub->plan->is_free
                ? $sub->ai_exam_count_remaining > 0
                : true;
        }

        return $this->isInTrial() || ! Plan::where('is_free', true)->exists()
            && $this->hasActiveAiAccess();
    }

    public function canUseAiExplain(): bool
    {
        $subscription = $this->activeSubscription;

        return $subscription !== null
            && $subscription->expires_at?->isFuture()
            && $subscription->plan !== null
            && ! $subscription->plan->is_free;
    }

    public function recordSuccessfulAiChat(): void
    {
        $progress = $this->onboarding_progress ?? [];
        $completedSteps = $progress['completed_steps'] ?? [];
        $completedSteps[] = 4;
        $progress['completed_steps'] = array_values(array_unique(array_map('intval', $completedSteps)));
        $progress['first_question_completed'] = true;
        $this->onboarding_progress = $progress;
        $this->first_ai_chat_at ??= now();

        if ($this->created_at && $this->first_ai_chat_at->lessThanOrEqualTo($this->created_at->copy()->addHours(24))) {
            $this->activated_at ??= $this->first_ai_chat_at;
        }

        if (count(array_intersect([1, 2, 3, 4], $progress['completed_steps'])) === 4) {
            $this->onboarding_completed_at ??= now();
        }

        $this->save();
    }

    public function isInTrial(): bool
    {
        if ($this->trial_ends_at) {
            return now()->lt($this->trial_ends_at);
        }

        return $this->created_at ? now()->lt($this->created_at->copy()->addDays(7)) : false;
    }

    public function trialDaysRemaining(): int
    {
        $endsAt = $this->trial_ends_at ?? $this->created_at?->copy()->addDays(7);
        if (! $endsAt || now()->gte($endsAt)) {
            return 0;
        }

        return (int) max(1, ceil(now()->floatDiffInDays($endsAt)));
    }

    public function maxUploadBookSlots(): int
    {
        if (app(AccessGrantService::class)->hasActiveAccess($this)) {
            return 50;
        }

        $sub = $this->activeSubscription;
        if ($sub && $sub->expires_at->isFuture()) {
            return (int) ($sub->plan->upload_book_limit ?? 1);
        }

        return 1; // Default for free/trial students: 1 concurrent book
    }

    public function getMonthlyReuploadsRemaining(): int
    {
        if (app(AccessGrantService::class)->hasActiveAccess($this)) {
            return 999;
        }

        $sub = $this->activeSubscription;
        if ($sub && $sub->expires_at->isFuture()) {
            return (int) ($sub->monthly_reuploads_remaining ?? 0);
        }

        // Free/trial students: default 2 uploads per calendar month
        $currentMonth = now()->format('Y-m');
        if ($this->trial_reuploads_month !== $currentMonth) {
            return 2;
        }

        return max(0, 2 - (int) $this->trial_reuploads_count);
    }

    public function recordBookUpload(): void
    {
        if (app(AccessGrantService::class)->hasActiveAccess($this)) {
            return;
        }

        $sub = $this->activeSubscription;
        if ($sub && $sub->expires_at->isFuture()) {
            if ($sub->monthly_reuploads_remaining > 0) {
                $sub->decrement('monthly_reuploads_remaining');
            }
            return;
        }

        $currentMonth = now()->format('Y-m');
        if ($this->trial_reuploads_month !== $currentMonth) {
            $this->update([
                'trial_reuploads_month' => $currentMonth,
                'trial_reuploads_count' => 1,
            ]);
        } else {
            $this->increment('trial_reuploads_count');
        }
    }

    public function getVisionRemaining(): int
    {
        if (app(AccessGrantService::class)->hasActiveAccess($this)) {
            return 999;
        }

        $sub = $this->activeSubscription;
        if ($sub && $sub->expires_at->isFuture()) {
            return (int) ($sub->ai_vision_remaining ?? 0);
        }

        // Free trial vision limit: 10 questions with image during trial, otherwise 3
        $freeLimit = $this->isInTrial() ? 10 : 3;
        return max(0, $freeLimit - (int) $this->ai_trial_vision_used);
    }

    public function canUseVision(): bool
    {
        return $this->getVisionRemaining() > 0;
    }

    public function recordVisionUsage(): void
    {
        if (app(AccessGrantService::class)->hasActiveAccess($this)) {
            return;
        }

        $sub = $this->activeSubscription;
        if ($sub && $sub->expires_at->isFuture()) {
            if ($sub->ai_vision_remaining > 0) {
                $sub->decrement('ai_vision_remaining');
            }
            return;
        }

        $this->increment('ai_trial_vision_used');
    }

    /**
     * Overrides the default Laravel mail so verification copy follows the
     * student's locale and the link points at Sikhun's own verify route.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification(
            URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
                'id' => $this->getKey(),
                'hash' => sha1($this->getEmailForVerification()),
            ])
        ));
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token, $this->getEmailForVerification()));
    }
}
