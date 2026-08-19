<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FreeCampaign extends Model
{
    protected $fillable = [
        'title', 'description', 'scope', 'starts_at', 'ends_at', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'free_campaign_students');
    }

    /**
     * Whether this campaign currently applies to the given student.
     */
    public function appliesTo(Student $student): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();

        if ($now->lt($this->starts_at) || $now->gte($this->ends_at)) {
            return false;
        }

        if ($this->scope === 'all') {
            return true;
        }

        return $this->students()->whereKey($student->id)->exists();
    }

    /**
     * Campaigns currently live for the given student.
     */
    public function scopeLiveFor(Builder $query, Student $student): Builder
    {
        $now = now();

        return $query->where('is_active', true)
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>', $now)
            ->where(function (Builder $q) use ($student) {
                $q->where('scope', 'all')
                    ->orWhereHas('students', fn (Builder $s) => $s->whereKey($student->id));
            });
    }
}
