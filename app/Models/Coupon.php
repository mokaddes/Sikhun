<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'name', 'description', 'student_id', 'duration_days',
        'starts_at', 'ends_at', 'max_uses', 'uses_count', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function redemptions(): HasMany { return $this->hasMany(CouponRedemption::class); }

    /**
     * Whether this coupon currently grants a given student full access.
     * Direct-assign coupons (student_id set) apply only to that student;
     * public-code coupons require a redemption record. A coupon is live when
     * enabled and inside its calendar window (if a window is set).
     */
    public function isActiveForStudent(Student $student): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->student_id !== null && (int) $this->student_id !== (int) $student->id) {
            return false;
        }

        $now = now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && $now->gte($this->ends_at)) {
            return false;
        }

        $redemption = $this->redemptions()->where('student_id', $student->id)->latest('redeemed_at')->first();

        if ($this->student_id !== null && ! $this->duration_days) {
            // Direct calendar-window grant — no redemption record needed.
            return true;
        }

        if (! $redemption) {
            return false;
        }

        if ($redemption->granted_until && $now->gte($redemption->granted_until)) {
            return false;
        }

        return true;
    }
}
