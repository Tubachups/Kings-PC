<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Redis;

class CheckoutService
{
    /**
     * @param  array<string, mixed>  $cart
     * @return array<int, mixed>
     */
    public function validateCart(string $redisKey, array $cart): array
    {
        if ($cart === []) {
            return [];
        }

        $decodedCart = [];

        foreach ($cart as $field => $value) {
            $decodedCart[$field] = $this->decodeValue($value);
        }

        $productIds = collect($decodedCart)
            ->map(fn (mixed $item): mixed => is_array($item) ? data_get($item, 'product.id') : null)
            ->filter(fn (mixed $id): bool => is_numeric($id))
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();

        $products = Product::query()
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $updatedCart = [];

        foreach ($decodedCart as $field => $item) {
            if (is_array($item)) {
                $productId = data_get($item, 'product.id');

                if (is_numeric($productId) && $products->has((int) $productId)) {
                    data_set($item, 'product.price', $products->get((int) $productId)?->price);
                }
            }

            $updatedCart[$field] = $this->encodeValue($item);
        }

        Redis::pipeline(function ($pipe) use ($redisKey, $updatedCart): void {
            $pipe->del($redisKey);

            foreach ($updatedCart as $field => $value) {
                $pipe->hset($redisKey, $field, $value);
            }

            $pipe->expire($redisKey, 43200);
        });

        return collect($updatedCart)
            ->map(fn (string $item): mixed => $this->decodeValue($item))
            ->values()
            ->all();
    }

    private function decodeValue(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $decoded = json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $value;
        }

        return $decoded;
    }

    private function encodeValue(mixed $value): string
    {
        $encoded = json_encode($value);

        if ($encoded === false) {
            return 'null';
        }

        return $encoded;
    }
}

