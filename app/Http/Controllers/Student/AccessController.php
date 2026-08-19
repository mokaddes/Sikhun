<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\AccessGrantService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccessController extends Controller
{
    public function index(AccessGrantService $grants): Response
    {
        $student = auth('web')->user();

        return Inertia::render('Student/Access/Index', [
            'access' => $grants->accessSummary($student),
        ]);
    }

    public function redeem(Request $request, AccessGrantService $grants): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string', 'max:40']]);

        try {
            $grants->redeem(auth('web')->user(), trim($request->code));
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('access.index')->with('success', 'Coupon redeemed — full access unlocked!');
    }
}
