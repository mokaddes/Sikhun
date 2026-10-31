<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicketMessage extends Model
{
    protected $fillable = ['ticket_id', 'sender_type', 'body', 'attachments'];

    protected function casts(): array
    {
        return ['ticket_id' => 'integer', 'attachments' => 'array'];
    }

    public function ticket() { return $this->belongsTo(SupportTicket::class, 'ticket_id'); }
}
