<?php

namespace App\Jobs;

use App\Models\CartItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

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
        

    }
}
