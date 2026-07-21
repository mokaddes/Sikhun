<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Course extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'cover_image', 'mentor_id',
        'category_id', 'level', 'price', 'is_active',
    ];

    protected $appends = ['cover_image_url'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'is_active' => 'boolean'];
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image ? asset('storage/'.$this->cover_image) : null;
    }

    public function mentor() { return $this->belongsTo(Mentor::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function sections() { return $this->hasMany(CourseSection::class)->orderBy('sort_order'); }
    public function enrollments() { return $this->hasMany(CourseEnrollment::class); }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
