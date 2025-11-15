@props([
  'href' => '#',
  'image' => null,
  'title' => 'Product Name',
  'price' => 0,
  'compareAt' => null, // harga normal (non-discount)
  'rating' => 0,
  'reviews' => 0,
  'badge' => null,
  'brand' => null,
  'stock' => 'In stock',
  'variation' => null,
  'currency' => '฿',
  'productId' => null,
])

@php
  // Format harga sederhana (locale bisa di-handle oleh helper/intl di layer lain)
  $fmt = function($n) use ($currency){ return $currency.number_format($n, 2); };
  $hasDiscount = $compareAt && $compareAt > $price;
  $resolvedImage = $image;
  if (! $resolvedImage) {
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
    $haystack = strtolower(trim(($title ?? '').' '.($brand ?? '').' '.($variation ?? '')));
    foreach ($keywords as $key => $needles) {
      foreach ($needles as $needle) {
        if ($needle !== '' && strpos($haystack, $needle) !== false) {
          $resolvedImage = $presets[$key] ?? $presets['default'];
          break 2;
        }
      }
    }
    if (! $resolvedImage) {
      $pool = array_values($presets);
      $count = count($pool);
      $resolvedImage = $count ? $pool[crc32($haystack ?: 'default') % $count] : null;
    }
  }
  $image = $resolvedImage ?? $image;
@endphp

@php($loginUrl = localized_route('login'))

<article x-data="{ loaded:false }" class="group rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 shadow-soft hover:shadow-elevated transition overflow-hidden focus-within:ring-2 focus-within:ring-accent-500" aria-label="{{ $title }}">
  <a href="{{ $href }}" class="block focus:outline-none">
    <!-- Image with 1:1 ratio and skeleton -->
    <div class="relative aspect-square bg-neutral-100 dark:bg-neutral-800">
      <div x-show="!loaded" class="absolute inset-0 animate-pulse">
        <div class="w-full h-full bg-neutral-200 dark:bg-neutral-700"></div>
      </div>
      <img loading="lazy" src="{{ $image }}" srcset="{{ $image }} 1x" sizes="(min-width: 1024px) 25vw, 50vw" alt="{{ $title }}" @load="loaded=true" class="absolute inset-0 w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-300"/>
      @if($badge)
        <div class="absolute top-2 left-2">
          <x-badge variant="accent">{{ $badge }}</x-badge>
        </div>
      @endif
      <!-- Quick actions -->
      <div class="absolute top-2 right-2 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition">
        <a href="{{ $href }}" aria-label="{{ __('Lihat detail produk') }}" class="p-2 rounded-md bg-white/90 dark:bg-neutral-800/90 border border-neutral-200 dark:border-neutral-700 hover:bg-white focus:outline-none focus:ring-2 focus:ring-accent-500">
          <i class="fa-regular fa-eye"></i>
        </a>
        @if(auth()->check())
        <button type="button" data-wishlist data-product-id="{{ $productId }}" data-name="{{ $title }}" data-price="{{ $price }}" data-image="{{ $image }}" data-url="{{ $href }}" aria-label="Add to wishlist" class="p-2 rounded-md bg-white/90 dark:bg-neutral-800/90 border border-neutral-200 dark:border-neutral-700 hover:bg-white focus:outline-none focus:ring-2 focus:ring-accent-500"><i class="fa-regular fa-heart"></i></button>
        @else
        <button type="button" onclick="window.location='{{ $loginUrl }}'" aria-label="Login to save" class="p-2 rounded-md bg-white/90 border border-neutral-200 hover:bg-white focus:outline-none focus:ring-2 focus:ring-accent-500"><i class="fa-regular fa-heart"></i></button>
        @endif
        <button data-cart-add data-product-id="{{ $productId }}" data-name="{{ $title }}" data-price="{{ $price }}" data-image="{{ $image }}" aria-label="Add to cart" class="p-2 rounded-md bg-accent-500 text-white hover:bg-accent-600 focus:outline-none focus:ring-2 focus:ring-accent-500"><i class="fa-solid fa-cart-plus"></i></button>
      </div>
    </div>
    <div class="p-4">
      <h3 class="text-sm font-medium text-neutral-900 dark:text-neutral-100 leading-snug" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical; overflow:hidden; min-height:3rem;">{{ $title }}</h3>
      <div class="mt-1 flex items-center gap-1 text-amber-500" aria-label="Rating {{ number_format($rating,1) }} of 5">
        @for($i=1;$i<=5;$i++)
          @if($rating >= $i)
            <i class="fa-solid fa-star"></i>
          @elseif($rating > $i-1)
            <i class="fa-regular fa-star-half-stroke"></i>
          @else
            <i class="fa-regular fa-star text-neutral-300"></i>
          @endif
        @endfor
        <span class="ml-1 text-xs text-neutral-500">({{ $reviews }})</span>
      </div>
      <div class="mt-2 flex items-baseline gap-2">
        <div class="text-base font-semibold text-neutral-900 dark:text-neutral-100">{{ $fmt($price) }}</div>
        @if($hasDiscount)
          <div class="text-xs text-neutral-500 line-through">{{ $fmt($compareAt) }}</div>
          <x-badge variant="success">-{{ round((1-($price/$compareAt))*100) }}%</x-badge>
        @endif
      </div>
    </div>
  </a>
  <div class="px-4 pb-4 flex items-center gap-2">
    <button
      type="button"
      class="flex-1 inline-flex items-center justify-center gap-2 rounded-2xl border border-neutral-200 bg-white py-2 text-sm font-medium text-neutral-700 transition hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 dark:hover:bg-neutral-800"
      aria-label="Tambah {{ $title }} ke keranjang"
      data-cart-add
      data-product-id="{{ $productId }}"
      data-name="{{ $title }}"
      data-price="{{ $price }}"
      data-image="{{ $image }}"
    >
      <i class="fa-solid fa-cart-shopping text-[13px] md:text-sm"></i>
      <span>{{ __('Tambah ke Keranjang') }}</span>
    </button>
    @if(auth()->check())
    <button type="button" aria-label="Wishlist" data-wishlist data-product-id="{{ $productId }}" data-name="{{ $title }}" data-price="{{ $price }}" data-image="{{ $image }}" data-url="{{ $href }}" class="p-2 rounded-md border border-neutral-200 dark:border-neutral-700 hover:bg-neutral-100 dark:hover:bg-neutral-800">
      <i class="fa-regular fa-heart"></i>
    </button>
    @else
    <button type="button" aria-label="Login to save" onclick="window.location='{{ $loginUrl }}'" class="p-2 rounded-md border border-neutral-200 hover:bg-neutral-100">
      <i class="fa-regular fa-heart"></i>
    </button>
    @endif
  </div>

</article>
