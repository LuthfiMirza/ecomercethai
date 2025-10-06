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
@endphp
<!-- Hero Section / Slider -->
<div class="hero-slider relative h-[600px] overflow-hidden">
    <div class="container mx-auto px-4 h-full flex items-center">
        <div class="w-full md:w-1/2">
            <h1 class="text-5xl font-bold text-cream mb-4">Gaming PC Ultra</h1>
            <p class="text-2xl text-accent mb-8">$1,999.99</p>
            <button class="bg-accent text-white px-8 py-3 rounded-lg hover:bg-accent/90 transition">SHOP NOW</button>
        </div>
    </div>
    <!-- Slider Navigation -->
    <button class="absolute left-4 top-1/2 -translate-y-1/2 text-cream hover:text-accent">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
    </button>
    <button class="absolute right-4 top-1/2 -translate-y-1/2 text-cream hover:text-accent">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
    </button>
</div>

<!-- Category Icons -->
<section class="py-10">
  <div class="container mx-auto px-4">
    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-7 gap-4 md:gap-6">
      @php
        $icons = [
          'CPU' => 'fa-microchip',
          'GPU' => 'fa-bolt',
          'RAM' => 'fa-memory',
          'SSD' => 'fa-sd-card',
          'HDD' => 'fa-hard-drive',
          'PSU' => 'fa-plug',
          'Monitor' => 'fa-display',
          'Fan' => 'fa-fan',
          'Mouse' => 'fa-computer-mouse',
          'Keyboard' => 'fa-keyboard',
          'Headset' => 'fa-headset',
          'USB' => 'fa-usb',
          'OS' => 'fa-windows',
          'Smartphone' => 'fa-mobile-screen-button',
        ];
      @endphp
      @foreach(array_keys($icons) as $category)
        <a href="{{ route('catalog', ['category' => $category]) }}" class="group text-center">
          <div class="w-16 h-16 mx-auto rounded-xl bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 shadow-soft flex items-center justify-center transition-transform group-hover:-translate-y-1">
            <i class="fa-solid {{ $icons[$category] }} text-xl text-neutral-700 dark:text-neutral-200"></i>
          </div>
          <div class="mt-2 text-xs md:text-sm text-neutral-700 dark:text-neutral-300">{{ $category }}</div>
        </a>
      @endforeach
    </div>
  </div>
</section>

<!-- Hot Categories (added) -->
<section class="container mx-auto px-6 py-10">
  <h2 class="text-2xl font-bold text-gray-900 mb-6">Hot Categories</h2>
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
    @foreach(['GPU','CPU','SSD','Monitor','Keyboard','Mouse','Headset','Motherboard','RAM','PSU','Cooler','Laptop'] as $cat)
      <a href="{{ route('catalog', ['category' => $cat]) }}" class="group rounded-xl border bg-white hover:shadow-soft transition p-4 flex flex-col items-center text-center">
        <img loading="lazy" src="https://source.unsplash.com/120x120/?{{ urlencode($cat) }}" alt="{{ $cat }}" class="w-16 h-16 object-cover rounded-md"/>
        <span class="mt-2 text-sm font-medium text-gray-800">{{ $cat }}</span>
      </a>
    @endforeach
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
    <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">Rekomendasi Produk</h2>
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
                <button data-cart-add data-name="{{ $product->name }}" data-price="{{ $formattedPrice }}" data-image="{{ $image }}" class="w-full bg-orange-500 text-white py-2 rounded-lg font-semibold hover:bg-orange-600 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>Buy Now
                </button>
                <button data-wishlist data-name="{{ $product->name }}" data-price="${{ $formattedPrice }}" data-image="{{ $image }}" class="mt-2 w-full border border-orange-300 text-orange-600 py-2 rounded-lg font-medium hover:bg-orange-50 transition-colors">
                    <i class="fa-regular fa-heart mr-2"></i> Wishlist
                </button>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow-lg border border-orange-100 p-6 col-span-full">Belum ada produk rekomendasi. Tambahkan produk melalui panel admin.</div>
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

  <div class="mt-6 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @forelse(($catalogProducts ?? collect()) as $product)
      @php
        $price = number_format($product->price, 2);
        $image = 'https://source.unsplash.com/600x600/?' . urlencode($product->name);
      @endphp
      <article class="group rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 shadow-soft hover:shadow-elevated transition overflow-hidden">
        <a href="{{ route('catalog') }}" class="block">
          <div class="relative aspect-square bg-neutral-100 dark:bg-neutral-800">
            <img src="{{ $image }}" alt="{{ $product->name }}" class="absolute inset-0 w-full h-full object-cover"/>
          </div>
          <div class="p-4 space-y-3">
            <h3 class="text-sm font-medium text-neutral-900 dark:text-neutral-100 leading-snug" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical; overflow:hidden; min-height:3rem;">{{ $product->name }}</h3>
            <div class="flex items-baseline gap-2">
              <div class="text-base font-semibold text-neutral-900 dark:text-neutral-100">${{ $price }}</div>
              <span class="text-xs text-neutral-500">In stock: {{ $product->stock }}</span>
            </div>
            <button data-cart-add data-name="{{ $product->name }}" data-price="{{ $price }}" data-image="{{ $image }}" class="w-full inline-flex items-center justify-center gap-2 rounded-lg border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 py-2 text-sm font-medium text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-800">
              <i class="fa-solid fa-cart-shopping"></i> Add to cart
            </button>
          </div>
        </a>
      </article>
    @empty
      <div class="col-span-full rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 p-6 text-center text-neutral-600 dark:text-neutral-300">
        Belum ada produk yang tersedia. Silakan tambahkan produk melalui panel admin.
      </div>
    @endforelse
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
      <h3 class="text-white text-xl md:text-2xl font-semibold">Berlangganan Newsletter</h3>
      <p class="text-white/90 text-sm mt-1">Dapatkan info terbaru dan penawaran spesial langsung di inbox Anda.</p>
    </div>
    <form class="w-full md:w-auto flex items-center gap-2" onsubmit="return false">
      <input type="email" placeholder="Masukkan email Anda" class="flex-1 md:w-72 rounded-md border border-white/20 bg-white/10 backdrop-blur px-3 py-2 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/70"/>
      <button class="px-4 py-2 rounded-md bg-white text-orange-600 font-medium hover:bg-white/90">Subscribe</button>
    </form>
  </div>
</section>

<!-- Blog Teasers (clean) -->
<section class="container mx-auto px-6 pb-16">
  <h2 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100 mb-6">From Our Blog</h2>
  <div class="grid md:grid-cols-3 gap-6">
    @for($i=0;$i<3;$i++)
      <article class="rounded-2xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 overflow-hidden hover:shadow-soft transition">
        <img loading="lazy" src="https://source.unsplash.com/800x480/?electronics,{{ $i }}" alt="Blog {{ $i+1 }}" class="w-full h-40 object-cover"/>
        <div class="p-4">
          <h3 class="font-semibold text-neutral-900 dark:text-white">Artikel Tech {{ $i+1 }}</h3>
          <p class="text-sm text-neutral-600 dark:text-neutral-300 mt-1">Tips memilih komponen untuk performa optimal.</p>
          <a href="#" class="text-sm text-accent-600 hover:text-accent-700">Read more</a>
        </div>
      </article>
    @endfor
  </div>
</section>
@endsection
