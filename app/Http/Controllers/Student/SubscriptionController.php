<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\SubscriptionPurchaseRequest;
use App\Models\Plan;
use App\Services\Payment\PaymentGatewayResolver;
use App\Services\Payment\ZinipayService;
use App\Services\PurchaseService;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionController extends Controller
{
    public function plans(): Response
    {
        $student = auth('web')->user();

        return Inertia::render('Student/Subscription/Plans', [
            'plans' => Plan::where('is_active', true)->orderBy('duration_months')->orderBy('price_monthly')->get(),
            'customPlanConfig' => [
                'enabled' => filter_var(app(\App\Services\SiteSettingService::class)->get('custom_plan_enabled', true), FILTER_VALIDATE_BOOLEAN),
                'base_fee' => (float) app(\App\Services\SiteSettingService::class)->get('custom_plan_base_fee', 35.0),
                'min_monthly' => (float) app(\App\Services\SiteSettingService::class)->get('custom_plan_min_monthly', 149.0),
                'rate_minute' => (float) app(\App\Services\SiteSettingService::class)->get('custom_plan_rate_minute', 0.22),
                'rate_image' => (float) app(\App\Services\SiteSettingService::class)->get('custom_plan_rate_image', 1.10),
                'rate_book' => (float) app(\App\Services\SiteSettingService::class)->get('custom_plan_rate_book', 14.00),
                'rate_reupload' => (float) app(\App\Services\SiteSettingService::class)->get('custom_plan_rate_reupload', 5.00),
                'rate_quiz' => (float) app(\App\Services\SiteSettingService::class)->get('custom_plan_rate_quiz', 2.50),
                'rate_explanation' => (float) app(\App\Services\SiteSettingService::class)->get('custom_plan_rate_explanation', 1.00),
            ],
            'activeSubscription' => $student ? $student->activeSubscription()->with('plan')->first() : null,
        ]);
    }

    public function purchase(SubscriptionPurchaseRequest $request, PurchaseService $purchases, PaymentGatewayResolver $resolver): \Symfony\Component\HttpFoundation\Response
    {
        $student = auth('web')->user();
        if ($request->boolean('is_custom')) {
            $gateway = $resolver->resolve($request->payment_method);
            $result = $purchases->purchaseCustomSubscription(
                $student,
                (array) $request->input('custom_config', []),
                (int) ($request->months ?: 1),
                $request->payment_method,
                $gateway
            );

            if ($result['redirect_url']) {
                return Inertia::location($result['redirect_url']);
            }

            return redirect()->route('subscription.plans')->with('success', 'কাস্টম সাবস্ক্রিপশন সফলভাবে সক্রিয় হয়েছে!');
        }

        $plan = Plan::findOrFail($request->plan_id);

$isFree = $plan->is_free || (float) $plan->price_monthly <= 0 || $request->payment_method === 'free';
        $gateway = $isFree ? null : $resolver->resolve($request->payment_method);

        $result = $purchases->purchaseSubscription(
            $student,
            $plan,
            (int) $request->months,
            $request->payment_method,
            $gateway
        );

        if ($result['redirect_url']) {
            return Inertia::location($result['redirect_url']);
        }

        return redirect()->route('subscription.plans')->with('success', 'Subscription activated!');
    }
}
