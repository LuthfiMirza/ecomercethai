@extends('layouts.app')

@section('content')
@php
    $productImagePresets = [
        'cpu' => 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=1200&q=80',
        'gpu' => 'https://images.unsplash.com/photo-1618005198919-d3d4b5a92eee?auto=format&fit=crop&w=1200&q=80',
        'motherboard' => 'https://images.unsplash.com/photo-1510877073473-6d90e0013e0b?auto=format&fit=crop&w=1200&q=80',
        'ram' => 'https://images.unsplash.com/photo-1517430816045-df4b7de49276?auto=format&fit=crop&w=1200&q=80',
        'storage' => 'https://images.unsplash.com/photo-1580894906472-2f564511e23b?auto=format&fit=crop&w=1200&q=80',
        'monitor' => 'https://images.unsplash.com/photo-1527443154391-507e9dc6c5cc?auto=format&fit=crop&w=1200&q=80',
        'peripheral' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=1200&q=80',
        'case' => 'https://images.unsplash.com/photo-1516922081966-6b3c1ecb4a28?auto=format&fit=crop&w=1200&q=80',
        'laptop' => 'https://images.unsplash.com/photo-1545239351-1141bd82e8a6?auto=format&fit=crop&w=1200&q=80',
        'default' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1200&q=80',
    ];
    $productImageKeywords = [
        'cpu' => ['cpu', 'processor', 'ryzen', 'intel', 'core'],
        'gpu' => ['gpu', 'graphics', 'rtx', 'radeon', 'vga', 'video card'],
        'motherboard' => ['motherboard', 'mainboard', 'mobo'],
        'ram' => ['ram', 'memory', 'ddr', 'so-dimm'],
        'storage' => ['ssd', 'hdd', 'storage', 'nvme', 'seagate', 'sata', 'm.2'],
        'monitor' => ['monitor', 'display', 'screen'],
        'peripheral' => ['keyboard', 'mouse', 'headset', 'peripheral', 'accessory'],
        'case' => ['casing', 'case', 'tower', 'chassis'],
        'laptop' => ['laptop', 'notebook', 'ultrabook'],
    ];
    $productImageResolver = static function ($product) use ($productImagePresets, $productImageKeywords) {
        $direct = $product->image_url ?? null;
        if ($direct) {
            return $direct;
        }

        $haystackParts = [
            optional($product->category)->slug,
            optional($product->category)->name,
            $product->slug ?? null,
            $product->name ?? null,
            $product->brand ?? null,
        ];
        $haystack = strtolower(implode(' ', array_filter($haystackParts)));

        foreach ($productImageKeywords as $key => $keywords) {
            foreach ($keywords as $needle) {
                if ($needle !== '' && strpos($haystack, $needle) !== false) {
                    return $productImagePresets[$key] ?? $productImagePresets['default'];
                }
            }
        }

        $pool = array_values($productImagePresets);
        $poolCount = count($pool);

        if ($poolCount === 0) {
            return null;
        }

        $index = crc32($haystack ?: 'default');
        return $pool[$index % $poolCount] ?? $productImagePresets['default'];
    };
    $request = request();
    $selectedCategories = collect(\Illuminate\Support\Arr::wrap($request->input('category')))
        ->filter(fn ($value) => filled($value))
        ->unique()
        ->values();
    $selectedBrands = collect(\Illuminate\Support\Arr::wrap($request->input('brand')))
        ->filter(fn ($value) => filled($value))
        ->unique()
        ->values();
    $minPrice = $request->input('min_price', $request->input('min'));
    $maxPrice = $request->input('max_price', $request->input('max'));
    $inStockOnly = $request->boolean('in_stock');
    $currentSort = $request->get('sort', 'newest');

    $filterCategories = collect($categories ?? [])
        ->map(function ($category) use ($selectedCategories) {
            $value = $category->slug ?? (string) $category->id;
            return [
                'label' => $category->name,
                'value' => $value,
                'checked' => $selectedCategories->contains($value),
            ];
        })
        ->sortBy('label')
        ->values()
        ->all();

    $filterBrands = collect($brands ?? [])
        ->filter()
        ->map(function ($brand) use ($selectedBrands) {
            $label = (string) $brand;
            return [
                'label' => $label,
                'value' => $label,
                'checked' => $selectedBrands->contains($label),
            ];
        })
        ->sortBy('label')
        ->values()
        ->all();
