<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Book extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'cover_image', 'author_id', 'publication_id',
        'category_id', 'subject', 'level', 'price', 'is_free', 'pdf_path',
        'total_pages', 'processing_status', 'processing_error', 'processing_started_at',
        'processing_completed_at', 'parser_name', 'parser_version', 'parsed_at',
        'pdf_content_hash', 'chapter_purchase_enabled', 'is_published', 'is_premium_gift',
        'reading_count',
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
            'chapter_purchase_enabled' => 'boolean',
            'processing_started_at' => 'datetime',
            'processing_completed_at' => 'datetime',
            'parsed_at' => 'datetime',
        ];
    }

    public function author() { return $this->belongsTo(Author::class); }
    public function publication() { return $this->belongsTo(Publication::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function bookShelves() { return $this->hasMany(BookShelf::class); }
    public function chunks() { return $this->hasMany(BookChunk::class); }
    public function chapters() { return $this->hasMany(BookChapter::class)->orderBy('sort_order'); }
    public function topChapters() { return $this->hasMany(BookChapter::class)->whereNull('parent_id')->orderBy('sort_order'); }
    public function pages() { return $this->hasMany(BookPage::class); }
    public function elements() { return $this->hasMany(BookElement::class); }
    public function tables() { return $this->hasMany(BookTable::class); }
    public function images() { return $this->hasMany(BookImage::class); }
    public function formulas() { return $this->hasMany(BookFormula::class); }
    public function chapterOwners() { return $this->hasMany(StudentBookChapter::class); }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image ? asset('storage/'.$this->cover_image) : null;
    }
}
