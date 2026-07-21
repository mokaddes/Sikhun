<?php

namespace App\Http\Controllers\Api;

use App\Models\CustomPage;
use Illuminate\Http\JsonResponse;

class PageController extends BaseApiController
{
    public function show(string $slug): JsonResponse
    {
        $page = CustomPage::where('slug', $slug)->where('is_published', true)->firstOrFail();

        return $this->success($page);
    }
}
