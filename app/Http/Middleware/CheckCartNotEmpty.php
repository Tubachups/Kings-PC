<?php

namespace App\Http\Middleware;

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
        $user = Auth::id();
        $cart = Redis::hgetall("cart:user:" . $user);

        if(empty($cart)){
            return redirect()->route('cart.index')
                    ->with('error', 'Your cart is empty! Add items to your cart first.');
        }
        
        return $next($request);
    }
}
