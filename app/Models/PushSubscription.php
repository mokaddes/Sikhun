<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushSubscription extends Model
{
    protected $fillable = ['student_id', 'channel', 'token'];

    protected function casts(): array
    {
        return ['student_id' => 'integer'];
    }

    public function student() { return $this->belongsTo(Student::class); }
}
