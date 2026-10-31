<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'student_id', 'order_id', 'invoice_number', 'type',
        'period_start', 'period_end', 'issued_at', 'due_at',
        'subtotal', 'discount', 'vat', 'total', 'currency',
        'status', 'pdf_path', 'notes', 'meta',
    ];

    protected function casts(): array
    {
        return [
            'student_id' => 'integer',
            'order_id' => 'integer',
            'period_start' => 'date',
            'period_end' => 'date',
            'issued_at' => 'datetime',
            'due_at' => 'date',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'vat' => 'decimal:2',
            'total' => 'decimal:2',
            'meta' => 'array',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function isOpen(): bool
    {
        return in_array($this->status, ['draft', 'sent', 'overdue'], true);
    }

    public function isOverdue(): bool
    {
        return $this->isOpen() && $this->due_at && $this->due_at->isPast();
    }
}
