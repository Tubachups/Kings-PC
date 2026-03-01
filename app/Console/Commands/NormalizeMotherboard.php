<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class NormalizeMotherboard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:normalize-motherboard';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // --- 1. ROLLBACK CPU MISTAKE ---
        $cpus = Product::where('category_id', 2)->get();
        foreach ($cpus as $cpu) {
            $specs = $cpu->specs;
            unset($specs['socket'], $specs['ram_support']); // Remove mistaken keys
            $cpu->update(['specs' => $specs]);
        }

        // --- 2. UPDATE MOTHERBOARD (The Correct way) ---
        $mobos = Product::where('category_id', 'motherboard')->get();
        foreach ($mobos as $mobo) {
            $specs = $mobo->specs;
            $name = strtoupper($mobo->name);

            // Map Socket from name
            if (str_contains($name, 'B550') || str_contains($name, 'X570')) $specs['socket'] = 'AM4';
            if (str_contains($name, 'B650') || str_contains($name, 'X870')) $specs['socket'] = 'AM5';
            if (str_contains($name, 'Z790') || str_contains($name, 'B760')) $specs['socket'] = 'LGA1700';

            // Map RAM Type
            $specs['ram_type'] = (str_contains($name, 'D4') || str_contains($name, 'DDR4')) ? 'DDR4' : 'DDR5';

            $mobo->update(['specs' => $specs]);
        }

        // --- 3. FIX GPU WATTAGE (Targeting specs->chipset) ---
        $gpus = Product::where('category_id', 6)->get();
        $updated = 0;

        foreach ($gpus as $gpu) {
            $specs = $gpu->specs;
            $chipset = $specs['chipset'] ?? ''; 
            $wattage = 200; // Default fallback

            // Mapping based on the RDNA 4 and Blackwell (2025/2026) chipsets
            if (str_contains($chipset, 'RX 9060 XT')) {
                // Your specific card (16GB models are usually 160W-180W)
                $wattage = 180; 
            } elseif (str_contains($chipset, 'RX 9070 XT')) {
                $wattage = 304;
            } elseif (str_contains($chipset, 'RX 9070')) {
                $wattage = 220;
            } elseif (str_contains($chipset, 'RTX 5090')) {
                $wattage = 500;
            } elseif (str_contains($chipset, 'RTX 5080')) {
                $wattage = 350;
            } elseif (str_contains($chipset, 'RTX 5070')) {
                $wattage = 220;
            }

            $specs['wattage_draw'] = $wattage;
            $gpu->update(['specs' => $specs]);
            $updated++;
        }

        $this->info("Database cleaned and synchronized successfully!");
    }
}
