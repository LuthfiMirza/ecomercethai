@extends('layouts.app')

@section('content')
@php
    $bannerCollection = $banners ?? collect();
    $findBanner = static function ($placement) use ($bannerCollection) {
        return $bannerCollection->firstWhere('placement', $placement);
    };
    $bannerUrl = static function ($banner) {
        if (! $banner) {
            return null;
        }
        $path = $banner->image_path;
        return \Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])
            ? $path
            : \Illuminate\Support\Facades\Storage::url($path);
    };
    $topBanner = $findBanner('homepage_top');
    $midBanner = $findBanner('homepage_sidebar');
    $bottomBanner = $findBanner('homepage_bottom');
    $heroSlides = [
        [
            'title' => __('Upgrade Your Battle Station'),
            'subtitle' => __('Custom gaming rigs, assembled and stress-tested'),
            'description' => __('Pilih build yang sesuai gaya bermainmu atau konsultasi dengan tim kami untuk spesifikasi terbaik.'),
            'image' => asset('image/heropc.jpeg'),
            'alt' => 'Custom gaming PC setup',
        ],
        [
            'title' => __('Next-Gen CPU Power'),
            'subtitle' => __('AMD Ryzen & Intel Core line-up ready to ship'),
            'description' => __('Temukan prosesor favoritmu dengan stok terjamin dan garansi resmi pabrikan.'),
            'image' => asset('image/herocpu.jpeg'),
            'alt' => 'Close-up of a high-end CPU',
        ],
        [
            'title' => __('Graphics Ready for Ray Tracing'),
            'subtitle' => __('RTX & Radeon terbaru untuk visual maksimal'),
            'description' => __('Rasakan performa GPU kelas atas untuk gaming 4K, VR, dan kebutuhan kreatif profesional.'),
            'image' => asset('image/herovga.jpeg'),
            'alt' => 'High-end graphics card on a desk',
        ],
    ];
@endphp

<!-- Hero Section / Slider -->
<section class="relative">
  <div class="hero-slider relative overflow-hidden rounded-b-[42px] bg-neutral-900 text-white shadow-lg md:rounded-b-[56px]" data-slider data-slider-autoplay="7000" data-slider-wrap="true">
    <div class="flex h-full min-h-[420px] sm:min-h-[520px] md:min-h-[600px] transition-transform duration-700 ease-out" data-slider-track>
      @foreach($heroSlides as $index => $slide)
        <article class="relative flex h-full w-full flex-shrink-0 basis-full items-center justify-center" data-slider-item>
          <img
            src="{{ $slide['image'] }}"
            alt="{{ $slide['alt'] }}"
            class="absolute inset-0 h-full w-full object-cover"
            @if($loop->first)
              loading="eager" fetchpriority="high"
            @else
              loading="lazy"
            @endif
          >
          <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-black/15"></div>
          <div class="relative z-10 w-full">
            <div class="container mx-auto flex h-full flex-col justify-center px-4 py-12 md:py-20">
              <div class="max-w-2xl">
                <div class="space-y-4 rounded-3xl border border-white/10 bg-white/10 p-6 text-white shadow-[0_20px_45px_rgba(15,15,15,0.45)] backdrop-blur-md sm:space-y-6 sm:p-8 md:bg-white/12">
                  <p class="text-xs font-semibold uppercase tracking-[0.35em] text-white/70 sm:text-sm">{{ __('Toko Thailand Exclusive') }}</p>
                  <h1 class="text-3xl font-black leading-tight sm:text-4xl md:text-5xl">{{ $slide['title'] }}</h1>
                  <p class="text-lg font-semibold text-white/95 md:text-xl">{{ $slide['subtitle'] }}</p>
                  <p class="text-sm text-white/85 md:text-base">{{ $slide['description'] }}</p>
                  <div class="flex flex-wrap items-center gap-3 pt-2">
                    <a href="{{ route('catalog') }}" class="inline-flex items-center gap-2 rounded-full bg-accent-500 px-6 py-2.5 text-sm font-semibold uppercase tracking-wide text-white shadow-lg transition hover:bg-accent-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-accent-500">
                      <i class="fa-solid fa-cart-shopping text-sm"></i> {{ __('Belanja Sekarang') }}
                    </a>
                    <a href="{{ route('catalog', ['category' => 'GPU']) }}" class="inline-flex items-center gap-2 rounded-full border border-white/40 px-6 py-2.5 text-sm font-semibold uppercase tracking-wide text-white/90 transition hover:bg-white/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-white">
                      {{ __('Lihat Promo GPU') }} <i class="fa-solid fa-arrow-right text-sm"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </article>
      @endforeach
    </div>

    @if(count($heroSlides) > 1)
      <button type="button" class="absolute left-4 top-1/2 hidden h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-neutral-800 shadow-lg transition hover:bg-white md:flex" data-slider-prev aria-label="{{ __('Slide sebelumnya') }}">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
      </button>
      <button type="button" class="absolute right-4 top-1/2 hidden h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-neutral-800 shadow-lg transition hover:bg-white md:flex" data-slider-next aria-label="{{ __('Slide berikutnya') }}">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
      </button>
      <div class="pointer-events-none absolute inset-x-0 bottom-6 flex justify-center">
        <div class="flex items-center gap-2 rounded-full bg-black/30 px-4 py-2 backdrop-blur-sm" data-slider-dots>
          @foreach($heroSlides as $index => $slide)
            <button
              type="button"
              class="pointer-events-auto h-2.5 w-2.5 rounded-full transition-opacity"
              data-slider-dot
              data-slider-index="{{ $index }}"
              data-slider-dot-active="bg-white"
              data-slider-dot-inactive="bg-white/40"
              data-slider-dot-inactive-dark=""
              aria-label="{{ __('Ke slide :number', ['number' => $index + 1]) }}"
            ></button>
          @endforeach
        </div>
      </div>
    @endif
  </div>
