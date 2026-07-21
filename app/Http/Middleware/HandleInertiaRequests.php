<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $student = auth('web')->user();
        $admin = auth('admin')->user();
        $locale = app()->getLocale();

        return [
            ...parent::share($request),
            'locale' => $locale,
            'translations' => $this->loadTranslations($locale),
            'auth' => [
                'student' => $student ? [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'type' => $student->type,
                    'avatar' => $student->avatar,
                    'theme_mode' => $student->theme_mode,
                    'wallet_balance' => $student->wallet_balance,
                ] : null,
                'admin' => $admin ? [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'email' => $admin->email,
                    'role' => $admin->role,
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }

    /**
     * Loads the frontend translation dictionary for the given locale from
     * lang/{locale}.json and caches it (translations rarely change at
     * runtime, so a long TTL is safe — clear with `php artisan cache:clear`
     * after editing a translation file).
     */
    private function loadTranslations(string $locale): array
    {
        return Cache::remember("translations:{$locale}", 3600, function () use ($locale) {
            $path = base_path("lang/{$locale}.json");

            if (! file_exists($path)) {
                return [];
            }

            return json_decode(file_get_contents($path), true) ?? [];
        });
    }
}