@endphp

<main id="main" class="container py-8 md:py-10" role="main">
  <h1 class="text-2xl font-semibold mb-4">Katalog</h1>
  <form method="get" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Sidebar filter -->
    <div class="lg:col-span-3" x-data="catalogFilterPanel()">
      <fieldset class="hidden lg:block sticky top-24 border-0 p-0 m-0" x-bind:disabled="!isDesktop">
        <x-filter
          :categories="$filterCategories"
          :brands="$filterBrands"
          :min-price="$minPrice"
          :max-price="$maxPrice"
          :in-stock="$inStockOnly"
        />
      </fieldset>
      <!-- Mobile filter button -->
      <div class="lg:hidden">
        <x-button @click.prevent="mobileOpen = true" class="w-full">Filter</x-button>
        <div x-show="mobileOpen" x-transition.opacity class="fixed inset-0 z-50" @keydown.escape.window="mobileOpen = false">
          <div class="absolute inset-0 bg-black/40" @click="mobileOpen = false"></div>
          <div class="absolute right-0 top-0 h-full w-[90vw] max-w-sm bg-white dark:bg-neutral-900 p-4 overflow-y-auto">
            <div class="flex items-center justify-between mb-2">
              <div class="font-medium">Filter</div>
              <button class="p-2" @click="mobileOpen = false"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <fieldset class="border-0 p-0 m-0" x-bind:disabled="isDesktop">
              <x-filter
                :categories="$filterCategories"
                :brands="$filterBrands"
                :min-price="$minPrice"
                :max-price="$maxPrice"
                :in-stock="$inStockOnly"
              />
            </fieldset>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="lg:col-span-9">
      <div class="flex items-center justify-between mb-4">
        <x-sort :value="$currentSort" />
        <div class="text-xs text-neutral-500">{{ __(':count results', ['count' => number_format($products->total())]) }}</div>
      </div>
      @if($products->count())
        <!-- Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4">
          @foreach($products as $product)
            @php
              $image = $productImageResolver($product);
              $detailUrl = route('product.show', $product->slug);
              $rawRating = $product->rating ?? ($product->reviews_avg_rating ?? 4.6);
              $rating = max(0, min(5, (float) $rawRating));
              $reviews = max(0, (int) ($product->reviews_count ?? ($product->order_items_count ?? 12)));
            @endphp
            <x-product-card
              :href="$detailUrl"
              :title="$product->name"
              :price="$product->price"
              :rating="$rating"
              :reviews="$reviews"
              :image="$image"
              :product-id="$product->id"
              currency="฿"
            />
          @endforeach
        </div>

        <div class="mt-8">
          {{ $products->onEachSide(1)->links() }}
        </div>
      @else
        <div class="rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 p-8 text-center">
          <img src="https://source.unsplash.com/800x480/?electronics,empty" alt="No products illustration" class="mx-auto mb-5 h-32 w-full max-w-md rounded-xl object-cover">
          <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-2">{{ __('No products found') }}</h2>
          <p class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Try adjusting your filters or search keywords to find what you need.') }}</p>
        </div>
      @endif
    </div>
  </form>
</main>
@endsection

@push('scripts')
<script>
  function catalogFilterPanel() {
    return {
      mobileOpen: false,
      isDesktop: window.matchMedia('(min-width: 1024px)').matches,
      init() {
        const mq = window.matchMedia('(min-width: 1024px)');
        const handler = (event) => {
          this.isDesktop = event.matches;
          if (event.matches) {
            this.mobileOpen = false;
          }
        };
        if (typeof mq.addEventListener === 'function') {
          mq.addEventListener('change', handler);
        } else if (typeof mq.addListener === 'function') {
          mq.addListener(handler);
        }
      },
    };
  }
</script>
@endpush
