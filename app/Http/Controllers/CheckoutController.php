<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    //
    public function index() {
        $user_id = Auth::id();
        $redisKey = "cart:user:" . $user_id;
        $cart = Redis::hgetall($redisKey);

        if (!empty($cart)) {
            $updatedCart = [];

            foreach ($cart as $field => $value) {
                $item = json_decode($value, true);
                $product = Product::find($item['product']['id']);

                if ($product) {
                    $item['product']['price'] = $product->price;
                }

                $updatedCart[$field] = json_encode($item);
            }

            // save fresh prices back to redis
            Redis::pipeline(function ($pipe) use ($redisKey, $updatedCart) {
                $pipe->del($redisKey);
                foreach ($updatedCart as $field => $value) {
                    $pipe->hset($redisKey, $field, $value);
                }
                $pipe->expire($redisKey, 43200);
            });

            $freshCart = collect($updatedCart)
                ->map(fn($item) => json_decode($item, true))
                ->values();
        }

        return Inertia::render('Shop/Checkout', [
            'freshCart'   => $freshCart ?? [],
            'shippingFee' => 150.00,
        ]);
    }

    public static function validateStepOne(Request $request) {

        $freshItems = collect($request->cart)->map(function ($item) {
            $product = Product::find($item['product']['id']);
            return [
                'id'        => $product->id,
                'price'     => $product->price,
                'name'      => $product->name,
                'quantity'  => $item['quantity']
            ];
        });

        return Inertia::render('Shop/Checkout', [
            'freshCart'   => $freshItems,
            'shippingFee' => 150.00,
        ]);
    }

}
