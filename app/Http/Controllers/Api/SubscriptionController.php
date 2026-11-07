<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Student\SubscriptionPurchaseRequest;
use App\Models\Plan;
use App\Services\Payment\PaymentGatewayResolver;
use App\Services\Payment\ZinipayService;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends BaseApiController
{
    public function plans(): JsonResponse
    {
        return $this->success(Plan::where('is_active', true)->orderBy('price_monthly')->get());
    }

    public function active(): JsonResponse
    {
        return $this->success(auth('sanctum')->user()->activeSubscription()->with('plan')->first());
    }

    public function purchase(SubscriptionPurchaseRequest $request, PurchaseService $purchases, PaymentGatewayResolver $resolver): JsonResponse
    {
        $student = auth('sanctum')->user();
        $plan = Plan::findOrFail($request->plan_id);

        $gateway = $resolver->resolve($request->payment_method);

        $result = $purchases->purchaseSubscription(
            $student, $plan, (int) $request->months, $request->payment_method,
            $gateway
        );

        return $this->success($result);
    }
}
