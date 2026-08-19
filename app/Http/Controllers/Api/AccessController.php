<?php

namespace App\Http\Controllers\Api;

use App\Services\AccessGrantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccessController extends BaseApiController
{
    public function status(AccessGrantService $grants): JsonResponse
    {
        return $this->success($grants->accessSummary(auth('sanctum')->user()));
    }

    public function redeem(Request $request, AccessGrantService $grants): JsonResponse
    {
        $request->validate(['code' => ['required', 'string', 'max:40']]);

        try {
            $grants->redeem(auth('sanctum')->user(), trim($request->code));
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), [], 422);
        }

        return $this->success($grants->accessSummary(auth('sanctum')->user()), 'Coupon redeemed', 201);
    }
}
