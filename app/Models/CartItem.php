<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


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

    public static function addToCart($user_id, $id, $quantity)
    {
        return self::updateOrCreate(
            ['user_id' => $user_id, 'product_id' => $id],
            ['quantity' => $quantity]
        );
        
    }

    public static function removeFromCart($user_id, $product_id)
    {
        return self::where('user_id', $user_id)
                    ->where('product_id', $product_id)
                    ->delete();
    }
}
