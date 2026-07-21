<?php

namespace App\Services;

use App\Models\LeaderboardEntry;
use App\Models\Student;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

/**
 * Top-100 leaderboard, Redis-cached for 5 minutes per unique filter
 * combination — the table can grow to tens of thousands of rows, and this
 * is read on every leaderboard page view, so it must never hit the DB
 * directly on the hot path.
 */
class LeaderboardService
{
    private int $ttlSeconds = 300;

    public function getTopStudents(string $period, array $filters = [], int $limit = 100): array
    {
        $cacheKey = $this->cacheKey($period, $filters);

        return Cache::remember($cacheKey, $this->ttlSeconds, function () use ($period, $filters, $limit) {
            return $this->query($period, $filters)
                ->with('student:id,name,type,avatar')
                ->orderByDesc('percentage')
                ->orderByDesc('questions_count')
                ->limit($limit)
                ->get()
                ->map(fn ($entry, $i) => [
                    'rank' => $i + 1,
                    'student_id' => $entry->student_id,
                    'name' => $entry->student->name,
                    'avatar' => $entry->student->avatar,
                    'type' => $entry->student_type,
                    'percentage' => (float) $entry->percentage,
                    'score' => $entry->score,
                    'total' => $entry->total,
                ])
                ->all();
        });
    }

    public function getStudentRank(Student $student, string $period, array $filters = []): ?array
    {
        $best = $this->query($period, $filters)
            ->where('student_id', $student->id)
            ->orderByDesc('percentage')
            ->first();

        if (! $best) {
            return null;
        }

        $rank = $this->query($period, $filters)
            ->where('percentage', '>', $best->percentage)
            ->count() + 1;

        return ['rank' => $rank, 'percentage' => (float) $best->percentage, 'score' => $best->score, 'total' => $best->total];
    }

    public function invalidate(string $period, array $filters = []): void
    {
        Cache::forget($this->cacheKey($period, $filters));
    }

    /** Nuke every cached leaderboard page — used by the weekly/monthly reset commands. */
    public function invalidateAll(): void
    {
        // Redis-specific (the cache store is Redis per .env) — a plain
        // Cache::flush() would wipe unrelated cached data too.
        $keys = Redis::keys('*leaderboard:*');
        if (! empty($keys)) {
            Redis::del($keys);
        }
    }

    private function query(string $period, array $filters)
    {
        $now = now();
        $query = LeaderboardEntry::query();

        match ($period) {
            'weekly' => $query->where('year', $now->year)->where('week_number', (int) $now->format('W')),
            'monthly' => $query->where('year', $now->year)->where('month_number', $now->month),
            default => null, // all-time: no date filter
        };

        if (! empty($filters['type'])) {
            $query->where('student_type', $filters['type']);
        }
        if (! empty($filters['subject'])) {
            $query->where('subject', $filters['subject']);
        }
        if (! empty($filters['book_id'])) {
            $query->where('book_id', $filters['book_id']);
        }

        return $query->whereHas('student', fn ($q) => $q->where('leaderboard_opt_out', false));
    }

    private function cacheKey(string $period, array $filters): string
    {
        return 'leaderboard:'.$period.':'.md5(json_encode($filters));
    }
}
