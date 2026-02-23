<?php

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\FacebookController;
use App\Models\Product;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Models\User;

Route::get('/home', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
        'totalProducts' => Product::count(),
        'lowStockCount' => Product::where('stock', '<', 10)->count(),
        'totalCustomers' => User::where('is_admin', false)->count(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin only
Route::middleware(['auth', 'verified', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('products', ProductController::class);
});

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
    Route::get('/redis', 'redis');
    Route::get('/{category:slug}', 'showByCategory')->name('shop.category');
});


require __DIR__.'/settings.php';
