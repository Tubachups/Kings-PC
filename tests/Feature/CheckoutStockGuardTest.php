<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Redis;

test('checkout confirmation fails when requested quantity exceeds stock', function () {
    $user = User::factory()->create();

    $category = Category::query()->create([
        'name' => 'Processors',
        'slug' => 'processors',
    ]);

    $product = Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Ryzen 7 7700',
        'slug' => 'ryzen-7-7700',
        'description' => 'Sample CPU',
        'price' => 14999,
        'stock' => 2,
        'specs' => ['cores' => 8],
        'is_active' => true,
    ]);

    $redisKey = 'cart:user:'.$user->id;

    Redis::hset($redisKey, (string) $product->id, json_encode([
        'quantity' => 3,
        'product' => [
            'id' => $product->id,
            'price' => $product->price,
        ],
    ]));

    $response = $this
        ->actingAs($user)
        ->from(route('checkout.index'))
        ->post(route('checkout.confirm'), [
            'full_name' => 'Test Customer',
            'address' => '123 Sample Street',
            'region' => 'NCR',
            'province' => '',
            'city' => 'Quezon City',
            'barangay' => 'Bagumbayan',
            'payment_method' => 'cod',
        ]);

    $response
        ->assertRedirect(route('checkout.index'))
        ->assertSessionHasErrors('cart');

    expect(Product::query()->findOrFail($product->id)->stock)->toBe(2);
    $this->assertDatabaseCount('orders', 0);
    $this->assertDatabaseCount('order_items', 0);

    Redis::del($redisKey);
});
