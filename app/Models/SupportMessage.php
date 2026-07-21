<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportMessage extends Model
{
    protected $fillable = ['support_conversation_id', 'sender_type', 'message'];

    public function conversation() { return $this->belongsTo(SupportConversation::class, 'support_conversation_id'); }
}
