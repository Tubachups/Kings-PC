<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'category_id' => 6,
                'name' => 'Darkflash DLM21 Mesh Micro ATX PC Case Black',
                'price' => 2250.00,
                'stock' => 10,
                'specs' => [
                    'brand' => 'Darkflash',
                    'form_factor' => 'mATX',
                    'color' => 'Black',
                    'side_panel' => 'Tempered Glass',
                ],
            ],
            [
                'category_id' => 6,
                'name' => 'Fantech CG80 Aero Mid Tower ATX PC Case',
                'price' => 2400.00,
                'stock' => 8,
                'specs' => [
                    'brand' => 'Fantech',
                    'form_factor' => 'ATX',
                    'color' => 'Black',
                    'fans_included' => 4,
                ],
            ],

            // 7. GPU (Examples)
            [
                'category_id' => 7,
                'name' => 'Galax GeForce RTX 3050 EX 1-Click OC 8GB GDDR6',
                'price' => 14950.00,
                'stock' => 5,
                'specs' => [
                    'brand' => 'Galax',
                    'memory' => '8GB',
                    'memory_type' => 'GDDR6',
                    'chipset' => 'NVIDIA GeForce',
                ],
            ],
            [
                'category_id' => 7,
                'name' => 'ASUS Dual Radeon RX 6600 8GB GDDR6',
                'price' => 13500.00,
                'stock' => 7,
                'specs' => [
                    'brand' => 'ASUS',
                    'memory' => '8GB',
                    'memory_type' => 'GDDR6',
                    'chipset' => 'AMD Radeon',
                ],
            ],

            // 8. Cooling (Examples)
            [
                'category_id' => 8,
                'name' => 'Darkflash Darkair Plus ARGB CPU Air Cooler',
                'price' => 1200.00,
                'stock' => 20,
                'specs' => [
                    'brand' => 'Darkflash',
                    'type' => 'Air Cooler',
                    'fan_size' => '120mm',
                    'rgb' => true,
                ],
            ],
            [
                'category_id' => 8,
                'name' => 'Deepcool LE520 240mm ARGB AIO Liquid Cooler',
                'price' => 3800.00,
                'stock' => 10,
                'specs' => [
                    'brand' => 'Deepcool',
                    'type' => 'AIO Liquid',
                    'radiator_size' => '240mm',
                    'rgb' => true,
                ],
            ],

            [
                'category_id' => 1,
                'name' => 'MSI Pro B760M-E M-ATX LGA 1700 DDR4 Motherboard',
                'price' => 5300.00,
                'stock' => 12,
                'specs' => [
                    'brand' => 'MSI',
                    'socket' => 'LGA 1700',
                    'form_factor' => 'mATX',
                    'memory_type' => 'DDR4',
                    'wifi' => false,
                ],
            ],

            // 2. Intel CPU
            [
                'category_id' => 2,
                'name' => 'Intel Core I5-12400F Alder Lake Socket 1700 2.5GHz Processor',
                'price' => 6295.00,
                'stock' => 18,
                'specs' => [
                    'brand' => 'Intel',
                    'socket' => 'LGA 1700',
                    'cores' => 6,
                    'threads' => 12,
                    'base_clock' => '2.5GHz',
                    'integrated_graphics' => false,
                ],
            ],

            // 3. RAM (Alternative Option)
            [
                'category_id' => 3,
                'name' => 'Kingston FURY Beast 16GB (2x8GB) 3200MHz DDR4',
                'price' => 3200.00,
                'stock' => 50,
                'specs' => [
                    'brand' => 'Kingston',
                    'capacity' => '16GB',
                    'modules' => '2x8GB',
                    'speed' => '3200MHz',
                    'memory_type' => 'DDR4',
                ],
            ],

            // 4. Storage (Alternative Option)
            [
                'category_id' => 4,
                'name' => 'Western Digital WD Blue SN570 1TB M.2 NVMe SSD',
                'price' => 3850.00,
                'stock' => 25,
                'specs' => [
                    'brand' => 'Western Digital',
                    'capacity' => '1TB',
                    'form_factor' => 'M.2',
                    'interface' => 'NVMe',
                ],
            ],

            // 5. PSU (Alternative Option)
            [
                'category_id' => 5,
                'name' => 'Cooler Master MWE 750 Bronze V2 750W 80+ Power Supply',
                'price' => 4150.00,
                'stock' => 15,
                'specs' => [
                    'brand' => 'Cooler Master',
                    'wattage' => '750W',
                    'efficiency' => '80 Plus Bronze',
                    'modular' => false,
                ],
            ],

            // 1. Motherboard
            [
                'category_id' => 1,
                'name' => 'MSI Mag B550m Pro-Vdh WIFI mATX AM4 Ddr4 Gaming Motherboard',
                'price' => 6322.00,
                'stock' => 15,
                'specs' => [
                    'brand' => 'MSI',
                    'socket' => 'AM4',
                    'form_factor' => 'mATX',
                    'memory_type' => 'DDR4',
                    'wifi' => true,
                ],
            ],

            // 2. CPU
            [
                'category_id' => 2,
                'name' => 'AMD Ryzen 5 5600X 6-Core 12-Thread AM4 3.7GHz Gaming Processor',
                'price' => 7295.00,
                'stock' => 25,
                'specs' => [
                    'brand' => 'AMD',
                    'socket' => 'AM4',
                    'cores' => 6,
                    'threads' => 12,
                    'base_clock' => '3.7GHz',
                ],
            ],

            // 3. RAM
            [
                'category_id' => 3,
                'name' => 'Team Elite Vulcan TUF 16gb 2x8 3200mhz Ddr4 Gaming Memory',
                'price' => 5530.00,
                'stock' => 40,
                'specs' => [
                    'brand' => 'Team Elite',
                    'capacity' => '16GB',
                    'modules' => '2x8GB',
                    'speed' => '3200MHz',
                    'memory_type' => 'DDR4',
                ],
            ],

            // 4. Storage (SSD/HDD)
            [
                'category_id' => 4,
                'name' => 'Lexar NM610 Pro 500gb M.2 NVME Solid State Drive',
                'price' => 5142.94,
                'stock' => 30,
                'specs' => [
                    'brand' => 'Lexar',
                    'capacity' => '500GB',
                    'form_factor' => 'M.2',
                    'interface' => 'NVMe',
                ],
            ],

            // 5. PSU
            [
                'category_id' => 5,
                'name' => 'Corsair CX650 650 watts 80 Plus Bronze Power Supply',
                'price' => 3185.00,
                'stock' => 20,
                'specs' => [
                    'brand' => 'Corsair',
                    'wattage' => '650W',
                    'efficiency' => '80 Plus Bronze',
                    'modular' => false,
                ],
            ],
        ];

        foreach ($products as $product) {
            Product::create([
                'category_id' => $product['category_id'],
                'name' => $product['name'],
                'slug' => Str::slug($product['name']), // Automatically generates a URL-friendly slug
                'price' => $product['price'],
                'stock' => $product['stock'],
                'specs' => $product['specs'],
                'is_active' => true,
            ]);
        }
    }
}
