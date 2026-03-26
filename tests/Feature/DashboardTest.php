<?php

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('dashboard includes order status cards counts', function () {
    Carbon::setTestNow('2026-03-26 10:00:00');

    $admin = User::factory()->create();
    $admin->forceFill(['is_admin' => true])->save();

    $customer = User::factory()->create();

    Order::query()->create([
        'user_id' => $customer->id,
        'total' => 999.99,
        'order_number' => 'KPC-'.Str::upper(Str::random(8)),
        'status' => 'Pending',
        'payment_method' => 'cod',
        'payment_status' => 'Pending',
    ]);

    Order::query()->create([
        'user_id' => $customer->id,
        'total' => 1099.99,
        'order_number' => 'KPC-'.Str::upper(Str::random(8)),
        'status' => 'Order Placed',
        'payment_method' => 'cod',
        'payment_status' => 'Pending',
    ]);

    Order::query()->create([
        'user_id' => $customer->id,
        'total' => 1199.99,
        'order_number' => 'KPC-'.Str::upper(Str::random(8)),
        'status' => 'Processing',
        'payment_method' => 'cod',
        'payment_status' => 'Pending',
        'created_at' => now()->copy()->subMonths(1),
        'updated_at' => now()->copy()->subMonths(1),
    ]);

    Order::query()->create([
        'user_id' => $customer->id,
        'total' => 1299.99,
        'order_number' => 'KPC-'.Str::upper(Str::random(8)),
        'status' => 'Shipped',
        'payment_method' => 'cod',
        'payment_status' => 'Pending',
        'created_at' => now()->copy()->subMonths(1),
        'updated_at' => now()->copy()->subMonths(1),
    ]);

    Order::query()->create([
        'user_id' => $customer->id,
        'total' => 1399.99,
        'order_number' => 'KPC-'.Str::upper(Str::random(8)),
        'status' => 'Delivered',
        'payment_method' => 'cod',
        'payment_status' => 'Pending',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('dashboard'));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('pendingOrdersCount', 2)
            ->where('processedOrdersCount', 1)
            ->where('shippedOrdersCount', 1)
            ->where('deliveredOrdersCount', 1)
            ->has('salesChart.labels', 12)
            ->has('salesChart.revenues', 12)
            ->has('salesChart.orders', 12)
            ->where('salesChart.totalRevenue', 3899.97)
            ->where('salesChart.totalSales', 3)
        );

    Carbon::setTestNow();
});
