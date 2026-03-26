<?php

use App\Models\Order;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('admins can view the customer management page with credential summaries', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $customer = User::factory()->create([
        'name' => 'Jamie Customer',
        'email' => 'jamie@example.com',
        'google_id' => 'google-123',
        'fb_id' => null,
        'email_verified_at' => now(),
        'two_factor_confirmed_at' => now(),
    ]);

    Order::query()->create([
        'user_id' => $customer->id,
        'total' => 5500,
        'order_number' => 'KPC-CUST0001',
        'status' => 'Delivered',
        'payment_method' => 'cod',
        'payment_status' => 'Paid',
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.customers.index'));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/customers/Index')
            ->where('summary.totalCustomers', 1)
            ->where('summary.verifiedCustomers', 1)
            ->where('summary.customersWithOrders', 1)
            ->has('customers.data', 1)
            ->where('customers.data.0.name', 'Jamie Customer')
            ->where('customers.data.0.email', 'jamie@example.com')
            ->where('customers.data.0.credentials.password_set', true)
            ->where('customers.data.0.credentials.google_linked', true)
            ->where('customers.data.0.credentials.facebook_linked', false)
            ->where('customers.data.0.credentials.email_verified', true)
            ->where('customers.data.0.credentials.two_factor_enabled', true)
            ->where('customers.data.0.orders_count', 1)
            ->where('customers.data.0.delivered_spend', 5500.0)
        );
});

test('customers are ordered alphabetically by default and can be sorted descending by customer name', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    User::factory()->create([
        'name' => 'Zara Customer',
        'email' => 'zara@example.com',
    ]);

    User::factory()->create([
        'name' => 'Alex Customer',
        'email' => 'alex@example.com',
    ]);

    User::factory()->create([
        'name' => 'Mia Customer',
        'email' => 'mia@example.com',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.customers.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('filters.sort', 'name')
            ->where('filters.direction', 'asc')
            ->where('customers.data.0.name', 'Alex Customer')
            ->where('customers.data.1.name', 'Mia Customer')
            ->where('customers.data.2.name', 'Zara Customer')
        );

    $this->actingAs($admin)
        ->get(route('admin.customers.index', [
            'sort' => 'name',
            'direction' => 'desc',
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('filters.sort', 'name')
            ->where('filters.direction', 'desc')
            ->where('customers.data.0.name', 'Zara Customer')
            ->where('customers.data.1.name', 'Mia Customer')
            ->where('customers.data.2.name', 'Alex Customer')
        );
});

test('non admins cannot view the customer management page', function () {
    $customer = User::factory()->create();

    $this->actingAs($customer)
        ->get(route('admin.customers.index'))
        ->assertForbidden();
});
