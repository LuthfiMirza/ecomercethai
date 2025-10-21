@php
    $price = number_format($product->price, 2);
    $image = 'https://source.unsplash.com/600x600/?' . urlencode($product->name);
@endphp
<article class="group rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 shadow-soft hover:shadow-elevated transition overflow-hidden">
  <a href="{{ route('catalog') }}" class="flex h-full flex-col">
    <div class="relative aspect-square bg-neutral-100 dark:bg-neutral-800">
      <img src="{{ $image }}" alt="{{ $product->name }}" class="absolute inset-0 h-full w-full object-cover"/>
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
        data-image="{{ $image }}"
        class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-neutral-300 bg-white py-1.5 text-xs font-medium text-neutral-700 transition hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-800 md:py-2 md:text-sm"
      >
        <i class="fa-solid fa-cart-shopping text-[13px] md:text-sm"></i>
        {{ __('Add to cart') }}
      </button>
    </div>
  </a>
</article>
