<?php

namespace App\Services\Admin;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PendingOrderService
{
    public function getDashboardOrderStatusCounts(): array
    {
        return [
            'pending' => Order::query()
                ->whereIn('status', ['Pending', 'Order Placed'])
                ->count(),
            'processing' => Order::query()
                ->where('status', 'Processing')
                ->count(),
            'shipped' => Order::query()
                ->where('status', 'Shipped')
                ->count(),
            'delivered' => Order::query()
                ->where('status', 'Delivered')
                ->count(),
        ];
    }

    public function getPendingOrders(int $limit = 0): Collection
    {
        $query = $this->baseOrderQuery()
            ->whereIn('status', ['Pending', 'Order Placed']);

        if ($limit > 0) {
            $query->limit($limit);
        }

        return $query
            ->get()
            ->map(fn (Order $order): array => $this->mapOrder($order))
            ->values();
    }

    public function getProcessedOrders(int $limit = 0): Collection
    {
        $query = $this->baseOrderQuery()
            ->where('status', 'Processing');

        if ($limit > 0) {
            $query->limit($limit);
        }

        return $query
            ->get()
            ->map(fn (Order $order): array => $this->mapOrder($order))
            ->values();
    }

    public function getShippedOrders(int $limit = 0): Collection
    {
        $query = $this->baseOrderQuery()
            ->where('status', 'Shipped');

        if ($limit > 0) {
            $query->limit($limit);
        }

        return $query
            ->get()
            ->map(fn (Order $order): array => $this->mapOrder($order))
            ->values();
    }

    public function getDeliveredOrders(int $limit = 0): Collection
    {
        $query = $this->baseOrderQuery()
            ->where('status', 'Delivered');

        if ($limit > 0) {
            $query->limit($limit);
        }

        return $query
            ->get()
            ->map(fn (Order $order): array => $this->mapOrder($order))
            ->values();
    }

    public function getPendingOrdersPaginated(int $perPage = 12): LengthAwarePaginator
    {
        return $this->baseOrderQuery()
            ->whereIn('status', ['Pending', 'Order Placed'])
            ->paginate($perPage)
            ->through(fn (Order $order): array => $this->mapOrder($order));
    }

    public function getProcessedOrdersPaginated(int $perPage = 12): LengthAwarePaginator
    {
        return $this->baseOrderQuery()
            ->where('status', 'Processing')
            ->paginate($perPage)
            ->through(fn (Order $order): array => $this->mapOrder($order));
    }

    public function getShippedOrdersPaginated(int $perPage = 12): LengthAwarePaginator
    {
        return $this->baseOrderQuery()
            ->where('status', 'Shipped')
            ->paginate($perPage)
            ->through(fn (Order $order): array => $this->mapOrder($order));
    }

    public function getDeliveredOrdersPaginated(int $perPage = 12): LengthAwarePaginator
    {
        return $this->baseOrderQuery()
            ->where('status', 'Delivered')
            ->paginate($perPage)
            ->through(fn (Order $order): array => $this->mapOrder($order));
    }

    protected function resolveNextStatus(?string $currentStatus): ?string
    {
        return match ($currentStatus) {
            'Pending', 'Order Placed' => 'Processing',
            'Processing' => 'Shipped',
            'Shipped' => 'Delivered',
            default => null,
        };
    }

    protected function resolveNextActionLabel(?string $currentStatus): ?string
    {
        return match ($currentStatus) {
            'Pending', 'Order Placed' => 'Mark as Processing',
            'Processing' => 'Mark as Shipped',
            'Shipped' => 'Mark as Delivered',
            default => null,
        };
    }

    protected function mapOrder(Order $order): array
    {
        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'total' => (float) $order->total,
            'status' => $order->status,
            'next_status' => $this->resolveNextStatus($order->status),
            'next_action_label' => $this->resolveNextActionLabel($order->status),
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'created_at' => $order->created_at?->toDateTimeString(),
            'items_count' => $order->order_items->sum('quantity'),
            'customer' => [
                'name' => $order->user?->name,
                'email' => $order->user?->email,
            ],
            'order_items' => $order->order_items->map(function ($item): array {
                return [
                    'id' => $item->id,
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'product' => [
                        'name' => $item->product?->name,
                        'image_url' => $item->product?->image_url,
                    ],
                ];
            })->values()->all(),
        ];
    }

    protected function baseOrderQuery(): Builder
    {
        return Order::query()
            ->with([
                'user:id,name,email',
                'order_items:id,order_id,product_id,quantity,unit_price',
                'order_items.product:id,name,image_url',
            ])
            ->latest()
            ->select(['id', 'user_id', 'order_number', 'total', 'status', 'payment_method', 'payment_status', 'created_at']);
    }
}
