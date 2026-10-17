<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogBlocklist extends Model
{
    protected $table = 'blog_blocklist';

    protected $fillable = [
        'source_url_hash',
        'content_hash',
        'title_fingerprint',
        'source_url',
        'source_title',
        'reason',
        'blocked_by',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'blocked_by');
    }
}