</section>

<!-- Category Icons -->
<section class="py-10">
  <div class="container mx-auto px-4">
    <div class="flex flex-wrap justify-center gap-6">
      @php
        $icons = [
          'CPU' => 'fa-microchip',
          'GPU' => 'fa-bolt',
          'RAM' => 'fa-memory',
          'SSD' => 'fa-sd-card',
        ];
      @endphp
      @foreach($icons as $category => $icon)
        <a href="{{ route('catalog', ['category' => $category]) }}" class="group w-[150px]">
          <div class="h-[120px] rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 shadow-soft flex flex-col items-center justify-center transition-transform group-hover:-translate-y-1">
            <i class="fa-solid {{ $icon }} text-2xl text-accent-500"></i>
            <span class="mt-3 text-sm font-semibold text-neutral-800 dark:text-neutral-100">{{ $category }}</span>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</section>

<!-- Top Advertisement Banner -->
<div class="container mx-auto px-6 my-8">
    <div class="bg-white p-4 rounded-md text-center">
        <img src="{{ asset('image/iklan.jpg') }}" 
             alt="Top Advertisement" 
             class="mx-auto max-w-full h-auto">
    </div>
</div>

<!-- Rekomendasi Produk Section -->
<div class="container mx-auto px-6">
    <h2 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100 mb-8 text-center">{{ __('Recommended Products') }}</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse(($featuredProducts ?? collect()) as $product)
        @php
            $formattedPrice = number_format($product->price, 2);
            $image = 'https://source.unsplash.com/600x400/?' . urlencode($product->name);
        @endphp
        <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 border border-orange-100 overflow-hidden relative">
            <div class="absolute top-2 right-2 bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded-full">New</div>
            <div class="relative h-48 overflow-hidden">
                <img src="{{ $image }}" alt="{{ $product->name }}" class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-300">
            </div>
            <div class="p-4 border-t border-orange-100">
                <h3 class="font-bold text-lg text-gray-800 mb-2">{{ $product->name }}</h3>
                <div class="flex justify-between items-center mb-4">
                    <span class="text-orange-500 font-bold text-xl">${{ $formattedPrice }}</span>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                        <span class="text-gray-600 text-sm ml-1">4.5</span>
                    </div>
                </div>
                <button data-cart-add data-product-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $formattedPrice }}" data-image="{{ $image }}" class="w-full bg-orange-500 text-white py-2 rounded-lg font-semibold hover:bg-orange-600 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>Buy Now
                </button>
                <button data-wishlist data-name="{{ $product->name }}" data-price="${{ $formattedPrice }}" data-image="{{ $image }}" class="mt-2 w-full border border-orange-300 text-orange-600 py-2 rounded-lg font-medium hover:bg-orange-50 transition-colors">
                    <i class="fa-regular fa-heart mr-2"></i> Wishlist
                </button>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow-lg border border-orange-100 p-6 col-span-full">{{ __('No recommended products yet. Please add products through the admin panel.') }}</div>
        @endforelse
    </div>
