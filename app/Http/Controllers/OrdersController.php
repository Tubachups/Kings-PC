<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrdersController extends Controller
{
    //
    public function index()
    {
        return Inertia::render('Shop/Order', [
            'orders' = Order::where('user_id', Auth::id())->with('orderItems')->get()
        ])
    }
}
