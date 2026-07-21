<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'referrer_student_id', 'referee_student_id', 'status',
        'referrer_reward', 'referee_reward', 'rewarded_at',
    ];

    protected function casts(): array
    {
        return [
            'referrer_reward' => 'decimal:2',
            'referee_reward' => 'decimal:2',
            'rewarded_at' => 'datetime',
        ];
    }

    public function referrer() { return $this->belongsTo(Student::class, 'referrer_student_id'); }
    public function referee() { return $this->belongsTo(Student::class, 'referee_student_id'); }
}
