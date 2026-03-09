<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Admin\PendingOrderService;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/dashboard', function (PendingOrderService $pendingOrderService) {

    $orderStatusCounts = $pendingOrderService->getDashboardOrderStatusCounts();

    return Inertia::render('Dashboard', [
        'totalProducts' => Product::count(),
        'totalOrders' => Order::where('status', '!=', 'pending')->count(),
        'lowStockCount' => Product::where('stock', '<', 10)->count(),
        'totalCustomers' => User::where('is_admin', false)->count(),
        'pendingOrdersCount' => $orderStatusCounts['pending'],
        'processedOrdersCount' => $orderStatusCounts['processing'],
        'shippedOrdersCount' => $orderStatusCounts['shipped'],
        'deliveredOrdersCount' => $orderStatusCounts['delivered'],
        'pendingCustomerOrders' => $pendingOrderService->getPendingOrders(5),
    ]);
})->name('dashboard');