</div>

<!-- Middle Advertisement Banner -->
<div class="container mx-auto px-6 my-12">
    <div class="bg-white border-2 border-orange-500 p-4 rounded-md text-center">
        <img src="{{ asset('images/ad-example.png') }}" 
             alt="Middle Advertisement" 
             class="mx-auto max-w-full h-auto"
             onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzMDAgMTAwIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjBmMGYwIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIyNCIgZmlsbD0iIzY2NiIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9XCIuM2VtXCI+QWR2ZXJ0aXNlbWVudDwvdGV4dD48L3N2Zz4='">
    </div>
</div>

<!-- Horizontal Banner Between Sections -->
<div class="container mx-auto px-6 py-8">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
        @if($bottomBanner && ($src = $bannerUrl($bottomBanner)))
            <img src="{{ $src }}" alt="{{ $bottomBanner->title }}" class="w-full h-40 object-cover">
        @else
            <img src="{{ asset('image/iklan.jpg') }}" alt="Wide Banner" class="w-full h-40 object-cover">
        @endif
    </div>
</div>

<!-- Product Catalog Section (Redesigned from here downward) -->
<section class="container mx-auto px-6 py-12">
  <div class="flex flex-wrap items-center justify-between gap-3">
    <h2 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">Our Products</h2>
    <p class="text-sm text-neutral-600 dark:text-neutral-300">Latest products curated by our team.</p>
  </div>

  <div class="mt-6">
    @php
      $catalogCollection = ($catalogProducts ?? collect());
      $mobileSlides = $catalogCollection->chunk(2);
      $desktopSlides = $catalogCollection->chunk(4);
    @endphp
    @if($catalogCollection->isEmpty())
      <div class="rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 p-6 text-center text-neutral-600 dark:text-neutral-300">
        {{ __('No products available yet. Please add products through the admin panel.') }}
      </div>
    @else
      <div class="space-y-10">
        <div class="relative md:hidden" data-slider>
          <div class="overflow-hidden">
            <div class="flex transition-transform duration-500 ease-out" data-slider-track>
              @foreach($mobileSlides as $slide)
                <div class="w-full flex-shrink-0 basis-full px-2 py-2" data-slider-item>
                  <div class="grid grid-cols-2 gap-3">
                    @foreach($slide as $product)
                      @include('components.catalog-product-card', ['product' => $product])
                    @endforeach
                  </div>
                </div>
              @endforeach
            </div>
          </div>
          @if($mobileSlides->count() > 1)
            <div class="mt-4 flex items-center justify-center gap-6">
              <button type="button" class="inline-flex min-w-[120px] items-center justify-center rounded-full border border-neutral-200 bg-white px-4 py-2 text-sm font-medium text-neutral-700 shadow-sm transition hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-800" data-slider-prev aria-label="{{ __('Previous products') }}">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                {{ __('Previous') }}
              </button>
              <button type="button" class="inline-flex min-w-[120px] items-center justify-center rounded-full border border-neutral-200 bg-white px-4 py-2 text-sm font-medium text-neutral-700 shadow-sm transition hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-800" data-slider-next aria-label="{{ __('Next products') }}">
                {{ __('Next') }}
                <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
            </div>
            <div class="mt-4 flex items-center justify-center gap-2" data-slider-dots>
              @foreach($mobileSlides as $index => $slide)
                <button
                  type="button"
                  class="h-2.5 w-2.5 rounded-full bg-neutral-300 transition-opacity dark:bg-neutral-700"
                  data-slider-dot
                  data-slider-index="{{ $index }}"
                  data-slider-dot-active="bg-accent-500"
                  data-slider-dot-inactive="bg-neutral-300"
                  data-slider-dot-inactive-dark="dark:bg-neutral-700"
                  aria-label="{{ __('Go to slide :number', ['number' => $index + 1]) }}"
                ></button>
              @endforeach
            </div>
          @endif
        </div>

        <div class="relative hidden md:block" data-slider>
          <div class="overflow-hidden">
            <div class="flex transition-transform duration-500 ease-out" data-slider-track>
              @foreach($desktopSlides as $slide)
                <div class="w-full flex-shrink-0 basis-full px-1 sm:px-2" data-slider-item>
                  <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                    @foreach($slide as $product)
                      @include('components.catalog-product-card', ['product' => $product])
                    @endforeach
                  </div>
                </div>
              @endforeach
            </div>
          </div>
          @if($desktopSlides->count() > 1)
            <button type="button" class="absolute left-0 top-1/2 hidden h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 border border-neutral-200 shadow hover:bg-white dark:bg-neutral-900/90 dark:border-neutral-800 dark:hover:bg-neutral-900 md:flex" data-slider-prev aria-label="{{ __('Previous products') }}">
              <svg class="h-4 w-4 text-neutral-700 dark:text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>
            <button type="button" class="absolute right-0 top-1/2 hidden h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 border border-neutral-200 shadow hover:bg-white dark:bg-neutral-900/90 dark:border-neutral-800 dark:hover:bg-neutral-900 md:flex" data-slider-next aria-label="{{ __('Next products') }}">
              <svg class="h-4 w-4 text-neutral-700 dark:text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
            <div class="mt-6 flex items-center justify-center gap-2" data-slider-dots>
              @foreach($desktopSlides as $index => $slide)
                <button
                  type="button"
                  class="h-2.5 w-2.5 rounded-full bg-neutral-300 transition-opacity dark:bg-neutral-700"
                  data-slider-dot
                  data-slider-index="{{ $index }}"
                  data-slider-dot-active="bg-accent-500"
                  data-slider-dot-inactive="bg-neutral-300"
                  data-slider-dot-inactive-dark="dark:bg-neutral-700"
                  aria-label="{{ __('Go to slide :number', ['number' => $index + 1]) }}"
                ></button>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    @endif
  </div>
