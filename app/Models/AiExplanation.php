<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiExplanation extends Model
{
    protected $fillable = [
        'cache_key', 'question', 'correct_answer', 'student_answer', 'source_hash', 'explanation',
    ];
}
