<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BookChapter extends Model
{
    protected $fillable = [
        'book_id', 'parent_id', 'chapter_number', 'title', 'slug', 'level',
        'start_page', 'end_page', 'sort_order', 'content', 'metadata', 'price',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'start_page' => 'integer',
            'end_page' => 'integer',
            'sort_order' => 'integer',
            'metadata' => 'array',
            'price' => 'decimal:2',
        ];
    }

    public function book() { return $this->belongsTo(Book::class); }
    public function parent() { return $this->belongsTo(BookChapter::class, 'parent_id'); }
    public function children() { return $this->hasMany(BookChapter::class, 'parent_id')->orderBy('sort_order'); }
    public function pages() { return $this->hasMany(BookPage::class); }
    public function elements() { return $this->hasMany(BookElement::class); }
    public function tables() { return $this->hasMany(BookTable::class); }
    public function images() { return $this->hasMany(BookImage::class); }
    public function formulas() { return $this->hasMany(BookFormula::class); }
    public function studentOwners() { return $this->hasMany(StudentBookChapter::class); }

    /**
     * This chapter plus all its descendant sections — the set of chapters a
     * student effectively owns when they own this chapter, and the set a
     * reader must skip when they don't.
     */
    public function scopeWithDescendants(Builder $query, int $chapterId): Builder
    {
        return $query->where('id', $chapterId)->orWhere('parent_id', $chapterId);
    }
}
