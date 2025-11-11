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
            'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=1600&q=80',
            'alt' => 'Custom gaming PC setup',
        ],
        [
            'title' => __('Next-Gen CPU Power'),
            'subtitle' => __('AMD Ryzen & Intel Core line-up ready to ship'),
            'description' => __('Temukan prosesor favoritmu dengan stok terjamin dan garansi resmi pabrikan.'),
            'image' => 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=1600&q=80',
            'alt' => 'Close-up of a high-end CPU',
        ],
        [
            'title' => __('Graphics Ready for Ray Tracing'),
            'subtitle' => __('RTX & Radeon terbaru untuk visual maksimal'),
            'description' => __('Rasakan performa GPU kelas atas untuk gaming 4K, VR, dan kebutuhan kreatif profesional.'),
            'image' => 'https://images.unsplash.com/photo-1618005198919-d3d4b5a92eee?auto=format&fit=crop&w=1600&q=80',
            'alt' => 'High-end graphics card on a desk',
        ],
    ];
    $defaultBannerImages = [
        'homepage_top' => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=1600&q=80',
        'homepage_sidebar' => 'https://images.unsplash.com/photo-1517430816045-df4b7de49276?auto=format&fit=crop&w=900&q=80',
        'homepage_bottom' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=1600&q=80',
    ];
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
@endphp

<!-- Hero Section / Slider -->
<section class="relative">
  <div class="hero-slider relative overflow-hidden rounded-b-[42px] bg-neutral-900 text-white shadow-lg md:rounded-b-[56px]" data-slider data-slider-autoplay="7000" data-slider-wrap="true">
    <div class="flex h-full min-h-[440px] sm:min-h-[500px] md:min-h-[560px] lg:min-h-[600px] transition-transform duration-700 ease-out" data-slider-track>
      @foreach($heroSlides as $index => $slide)
        <article class="relative flex h-full w-full flex-shrink-0 basis-full items-center justify-center px-4 sm:px-6 md:px-0" data-slider-item>
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
            <div class="container mx-auto flex h-full flex-col justify-center px-4 py-12 md:py-20 lg:py-24">
              <div class="max-w-2xl">
                <div class="space-y-4 rounded-3xl border border-white/10 bg-white/10 p-6 text-white shadow-[0_20px_45px_rgba(15,15,15,0.45)] backdrop-blur-md sm:space-y-6 sm:p-8 md:bg-white/12 transform translate-y-4 sm:translate-y-6 md:translate-y-8 lg:translate-y-10">
                  <div class="flex items-center gap-3">
                    <img src="{{ asset('image/logo.jpg') }}" alt="{{ config('app.name', 'Lungpaeit') }}" class="h-10 w-10 rounded-full object-cover shadow" loading="lazy">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-white/70 sm:text-sm">{{ strtoupper(config('app.name', 'Lungpaeit')) }} Exclusive</p>
                  </div>
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
        $iconCategories = [
          ['slug' => 'CPU', 'icon' => 'fa-microchip', 'label' => __('home.category_cpu')],
          ['slug' => 'GPU', 'icon' => 'fa-bolt', 'label' => __('home.category_gpu')],
          ['slug' => 'RAM', 'icon' => 'fa-memory', 'label' => __('home.category_ram')],
          ['slug' => 'SSD', 'icon' => 'fa-sd-card', 'label' => __('home.category_ssd')],
        ];
      @endphp
      @foreach($iconCategories as $item)
        <a href="{{ route('catalog', ['category' => $item['slug']]) }}" class="group w-[150px]">
          <div class="h-[120px] rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 shadow-soft flex flex-col items-center justify-center transition-transform group-hover:-translate-y-1">
            <i class="fa-solid {{ $item['icon'] }} text-2xl text-accent-500"></i>
            <span class="mt-3 text-sm font-semibold text-neutral-800 dark:text-neutral-100">{{ $item['label'] }}</span>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</section>

<!-- Top Advertisement Banner -->
<div class="container mx-auto px-6 my-8">
    <div class="bg-white dark:bg-neutral-900 p-4 rounded-md text-center border border-neutral-200 dark:border-neutral-800">
        @if($topBanner && ($src = $bannerUrl($topBanner)))
            <img src="{{ $src }}"
                 alt="{{ optional($topBanner)->title ?? __('home.top_banner_alt') }}"
                 class="mx-auto max-w-full h-auto rounded-lg object-cover">
        @else
            <img src="{{ $defaultBannerImages['homepage_top'] }}"
                 alt="{{ __('home.top_banner_fallback') }}"
                 class="mx-auto max-w-full h-auto rounded-lg object-cover">
        @endif
    </div>
</div>

