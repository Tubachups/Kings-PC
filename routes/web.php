<?php

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/home', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin only
Route::middleware(['auth', 'verified', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('products', ProductController::class);
});

// All guests can access the shop and category pages, so we don't apply any auth middleware here.
Route::get('/', [ShopController::class, 'index'])->name('shop');
Route::get('/components', [ShopController::class, 'components'])->name('components');
Route::get ('/{category:slug}', [ShopController::class, 'showByCategory'])->name('shop.category');


require __DIR__.'/settings.php';
