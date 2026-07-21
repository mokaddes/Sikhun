<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Student\SubscriptionPurchaseRequest;
use App\Models\Plan;
use App\Services\Payment\SslcommerzService;
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

    public function purchase(SubscriptionPurchaseRequest $request, PurchaseService $purchases, SslcommerzService $sslcommerz): JsonResponse
    {
        $student = auth('sanctum')->user();
        $plan = Plan::findOrFail($request->plan_id);

        $result = $purchases->purchaseSubscription(
            $student, $plan, (int) $request->months, $request->payment_method,
            $request->payment_method === 'sslcommerz' ? $sslcommerz : null
        );

        return $this->success($result);
    }
}
