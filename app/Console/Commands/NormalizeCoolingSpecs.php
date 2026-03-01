<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class NormalizeCoolingSpecs extends Command
{
    protected $signature = 'pc:normalize-cooling';
    protected $description = 'Injects universal socket compatibility into AIO cooling specs';

    public function handle()
    {
        $coolingCategoryId = 8; 

        $coolers = Product::where('category_id', $coolingCategoryId)->get();
        $updated = 0;

        foreach ($coolers as $cooler) {
            $specs = $cooler->specs;
            
            if (isset($specs['radiator_size'])) {
                $specs['supported_sockets'] = [
                    'AM4', 
                    'AM5', 
                    'LGA1700', 
                    'LGA1851'
                ];
                
                $specs['type'] = 'liquid';
                
                $cooler->specs = $specs;
                $cooler->save();
                $updated++;
            }
        }

        $this->info("Successfully updated {$updated} coolers with universal socket support.");
    }
}