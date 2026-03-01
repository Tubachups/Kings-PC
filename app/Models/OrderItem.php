<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price', //snapshot price of the item on checkout time
        'variant',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public static function createOrderItem($order_id, $item)
    {
        return self::create([
                    'order_id'      => $order_id,
                    'product_id'    => $item['product']['id'],
                    'unit_price'    => $item['product']['price'],
                    'quantity'      => $item['quantity'],
                    'variant'       => null,
                ]);
    }
}
