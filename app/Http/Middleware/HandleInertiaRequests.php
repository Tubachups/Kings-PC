<?php

namespace App\Http\Middleware;

use App\Models\CartItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'categories' => cache()->remember('global_categories', 3600, function () {
                return Category::select('id', 'name', 'slug')->get();
            }),
            'cart' => function () {
                if(!Auth::check()) return [];

                $user_id = Auth::id();
                $redisKey = "cart:user:" . $user_id;

                $redisCart = Redis::hgetall($redisKey);

                if(empty($redisCart)) {
                    $dbItems = CartItem::where('user_id', $user_id)
                        ->pluck('quantity', 'product_id')   
                        ->toArray();
                    
                    if(!empty($dbItems)) {

                        Redis::hmset($redisKey, $dbItems);

                        return array_map('intval', $dbItems);
                    }
                    return [];
                }

                return array_map('intval', $redisCart);
            }
        ];
    }
}
