@extends('layouts.app')

@php
  $paymentRoutes = [
      'bank_transfer' => localized_route('payment.bank-transfer', ['order' => $order->id]),
  ];
  $paymentLabels = [
      'bank_transfer' => __('Transfer Bank Manual'),
  ];
  $paymentLabel = $paymentLabels[$order->payment_method] ?? ucfirst(str_replace('_', ' ', $order->payment_method));
@endphp

@section('content')
<main class="container max-w-4xl py-14 space-y-10" role="main">
  @if(session('success'))
    <x-alert type="success">{{ session('success') }}</x-alert>
  @endif
  <section class="soft-card p-8 space-y-6 text-center">
    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-300">
      <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 13l4 4L19 7" />
      </svg>
    </div>
    <div class="space-y-2">
      <h1 class="text-3xl font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Pesanan Berhasil Dibuat!') }}</h1>
      <p class="text-sm text-neutral-500 dark:text-neutral-300">{{ __('Terima kasih, pesanan Anda sudah kami terima dan sedang diproses.') }}</p>
    </div>
    <div class="inline-flex items-center gap-3 rounded-full bg-neutral-100 px-4 py-2 text-sm font-medium text-neutral-700 shadow-inner dark:bg-neutral-900/60 dark:text-neutral-200">
      <span class="uppercase tracking-wide text-xs text-neutral-400">{{ __('Nomor Pesanan') }}</span>
      <span>#{{ $order->id }}</span>
      <span class="text-xs text-neutral-400">• {{ $order->created_at?->format('d M Y, H:i') }}</span>
    </div>
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-center">
      <a href="{{ localized_route('orders.show', ['order' => $order]) }}" class="btn-primary">{{ __('Lihat Detail Pesanan') }}</a>
      @isset($paymentRoutes[$order->payment_method])
        <a href="{{ $paymentRoutes[$order->payment_method] }}" class="btn-outline">{{ __('Instruksi Pembayaran :method', ['method' => $paymentLabel]) }}</a>
      @endisset
      @if($order->track_url)
        <a href="{{ $order->track_url }}" class="btn-outline">{{ __('Lacak Pesanan (Publik)') }}</a>
      @endif
      <a href="{{ localized_route('home') }}" class="btn-ghost">{{ __('Belanja Lagi') }}</a>
    </div>
  </section>

  <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_260px]">
    <div class="soft-card p-6 space-y-4">
      <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Ringkasan Pesanan') }}</h2>
      <div class="divide-y divide-white/60 dark:divide-neutral-800">
        @foreach($items as $item)
          <div class="flex flex-wrap items-center justify-between gap-4 py-4 first:pt-0">
            <div class="flex-1 min-w-[200px]">
              <div class="text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $item->product?->name ?? __('Produk') }}</div>
              @if($item->product?->brand)
                <div class="text-xs text-neutral-500">{{ $item->product->brand }}</div>
              @endif
              <div class="text-xs text-neutral-500">{{ __('Qty: :qty', ['qty' => $item->quantity]) }}</div>
            </div>
            <div class="text-right">
              <div class="text-sm text-neutral-500">{{ format_price($item->price) }}</div>
              <div class="font-semibold text-neutral-900 dark:text-neutral-100">{{ format_price($item->price * $item->quantity) }}</div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    <aside class="soft-card p-6 space-y-4 h-max">
      <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Detail Pengiriman') }}</h2>
      <ul class="space-y-1 text-sm text-neutral-600 dark:text-neutral-300">
        <li class="font-medium text-neutral-900 dark:text-neutral-100">{{ $shipping->get('name') }}</li>
        <li>{{ $shipping->get('phone') }}</li>
        <li>{{ $shipping->get('address_line1') }}</li>
        @if($shipping->get('address_line2'))
          <li>{{ $shipping->get('address_line2') }}</li>
        @endif
        <li>{{ collect([$shipping->get('city'), $shipping->get('state'), $shipping->get('postal_code')])->filter()->implode(', ') }}</li>
        <li>{{ $shipping->get('country') }}</li>
      </ul>
      <div class="border-t border-white/60 pt-4 text-sm text-neutral-600 dark:border-neutral-800 dark:text-neutral-300 space-y-1">
        <div class="flex justify-between"><span>{{ __('Subtotal') }}</span><span>{{ format_price($itemsTotal) }}</span></div>
        <div class="flex justify-between"><span>{{ __('Diskon') }}</span><span>{{ $discountAmount > 0 ? '-'.format_price($discountAmount) : format_price(0) }}</span></div>
        <div class="flex justify-between"><span>{{ __('Pengiriman') }}</span><span>{{ format_price($shippingTotal) }}</span></div>
        <div class="flex justify-between border-t border-white/60 pt-3 text-base font-semibold text-neutral-900 dark:border-neutral-800 dark:text-neutral-100">
          <span>{{ __('Total') }}</span><span>{{ format_price($order->total_amount) }}</span>
        </div>
      </div>
      <x-alert type="info" class="text-xs">
        {{ __('Kami telah mengirim email konfirmasi. Jika Anda memilih pembayaran manual, selesaikan pembayaran agar pesanan segera diproses.') }}
      </x-alert>
    </aside>
  </section>
</main>
@endsection
