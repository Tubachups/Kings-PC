<?php

namespace App\Jobs;

use App\Models\CartItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SyncCartToDatabase implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $user_id,
        public int $product_id,
        public int $quantity
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::alert("message");
        CartItem::addToCart($this->user_id, $this->product_id, $this->quantity);
    }
}
