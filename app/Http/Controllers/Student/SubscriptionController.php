<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\SubscriptionPurchaseRequest;
use App\Models\Plan;
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
            'activeSubscription' => $student->activeSubscription()->with('plan')->first(),
        ]);
    }

    public function purchase(SubscriptionPurchaseRequest $request, PurchaseService $purchases, ZinipayService $zinipay): \Symfony\Component\HttpFoundation\Response
    {
        $student = auth('web')->user();
        $plan = Plan::findOrFail($request->plan_id);

        $result = $purchases->purchaseSubscription(
            $student,
            $plan,
            (int) $request->months,
            $request->payment_method,
            $request->payment_method === 'zinipay' ? $zinipay : null
        );

        if ($result['redirect_url']) {
            return Inertia::location($result['redirect_url']);
        }

        return redirect()->route('subscription.plans')->with('success', 'Subscription activated!');
    }
}
