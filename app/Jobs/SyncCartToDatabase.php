<?php

namespace App\Jobs;

use App\Models\CartItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class SyncCartToDatabase implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $user_id,
        public int $id,
        public int $quantity
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        CartItem::addToCart($this->user_id, $this->id, $this->quantity);

        $currentDbState = CartItem::getCartForRedis($this->user_id);

        $redisKey = "cart:user:" . $this->user_id;

        $currentDbState = CartItem::getCartForRedis($this->user_id);

        Redis::del($redisKey);

        if (!empty($currentDbState)) {
            Redis::pipeline(function ($pipe) use ($redisKey, $currentDbState) {
                $pipe->del($redisKey);
                foreach ($currentDbState as $field => $value) {
                    $pipe->hset($redisKey, $field, $value);
                }
                $pipe->expire($redisKey, 43200);
            });
        }
    }
}
