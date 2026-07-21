<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentNotificationPreference extends Model
{
    protected $fillable = ['student_id', 'type', 'is_enabled', 'preferred_time'];

    protected function casts(): array
    {
        return ['is_enabled' => 'boolean'];
    }

    public function student() { return $this->belongsTo(Student::class); }
}
