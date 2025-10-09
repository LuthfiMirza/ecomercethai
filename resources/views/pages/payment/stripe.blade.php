@extends('layouts.app')

@php
  $shipping = collect(json_decode($order->shipping_address ?? '[]', true));
  $items = $order->orderItems ?? collect();
  $itemsTotal = $items->sum(fn($item) => $item->price * $item->quantity);
  $discountAmount = $order->discount_amount ?? 0;
  $shippingTotal = max($order->total_amount - $itemsTotal + $discountAmount, 0);
@endphp

@section('content')
<main class="container max-w-4xl py-10 space-y-6" role="main">
  <a href="{{ route('orders.show', $order->id) }}" class="inline-flex items-center gap-2 text-sm text-neutral-500 hover:text-neutral-700">
    <i class="fa-solid fa-chevron-left text-xs"></i>
    <span>{{ __('Kembali ke detail pesanan') }}</span>
  </a>

  <section class="soft-card p-6 md:p-8 space-y-6">
    <header class="space-y-2">
      <x-badge variant="accent">{{ ucfirst($order->payment_status) }}</x-badge>
      <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
          <h1 class="text-3xl font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Pembayaran via Stripe') }}</h1>
          <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Gunakan kartu kredit/debit internasional dengan perlindungan 3D Secure.') }}</p>
        </div>
        <div class="text-right space-y-1">
          <div class="text-xs uppercase tracking-wide text-neutral-400">{{ __('Nomor Pesanan') }}</div>
          <div class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">#{{ $order->id }}</div>
          <div class="text-xs text-neutral-500">{{ $order->created_at?->format('d M Y, H:i') }}</div>
        </div>
      </div>
    </header>

    <div class="soft-card p-5 bg-white/80 dark:bg-neutral-900/60 space-y-4">
      <h2 class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Langkah Pembayaran Stripe') }}</h2>
      <ol class="list-decimal list-inside space-y-2 text-sm text-neutral-600 dark:text-neutral-300">
        <li>{{ __('Klik tombol di bawah untuk membuka sesi pembayaran Stripe.') }}</li>
        <li>{{ __('Masukkan detail kartu Anda pada halaman Stripe yang aman.') }}</li>
        <li>{{ __('Selesaikan autentikasi 3D Secure jika diminta.') }}</li>
        <li>{{ __('Anda akan diarahkan kembali ke situs setelah pembayaran berhasil.') }}</li>
      </ol>
      <x-button type="button" class="inline-flex items-center gap-2">
        <i class="fa-brands fa-cc-stripe text-base"></i>
        <span>{{ __('Buka Checkout Stripe') }}</span>
      </x-button>
      <x-alert type="info" class="text-xs">{{ __('Aktifkan Stripe API key dan sesi checkout sebelum menggunakan tombol ini di produksi.') }}</x-alert>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
      <div class="soft-card p-5 space-y-3">
        <h2 class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Ringkasan Pembayaran') }}</h2>
        <dl class="text-sm text-neutral-600 dark:text-neutral-300 space-y-2">
          <div class="flex justify-between"><dt>{{ __('Subtotal') }}</dt><dd>{{ format_price($itemsTotal) }}</dd></div>
          <div class="flex justify-between"><dt>{{ __('Diskon') }}</dt><dd>{{ $discountAmount > 0 ? '-'.format_price($discountAmount) : format_price(0) }}</dd></div>
          <div class="flex justify-between"><dt>{{ __('Pengiriman') }}</dt><dd>{{ format_price($shippingTotal) }}</dd></div>
          <div class="flex justify-between text-base font-semibold text-neutral-900 dark:text-neutral-100"><dt>{{ __('Total Dibayar') }}</dt><dd>{{ format_price($order->total_amount) }}</dd></div>
        </dl>
      </div>
      <div class="soft-card p-5 space-y-3">
        <h2 class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Alamat Pengiriman') }}</h2>
        <ul class="text-sm text-neutral-600 dark:text-neutral-300 space-y-1">
          <li class="font-medium text-neutral-900 dark:text-neutral-100">{{ $shipping->get('name') }}</li>
          <li>{{ $shipping->get('phone') }}</li>
          <li>{{ $shipping->get('address_line1') }}</li>
          @if($shipping->get('address_line2'))
            <li>{{ $shipping->get('address_line2') }}</li>
          @endif
          <li>{{ collect([$shipping->get('city'), $shipping->get('state'), $shipping->get('postal_code')])->filter()->implode(', ') }}</li>
          <li>{{ $shipping->get('country') }}</li>
        </ul>
      </div>
    </div>

    <div class="soft-card p-5">
      <h2 class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Detail Produk') }}</h2>
      <div class="mt-4 space-y-4 text-sm">
        @foreach($items as $item)
          <div class="flex items-center justify-between gap-4">
            <div class="flex-1">
              <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $item->product?->name ?? __('Produk') }}</p>
              <p class="text-xs text-neutral-500">{{ __('Qty: :qty', ['qty' => $item->quantity]) }}</p>
            </div>
            <div class="text-right">
              <p class="text-sm text-neutral-500">{{ format_price($item->price) }}</p>
              <p class="font-semibold text-neutral-900 dark:text-neutral-100">{{ format_price($item->price * $item->quantity) }}</p>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>
</main>
@endsection
