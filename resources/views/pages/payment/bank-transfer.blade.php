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
      <x-badge variant="accent">{{ __('Menunggu Pembayaran') }}</x-badge>
      <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
          <h1 class="text-3xl font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Transfer Bank Manual') }}</h1>
          <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Selesaikan pembayaran Anda kemudian unggah bukti transfer untuk kami verifikasi.') }}</p>
        </div>
        <div class="text-right space-y-1">
          <div class="text-xs uppercase tracking-wide text-neutral-400">{{ __('Nomor Pesanan') }}</div>
          <div class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">#{{ $order->id }}</div>
          <div class="text-xs text-neutral-500">{{ $order->created_at?->format('d M Y, H:i') }}</div>
        </div>
      </div>
    </header>

    <div class="grid gap-6 md:grid-cols-2">
      <div class="soft-card border border-dashed border-neutral-300/80 bg-white/80 p-5 dark:border-neutral-700 dark:bg-neutral-900/60">
        <h2 class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Informasi Rekening') }}</h2>
        <ul class="mt-3 space-y-3 text-sm text-neutral-600 dark:text-neutral-300">
          <li>
            <div class="flex items-center justify-between">
              <span class="font-medium">BCA</span>
              <x-badge variant="outline">{{ __('Utama') }}</x-badge>
            </div>
            <div class="mt-1 text-neutral-500">No. Rek: <strong>123 456 7890</strong><br/>a.n <strong>PT Toko Thailand</strong></div>
          </li>
          <li class="border-t border-dashed border-neutral-200 pt-3 dark:border-neutral-800">
            <div class="flex items-center justify-between">
              <span class="font-medium">Mandiri</span>
            </div>
            <div class="mt-1 text-neutral-500">No. Rek: <strong>987 654 3210</strong><br/>a.n <strong>PT Toko Thailand</strong></div>
          </li>
        </ul>
        <x-alert type="info" class="mt-4 text-xs">{{ __('Gunakan berita “Pembayaran pesanan #” dan pastikan jumlah transfer sesuai total pesanan.') }}</x-alert>
      </div>

      <div class="soft-card bg-white/80 p-5 space-y-4 dark:bg-neutral-900/60">
        <h2 class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Unggah Bukti Pembayaran') }}</h2>
        <form method="POST" action="{{ route('payment.upload-proof', $order->id) }}" enctype="multipart/form-data" class="space-y-4">
          @csrf
          <x-payment-proof-upload />
          <x-button type="submit" class="w-full justify-center">
            <i class="fa-solid fa-cloud-arrow-up mr-2"></i>{{ __('Kirim Bukti Transfer') }}
          </x-button>
        </form>
        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('Tim kami akan memverifikasi pembayaran dalam 1x24 jam kerja.') }}</p>
        @if($order->payment_proof_path)
          <x-alert type="success" class="text-xs">{{ __('Bukti pembayaran sudah diterima. Status saat ini:') }} <strong>{{ __(ucfirst($order->payment_status)) }}</strong></x-alert>
        @endif
      </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
      <div class="soft-card p-5 space-y-3">
        <h2 class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Alamat Pengiriman') }}</h2>
        <ul class="text-sm text-neutral-600 dark:text-neutral-300 space-y-1">
          <li class="font-medium text-neutral-900 dark:text-neutral-100">{{ $shipping->get('name') }}</li>
          <li>{{ $shipping->get('phone') }}</li>
          @foreach(($shipping->get('address_line1') ? [$shipping->get('address_line1')] : []) as $line)
            <li>{{ $line }}</li>
          @endforeach
          @if($shipping->get('address_line2'))
            <li>{{ $shipping->get('address_line2') }}</li>
          @endif
          <li>{{ collect([$shipping->get('city'), $shipping->get('state'), $shipping->get('postal_code')])->filter()->implode(', ') }}</li>
          <li>{{ $shipping->get('country') }}</li>
        </ul>
      </div>

      <div class="soft-card p-5 space-y-3">
        <h2 class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Ringkasan Pembayaran') }}</h2>
        <dl class="text-sm text-neutral-600 dark:text-neutral-300 space-y-2">
          <div class="flex justify-between">
            <dt>{{ __('Subtotal') }}</dt>
            <dd>{{ format_price($itemsTotal) }}</dd>
          </div>
          <div class="flex justify-between">
            <dt>{{ __('Diskon') }}</dt>
            <dd>{{ $discountAmount > 0 ? '-'.format_price($discountAmount) : format_price(0) }}</dd>
          </div>
          <div class="flex justify-between">
            <dt>{{ __('Pengiriman') }}</dt>
            <dd>{{ format_price($shippingTotal) }}</dd>
          </div>
          <div class="flex justify-between text-base font-semibold text-neutral-900 dark:text-neutral-100">
            <dt>{{ __('Total Dibayar') }}</dt>
            <dd>{{ format_price($order->total_amount) }}</dd>
          </div>
        </dl>
      </div>
    </div>

    <div class="soft-card p-5">
      <h2 class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Detail Produk') }}</h2>
      <div class="mt-4 space-y-4">
        @foreach($items as $item)
          <div class="flex items-center justify-between gap-4 text-sm">
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
