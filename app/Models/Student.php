<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Student extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $guard = 'web';

    protected $fillable = [
        'name', 'email', 'password', 'type', 'status',
        'wallet_balance', 'ai_trial_minutes_used', 'referral_code',
        'referred_by_student_id', 'avatar', 'theme_mode', 'leaderboard_opt_out',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'wallet_balance' => 'decimal:2',
            'leaderboard_opt_out' => 'boolean',
        ];
    }

    // Relationships
    public function referredBy() { return $this->belongsTo(Student::class, 'referred_by_student_id'); }
    public function referredStudents() { return $this->hasMany(Student::class, 'referred_by_student_id'); }
    public function bookShelf() { return $this->hasMany(BookShelf::class); }
    public function books() { return $this->belongsToMany(Book::class, 'book_shelves')->withPivot('source', 'added_at'); }
    public function subscriptions() { return $this->hasMany(StudentSubscription::class); }
    public function activeSubscription() { return $this->hasOne(StudentSubscription::class)->where('status', 'active')->latest(); }
    public function walletTransactions() { return $this->hasMany(WalletTransaction::class); }
    public function orders() { return $this->hasMany(Order::class); }
    public function aiSessions() { return $this->hasMany(AiSession::class); }
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
        $sub = $this->activeSubscription;
        if ($sub && $sub->expires_at->isFuture()) {
            return true;
        }

        $trialLimit = Plan::where('is_active', true)->max('trial_ai_minutes') ?: 10;

        return $this->ai_trial_minutes_used < $trialLimit;
    }
}
