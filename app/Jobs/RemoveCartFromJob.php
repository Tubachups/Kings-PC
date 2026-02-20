<?php

namespace App\Jobs;

use App\Models\CartItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Redis;

class RemoveCartFromJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $user_id,
        public int $product_id
    ){}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        CartItem::removeFromCart($this->user_id, $this->product_id);

        $redisKey = "cart:user:{$this->user_id}";
        $currentDbState = CartItem::getCartForRedis($this->user_id);

        Redis::pipeline(function ($pipe) use ($redisKey, $currentDbState) {
            $pipe->del($redisKey);
            foreach ($currentDbState as $field => $value) {
                $pipe->hset($redisKey, $field, $value);
            }
            if (!empty($currentDbState)) {
                $pipe->expire($redisKey, 43200);
            }
        });
    }
}
