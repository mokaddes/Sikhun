<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportConversation extends Model
{
    protected $fillable = ['student_id', 'guest_token', 'status', 'bot_enabled'];

    protected function casts(): array
    {
        return ['student_id' => 'integer', 'bot_enabled' => 'boolean'];
    }

    public function student() { return $this->belongsTo(Student::class); }
    public function messages() { return $this->hasMany(SupportMessage::class); }
}
