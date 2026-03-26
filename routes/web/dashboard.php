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
    $user = request()->user();
    $customerAddresses = collect();
    $customerOrders = collect();
    $customerWishlist = collect();

    if ($user && ! $user->is_admin) {
        $customerAddresses = $user->addresses()
            ->orderByDesc('is_default')
            ->latest('id')
            ->get([
                'id',
                'label',
                'full_name',
                'address',
                'region',
                'province',
                'city',
                'barangay',
                'is_default',
            ]);

        $customerOrders = $user->orders()
            ->latest('id')
            ->limit(8)
            ->get([
                'id',
                'total',
                'status',
                'created_at',
            ]);

        $customerWishlist = Product::query()
            ->whereHas('wishlistItems', function ($query) use ($user): void {
                $query->where('user_id', $user->id);
            })
            ->with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    $orderStatusCounts = $pendingOrderService->getDashboardOrderStatusCounts();
    $salesStartMonth = now()->startOfMonth()->subMonths(11);
    $salesSeries = Order::query()
        ->selectRaw("DATE_FORMAT(created_at, '%Y-%m-01') as order_month")
        ->selectRaw('COUNT(*) as total_orders')
        ->selectRaw('COALESCE(SUM(total), 0) as total_revenue')
        ->whereIn('status', ['Processing', 'Shipped', 'Delivered'])
        ->where('created_at', '>=', $salesStartMonth)
        ->groupBy('order_month')
        ->orderBy('order_month')
        ->get()
        ->keyBy(fn (Order $order) => Carbon::parse($order->order_month)->startOfMonth()->toDateString());

    $chartMonths = collect(CarbonPeriod::create($salesStartMonth, '1 month', now()->startOfMonth()))
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
            'labels' => $chartMonths
                ->map(fn (Carbon $date) => $date->format('M Y'))
                ->values()
                ->all(),
            'revenues' => $chartMonths
                ->map(fn (Carbon $date) => (float) ($salesSeries->get($date->startOfMonth()->toDateString())?->total_revenue ?? 0))
                ->values()
                ->all(),
            'orders' => $chartMonths
                ->map(fn (Carbon $date) => (int) ($salesSeries->get($date->startOfMonth()->toDateString())?->total_orders ?? 0))
                ->values()
                ->all(),
            'totalRevenue' => (float) $chartMonths
                ->sum(fn (Carbon $date) => (float) ($salesSeries->get($date->startOfMonth()->toDateString())?->total_revenue ?? 0)),
            'totalSales' => (int) $chartMonths
                ->sum(fn (Carbon $date) => (int) ($salesSeries->get($date->startOfMonth()->toDateString())?->total_orders ?? 0)),
        ],
        'customerSummary' => [
            'ordersCount' => $customerOrders->count(),
            'addressCount' => $customerAddresses->count(),
            'wishlistCount' => $customerWishlist->count(),
        ],
        'customerData' => [
            'addresses' => $customerAddresses,
            'orders' => $customerOrders,
            'wishlist' => $customerWishlist,
        ],
    ]);
})->name('dashboard');
