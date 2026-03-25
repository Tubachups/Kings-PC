<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Admin\PendingOrderService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/dashboard', function (PendingOrderService $pendingOrderService) {

    $orderStatusCounts = $pendingOrderService->getDashboardOrderStatusCounts();
    $salesStartDate = now()->startOfDay()->subDays(6);
    $salesSeries = Order::query()
        ->selectRaw('DATE(created_at) as order_date')
        ->selectRaw('COUNT(*) as total_orders')
        ->selectRaw('COALESCE(SUM(total), 0) as total_revenue')
        ->whereIn('status', ['Processing', 'Shipped', 'Delivered'])
        ->where('created_at', '>=', $salesStartDate)
        ->groupBy('order_date')
        ->orderBy('order_date')
        ->get()
        ->keyBy(fn (Order $order) => Carbon::parse($order->order_date)->toDateString());

    $chartDates = collect(CarbonPeriod::create($salesStartDate, now()->startOfDay()))
        ->map(fn (Carbon $date) => $date->copy());

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
        'salesChart' => [
            'labels' => $chartDates
                ->map(fn (Carbon $date) => $date->format('M d'))
                ->values()
                ->all(),
            'revenues' => $chartDates
                ->map(fn (Carbon $date) => (float) ($salesSeries->get($date->toDateString())?->total_revenue ?? 0))
                ->values()
                ->all(),
            'orders' => $chartDates
                ->map(fn (Carbon $date) => (int) ($salesSeries->get($date->toDateString())?->total_orders ?? 0))
                ->values()
                ->all(),
            'totalRevenue' => (float) $chartDates
                ->sum(fn (Carbon $date) => (float) ($salesSeries->get($date->toDateString())?->total_revenue ?? 0)),
            'totalSales' => (int) $chartDates
                ->sum(fn (Carbon $date) => (int) ($salesSeries->get($date->toDateString())?->total_orders ?? 0)),
        ],
    ]);
})->name('dashboard');
