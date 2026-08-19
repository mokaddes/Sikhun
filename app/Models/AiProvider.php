<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiProvider extends Model
{
    protected $fillable = [
        'name', 'type', 'api_key', 'model_name', 'api_endpoint',
        'custom_headers', 'is_active', 'max_tokens', 'temperature',
    ];

    protected $hidden = ['api_key'];

    protected $appends = ['use_case_list', 'default_use_case_list'];

    protected function casts(): array
    {
        return [
            'api_key' => 'encrypted',
            'custom_headers' => 'array',
            'is_active' => 'boolean',
            'temperature' => 'decimal:2',
        ];
    }

    public function useCases() { return $this->hasMany(AiProviderUseCase::class); }

    /** Convenience accessor for the admin UI: ['exam_gen', 'book_chat', ...] */
    public function getUseCaseListAttribute(): array
    {
        return $this->useCases->pluck('use_case')->all();
    }

    /** Convenience accessor: which of this provider's use cases it's the default for. */
    public function getDefaultUseCaseListAttribute(): array
    {
        return $this->useCases->where('is_default', true)->pluck('use_case')->all();
    }
}
