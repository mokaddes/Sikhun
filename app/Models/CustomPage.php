<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CustomPage extends Model
{
    protected $fillable = [
        'slug', 'title_bn', 'title_en', 'content_bn', 'content_en',
        'meta_title_bn', 'meta_title_en', 'meta_description_bn', 'meta_description_en',
        'is_published',
        // Old single-language columns kept for backward compatibility only —
        // new code should always read/write the _bn / _en fields above.
        'title', 'content', 'meta_title', 'meta_description',
    ];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /** Returns this page's content in whatever the CURRENT app locale is. */
    public function localizedTitle(): ?string
    {
        return app()->getLocale() === 'en' ? ($this->title_en ?: $this->title_bn) : ($this->title_bn ?: $this->title_en);
    }

    public function localizedContent(): ?string
    {
        return app()->getLocale() === 'en' ? ($this->content_en ?: $this->content_bn) : ($this->content_bn ?: $this->content_en);
    }

    public function localizedMetaTitle(): ?string
    {
        return app()->getLocale() === 'en' ? ($this->meta_title_en ?: $this->meta_title_bn) : ($this->meta_title_bn ?: $this->meta_title_en);
    }

    public function localizedMetaDescription(): ?string
    {
        return app()->getLocale() === 'en' ? ($this->meta_description_en ?: $this->meta_description_bn) : ($this->meta_description_bn ?: $this->meta_description_en);
    }
}
