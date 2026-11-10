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
            'plans' => Plan::where('is_active', true)->orderBy('price_monthly')->get(),
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
