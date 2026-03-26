<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function index(CheckoutService $checkout)
    {
        $user_id = Auth::id();
        $redisKey = 'cart:user:'.$user_id;
        $cart = Redis::hgetall($redisKey);

        $freshCart = $checkout->validateCart($redisKey, $cart);

        return Inertia::render('shop/Checkout', [
            'currentUser' => Auth::user(),
            'freshCart' => $freshCart ?? [],
            'shippingFee' => 150.00,
        ]);
    }

    public static function checkoutConfirm(Request $request)
    {
        $redis_key = 'cart:user:'.Auth::id();
        $cart_items = Redis::hgetall($redis_key);

        return DB::transaction(function () use ($request, $redis_key, $cart_items) {
            $order = Order::createOrder($request);

            $grandTotal = 0;

            foreach ($cart_items as $product_id => $item_json) {
                $item = json_decode($item_json, true);
                $product = null;

                // Backward compatibility: some cart entries are stored as raw quantity ints.
                if (is_int($item) || (is_string($item) && is_numeric($item))) {
                    $quantity = (int) $item;
                    if ($quantity <= 0) {
                        continue;
                    }

                    $product = Product::find((int) $product_id);
                    if (! $product) {
                        continue;
                    }

                    $item = [
                        'quantity' => $quantity,
                        'product' => [
                            'id' => $product->id,
                            'price' => $product->price,
                        ],
                    ];
                }

                if (
                    ! is_array($item)
                    || ! isset($item['quantity'], $item['product'])
                    || ! is_array($item['product'])
                    || ! isset($item['product']['id'])
                ) {
                    continue;
                }

                $quantity = (int) $item['quantity'];
                if ($quantity <= 0) {
                    continue;
                }

                $productId = (int) $item['product']['id'];
                $product ??= Product::query()->whereKey($productId)->lockForUpdate()->first();
                if (! $product) {
                    continue;
                }

                if ($quantity > $product->stock) {
                    throw ValidationException::withMessages([
                        'cart' => "{$product->name} only has {$product->stock} item(s) left in stock.",
                    ]);
                }

                // Always use latest DB price to avoid malformed/stale cart prices.
                $item['product']['price'] = $product->price;
                $item['quantity'] = $quantity;

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
