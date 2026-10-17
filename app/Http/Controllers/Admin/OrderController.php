<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PurchaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $orders = Order::with('student:id,name,email')
            ->when($request->search, function ($q) use ($request) {
                $search = trim($request->search);
                $q->where(function ($sub) use ($search) {
                    $sub->where('order_number', 'like', "%{$search}%")
                        ->orWhereHas('student', fn ($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
                });
            })
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'filters' => $request->only('status', 'search'),
        ]);
    }

    /**
     * Manual/bank-transfer orders sit in `pending` until an admin confirms
     * the money actually arrived. Approving now goes through the same
     * PurchaseService::fulfill() the automated gateway path uses, so a
     * manually-approved wallet recharge / book / subscription order
     * delivers exactly the same access as a card payment would.
     */
    public function approve(Order $order, PurchaseService $purchases): RedirectResponse
    {
        if ($order->status === 'completed') {
            return back()->with('error', 'Order was already completed.');
        }

        $order->update(['status' => 'completed']);
        $purchases->fulfill($order);

        return back()->with('success', 'Order approved and fulfilled.');
    }
}
