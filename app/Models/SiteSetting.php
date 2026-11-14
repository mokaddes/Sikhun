<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    protected function casts(): array
    {
        return ['value' => 'array'];
    }

    /**
     * Get a setting by key from cache/database.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return app(\App\Services\SiteSettingService::class)->get($key, $default);
    }

    /**
     * Alias for get().
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        return static::get($key, $default);
    }
}
