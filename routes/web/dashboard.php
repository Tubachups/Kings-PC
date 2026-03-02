<?php

use App\Models\Product;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;


Route::get('/dashboard', function () {
        return Inertia::render('Dashboard', [
            'totalProducts' => Product::count(),
            'lowStockCount' => Product::where('stock', '<', 10)->count(),
            'totalCustomers' => User::where('is_admin', false)->count(),
        ]);
    })->name('dashboard');