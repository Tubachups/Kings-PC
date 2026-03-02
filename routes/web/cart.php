<?php
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;



Route::delete('cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::resource('cart', CartController::class);
