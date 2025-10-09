@extends('layouts.app')

@section('content')
<main class="container max-w-6xl py-10 space-y-6" role="main">
  <div class="flex items-center justify-between gap-3 flex-wrap">
    <div>
      <h1 class="text-3xl font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Keranjang Belanja') }}</h1>
      <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Tinjau produk yang ingin Anda beli sebelum melanjutkan ke checkout.') }}</p>
    </div>
    @if($cartItems->isNotEmpty())
      <form method="POST" action="{{ route('cart.clear') }}">
        @csrf
        @method('DELETE')
        <x-button type="submit" variant="ghost" class="text-sm text-red-600 hover:text-red-700">{{ __('Kosongkan Keranjang') }}</x-button>
      </form>
    @endif
  </div>

  @if(session('success'))
    <x-alert type="success">{{ session('success') }}</x-alert>
  @endif
  @if(session('error'))
    <x-alert type="error">{{ session('error') }}</x-alert>
  @endif
  @if($errors->any())
    <x-alert type="error">
      {{ $errors->first() }}
    </x-alert>
  @endif

  @if($cartItems->isEmpty())
    <section class="soft-card p-10 text-center space-y-4">
      <div class="text-5xl">🛒</div>
      <h2 class="text-xl font-semibold text-neutral-800 dark:text-neutral-100">{{ __('Keranjang Anda masih kosong') }}</h2>
      <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Mari temukan produk menarik dan tambahkan ke keranjang Anda.') }}</p>
      <x-button href="{{ route('catalog') }}" class="inline-flex items-center gap-2">
        <i class="fa-solid fa-store"></i>
        <span>{{ __('Jelajahi Katalog') }}</span>
      </x-button>
    </section>
  @else
    <div class="grid lg:grid-cols-[minmax(0,1fr)_320px] gap-6">
      <section class="space-y-4">
        @foreach($cartItems as $item)
          @php
            $product = $item->product;
            $image = null;
            if ($product && $product->image) {
                $image = \Illuminate\Support\Str::startsWith($product->image, ['http://', 'https://'])
                    ? $product->image
                    : asset('storage/' . ltrim($product->image, '/'));
            }
            $image = $image ?? 'https://source.unsplash.com/160x160/?product,' . urlencode($product?->name ?? '');
          @endphp
          <article class="soft-card p-5 flex gap-4 items-start">
            <img src="{{ $image }}" alt="{{ $product?->name ?? __('Produk') }}" class="w-24 h-24 object-cover rounded-xl border border-white/60 dark:border-neutral-700" loading="lazy"/>
            <div class="flex-1 space-y-2">
              <div>
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ $product?->name ?? __('Produk tidak tersedia') }}</h3>
                @if($product?->brand)
                  <p class="text-xs uppercase tracking-wide text-neutral-400">{{ $product->brand }}</p>
                @endif
              </div>
              <div class="flex flex-wrap items-center gap-3 text-sm text-neutral-500 dark:text-neutral-400">
                <span>{{ __('Harga satuan:') }} <strong class="text-neutral-800 dark:text-neutral-100">{{ format_price($item->price) }}</strong></span>
                <span>&bull;</span>
                <span>{{ __('Subtotal:') }} <strong class="text-neutral-800 dark:text-neutral-100">{{ format_price($item->subtotal) }}</strong></span>
              </div>
              <div class="flex items-center gap-3 flex-wrap">
                <form method="POST" action="{{ route('cart.update', $item->id) }}" class="flex items-center gap-3 flex-wrap">
                  @csrf
                  @method('PUT')
                  <label class="inline-flex items-center gap-2 text-sm text-neutral-600 dark:text-neutral-300">
                    <span>{{ __('Jumlah') }}</span>
                    <input type="number" name="quantity" min="1" value="{{ $item->quantity }}" class="w-20 rounded-md border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 px-2 py-1 text-sm" />
                  </label>
                  <x-button type="submit" size="sm" variant="outline">{{ __('Perbarui') }}</x-button>
                </form>
                <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                  @csrf
                  @method('DELETE')
                  <x-button type="submit" size="sm" variant="ghost" class="text-red-600 hover:text-red-700">{{ __('Hapus') }}</x-button>
                </form>
              </div>
            </div>
          </article>
        @endforeach
      </section>
      <aside class="soft-card p-6 space-y-4 h-max">
        <header class="space-y-1">
          <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Ringkasan Pesanan') }}</h2>
          <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Pastikan detail sudah benar sebelum checkout.') }}</p>
        </header>
        <dl class="space-y-2 text-sm text-neutral-600 dark:text-neutral-300">
          <div class="flex justify-between">
            <dt>{{ __('Jumlah item') }}</dt>
            <dd>{{ $cartItems->sum('quantity') }}</dd>
          </div>
          <div class="flex justify-between">
            <dt>{{ __('Subtotal') }}</dt>
            <dd>{{ format_price($subtotal) }}</dd>
          </div>
          <div class="flex justify-between">
            <dt>{{ __('Perkiraan Pengiriman') }}</dt>
            <dd>{{ __('Dihitung saat checkout') }}</dd>
          </div>
        </dl>
        <div class="border-t border-white/70 dark:border-neutral-800 pt-3 flex justify-between items-center text-base font-semibold text-neutral-900 dark:text-neutral-100">
          <span>{{ __('Total Sementara') }}</span>
          <span>{{ format_price($subtotal) }}</span>
        </div>
        @auth
          <x-button href="{{ route('checkout') }}" class="w-full justify-center">{{ __('Lanjut ke Checkout') }}</x-button>
        @else
          <x-alert type="info" class="text-sm">{{ __('Masuk terlebih dahulu untuk melanjutkan checkout.') }}</x-alert>
          <x-button href="{{ route('login') }}" class="w-full justify-center">{{ __('Masuk / Daftar') }}</x-button>
        @endauth
      </aside>
    </div>
  @endif
</main>
@endsection
