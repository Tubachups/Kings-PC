<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Redis;
use App\Models\CartItem;

class ClearCart implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $user_id,
    ){}


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        CartItem::clearCart($this->user_id);
        $redisKey = "cart:user:{$this->user_id}";
        Redis::del($redisKey); 
    }
}
