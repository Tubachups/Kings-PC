<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class CartItem extends Model
{
    //
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'variant',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public static function addToCart($user_id, $product_id, $quantity)
    {
        Log::alert('asdasdada');
        return self::updateOrCreate(
            ['user_id' => $user_id, 'product_id' => $product_id],
            ['quantity' => $quantity]
        );
    }

    public static function removeFromCart($user_id, $product_id)
    {
        Log::info("SQL Attempt: User $user_id, Product $product_id");
        return self::where('user_id', $user_id)
                    ->where('product_id', $product_id)
                    ->delete();
    }
}
