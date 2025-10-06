<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset and seed fixed, realistic categories
        Schema::disableForeignKeyConstraints();
        Category::query()->delete();
        Schema::enableForeignKeyConstraints();

        $categories = [
            [
                'name' => 'Mainboard',
                'description' => 'Motherboards for Intel and AMD platforms',
            ],
            [
                'name' => 'CPU',
                'description' => 'High-performance processors for gaming and productivity',
            ],
            [
                'name' => 'RAM',
                'description' => 'DDR4 & DDR5 memory kits for every build',
            ],
            [
                'name' => 'Gaming Chair & Desk',
                'description' => 'Ergonomic seating and desks for immersive gaming',
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'description' => $category['description'],
                'image' => null,
            ]);
        }
    }
}
