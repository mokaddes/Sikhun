<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralEvent extends Model
{
    protected $fillable = [
        'referrer_student_id', 'referral_id', 'event_type', 'phone_hash',
        'device_hash', 'ip_hash', 'session_hash',
    ];

    protected function casts(): array
    {
        return [
            'referrer_student_id' => 'integer',
            'referral_id' => 'integer',
        ];
    }

    public function referrer()
    {
        return $this->belongsTo(Student::class, 'referrer_student_id');
    }

    public function referral()
    {
        return $this->belongsTo(Referral::class);
    }
}
