<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Banner::query()->delete();
        Schema::enableForeignKeyConstraints();

        $now = now();

        $banners = [
            [
                'title' => 'Limited GPU Drops',
                'image_path' => 'https://images.unsplash.com/photo-1618005198919-d3d4b5a92eee?auto=format&fit=crop&w=1600&q=80',
                'link_url' => '/en/catalog?category=gpu',
                'placement' => 'homepage_top',
                'starts_at' => $now->clone()->subWeeks(1),
                'ends_at' => $now->clone()->addMonths(1),
                'is_active' => true,
                'priority' => 5,
            ],
            [
                'title' => 'Build Guides & Consultation',
                'image_path' => 'https://images.unsplash.com/photo-1517430816045-df4b7de49276?auto=format&fit=crop&w=900&q=80',
                'link_url' => '/en/consultation',
                'placement' => 'homepage_sidebar',
                'starts_at' => $now->clone()->subWeeks(2),
                'ends_at' => $now->clone()->addWeeks(4),
                'is_active' => true,
                'priority' => 4,
            ],
            [
                'title' => 'Accessories Bundles',
                'image_path' => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=1600&q=80',
                'link_url' => '/en/catalog?category=peripheral',
                'placement' => 'homepage_bottom',
                'starts_at' => $now->clone()->subDays(10),
                'ends_at' => null,
                'is_active' => true,
                'priority' => 3,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::create($banner);
        }
    }
}

