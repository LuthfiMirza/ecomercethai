@php
    $price = number_format($product->price, 2);
    $imageSource = $image ?? $product->image_url;
    if (! $imageSource) {
        $presets = [
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
        $keywords = [
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
        $haystackParts = [
            optional($product->category)->slug,
            optional($product->category)->name,
            $product->slug ?? null,
            $product->name ?? null,
            $product->brand ?? null,
        ];
        $haystack = strtolower(implode(' ', array_filter($haystackParts)));
        foreach ($keywords as $key => $needles) {
            foreach ($needles as $needle) {
                if ($needle !== '' && strpos($haystack, $needle) !== false) {
                    $imageSource = $presets[$key] ?? $presets['default'];
                    break 2;
                }
            }
        }
        if (! $imageSource) {
            $pool = array_values($presets);
            $count = count($pool);
            $index = $count ? crc32($haystack ?: 'default') % $count : 0;
            $imageSource = $pool[$index] ?? $presets['default'];
        }
    }
    $detailUrl = $product->slug
        ? localized_route('product.show', ['slug' => $product->slug])
        : localized_route('catalog');
@endphp
<article class="group rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 shadow-soft hover:shadow-elevated transition overflow-hidden">
  <a href="{{ $detailUrl }}" class="flex h-full flex-col">
    <div class="relative aspect-square bg-neutral-100 dark:bg-neutral-800">
      <img src="{{ $imageSource }}" alt="{{ $product->name }}" class="absolute inset-0 h-full w-full object-cover"/>
    </div>
    <div class="flex flex-1 flex-col justify-between space-y-2 p-3 md:space-y-3 md:p-4">
      <h3 class="min-h-[2.5rem] text-xs font-medium leading-snug text-neutral-900 dark:text-neutral-100 sm:text-sm md:min-h-[3rem] md:text-base" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical; overflow:hidden;">{{ $product->name }}</h3>
      <div class="flex items-baseline gap-1 md:gap-2">
        <div class="text-sm font-semibold text-neutral-900 dark:text-neutral-100 md:text-base">${{ $price }}</div>
        <span class="text-[11px] text-neutral-500 md:text-xs">{{ __('In stock: :stock', ['stock' => $product->stock]) }}</span>
      </div>
      <button
        data-cart-add
        data-product-id="{{ $product->id }}"
        data-name="{{ $product->name }}"
        data-price="{{ $price }}"
        data-image="{{ $imageSource }}"
        class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-neutral-300 bg-white py-1.5 text-xs font-medium text-neutral-700 transition hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-800 md:py-2 md:text-sm"
      >
        <i class="fa-solid fa-cart-shopping text-[13px] md:text-sm"></i>
        {{ __('Add to cart') }}
      </button>
    </div>
  </a>
</article>
