<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledNotification extends Model
{
    protected $fillable = [
        'type',
        'title',
        'body',
        'action_url',
        'send_email',
        'target_audience',
        'target_student_id',
        'scheduled_for',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'send_email' => 'boolean',
            'target_student_id' => 'integer',
            'scheduled_for' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }
}
