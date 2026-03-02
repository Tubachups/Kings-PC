<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Redis;

class CheckoutService
{
    public function validateCart($redisKey, $cart)
    {
        $freshCart = [];
        if (! empty($cart)) {
            $updatedCart = [];

            foreach ($cart as $field => $value) {
                $item = json_decode($value, true);
                $product = Product::find($item['product']['id']);

                if ($product) {
                    $item['product']['price'] = $product->price;
                }

                $updatedCart[$field] = json_encode($item);
            }

            Redis::pipeline(function ($pipe) use ($redisKey, $updatedCart) {
                $pipe->del($redisKey);
                foreach ($updatedCart as $field => $value) {
                    $pipe->hset($redisKey, $field, $value);
                }
                $pipe->expire($redisKey, 43200);
            });

            $freshCart = collect($updatedCart)
                ->map(fn ($item) => json_decode($item, true))
                ->values();
        }

        return $freshCart;
    }
}
