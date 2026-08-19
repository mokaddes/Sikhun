<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponRedemption extends Model
{
    protected $fillable = [
        'coupon_id', 'student_id', 'granted_until', 'redeemed_at',
    ];

    protected function casts(): array
    {
        return [
            'student_id' => 'integer',
            'granted_until' => 'datetime',
            'redeemed_at' => 'datetime',
        ];
    }

    public function coupon(): BelongsTo { return $this->belongsTo(Coupon::class); }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
}
