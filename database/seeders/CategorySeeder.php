<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Your original 5
            ['name' => 'Motherboard', 'slug' => 'motherboard'],
            ['name' => 'CPU', 'slug' => 'cpu'],
            ['name' => 'RAM', 'slug' => 'ram'],
            ['name' => 'SSD/HDD', 'slug' => 'storage'],
            ['name' => 'PSU', 'slug' => 'psu'],

            // Your 3 new additions
            ['name' => 'GPU', 'slug' => 'gpu'],
            ['name' => 'Case', 'slug' => 'case'],
            ['name' => 'Cooling', 'slug' => 'cooling'],
        ];

        foreach ($categories as $category) {
            // firstOrCreate checks if a record with the given slug exists.
            // If it does, it skips it. If it doesn't, it creates it.
            Category::firstOrCreate(
                ['slug' => $category['slug']], // The column to check for duplicates
                ['name' => $category['name']]  // The data to add if it is new
            );
        }
    }
}
