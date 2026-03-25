<?php

use App\Http\Controllers\Admin\CustomerController;
use Illuminate\Support\Facades\Route;

Route::controller(CustomerController::class)
    ->prefix('customers')
    ->name('customers.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
    });
