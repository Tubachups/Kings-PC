<?php

use App\Http\Controllers\OrdersController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/orders', [OrdersController::class, 'index'])->name('orders');
});
