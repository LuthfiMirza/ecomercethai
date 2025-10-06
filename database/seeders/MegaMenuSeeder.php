<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MegaMenuSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $catalog = [
                'Design' => [
                    ['UI/UX Design Kit', 149.99, 'Koleksi wireframe dan komponen siap pakai untuk Figma.'],
                    ['Design System Toolkit', 189.00, 'Design system modular lengkap dengan dokumentasi.'],
                    ['Digital Branding Pack', 129.50, 'Paket brand guideline, logo, dan aset promosi digital.'],
                ],
                'Development' => [
                    ['Headless Commerce Starter', 249.00, 'Template Laravel + Vue untuk toko modern.'],
                    ['API Accelerator', 199.00, 'Starter kit API dengan dokumentasi dan Postman collection.'],
                    ['SaaS Dashboard UI', 179.00, 'Dashboard admin responsif dengan mode gelap.'],
                ],
                'SEO & Marketing' => [
                    ['SEO Automation Suite', 129.00, 'Workflow otomatis audit SEO dan laporan berkala.'],
                    ['Marketing Landing Pages', 109.00, 'Kumpulan landing page siap pakai dengan copy premium.'],
                    ['Content Calendar Board', 89.00, 'Template Notion untuk perencanaan konten multichannel.'],
                ],
            ];

            foreach ($catalog as $categoryName => $products) {
                $category = Category::updateOrCreate(
                    ['slug' => Str::slug($categoryName)],
                    [
                        'name' => $categoryName,
                        'description' => $categoryName . ' solutions curated for growth teams.',
                    ]
                );

                foreach ($products as [$name, $price, $description]) {
                    Product::updateOrCreate(
                        ['slug' => Str::slug($name)],
                        [
                            'category_id' => $category->id,
                            'name' => $name,
                            'description' => $description,
                            'price' => $price,
                            'stock' => 25,
                            'brand' => 'Toko Thailand Labs',
                            'image' => 'https://source.unsplash.com/600x600/?' . urlencode($name),
                            'is_active' => true,
                        ]
                    );
                }
            }
        });

        Cache::forget('mega-menu:categories');
    }
}
