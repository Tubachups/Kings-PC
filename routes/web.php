<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->group(base_path('routes/web/dashboard.php'));

// Admin Routes
Route::middleware(['auth', 'verified', 'is_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(base_path('routes/web/admin/products.php'));


// Checkout
Route::middleware(['auth', 'cart.not_empty'])
    ->prefix('checkout')
    ->name('checkout.')
    ->group(base_path('routes/web/checkout.php'));

require __DIR__.'/web/cart.php';
require __DIR__.'/web/order.php';
require __DIR__.'/web/reviews.php';
require __DIR__.'/web/shop.php';
require __DIR__.'/web/socialite.php';

require __DIR__.'/settings.php';
