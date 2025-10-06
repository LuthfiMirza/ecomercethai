<?php

namespace App\Support;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class MegaMenuBuilder
{
    /**
     * Build the mega menu dataset.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function build(): array
    {
        return Cache::remember('mega-menu:categories:v2', now()->addMinutes(5), function () {
            /** @var Collection<int, Category> $categories */
            $categories = Category::query()
                ->with(['products' => function ($query) {
                    $query->select('id', 'category_id', 'name', 'slug', 'price', 'image', 'is_active')
                        ->where('is_active', true)
                        ->orderByDesc('created_at')
                        ->take(8);
                }])
                ->whereHas('products', function ($query) {
                    $query->where('is_active', true);
                })
                ->orderBy('name')
                ->get();

            $normalized = $categories->map(function (Category $category) {
                $products = $category->products->map(function ($product) {
                    $image = $product->image;

                    if ($image && ! Str::startsWith($image, ['http://', 'https://'])) {
                        $image = asset('storage/' . ltrim($image, '/'));
                    }

                    if (! $image) {
                        $image = 'https://source.unsplash.com/400x400/?' . urlencode($product->name);
                    }

                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'price' => $product->price,
                        'price_formatted' => format_price($product->price ?? 0),
                        'url' => localized_route('product.show', ['slug' => $product->slug]),
                        'image' => $image,
                    ];
                })->values();

                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'url' => localized_route('catalog', ['category' => $category->slug ?? $category->id]),
                    'products' => $products,
                ];
            })->values();

            if ($normalized->isNotEmpty()) {
                return $normalized->toArray();
            }

            return static::fallbackCategories();
        });
    }

    /**
     * Provide demo-friendly fallback data when no categories exist yet.
     */
    protected static function fallbackCategories(): array
    {
        $catalogRoute = function (string $category): string {
            return function_exists('localized_route')
                ? localized_route('catalog', ['category' => $category])
                : route('catalog', ['category' => $category]);
        };

        $products = function (array $items): array {
            return collect($items)->map(function (array $item) {
                $price = $item['price'] ?? 0;

                return [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'slug' => $item['slug'],
                    'price' => $price,
                    'price_formatted' => function_exists('format_price') ? format_price($price) : 'Rp ' . number_format($price, 0, ',', '.'),
                    'url' => function_exists('localized_route')
                        ? localized_route('catalog', ['q' => $item['query'] ?? $item['name']])
                        : route('catalog', ['q' => $item['query'] ?? $item['name']]),
                    'image' => $item['image'] ?? 'https://source.unsplash.com/400x400/?' . urlencode($item['name']),
                ];
            })->toArray();
        };

        return [
            [
                'id' => 'demo-gaming',
                'name' => __('Gaming Essentials'),
                'slug' => 'gaming-essentials',
                'description' => __('Peralatan gaming favorit pelanggan kami.'),
                'url' => $catalogRoute('gaming'),
                'products' => $products([
                    ['id' => 'demo-headset', 'name' => 'Surround RGB Headset', 'slug' => 'surround-rgb-headset', 'price' => 1299000, 'query' => 'headset gaming top'],
                    ['id' => 'demo-mouse', 'name' => 'Precision Gaming Mouse', 'slug' => 'precision-gaming-mouse', 'price' => 599000, 'query' => 'mouse gaming wireless'],
                    ['id' => 'demo-chair', 'name' => 'Ergo Gaming Chair', 'slug' => 'ergo-gaming-chair', 'price' => 2850000, 'query' => 'kursi gaming ergonomis'],
                    ['id' => 'demo-keyboard', 'name' => 'Hot-swap Mechanical Keyboard', 'slug' => 'hot-swap-mechanical-keyboard', 'price' => 1499000, 'query' => 'keyboard mechanical'],
                ]),
            ],
            [
                'id' => 'demo-laptop',
                'name' => __('Premium Laptops'),
                'slug' => 'premium-laptops',
                'description' => __('Laptop pilihan untuk kerja kreatif dan bisnis.'),
                'url' => $catalogRoute('laptop'),
                'products' => $products([
                    ['id' => 'demo-creator', 'name' => 'Creator Pro 16', 'slug' => 'creator-pro-16', 'price' => 18999000, 'query' => 'laptop kreator'],
                    ['id' => 'demo-ultrabook', 'name' => 'Feather Ultrabook 14"', 'slug' => 'feather-ultrabook-14', 'price' => 14999000, 'query' => 'ultrabook ringan'],
                    ['id' => 'demo-gaming-laptop', 'name' => 'Blaze RTX Laptop', 'slug' => 'blaze-rtx-laptop', 'price' => 21499000, 'query' => 'laptop rtx 4060'],
                ]),
            ],
            [
                'id' => 'demo-accessories',
                'name' => __('Smart Accessories'),
                'slug' => 'smart-accessories',
                'description' => __('Aksesoris pintar untuk melengkapi setup Anda.'),
                'url' => $catalogRoute('accessories'),
                'products' => $products([
                    ['id' => 'demo-hub', 'name' => 'USB-C Thunder Hub', 'slug' => 'usb-c-thunder-hub', 'price' => 899000, 'query' => 'usb c hub thunderbolt'],
                    ['id' => 'demo-powerbank', 'name' => 'GaN Fast Power Bank', 'slug' => 'gan-fast-power-bank', 'price' => 749000, 'query' => 'power bank gan'],
                    ['id' => 'demo-smart-home', 'name' => 'Smart Home Starter Kit', 'slug' => 'smart-home-starter-kit', 'price' => 1299000, 'query' => 'smart home'],
                ]),
            ],
        ];
    }
}
