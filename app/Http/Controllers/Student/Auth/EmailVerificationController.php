<?php

namespace App\Http\Controllers\Student\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerificationController extends Controller
{
    public function notice(Request $request): Response|RedirectResponse
    {
        $student = $request->user('web');

        if ($student->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        return Inertia::render('Auth/VerifyEmail', [
            'email' => $student->email,
        ]);
    }

    public function verify(Request $request, string $id, string $hash): RedirectResponse
    {
        $student = Student::findOrFail($id);

        // The signed URL proves mailbox ownership, but it must only ever
        // verify the account that is currently signed in — never switch
        // the session to whoever the link belongs to.
        abort_unless((int) $request->user('web')->getKey() === (int) $student->getKey(), 403);

        abort_unless(hash_equals($hash, sha1($student->getEmailForVerification())), 403);

        if (! $student->hasVerifiedEmail()) {
            $student->markEmailAsVerified();
            event(new Verified($student));
        }

        return redirect()
            ->intended(route('dashboard'))
            ->with('success', app()->getLocale() === 'bn'
                ? 'ইমেইল যাচাই সম্পন্ন হয়েছে। ধন্যবাদ!'
                : 'Your email has been verified. Thank you!');
    }

    public function send(Request $request): RedirectResponse
    {
        $student = $request->user('web');

        if ($student->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        $student->sendEmailVerificationNotification();

        return back()->with('success', app()->getLocale() === 'bn'
            ? 'যাচাই লিংক ইমেইলে পাঠানো হয়েছে। ইনবক্স দেখুন।'
            : 'A fresh verification link has been sent to your email.');
    }
}
