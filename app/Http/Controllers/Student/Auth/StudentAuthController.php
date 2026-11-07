<?php

namespace App\Http\Controllers\Student\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\LoginRequest;
use App\Http\Requests\Student\RegisterRequest;
use App\Mail\WelcomeMail;
use App\Models\Plan;
use App\Models\Student;
use App\Services\SubscriptionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class StudentAuthController extends Controller
{
    public function showRegister(\Illuminate\Http\Request $request): Response
    {
        if ($redirect = $request->query('redirect')) {
            if (is_string($redirect) && str_starts_with($redirect, '/') && ! str_starts_with($redirect, '//')) {
                session(['url.intended' => $redirect]);
            }
        }

        return Inertia::render('Auth/Register', [
            'plans' => Plan::where('is_active', true)->orderBy('price_monthly')->get(),
            'defaultPlanId' => $request->query('plan_id'),
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $student = DB::transaction(function () use ($request) {
            $student = Student::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password, // hashed via cast
                'type' => $request->type,
                'trial_ends_at' => null, // No more 7-day trial limit
                'referral_code' => 'SIKHU-'.strtoupper(Str::random(5)),
                'referred_by_student_id' => $this->resolveReferrer($request->query('ref')),
            ]);

            // Default to Free Plan
            $freePlan = Plan::where('is_free', true)->orWhere('price_monthly', 0)->first();
            if ($freePlan) {
                app(SubscriptionService::class)->assign($student, $freePlan, 12);
            }

            return $student;
        });

        Auth::guard('web')->login($student);
        $request->session()->regenerate();

        try {
            Mail::to($student->email)->send(new WelcomeMail($student));
        } catch (\Throwable) {
            // Do not block registration if mail transport is not configured
        }

        $planId = $request->input('plan_id');
        if ($planId) {
            $chosenPlan = Plan::find($planId);
            if ($chosenPlan && (float) $chosenPlan->price_monthly > 0) {
                return redirect()->route('subscription.plans', ['selected' => $chosenPlan->id]);
            }
        }

        return redirect()->intended(route('dashboard'))->with('success', 'স্বাগতম! আপনি ফ্রি প্ল্যানে সফলভাবে যুক্ত হয়েছেন।');
    }

    public function showLogin(\Illuminate\Http\Request $request): Response
    {
        if ($redirect = $request->query('redirect')) {
            if (is_string($redirect) && str_starts_with($redirect, '/') && ! str_starts_with($redirect, '//')) {
                session(['url.intended' => $redirect]);
            }
        }

        return Inertia::render('Auth/Login');
    }

    public function login(LoginRequest $request)
    {
        if (! Auth::guard('web')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $student = Auth::guard('web')->user();

        if ($student->status === 'inactive') {
            Auth::guard('web')->logout();

            throw ValidationException::withMessages([
                'email' => 'Your account has been deactivated. Please contact support.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(\Illuminate\Http\Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function resolveReferrer(?string $code): ?int
    {
        if (! $code) {
            return null;
        }

        return Student::where('referral_code', $code)->value('id');
    }
}
