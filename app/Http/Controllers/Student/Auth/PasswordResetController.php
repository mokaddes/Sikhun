<?php

namespace App\Http\Controllers\Student\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetController extends Controller
{
    public function showRequestForm(): Response
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        $status = Password::broker('students')->sendResetLink($request->only('email'));

        if ($status === Password::RESET_THROTTLED) {
            throw ValidationException::withMessages([
                'email' => $this->statusMessage($status),
            ]);
        }

        // Deliberately the same response whether or not the address exists,
        // so this endpoint cannot be used to enumerate student accounts.
        return back()->with('success', $this->statusMessage(Password::RESET_LINK_SENT));
    }

    public function showResetForm(Request $request, string $token): Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $status = Password::broker('students')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Student $student, string $password): void {
                $student->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($student));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => $this->statusMessage($status),
            ]);
        }

        return redirect()->route('login')->with('success', $this->statusMessage($status));
    }

    private function statusMessage(string $status): string
    {
        $bn = app()->getLocale() === 'bn';

        return match ($status) {
            Password::RESET_LINK_SENT => $bn
                ? 'রিসেট লিংক ইমেইলে পাঠানো হয়েছে। ইনবক্স (এবং স্প্যাম) দেখুন।'
                : 'We have emailed your password reset link. Check your inbox and spam folder.',
            Password::PASSWORD_RESET => $bn
                ? 'পাসওয়ার্ড বদলানো হয়েছে। এখন নতুন পাসওয়ার্ড দিয়ে লগইন করুন।'
                : 'Your password has been reset. You can now sign in with the new password.',
            Password::INVALID_TOKEN => $bn
                ? 'লিংকটি মেয়াদোত্তীর্ণ বা ভুল। আবার রিসেট লিংক চান।'
                : 'This password reset link is invalid or has expired. Request a new one.',
            Password::INVALID_USER => $bn
                ? 'এই ইমেইলে কোনো অ্যাকাউন্ট পাওয়া যায়নি।'
                : "We can't find an account with that email address.",
            Password::RESET_THROTTLED => $bn
                ? 'একটু অপেক্ষা করুন, তারপর আবার চেষ্টা করুন।'
                : 'Please wait a moment before trying again.',
            default => $bn
                ? 'কিছু একটা ভুল হয়েছে। আবার চেষ্টা করুন।'
                : 'Something went wrong. Please try again.',
        };
    }
}
