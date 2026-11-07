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

    public static function normalizeDigits(?string $val): ?string
    {
        if ($val === null || $val === '') {
            return null;
        }
        $bn = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        return str_replace($bn, $en, (string) $val);
    }

    public static function toBengaliDigits($val): string
    {
        if ($val === null || $val === '') {
            return '';
        }
        $bn = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        return str_replace($en, $bn, (string) $val);
    }

    public static function parseNumber(?string $val): ?int
    {
        $normalized = self::normalizeDigits($val);
        if ($normalized === null) {
            return null;
        }
        if (preg_match('/\d+/', $normalized, $matches)) {
            return (int) $matches[0];
        }
        return null;
    }

    /**
     * Resequence all chapters of a book into pure reading order:
     * 1. start_page ASC (if positive)
     * 2. numeric chapter_number ASC
     * 3. sort_order ASC
     * 4. id ASC
     *
     * Re-assigns sort_order as 1, 2, 3... and normalizes chapter_number.
     */
    public static function resequenceForBook(int $bookId): void
    {
        $chapters = static::where('book_id', $bookId)->get();
        if ($chapters->isEmpty()) {
            return;
        }

        $sorted = $chapters->sort(function ($a, $b) {
            $pageA = ($a->start_page && $a->start_page > 0) ? (int) $a->start_page : 999999;
            $pageB = ($b->start_page && $b->start_page > 0) ? (int) $b->start_page : 999999;
            if ($pageA !== $pageB) {
                return $pageA <=> $pageB;
            }

            $numA = self::parseNumber($a->chapter_number) ?? 999999;
            $numB = self::parseNumber($b->chapter_number) ?? 999999;
            if ($numA !== $numB) {
                return $numA <=> $numB;
            }

            $sortA = (int) ($a->sort_order ?? 0);
            $sortB = (int) ($b->sort_order ?? 0);
            if ($sortA !== $sortB) {
                return $sortA <=> $sortB;
            }

            return $a->id <=> $b->id;
        })->values();

        foreach ($sorted as $index => $chapter) {
            $desiredOrder = $index + 1;
            $parsedNum = self::parseNumber($chapter->chapter_number);

            $updates = [];
            if ((int) $chapter->sort_order !== $desiredOrder) {
                $updates['sort_order'] = $desiredOrder;
            }

            if ($parsedNum !== null && (string) $chapter->chapter_number !== (string) $parsedNum) {
                $updates['chapter_number'] = (string) $parsedNum;
            }

            if (!empty($updates)) {
                $chapter->update($updates);
            }
        }
    }

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
