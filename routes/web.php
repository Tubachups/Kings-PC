<?php

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ShopController;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard Route
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard', [
            'totalProducts' => Product::count(),
            'lowStockCount' => Product::where('stock', '<', 10)->count(),
            'totalCustomers' => User::where('is_admin', false)->count(),
        ]);
    })->name('dashboard');

    // Admin Routes
    Route::middleware('is_admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // 1. Custom Routes (Must come BEFORE the resource route)
            Route::controller(ProductController::class)
                ->prefix('products')
                ->name('products.')
                ->group(function () {

                    // Static URIs
                    Route::get('archived', 'archived')->name('archived');
                    Route::post('bulk-archive', 'bulkArchive')->name('bulkArchive');
                    Route::post('bulk-update-status', 'bulkUpdateStatus')->name('bulkUpdateStatus');
                    Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
                    Route::post('bulk-force-delete', 'bulkForceDelete')->name('bulkForceDelete');

                    // Dynamic URIs (with parameters)
                    Route::patch('{product}/status', 'updateStatus')->name('updateStatus');
                    Route::patch('{id}/restore', 'restore')->name('restore');
                    Route::delete('{id}/force-delete', 'forceDelete')->name('forceDelete');
                });

            // 2. Standard CRUD Resource Route
            Route::resource('products', ProductController::class);

        });

});

Route::middleware(['auth', 'cart.not_empty'])->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/confirm', [CheckoutController::class, 'checkoutConfirm'])->name('confirm');
});
Route::get('/orders', [OrdersController::class, 'index'])->name('orders');
Route::delete('cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::resource('cart', CartController::class);

// Google OAuth routes
Route::controller(GoogleController::class)->group(function () {
    Route::get('/auth/google/redirect', 'redirectToGoogle')->name('google.redirect');
    Route::get('/auth/google/callback', 'handleGoogleCallback')->name('google.callback');
});

// Testimonials
Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');

// Shop (guest) routes
Route::controller(ShopController::class)->group(function () {
    Route::get('/', 'index')->name('shop');
    Route::get('/contacts', 'contacts')->name('contacts');
    Route::get('/components', 'components')->name('components');
    Route::get('/builds', 'builds')->name('builds');
    Route::get('/builder', 'builder')->name('builder');
    Route::post('/builder/ai', 'generate')->name('builder.ai');
    // Route::get('/redis', 'redis');
    Route::get('/{category:slug}', 'showByCategory')->name('shop.category');
    Route::get('/builds/{buildPost}/image/{slot}', 'buildImage')->whereNumber('slot')->name('builds.image');
});

require __DIR__.'/settings.php';
