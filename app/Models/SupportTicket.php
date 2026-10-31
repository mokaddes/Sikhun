<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = [
        'student_id', 'conversation_id', 'subject', 'category', 'priority', 'status',
        'assigned_admin_id', 'sla_due_at',
    ];

    protected function casts(): array
    {
        return [
            'student_id' => 'integer',
            'conversation_id' => 'integer',
            'assigned_admin_id' => 'integer',
            'sla_due_at' => 'datetime',
        ];
    }

    public function student() { return $this->belongsTo(Student::class); }
    public function conversation() { return $this->belongsTo(SupportConversation::class); }
    public function assignedAdmin() { return $this->belongsTo(Admin::class, 'assigned_admin_id'); }
    public function messages() { return $this->hasMany(SupportTicketMessage::class, 'ticket_id'); }
}
