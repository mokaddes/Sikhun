<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Student\SubscriptionPurchaseRequest;
use App\Models\Plan;
use App\Models\SiteSetting;
use App\Services\Payment\PaymentGatewayResolver;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends BaseApiController
{
    public function plans(): JsonResponse
    {
        return $this->success([
            'plans' => Plan::where('is_active', true)->orderBy('sort_order')->orderBy('price_monthly')->get(),
            'custom_plan_config' => [
                'rate_minute' => (float) SiteSetting::get('custom_plan_rate_minute', 0.22),
                'rate_image' => (float) SiteSetting::get('custom_plan_rate_image', 1.10),
                'rate_book' => (float) SiteSetting::get('custom_plan_rate_book', 14.0),
                'rate_reupload' => (float) SiteSetting::get('custom_plan_rate_reupload', 5.0),
                'rate_quiz' => (float) SiteSetting::get('custom_plan_rate_quiz', 2.5),
                'base_fee' => (float) SiteSetting::get('custom_plan_base_fee', 35.0),
                'min_monthly' => (float) SiteSetting::get('custom_plan_min_monthly', 149.0),
            ],
        ]);
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

    public function purchaseCustom(Request $request, PurchaseService $purchases, PaymentGatewayResolver $resolver): JsonResponse
    {
        $validated = $request->validate([
            'months' => ['required', 'integer', 'in:1,2,3,6,12'],
            'payment_method' => ['required', 'in:wallet,zinipay,mhpay'],
            'custom_config' => ['required', 'array'],
            'custom_config.minutes' => ['required', 'integer', 'min:100', 'max:5000'],
            'custom_config.images' => ['required', 'integer', 'min:10', 'max:1000'],
            'custom_config.books' => ['required', 'integer', 'min:1', 'max:50'],
            'custom_config.reuploads' => ['required', 'integer', 'min:1', 'max:50'],
            'custom_config.quizzes' => ['required', 'integer', 'min:5', 'max:500'],
            'custom_config.total_price' => ['required', 'numeric', 'min:149'],
        ]);

        try {
            $gateway = $resolver->resolve($validated['payment_method']);
            $result = $purchases->purchaseCustomSubscription(
                auth('sanctum')->user(),
                $validated['custom_config'],
                (int) $validated['months'],
                $validated['payment_method'],
                $gateway
            );
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), [], 422);
        }

        return $this->success($result);
    }
}
