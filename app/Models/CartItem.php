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

    public static function getCartForRedis($user_id)
    {
        $items = self::where('user_id', $user_id)->with('product')->get();

        $payload = [];
        foreach ($items as $item) {
            $payload[$item->product_id] = json_encode([
                'quantity'  => $item->quantity,
                'product'   => [
                    'id'        => $item->product->id,
                    'name'      => $item->product->name,
                    'price'     => $item->product->price,
                    'image_url' => $item->product->image_url,
                ]
            ]);
        }

        return $payload;
    }

    public static function removeFromCart($user_id, $product_id)
    {
        return self::where('user_id', $user_id)
                    ->where('product_id', $product_id)
                    ->delete();
    }

    public static function clearCart($user_id)
    {
        return self::where('user_id', $user_id)
                    ->delete();
    }
}
