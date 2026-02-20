<?php

namespace App\Jobs;

use App\Models\CartItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

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
    }
}
