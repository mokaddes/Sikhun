<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'referrer_student_id', 'referee_student_id', 'status', 'fraud_status',
        'risk_signals', 'registered_at', 'paid_at', 'reviewed_at', 'reviewed_by_admin_id',
        'referrer_reward', 'referee_reward', 'rewarded_at',
    ];

    protected function casts(): array
    {
        return [
            'referrer_student_id' => 'integer',
            'referee_student_id' => 'integer',
            'referrer_reward' => 'decimal:2',
            'referee_reward' => 'decimal:2',
            'risk_signals' => 'array',
            'registered_at' => 'datetime',
            'paid_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'reviewed_by_admin_id' => 'integer',
            'rewarded_at' => 'datetime',
        ];
    }

    public function referrer() { return $this->belongsTo(Student::class, 'referrer_student_id'); }
    public function referee() { return $this->belongsTo(Student::class, 'referee_student_id'); }
    public function events() { return $this->hasMany(ReferralEvent::class); }
}
