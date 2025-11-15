@extends('layouts.app')

@section('content')
@php
    $currency = config('app.currency', 'THB');
    $fallbackMain = 'https://source.unsplash.com/900x900/?product,' . urlencode($product->slug);
    $galleryImages = collect($product->gallery_image_urls ?? []);
    if ($product->image_url) {
        $galleryImages->prepend($product->image_url);
    }
    $galleryImages = $galleryImages->filter()->unique()->values();
    if ($galleryImages->isEmpty()) {
        $galleryImages = collect([
            $fallbackMain,
            'https://source.unsplash.com/600x600/?electronics,' . urlencode($product->slug . '1'),
            'https://source.unsplash.com/600x600/?electronics,' . urlencode($product->slug . '2'),
            'https://source.unsplash.com/600x600/?electronics,' . urlencode($product->slug . '3'),
        ])->filter();
    }
    $mainImage = $galleryImages->first() ?? $fallbackMain;
    $stock = max(0, (int) ($product->stock ?? 0));
    $canPurchase = $stock > 0;
    $description = $product->description ?: __('product.no_description');
    $colorOptions = collect($product->available_colors ?? []);
    $selectedColor = old('color');
@endphp

<main id="main" class="container py-8 md:py-10" role="main">
  <article itemscope itemtype="https://schema.org/Product">
    <meta itemprop="name" content="{{ $product->name }}" />

    <div class="grid gap-8 lg:grid-cols-2">
      <div class="space-y-3">
        <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-800">
          <img loading="lazy" src="{{ $mainImage }}" alt="{{ $product->name }}" class="w-full h-full object-cover" data-gallery-main-image/>
        </div>
        <div class="grid grid-cols-4 gap-3">
          @foreach($galleryImages as $gallery)
            <button type="button"
                    data-gallery-thumb
                    data-image="{{ $gallery }}"
                    class="relative rounded-lg border border-transparent focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-500 transition hover:border-accent-400">
              <img loading="lazy" src="{{ $gallery }}" alt="{{ $product->name }} thumbnail" class="w-full h-24 rounded-lg object-cover pointer-events-none"/>
            </button>
          @endforeach
        </div>
      </div>

      <div>
        <div class="space-y-2">
          <p class="text-sm text-neutral-500">{{ $product->brand ?? $product->category->name ?? __('common.product') }}</p>
          <h1 class="text-2xl md:text-3xl font-semibold text-neutral-900 dark:text-neutral-100">{{ $product->name }}</h1>
        </div>
        <div class="mt-4 text-3xl font-semibold text-neutral-900 dark:text-neutral-100">
          {{ format_price($product->price) }}
        </div>
        <div class="mt-2 text-sm text-neutral-600 dark:text-neutral-300">
          {{ __('product.stock') }}:
          <span class="font-medium {{ $canPurchase ? 'text-emerald-600' : 'text-rose-600' }}">
            {{ $canPurchase ? __('product.stock_available', ['qty' => $stock]) : __('product.stock_unavailable') }}
          </span>
        </div>

        <div class="mt-6 space-y-4">
          <form method="POST" action="{{ localized_route('cart.add') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            @if($colorOptions->isNotEmpty())
              <div>
                <div class="flex items-center justify-between">
                  <label class="text-sm font-medium text-neutral-700 dark:text-neutral-200">{{ __('product.color') }}</label>
                  <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('product.color_optional_hint') }}</span>
                </div>
                <div class="mt-2 flex flex-wrap gap-2">
                  @foreach($colorOptions as $color)
                    @php
                      $colorId = 'color-' . $loop->index;
                      $isSelected = $selectedColor === $color;
                    @endphp
                    <label for="{{ $colorId }}" class="cursor-pointer rounded-2xl border px-4 py-2 text-sm font-medium transition-all {{ $isSelected ? 'border-accent-500 bg-accent-500/10 text-accent-600 shadow-inner' : 'border-neutral-200 text-neutral-600 hover:border-accent-300 dark:border-neutral-700 dark:text-neutral-200 dark:hover:border-accent-400' }}">
                      <input type="radio" name="color" id="{{ $colorId }}" value="{{ $color }}" class="sr-only" {{ $isSelected ? 'checked' : '' }}>
                      <span>{{ $color }}</span>
                    </label>
                  @endforeach
                </div>
                @error('color')
                  <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                @enderror
              </div>
            @endif
            <div>
              <label for="quantity" class="text-sm font-medium text-neutral-700 dark:text-neutral-200">{{ __('product.quantity') }}</label>
              <div class="mt-2 flex w-full max-w-xs items-center rounded-xl border border-neutral-200 dark:border-neutral-700">
                <input
                  type="number"
                  id="quantity"
                  name="quantity"
                  min="1"
                  max="{{ max(1, $stock) }}"
                  value="1"
                  class="w-full rounded-xl border-none bg-transparent px-4 py-2 text-base focus:outline-none"
                  @if(! $canPurchase) disabled @endif
                >
              </div>
              @error('quantity')
                <p class="text-sm text-rose-600 mt-1">{{ $messages[0] }}</p>
              @enderror
            </div>
            <div class="flex flex-wrap gap-3">
              @if($canPurchase)
                <x-button type="submit" class="flex-1 justify-center">
                  {{ __('product.add_to_cart') }}
                </x-button>
              @else
                <x-button type="button" class="flex-1 justify-center" disabled>
                  {{ __('product.stock_unavailable') }}
                </x-button>
              @endif
            </div>
          </form>
          
          <div class="flex flex-wrap gap-3">
            @if(auth()->check())
              <button type="button"
                      data-wishlist
                      data-product-id="{{ $product->id }}"
                      data-name="{{ $product->name }}"
                      data-price="{{ $product->price }}"
                      data-image="{{ $mainImage }}"
                      data-url="{{ localized_route('product.show', ['slug' => $product->slug]) }}"
                      class="inline-flex items-center justify-center font-medium rounded-2xl transition-all focus:outline-none focus-visible:ring-4 focus-visible:ring-orange-200/70 disabled:opacity-50 disabled:cursor-not-allowed px-5 py-2.5 text-sm border border-white/60 bg-white/80 text-neutral-700 shadow-inner hover:border-orange-300 backdrop-blur-xl dark:border-neutral-700 dark:bg-neutral-900/60 dark:text-neutral-100"
                      aria-label="{{ __('common.wishlist') }}">
                <i class="fa-regular fa-heart mr-2"></i>{{ __('common.wishlist') }}
              </button>
            @else
              <a href="{{ localized_route('login') }}" class="inline-flex items-center justify-center rounded-2xl border border-neutral-300 px-6 py-2.5 text-sm font-semibold text-neutral-700 hover:bg-neutral-50">
                <i class="fa-regular fa-heart mr-2"></i>{{ __('common.wishlist_login_prompt') }}
              </a>
            @endif
          </div>
        </div>

        <div class="mt-6 border-t pt-4 text-sm text-neutral-600 dark:text-neutral-300 space-y-2">
          <div class="flex items-center gap-2"><i class="fa-solid fa-truck-fast text-accent-500"></i><span>{{ __('product.fast_shipping') }}</span></div>
          <div class="flex items-center gap-2"><i class="fa-solid fa-shield-halved text-accent-500"></i><span>{{ __('product.guaranteed_warranty') }}</span></div>
          <div class="flex items-center gap-2"><i class="fa-solid fa-rotate-left text-accent-500"></i><span>{{ __('product.easy_returns') }}</span></div>
        </div>
      </div>
    </div>

    <section class="mt-12 space-y-8">
      <div class="border-b flex gap-6 text-sm">
        <a href="#desc" class="py-2 border-b-2 border-accent-500">{{ __('product.description_tab') }}</a>
        <a href="#spec" class="py-2">{{ __('product.details_tab') }}</a>
      </div>
      <div id="desc" class="prose dark:prose-invert max-w-none">
        {!! nl2br(e($description)) !!}
      </div>
      <div id="spec" class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
        <div class="flex justify-between border-b py-2"><span>{{ __('product.brand') }}</span><span>{{ $product->brand ?? __('product.not_available') }}</span></div>
        <div class="flex justify-between border-b py-2"><span>{{ __('product.category') }}</span><span>{{ $product->category->name ?? __('product.general_category') }}</span></div>
        <div class="flex justify-between border-b py-2"><span>{{ __('product.stock') }}</span><span>{{ $stock }}</span></div>
        <div class="flex justify-between border-b py-2"><span>{{ __('product.price') }}</span><span>{{ format_price($product->price) }}</span></div>
      </div>
    </section>

    @if($relatedProducts->isNotEmpty())
    <section class="mt-12">
      <h2 class="text-xl font-semibold mb-4">{{ __('product.related_products') }}</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($relatedProducts as $related)
          @php
            $relatedImage = $related->image_url ?? 'https://source.unsplash.com/600x600/?product,' . urlencode($related->slug);
            $detailUrl = localized_route('product.show', ['slug' => $related->slug]);
          @endphp
          <x-product-card
            :href="$detailUrl"
            :title="$related->name"
            :price="$related->price"
            :rating="4.8"
            :reviews="$related->order_items_count ?? 0"
            :image="$relatedImage"
            :product-id="$related->id"
            currency="฿"
          />
        @endforeach
      </div>
    </section>
    @endif
  </article>
</main>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const mainImage = document.querySelector('[data-gallery-main-image]');
    if (!mainImage) {
      return;
    }

    document.querySelectorAll('[data-gallery-thumb]').forEach((thumb) => {
      thumb.addEventListener('click', () => {
        const target = thumb.getAttribute('data-image');
        if (target) {
          mainImage.src = target;
        }
      });
    });
  });
</script>
@endpush
