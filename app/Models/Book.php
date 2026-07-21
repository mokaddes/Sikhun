<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Book extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'cover_image', 'author_id', 'publication_id',
        'category_id', 'subject', 'level', 'price', 'is_free', 'pdf_path',
        'total_pages', 'is_published', 'is_premium_gift', 'reading_count',
    ];

    // Without this, getCoverImageUrlAttribute() would compute correctly but
    // never actually appear in the JSON Inertia sends to the frontend —
    // accessors are opt-in to serialization, not automatic.
    protected $appends = ['cover_image_url'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_free' => 'boolean',
            'is_published' => 'boolean',
            'is_premium_gift' => 'boolean',
        ];
    }

    public function author() { return $this->belongsTo(Author::class); }
    public function publication() { return $this->belongsTo(Publication::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function bookShelves() { return $this->hasMany(BookShelf::class); }
    public function chunks() { return $this->hasMany(BookChunk::class); }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image ? asset('storage/'.$this->cover_image) : null;
    }
}
