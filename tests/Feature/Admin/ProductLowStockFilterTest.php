<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('admins can filter products by low stock', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $category = Category::query()->create([
        'name' => 'Graphics Cards',
        'slug' => 'graphics-cards',
    ]);

    Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Low Stock GPU',
        'slug' => 'low-stock-gpu',
        'description' => 'Low stock product',
        'price' => 12999,
        'stock' => 4,
        'image_url' => 'products/low-stock-gpu.jpg',
        'is_active' => true,
    ]);

    Product::query()->create([
        'category_id' => $category->id,
        'name' => 'Healthy Stock GPU',
        'slug' => 'healthy-stock-gpu',
        'description' => 'Healthy stock product',
        'price' => 15999,
        'stock' => 18,
        'image_url' => 'products/healthy-stock-gpu.jpg',
        'is_active' => true,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.products.index', ['low_stock' => 1]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/products/Index')
            ->where('filters.low_stock', true)
            ->has('products.data', 1)
            ->where('products.data.0.name', 'Low Stock GPU')
            ->where('products.data.0.stock', 4)
        );
});
