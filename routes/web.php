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
Route::get('/auth/google/redirect', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

// Testimonials
Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');

// All guests can access the shop and category pages, so we don't apply any auth middleware here.
Route::get('/', [ShopController::class, 'index'])->name('shop');
Route::get('/components', [ShopController::class, 'components'])->name('components');
Route::get('/contacts', [ShopController::class, 'contacts'])->name('contacts');
Route::get('/redis', [ShopController::class, 'redis']);
Route::get ('/{category:slug}', [ShopController::class, 'showByCategory'])->name('shop.category');


require __DIR__.'/settings.php';
