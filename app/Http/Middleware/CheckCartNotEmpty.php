<?php

namespace App\Http\Middleware;

use App\Models\CartItem;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class CheckCartNotEmpty
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = Auth::id();
        $cart = Redis::hgetall("cart:user:{$userId}");

        if (empty($cart)) {
            $hasDbCartItems = CartItem::where('user_id', $userId)->exists();

            if ($hasDbCartItems) {
                return $next($request);
            }

            return redirect()->route('shop')
                    ->with('error', 'Your cart is empty! Add items to your cart first.');
        }
        
        return $next($request);
    }
}
