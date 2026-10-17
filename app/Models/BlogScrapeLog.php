<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogScrapeLog extends Model
{
    protected $fillable = [
        'blog_source_id',
        'found',
        'duplicates',
        'blocked',
        'drafted',
        'failed',
        'error',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'found' => 'integer',
            'duplicates' => 'integer',
            'blocked' => 'integer',
            'drafted' => 'integer',
            'failed' => 'integer',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(BlogSource::class, 'blog_source_id');
    }
}
