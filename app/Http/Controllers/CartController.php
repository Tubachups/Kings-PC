<?php

namespace App\Http\Controllers;

use App\Jobs\ClearCart;
use App\Jobs\RemoveCartFromJob;
use App\Jobs\SyncCartToDatabase;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Inertia\Inertia;

class CartController extends Controller
{

    public function index()
    {
        return Inertia::render('Shop/Cart', [
        ]);
    }


    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        $start = microtime(true);
        $user_id = Auth::id() ?? 1;
        $quantity = (int) $request->input('quantity');
        Redis::hset("cart:user:" . $user_id, $id, $quantity);
        SyncCartToDatabase::dispatch($user_id, $id, $quantity);
        $end = microtime(true);
        $executionTime = $end - $start;
        Log::info('Cart update execution time: ' . $executionTime . ' seconds');
        return back();
    }


    public function destroy(Request $request, string $id)
    {
        $start = microtime(true);
        $user_id = Auth::id() ?? 1;
        $product_id = (int) $id;

        Redis::hdel("cart:user:{$user_id}", (string) $product_id);
        
        RemoveCartFromJob::dispatch($user_id, $product_id);

        $end = microtime(true);
        Log::info('Cart delete execution time: ' . ($end - $start) . ' seconds');
        
        return back();
    }

    public function clear()
    {
        $user_id = Auth::id();
        Redis::del("cart:user:{$user_id}");
        ClearCart::dispatch($user_id);

        return back();
    }
}