</section>

<!-- Value Props (clean) -->
<section class="container mx-auto px-6 py-10">
  <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
    @foreach([
      ['Free Shipping','fa-truck-fast'],
      ['Next-day Delivery','fa-bolt'],
      ['60‑day Returns','fa-rotate-left'],
      ['Expert CS','fa-headset'],
      ['Exclusive Brands','fa-gem'],
    ] as $vp)
      <div class="rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-5 text-center">
        <i class="fa-solid {{ $vp[1] }} text-accent-600 text-2xl"></i>
        <div class="mt-2 text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $vp[0] }}</div>
      </div>
    @endforeach
  </div>
</section>

<!-- Newsletter CTA -->
<section class="container mx-auto px-6 pb-12">
  <div class="rounded-2xl bg-gradient-to-r from-orange-500 to-amber-500 p-6 md:p-8 flex flex-col md:flex-row items-center md:items-end justify-between gap-4">
    <div>
      <h3 class="text-white text-xl md:text-2xl font-semibold">{{ __('Subscribe to our newsletter') }}</h3>
      <p class="text-white/90 text-sm mt-1">{{ __('Get the latest updates and special offers delivered straight to your inbox.') }}</p>
    </div>
    <form class="w-full md:w-auto flex items-center gap-2" onsubmit="return false">
      <input type="email" placeholder="{{ __('Enter your email address') }}" class="flex-1 md:w-72 rounded-md border border-white/20 bg-white/10 backdrop-blur px-3 py-2 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/70"/>
      <button class="px-4 py-2 rounded-md bg-white text-orange-600 font-medium hover:bg-white/90">{{ __('Subscribe') }}</button>
    </form>
  </div>
