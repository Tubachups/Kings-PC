<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total',
        'order_number',
        'status',
        'shipping_address',
        'billing_address',
        'payment_method',
        'payment_status'
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'billing_address' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function createOrder($request)
    {
        return self::create([
                'user_id'           => Auth::id(),
                'status'            => "Order Placed",
                'total'             => 0,
                'order_number' => "KPC - " . strtoupper(Str::random(8)),
                'shipping_address'  => [
                    'address'   => $request->address,
                    'region'    => $request->region,
                    'province'  => $request->province,
                    'city'      => $request->city,
                    'barangay'  => $request->barangay,
                ],
                'billing_address'   => [
                    'address'   => $request->address,
                    'region'    => $request->region,
                    'province'  => $request->province,
                    'city'      => $request->city,
                    'barangay'  => $request->barangay,
                ],
                'payment_method'    => $request->payment_method,
                'payment_status'    => $request->payment_method === "cod" ? "Pending" : "Paid", 
            ]);
    }
}
