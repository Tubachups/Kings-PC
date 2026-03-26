<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->string('search'));
        $perPage = (int) $request->integer('per_page', 12);
        $perPage = max(6, min($perPage, 48));
        $sort = (string) $request->string('sort', 'name');
        $direction = strtolower((string) $request->string('direction', 'asc'));

        $allowedSorts = [
            'name',
            'orders_count',
            'delivered_spend',
            'created_at',
        ];

        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'name';
        }

        if (! in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'asc';
        }

        $customers = User::query()
            ->where('is_admin', false)
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($customerQuery) use ($search): void {
                    $customerQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->withCount('orders')
            ->withSum(['orders as delivered_spend' => function ($query): void {
                $query->where('status', 'Delivered');
            }], 'total')
            ->orderBy($sort, $direction)
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString()
            ->through(function (User $customer): array {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'orders_count' => $customer->orders_count,
                    'delivered_spend' => (float) ($customer->delivered_spend ?? 0),
                    'created_at' => $customer->created_at?->toDateTimeString(),
                    'credentials' => [
                        'password_set' => filled($customer->password),
                        'google_linked' => filled($customer->google_id),
                        'facebook_linked' => filled($customer->fb_id),
                        'email_verified' => $customer->email_verified_at !== null,
                        'two_factor_enabled' => $customer->two_factor_confirmed_at !== null,
                    ],
                ];
            });

        return Inertia::render('admin/customers/Index', [
            'customers' => $customers,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction,
            ],
            'summary' => [
                'totalCustomers' => User::query()->where('is_admin', false)->count(),
                'verifiedCustomers' => User::query()
                    ->where('is_admin', false)
                    ->whereNotNull('email_verified_at')
                    ->count(),
                'customersWithOrders' => User::query()
                    ->where('is_admin', false)
                    ->whereHas('orders')
                    ->count(),
            ],
        ]);
    }
}
