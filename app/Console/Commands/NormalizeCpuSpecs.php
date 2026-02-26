<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class NormalizeCpuSpecs extends Command
{
    protected $signature = 'app:normalize-cpu-specs';
    protected $description = 'Injects socket and RAM compatibility into CPU specs';

    public function handle()
    {
        $mappings = [
            // AMD: Strict 1-to-1 mapping
            'Zen 2' => ['socket' => 'AM4', 'ram_type' => ['DDR4']],
            'Zen 3' => ['socket' => 'AM4', 'ram_type' => ['DDR4']],
            'Zen 4' => ['socket' => 'AM5', 'ram_type' => ['DDR5']],
            'Zen 5' => ['socket' => 'AM5', 'ram_type' => ['DDR5']],
            
            // Intel: LGA1700 supports both, so we provide an array
            'Coffee Lake' => ['socket' => 'LGA1151', 'ram_type' => ['DDR4']],
            'Comet Lake'  => ['socket' => 'LGA1200', 'ram_type' => ['DDR4']],
            'Rocket Lake' => ['socket' => 'LGA1200', 'ram_type' => ['DDR4']],
            'Alder Lake'  => ['socket' => 'LGA1700', 'ram_type' => ['DDR4', 'DDR5']],
            'Raptor Lake' => ['socket' => 'LGA1700', 'ram_type' => ['DDR4', 'DDR5']],
        ];

        // Ensure category_id matches your DB (usually 1 for CPU, 2 for Mobo, etc.)
        $cpus = Product::where('category_id', 2)->get(); 
        $updated = 0;

        foreach ($cpus as $cpu) {
            $specs = $cpu->specs;
            $arch = $specs['microarchitecture'] ?? null;

            if ($arch && isset($mappings[$arch])) {
                // Update Socket
                $specs['socket'] = $mappings[$arch]['socket'];
                
                // Update RAM Type (Stores as an array to handle Intel Hybrid support)
                $specs['ram_support'] = $mappings[$arch]['ram_type'];
                
                $cpu->specs = $specs;
                $cpu->save();
                $updated++;
            }
        }

        $this->info("Done! Updated {$updated} CPUs with Socket and RAM compatibility.");
    }
}