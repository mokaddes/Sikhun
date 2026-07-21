<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiProviderUseCase extends Model
{
    protected $fillable = ['ai_provider_id', 'use_case', 'is_default'];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    public function provider() { return $this->belongsTo(AiProvider::class, 'ai_provider_id'); }
}
