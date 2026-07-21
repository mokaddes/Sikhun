<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushSubscription extends Model
{
    protected $fillable = ['student_id', 'channel', 'token'];

    public function student() { return $this->belongsTo(Student::class); }
}
