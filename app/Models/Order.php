<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'student_id', 'order_number', 'orderable_type', 'orderable_id', 'amount',
        'payment_method', 'gateway_transaction_id', 'gateway_invoice_id', 'meta', 'status',
    ];

    protected function casts(): array
    {
        return ['student_id' => 'integer', 'amount' => 'decimal:2', 'meta' => 'array'];
    }

    public function student() { return $this->belongsTo(Student::class); }
}
