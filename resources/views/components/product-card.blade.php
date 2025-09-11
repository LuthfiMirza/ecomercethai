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
])

@php
  // Format harga sederhana (locale bisa di-handle oleh helper/intl di layer lain)
  $fmt = function($n) use ($currency){ return $currency.number_format($n, 2); };
  $hasDiscount = $compareAt && $compareAt > $price;
@endphp

<article x-data="{ loaded:false, quick:false }" class="group rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 shadow-soft hover:shadow-elevated transition overflow-hidden focus-within:ring-2 focus-within:ring-accent-500" aria-label="{{ $title }}">
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
        <button @click.prevent.stop="quick=true" aria-label="Quick view" class="p-2 rounded-md bg-white/90 dark:bg-neutral-800/90 border border-neutral-200 dark:border-neutral-700 hover:bg-white focus:outline-none focus:ring-2 focus:ring-accent-500"><i class="fa-regular fa-eye"></i></button>
        <button data-compare data-name="{{ $title }}" data-price="{{ $price }}" data-image="{{ $image }}" data-brand="{{ $brand }}" data-stock="{{ $stock }}" data-variation="{{ $variation }}" data-rating="{{ $rating }}" aria-label="Add to compare" class="p-2 rounded-md bg-white/90 dark:bg-neutral-800/90 border border-neutral-200 dark:border-neutral-700 hover:bg-white focus:outline-none focus:ring-2 focus:ring-accent-500"><i class="fa-solid fa-code-compare"></i></button>
        <button data-wishlist data-name="{{ $title }}" data-price="{{ $price }}" data-image="{{ $image }}" aria-label="Add to wishlist" class="p-2 rounded-md bg-white/90 dark:bg-neutral-800/90 border border-neutral-200 dark:border-neutral-700 hover:bg-white focus:outline-none focus:ring-2 focus:ring-accent-500"><i class="fa-regular fa-heart"></i></button>
        <button data-cart-add data-name="{{ $title }}" data-price="{{ $price }}" data-image="{{ $image }}" aria-label="Add to cart" class="p-2 rounded-md bg-accent-500 text-white hover:bg-accent-600 focus:outline-none focus:ring-2 focus:ring-accent-500"><i class="fa-solid fa-cart-plus"></i></button>
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
    <x-button class="flex-1" aria-label="Tambah {{ $title }} ke keranjang" data-cart-add data-name="{{ $title }}" data-price="{{ $price }}" data-image="{{ $image }}">Tambah ke Keranjang</x-button>
    <button aria-label="Wishlist" data-wishlist data-name="{{ $title }}" data-price="{{ $price }}" data-image="{{ $image }}" class="p-2 rounded-md border border-neutral-200 dark:border-neutral-700 hover:bg-neutral-100 dark:hover:bg-neutral-800">
      <i class="fa-regular fa-heart"></i>
    </button>
  </div>

  <!-- Quick View Modal -->
  <div x-cloak x-show="quick" x-transition.opacity class="fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/40" @click="quick=false"></div>
    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[92vw] max-w-xl bg-white dark:bg-neutral-900 rounded-2xl shadow-elevated overflow-hidden">
      <div class="flex">
        <img src="{{ $image }}" alt="{{ $title }}" class="w-1/2 h-full object-cover hidden md:block"/>
        <div class="p-4 flex-1">
          <div class="flex justify-between items-start">
            <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ $title }}</h3>
            <button class="p-2" @click="quick=false" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
          </div>
          <div class="mt-2 text-sm text-neutral-600 dark:text-neutral-300">{{ $fmt($price) }} @if($hasDiscount)<span class="line-through text-neutral-400 ml-2">{{ $fmt($compareAt) }}</span>@endif</div>
          <div class="mt-4 flex gap-2">
            <x-button data-cart-add data-name="{{ $title }}" data-price="{{ $price }}" data-image="{{ $image }}">Add to Cart</x-button>
            <x-button variant="outline">Select Options</x-button>
          </div>
        </div>
      </div>
    </div>
  </div>
</article>
