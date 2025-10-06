<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Product::query()->delete();
        Schema::enableForeignKeyConstraints();

        $catalog = [
            'Mainboard' => [
                ['ASUS ROG Strix B760-F Gaming WiFi', 3599000, 'Premium Intel 13th/14th Gen motherboard with robust power delivery.'],
                ['MSI MAG B650 Tomahawk WiFi', 4299000, 'AM5 motherboard ready for Ryzen 7000 series builds.'],
                ['Gigabyte Z790 Aorus Elite AX', 5150000, 'High-end Intel board with PCIe 5.0 and WiFi 6E.'],
            ],
            'CPU' => [
                ['Intel Core i9-14900K', 9999000, 'Flagship 24-core processor for extreme gaming rigs.'],
                ['AMD Ryzen 7 7800X3D', 7399000, 'Gaming powerhouse with 3D V-Cache technology.'],
                ['Intel Core i5-13600K', 5249000, 'Best value hybrid CPU for performance and efficiency.'],
            ],
            'RAM' => [
                ['Corsair Vengeance RGB DDR5 32GB (2x16GB) 6000MHz', 3299000, 'Dual-channel DDR5 memory kit tuned for modern platforms.'],
                ['G.Skill Trident Z Neo DDR4 32GB (2x16GB) 3600MHz', 2599000, 'Low-latency memory optimized for AMD builds.'],
                ['Kingston Fury Beast DDR5 16GB (2x8GB) 5600MHz', 1899000, 'Reliable DDR5 performance for mainstream systems.'],
            ],
            'Gaming Chair & Desk' => [
                ['Secretlab TITAN Evo 2024 - Stealth', 8850000, 'Ergonomic premium chair with adjustable lumbar support.'],
                ['Razer Iskur Gaming Chair', 6250000, 'Comfort-first chair with sculpted lumbar support.'],
                ['DXRacer Air Series Mesh Chair', 5699000, 'Breathable mesh gaming chair for long sessions.'],
                ['Arozzi Arena Gaming Desk', 4899000, '160cm wide desk with full-surface mouse mat.'],
            ],
        ];

        $imageLookup = [
            'Mainboard' => 'https://source.unsplash.com/600x600/?motherboard',
            'CPU' => 'https://source.unsplash.com/600x600/?cpu,processor',
            'RAM' => 'https://source.unsplash.com/600x600/?ram,memory',
            'Gaming Chair & Desk' => 'https://source.unsplash.com/600x600/?gaming,desk',
        ];

        foreach ($catalog as $categoryName => $items) {
            $category = Category::firstOrCreate(
                ['name' => $categoryName],
                ['description' => $categoryName . ' products', 'image' => null]
            );

            foreach ($items as [$name, $price, $description]) {
                Product::create([
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'stock' => rand(8, 40),
                    'category_id' => $category->id,
                    'image' => $imageLookup[$categoryName] ?? 'https://via.placeholder.com/600x600?text=Hardware',
                    'status' => 'active',
                ]);
            }
        }
    }
}
