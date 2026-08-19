<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'student_id', 'type', 'category', 'amount', 'balance_before',
        'balance_after', 'reference', 'notes',
    ];

    protected function casts(): array
    {
        return ['student_id' => 'integer', 'amount' => 'decimal:2', 'balance_before' => 'decimal:2', 'balance_after' => 'decimal:2'];
    }

    public function student() { return $this->belongsTo(Student::class); }
}
