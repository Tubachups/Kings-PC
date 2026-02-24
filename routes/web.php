<?php

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ShopController;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard', [
            'totalProducts' => Product::count(),
            'lowStockCount' => Product::where('stock', '<', 10)->count(),
            'totalCustomers' => User::where('is_admin', false)->count(),
        ]);
    })->name('dashboard');

});

// Admin only
Route::middleware(['auth', 'verified', 'is_admin'])
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

Route::get('/checkout', function () {
    return Inertia::render('Shop/Checkout');
});
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
    // Route::get('/redis', 'redis');
    Route::get('/{category:slug}', 'showByCategory')->name('shop.category');
});

require __DIR__.'/settings.php';
