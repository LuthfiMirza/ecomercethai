@extends('layouts.app')

@section('content')
<main id="main" class="min-h-screen" role="main">
  <!-- Hero -->
  <section class="relative overflow-hidden bg-gradient-to-r from-neutral-900 via-neutral-800 to-neutral-900 text-white">
    <div class="container py-16 md:py-20 grid md:grid-cols-2 items-center gap-10">
      <div>
        <h1 class="text-3xl md:text-5xl font-bold leading-tight">Rakitan PC Modern untuk Semua Kebutuhan</h1>
        <p class="mt-4 text-neutral-300">Dari gaming hingga kreator, temukan komponen berkualitas dengan harga bersaing.</p>
        <div class="mt-6 flex gap-3">
          <x-button href="{{ route('catalog') }}">Belanja Sekarang</x-button>
          <x-button href="{{ localized_url('deals') }}" variant="outline">Lihat Promo</x-button>
        </div>
      </div>
      <div class="relative">
        <img loading="lazy" src="https://images.unsplash.com/photo-1518779578993-ec3579fee39f?q=80&w=1200&auto=format&fit=crop" alt="PC Rakitan" class="rounded-2xl shadow-elevated"/>
      </div>
    </div>
  </section>

  <!-- Features -->
  <section class="container py-12 md:py-16">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div class="rounded-xl p-5 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 shadow-soft text-center">
        <i class="fa-solid fa-truck-fast text-2xl text-accent-600"></i>
        <div class="mt-2 font-medium">Pengiriman Cepat</div>
      </div>
      <div class="rounded-xl p-5 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 shadow-soft text-center">
        <i class="fa-solid fa-shield-halved text-2xl text-accent-600"></i>
        <div class="mt-2 font-medium">Garansi Resmi</div>
      </div>
      <div class="rounded-xl p-5 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 shadow-soft text-center">
        <i class="fa-solid fa-headset text-2xl text-accent-600"></i>
        <div class="mt-2 font-medium">Dukungan Pro</div>
      </div>
      <div class="rounded-xl p-5 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 shadow-soft text-center">
        <i class="fa-solid fa-tags text-2xl text-accent-600"></i>
        <div class="mt-2 font-medium">Harga Terbaik</div>
      </div>
    </div>
  </section>

  <!-- Product grid example usage -->
  <section class="container py-8 md:py-12">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-xl md:text-2xl font-semibold">Produk Unggulan</h2>
      <a href="{{ route('catalog') }}" class="text-accent-600 hover:text-accent-700 text-sm">Lihat semua</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
      @foreach([
        ['RTX 4070 Ti', 1299.99, 1499.99, 4.7, 221, 'https://images.unsplash.com/photo-1587202372616-b0434d16fc5f?q=80&w=1200&auto=format&fit=crop'],
        ['Mechanical Keyboard', 89.00, 99.00, 4.5, 145, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?q=80&w=1200&auto=format&fit=crop'],
        ['27" 4K Monitor', 399.00, null, 4.6, 90, 'https://images.unsplash.com/photo-1587206668285-7d78f0b8ab0e?q=80&w=1200&auto=format&fit=crop'],
        ['NVMe SSD 1TB', 119.00, 159.00, 4.8, 500, 'https://images.unsplash.com/photo-1616031037116-99b9f8a6b9ce?q=80&w=1200&auto=format&fit=crop'],
      ] as $p)
        <x-product-card :title="$p[0]" :price="$p[1]" :compare-at="$p[2]" :rating="$p[3]" :reviews="$p[4]" :image="$p[5]" badge="Hot" />
      @endforeach
    </div>
  </section>
</main>
@endsection
