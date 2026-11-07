<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobSource extends Model
{
    protected $fillable = [
        'name',
        'url',
        'feed_url',
        'type',
        'default_category',
        'is_active',
        'last_scraped_at',
        'last_scraped_status',
        'jobs_imported_count',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_scraped_at' => 'datetime',
            'jobs_imported_count' => 'integer',
        ];
    }
}
