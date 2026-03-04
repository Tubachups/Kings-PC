<?php

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;

test('admin can view delivered orders page', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['is_admin' => true])->save();

    $customer = User::factory()->create();

    foreach (range(1, 13) as $index) {
        Order::query()->create([
            'user_id' => $customer->id,
            'total' => 1999.99 + $index,
            'order_number' => 'KPC-'.Str::upper(Str::random(8)),
            'status' => 'Delivered',
            'payment_method' => 'cod',
            'payment_status' => 'Pending',
        ]);
    }

    Order::query()->create([
        'user_id' => $customer->id,
        'total' => 2999.99,
        'order_number' => 'KPC-'.Str::upper(Str::random(8)),
        'status' => 'Processing',
        'payment_method' => 'cod',
        'payment_status' => 'Pending',
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.products.deliveredOrders'));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Products/DeliveredOrders')
            ->has('orders.data', 12)
            ->where('orders.current_page', 1)
            ->where('orders.last_page', 2)
            ->where('orders.total', 13)
            ->where('orders.data.0.status', 'Delivered')
        );
});
