<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CheckoutController extends Controller
{

    public function index(CheckoutService $checkout) {
        $user_id = Auth::id();
        $redisKey = "cart:user:" . $user_id;
        $cart = Redis::hgetall($redisKey);

        $freshCart = $checkout->validateCart($redisKey, $cart);

        return Inertia::render('Shop/Checkout', [
            'freshCart'   => $freshCart ?? [],
            'shippingFee' => 150.00,
        ]);
    }

    public static function checkoutConfirm(Request $request) {
        $redis_key = "cart:user:" . Auth::id();
        $cart_items = Redis::hgetall($redis_key);
        return DB::transaction(function () use ($request, $redis_key, $cart_items) {
            $order = Order::createOrder($request);

            $grandTotal = 0;

            foreach($cart_items as $product_id => $item_json) {
                $item = json_decode($item_json, true);
                $subtotal = $item['product']['price'] * $item['quantity'];
                $grandTotal += $subtotal;
                OrderItem::createOrderItem($order->id, $item);
                Product::checkoutStock($item['product']['id'], $item['quantity']);
            }

            $order->update(['total' => $grandTotal + 150]);
            Redis::del($redis_key);
            return redirect()->route('shop')->with('success', "Order {$order->order_number} placed!");
        });
    }

}
