<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Student\RechargeRequest;
use App\Services\Payment\ZinipayService;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;

class WalletController extends BaseApiController
{
    public function index(): JsonResponse
    {
        $student = auth('sanctum')->user();

        return $this->success(['balance' => $student->wallet_balance]);
    }

    public function transactions(): JsonResponse
    {
        return $this->success(auth('sanctum')->user()->walletTransactions()->latest()->paginate(20));
    }

    public function recharge(RechargeRequest $request, PurchaseService $purchases, ZinipayService $zinipay): JsonResponse
    {
        $student = auth('sanctum')->user();

        $result = $purchases->initiateWalletRecharge(
            $student,
            (float) $request->amount,
            $request->method,
            $request->method === 'zinipay' ? $zinipay : null
        );

        if ($request->method === 'manual') {
            $result['order']->update(['gateway_transaction_id' => $request->transaction_reference]);
        }

        return $this->success($result);
    }
}
