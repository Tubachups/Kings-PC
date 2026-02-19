<?php

namespace App\Jobs;

use App\Models\CartItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
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
        $currentDbState = CartItem::addToCart($this->user_id, $this->id, $this->quantity);
        Redis::del("cart:user:" . $this->user_id);
        if (!empty($currentDbState)) {
            Redis::hmset("cart:user:" . $this->user_id, $currentDbState);
        }
    }
}
