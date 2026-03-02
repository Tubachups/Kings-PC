<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CheckoutController;


Route::get('/', [CheckoutController::class, 'index'])->name('index');
Route::post('/confirm', [CheckoutController::class, 'checkoutConfirm'])->name('confirm');
