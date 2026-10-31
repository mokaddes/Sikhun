<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\ReferralService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReferralController extends Controller
{
    public function trackClick(Request $request, string $code, ReferralService $service): RedirectResponse
    {
        $event = $service->recordClick($request, $code);
        $destination = $event
            ? '/register?ref='.urlencode($code)
            : '/register';

        $response = redirect($destination);

        if (! $request->cookie('referral_device_id')) {
            $response->withCookie(cookie(
                'referral_device_id',
                (string) Str::uuid(),
                60 * 24 * 365,
                '/',
                null,
                $request->isSecure(),
                true,
                false,
                'Lax'
            ));
        }

        return $response;
    }
}
