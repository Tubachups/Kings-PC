<?php

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Str;

test('admin can advance a pending order to processing', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['is_admin' => true])->save();

    $customer = User::factory()->create();

    $order = Order::query()->create([
        'user_id' => $customer->id,
        'total' => 1999.99,
        'order_number' => 'KPC-'.Str::upper(Str::random(8)),
        'status' => 'Order Placed',
        'payment_method' => 'cod',
        'payment_status' => 'Pending',
    ]);

    $response = $this
        ->actingAs($admin)
        ->patch(route('admin.products.advancePendingOrder', $order));

    $response->assertRedirect();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'Processing',
    ]);
});

test('admin can not advance an order that is already delivered', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['is_admin' => true])->save();

    $customer = User::factory()->create();

    $order = Order::query()->create([
        'user_id' => $customer->id,
        'total' => 2999.99,
        'order_number' => 'KPC-'.Str::upper(Str::random(8)),
        'status' => 'Delivered',
        'payment_method' => 'cod',
        'payment_status' => 'Pending',
    ]);

    $response = $this
        ->actingAs($admin)
        ->patch(route('admin.products.advancePendingOrder', $order));

    $response
        ->assertRedirect()
        ->assertSessionHas('error', 'Order is already at its final status.');

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'Delivered',
    ]);
});
