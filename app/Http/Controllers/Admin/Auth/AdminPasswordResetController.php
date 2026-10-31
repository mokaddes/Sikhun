<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AdminPasswordResetController extends Controller
{
    public function showRequestForm(): Response
    {
        return Inertia::render('Auth/ForgotPassword', ['scope' => 'admin']);
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        $status = Password::broker('admins')->sendResetLink($request->only('email'));

        if ($status === Password::RESET_THROTTLED) {
            throw ValidationException::withMessages([
                'email' => $this->statusMessage($status),
            ]);
        }

        return back()->with('success', $this->statusMessage(Password::RESET_LINK_SENT));
    }

    public function showResetForm(Request $request, string $token): Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
            'scope' => 'admin',
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $status = Password::broker('admins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Admin $admin, string $password): void {
                $admin->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($admin));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => $this->statusMessage($status),
            ]);
        }

        return redirect()->route('admin.login')->with('success', $this->statusMessage($status));
    }

    private function statusMessage(string $status): string
    {
        $bn = app()->getLocale() === 'bn';

        return match ($status) {
            Password::RESET_LINK_SENT => $bn
                ? 'রিসেট লিংক অ্যাডমিন ইমেইলে পাঠানো হয়েছে।'
                : 'We have emailed the admin password reset link.',
            Password::PASSWORD_RESET => $bn
                ? 'পাসওয়ার্ড বদলানো হয়েছে। এখন নতুন পাসওয়ার্ড দিয়ে লগইন করুন।'
                : 'Password reset. Sign in with your new password.',
            Password::INVALID_TOKEN => $bn
                ? 'লিংকটি মেয়াদোত্তীর্ণ বা ভুল। আবার চেষ্টা করুন।'
                : 'This reset link is invalid or has expired.',
            Password::INVALID_USER => $bn
                ? 'এই ইমেইলে কোনো অ্যাডমিন অ্যাকাউন্ট নেই।'
                : "We can't find an admin with that email address.",
            Password::RESET_THROTTLED => $bn
                ? 'একটু অপেক্ষা করুন, তারপর আবার চেষ্টা করুন।'
                : 'Please wait a moment before trying again.',
            default => $bn
                ? 'কিছু একটা ভুল হয়েছে। আবার চেষ্টা করুন।'
                : 'Something went wrong. Please try again.',
        };
    }
}