<!-- Rekomendasi Produk Section -->
<div class="container mx-auto px-6">
    <h2 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100 mb-8 text-center">{{ __('home.recommended_title') }}</h2>
    <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 justify-items-center">
        @forelse(($featuredProducts ?? collect()) as $product)
            @php
                $image = $productImageResolver($product);
            @endphp
            @include('components.catalog-product-card', ['product' => $product, 'image' => $image])
        @empty
            <div class="col-span-full rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 p-8 text-center text-neutral-700 dark:text-neutral-300">
                <img src="https://images.unsplash.com/photo-1516700675895-4204b3f074bf?auto=format&fit=crop&w=600&q=80"
                     alt="{{ __('home.recommended_empty_alt') }}"
                     class="mx-auto mb-5 h-28 w-28 rounded-full object-cover shadow-inner">
                <h3 class="text-lg font-semibold mb-2">{{ __('home.recommended_empty_title') }}</h3>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('home.recommended_empty_desc') }}</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Middle Advertisement Banner -->
<div class="container mx-auto px-6 my-12">
    @php $midBannerSrc = $midBanner ? $bannerUrl($midBanner) : null; @endphp
    <div class="bg-white dark:bg-neutral-900 border-2 border-orange-500/80 dark:border-orange-400/70 p-4 rounded-md text-center">
        <img src="{{ $midBannerSrc ?? $defaultBannerImages['homepage_sidebar'] }}"
             alt="{{ optional($midBanner)->title ?? __('home.mid_banner_alt') }}"
             class="mx-auto max-w-full h-auto rounded-lg object-cover">
    </div>
</div>

<!-- Horizontal Banner Between Sections -->
<div class="container mx-auto px-6 py-8">
    <div class="bg-white dark:bg-neutral-900 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow border border-neutral-200 dark:border-neutral-800">
        @if($bottomBanner && ($src = $bannerUrl($bottomBanner)))
            <img src="{{ $src }}" alt="{{ $bottomBanner->title }}" class="w-full h-40 object-cover">
        @else
            <img src="{{ $defaultBannerImages['homepage_bottom'] }}" alt="{{ __('home.bottom_banner_alt') }}" class="w-full h-40 object-cover">
        @endif
    </div>
</div>

<!-- Product Catalog Section (Redesigned from here downward) -->
<section class="container mx-auto px-6 py-12">
  <div class="flex flex-wrap items-center justify-between gap-3">
    <h2 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('home.catalog_title') }}</h2>
    <p class="text-sm text-neutral-600 dark:text-neutral-300">{{ __('home.catalog_subtitle') }}</p>
  </div>

  <div class="mt-6">
    @php
      $catalogCollection = ($catalogProducts ?? collect());
      $mobileSlides = $catalogCollection->chunk(2);
      $desktopSlides = $catalogCollection->chunk(4);
    @endphp
    @if($catalogCollection->isEmpty())
      <div class="rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 p-6 text-center text-neutral-600 dark:text-neutral-300">
        {{ __('home.catalog_empty') }}
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
                      @php
                        $productImage = $productImageResolver($product);
                      @endphp
                      @include('components.catalog-product-card', ['product' => $product, 'image' => $productImage])
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
                      @php
                        $productImage = $productImageResolver($product);
                      @endphp
                      @include('components.catalog-product-card', ['product' => $product, 'image' => $productImage])
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
  @php
    $valueProps = trans('home.value_props');
  @endphp
  <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
    @foreach($valueProps as $vp)
      <div class="rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-5 text-center">
        <i class="fa-solid {{ $vp['icon'] }} text-accent-600 text-2xl"></i>
        <div class="mt-2 text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $vp['label'] }}</div>
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
  <h2 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100 mb-6">{{ __('home.blog_title') }}</h2>
  @php
    $blogImages = [
      'https://source.unsplash.com/800x480/?electronics,1',
      'https://source.unsplash.com/800x480/?electronics,2',
      'https://source.unsplash.com/800x480/?electronics,3',
    ];
  @endphp
  <div class="grid md:grid-cols-3 gap-6">
    @for($i=0;$i<3;$i++)
      <article class="rounded-2xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 overflow-hidden hover:shadow-soft transition">
        <img loading="lazy" src="{{ $blogImages[$i] ?? $blogImages[0] }}" alt="{{ __('home.blog_image_alt', ['number' => $i + 1]) }}" class="w-full h-40 object-cover"/>
        <div class="p-4">
          <h3 class="font-semibold text-neutral-900 dark:text-white">{{ __('home.blog_card_title') }} {{ $i+1 }}</h3>
          <p class="text-sm text-neutral-600 dark:text-neutral-300 mt-1">{{ __('home.blog_card_excerpt') }}</p>
          <a href="#" class="text-sm text-accent-600 hover:text-accent-700">{{ __('home.blog_card_cta') }}</a>
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
