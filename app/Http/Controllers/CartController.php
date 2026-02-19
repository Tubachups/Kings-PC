<?php

namespace App\Http\Controllers;

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
            'cart_items' => Inertia::lazy(fn () => CartItem::where('user_id', Auth::id())->with('product')->get())
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

        Redis::hset("cart:user:" . $user_id, $id, $request->quantity);
        SyncCartToDatabase::dispatch($user_id, $id, $request->quantity);

        $end = microtime(true);
        $executionTime = $end - $start;

        Log::info('Cart update execution time: ' . $executionTime . ' seconds');
        return back();
    }


    public function destroy(Request $request, string $id)
    {
        $user_id = Auth::id() ?? 1;
        $product_id = $request->product_id ?? $id;
        Redis::hdel("cart:user:{$user_id}", $product_id);
        RemoveCartFromJob::dispatch($user_id, $product_id);
        return back()->with('success', 'Item removed from cart.');
    }
}