</section>

<!-- Blog Teasers (clean) -->
<section class="container mx-auto px-6 pb-16">
  <h2 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100 mb-6">{{ __('From Our Blog') }}</h2>
  <div class="grid md:grid-cols-3 gap-6">
    @for($i=0;$i<3;$i++)
      <article class="rounded-2xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 overflow-hidden hover:shadow-soft transition">
        <img loading="lazy" src="https://source.unsplash.com/800x480/?electronics,{{ $i }}" alt="Blog {{ $i+1 }}" class="w-full h-40 object-cover"/>
        <div class="p-4">
          <h3 class="font-semibold text-neutral-900 dark:text-white">{{ __('Tech Article') }} {{ $i+1 }}</h3>
          <p class="text-sm text-neutral-600 dark:text-neutral-300 mt-1">{{ __('Tips for choosing components for optimal performance.') }}</p>
          <a href="#" class="text-sm text-accent-600 hover:text-accent-700">{{ __('Read more') }}</a>
        </div>
      </article>
    @endfor
  </div>
</section>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const sliders = document.querySelectorAll('[data-slider]');

    sliders.forEach(function (slider) {
      if (slider.dataset.sliderReady === 'true') {
        return;
      }
      slider.dataset.sliderReady = 'true';

      const track = slider.querySelector('[data-slider-track]');
      const slides = Array.from(slider.querySelectorAll('[data-slider-item]'));

      if (!track || slides.length === 0) {
        return;
      }

      let activeIndex = 0;
      const prevButtons = Array.from(slider.querySelectorAll('[data-slider-prev]'));
      const nextButtons = Array.from(slider.querySelectorAll('[data-slider-next]'));
      const dots = Array.from(slider.querySelectorAll('[data-slider-dot]'));
      const wrapSlides = slider.dataset.sliderWrap === 'true';
      const autoplayDelay = Number(slider.dataset.sliderAutoplay) || 0;
      let autoplayTimer = null;

      const parseClasses = function (dot, attribute, fallback) {
        if (!dot) {
          return fallback;
        }
        const raw = dot.getAttribute(attribute);
        if (raw === null) {
          return fallback;
        }
        const trimmed = raw.trim();
        return trimmed.length === 0 ? [] : trimmed.split(/\s+/);
      };

      const dotConfigs = dots.map(function (dot) {
        return {
          el: dot,
          active: parseClasses(dot, 'data-slider-dot-active', ['bg-accent-500']),
          inactive: parseClasses(dot, 'data-slider-dot-inactive', ['bg-neutral-300']),
          inactiveDark: parseClasses(dot, 'data-slider-dot-inactive-dark', ['dark:bg-neutral-700']),
        };
      });

      const clampIndex = function (value) {
        return Math.min(Math.max(value, 0), slides.length - 1);
      };

      const toggleButtons = function (buttons, isDisabled) {
        buttons.forEach(function (button) {
          button.disabled = isDisabled;
          button.classList.toggle('opacity-50', isDisabled);
          button.classList.toggle('cursor-not-allowed', isDisabled);
          button.setAttribute('aria-disabled', isDisabled ? 'true' : 'false');
        });
      };

      const update = function () {
        track.style.transform = 'translateX(-' + (activeIndex * 100) + '%)';

        dotConfigs.forEach(function (config, dotIndex) {
          const dot = config.el;
          const isActive = dotIndex === activeIndex;
          config.active.forEach(function (cls) { dot.classList.toggle(cls, isActive); });
          config.inactive.forEach(function (cls) { dot.classList.toggle(cls, !isActive); });
          config.inactiveDark.forEach(function (cls) { dot.classList.toggle(cls, !isActive); });
          dot.classList.toggle('opacity-100', isActive);
          dot.classList.toggle('opacity-50', !isActive);
          dot.setAttribute('aria-pressed', isActive ? 'true' : 'false');
          dot.setAttribute('aria-current', isActive ? 'true' : 'false');
        });

        if (wrapSlides) {
          toggleButtons(prevButtons, false);
          toggleButtons(nextButtons, false);
        } else {
          toggleButtons(prevButtons, activeIndex === 0);
          toggleButtons(nextButtons, activeIndex === slides.length - 1);
        }
      };

      const scheduleAutoplay = function () {
        if (!autoplayDelay || slides.length <= 1) {
          return;
        }
        if (autoplayTimer) {
          clearTimeout(autoplayTimer);
        }
        autoplayTimer = setTimeout(function () {
          goTo(activeIndex + 1);
        }, autoplayDelay);
      };

      const goTo = function (index) {
        let target = index;
        if (wrapSlides) {
          if (target < 0) {
            target = slides.length - 1;
          } else if (target >= slides.length) {
            target = 0;
          }
        } else {
          target = clampIndex(target);
        }

        if (target === activeIndex) {
          return;
        }
        activeIndex = target;
        update();
        scheduleAutoplay();
      };

      prevButtons.forEach(function (button) {
        button.addEventListener('click', function () {
          goTo(activeIndex - 1);
        });
      });

      nextButtons.forEach(function (button) {
        button.addEventListener('click', function () {
          goTo(activeIndex + 1);
        });
      });

      dots.forEach(function (dot) {
        dot.addEventListener('click', function (event) {
          event.preventDefault();
          var target = Number(dot.getAttribute('data-slider-index'));
          if (!Number.isNaN(target)) {
            goTo(target);
          }
        });
      });

      slider.setAttribute('tabindex', '0');
      slider.addEventListener('keydown', function (event) {
        if (event.key === 'ArrowLeft') {
          event.preventDefault();
          goTo(activeIndex - 1);
        } else if (event.key === 'ArrowRight') {
          event.preventDefault();
          goTo(activeIndex + 1);
        }
      });

      let touchStartX = 0;
      let touchCurrentX = 0;
      let isTouching = false;

      slider.addEventListener('touchstart', function (event) {
        if (event.touches.length !== 1) {
          return;
        }
        touchStartX = event.touches[0].clientX;
        touchCurrentX = touchStartX;
        isTouching = true;
      }, { passive: true });

      slider.addEventListener('touchmove', function (event) {
        if (!isTouching) {
          return;
        }
        touchCurrentX = event.touches[0].clientX;
      }, { passive: true });

      const endTouch = function () {
        if (!isTouching) {
          return;
        }
        const deltaX = touchCurrentX - touchStartX;
        const threshold = Math.min(slider.clientWidth * 0.15, 80);
        if (Math.abs(deltaX) > threshold) {
          if (deltaX < 0) {
            goTo(activeIndex + 1);
          } else {
            goTo(activeIndex - 1);
          }
        }
        isTouching = false;
      };

      slider.addEventListener('touchend', endTouch, { passive: true });
      slider.addEventListener('touchcancel', endTouch, { passive: true });

      window.addEventListener('resize', function () {
        update();
      });

      slider.addEventListener('mouseenter', function () {
        if (autoplayTimer) {
          clearTimeout(autoplayTimer);
        }
      });

      slider.addEventListener('mouseleave', function () {
        scheduleAutoplay();
      });

      update();
      scheduleAutoplay();
    });
  });
</script>
@endpush
@endsection
