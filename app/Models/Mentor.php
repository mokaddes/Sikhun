<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mentor extends Model
{
    protected $fillable = ['name', 'slug', 'designation', 'bio', 'photo', 'expertise'];

    protected function casts(): array
    {
        return ['expertise' => 'array'];
    }

    public function courses() { return $this->hasMany(Course::class); }
}
