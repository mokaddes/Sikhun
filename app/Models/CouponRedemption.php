<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponRedemption extends Model
{
    protected $fillable = [
        'coupon_id', 'student_id', 'order_id', 'invoice_id',
        'target_type', 'target_id', 'discount_amount',
        'granted_until', 'redeemed_at',
    ];

    protected function casts(): array
    {
        return [
            'student_id' => 'integer',
            'order_id' => 'integer',
            'invoice_id' => 'integer',
            'target_id' => 'integer',
            'discount_amount' => 'float',
            'granted_until' => 'datetime',
            'redeemed_at' => 'datetime',
        ];
    }

    public function coupon(): BelongsTo { return $this->belongsTo(Coupon::class); }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
}
