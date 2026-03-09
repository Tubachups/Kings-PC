<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrdersController extends Controller
{
    //
    public function index(Request $request)
    {
        return Inertia::render('shop/Orders', [
            'orders' => $request->user()
                ->orders()
                ->latest()
                ->get(['id', 'total', 'status', 'created_at']),

            'selectedOrder' => Inertia::lazy(function () use ($request) {
                $orderId = $request->query('orderId');
                if (! $orderId) {
                    return null;
                }

                return Order::with(['order_items.product'])
                    ->where('user_id', $request->user()->id)
                    ->findOrFail($orderId);
            }),
        ]);
    }
}
