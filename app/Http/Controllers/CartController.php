<?php

namespace App\Http\Controllers;

use App\Jobs\RemoveCartFromJob;
use App\Jobs\SyncCartToDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Inertia\Inertia;

class CartController extends Controller
{

    public function index()
    {
        return Inertia::render('Shop/Cart', []);
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
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'required|integer|min:1|max:99',
        ]);

        $userId = Auth::id() ?? 1;
        Log::alert('asdasdas');

        Redis::hset("cart:user:" . $userId, $validated['product_id'], $validated['quantity']);
        SyncCartToDatabase::dispatch($userId, $validated['product_id'], $validated['quantity']);

        return back()->with('success', 'Item added to cart.');
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
