@extends('layouts.app')

@php
  $shipping = collect(json_decode($order->shipping_address ?? '[]', true));
  $items = $order->orderItems ?? collect();
  $itemsTotal = $items->sum(fn($item) => ($item->price ?? 0) * ($item->quantity ?? 0));
  $discountAmount = $order->discount_amount ?? 0;
  $shippingTotal = max($order->total_amount - $itemsTotal + $discountAmount, 0);
  $paymentLabels = ['bank_transfer' => __('Transfer Bank Manual')];
  $paymentLabel = $paymentLabels[$order->payment_method] ?? ucfirst(str_replace('_', ' ', $order->payment_method));
@endphp

@section('content')
<main class="container max-w-4xl py-12 space-y-8" role="main">
  <section class="soft-card p-8 space-y-4 text-center">
    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-sky-100 text-sky-600 dark:bg-sky-900/40 dark:text-sky-300">
      <i class="fa-solid fa-truck-fast text-2xl"></i>
    </div>
    <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Pelacakan Pesanan') }}</h1>
    <div class="inline-flex items-center gap-3 rounded-full bg-neutral-100 px-4 py-1.5 text-xs font-medium text-neutral-700 shadow-inner dark:bg-neutral-900/60 dark:text-neutral-200">
      <span class="uppercase tracking-wide text-[10px] text-neutral-400">{{ __('Nomor Pesanan') }}</span>
      <span>#{{ $order->id }}</span>
      <span class="text-neutral-400">•</span>
      <span>{{ $order->created_at?->format('d M Y H:i') }}</span>
    </div>
    <p class="text-sm text-neutral-500 dark:text-neutral-300">{{ __('Halaman ini dapat diakses siapa saja yang memiliki tautan rahasia Anda.') }}</p>
  </section>

  <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_260px]">
    <div class="soft-card p-6 space-y-6">
      <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="space-y-1">
          <div class="text-xs uppercase tracking-wide text-neutral-400">{{ __('Status Pesanan') }}</div>
          <div class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ ucfirst($order->status) }}</div>
        </div>
        <div class="space-y-1 text-right">
          <div class="text-xs uppercase tracking-wide text-neutral-400">{{ __('Pembayaran') }}</div>
          <div class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ ucfirst($order->payment_status) }}</div>
        </div>
      </div>

      <div class="border-t border-white/60 pt-4 dark:border-neutral-800">
        <h2 class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Ringkasan Produk') }}</h2>
        <div class="mt-3 divide-y divide-white/60 dark:divide-neutral-800">
          @foreach($items as $item)
            <div class="flex items-center justify-between gap-4 py-3 first:pt-0">
              <div class="min-w-[200px]">
                <div class="text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $item->product?->name ?? __('Produk') }}</div>
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
    </div>

    <aside class="soft-card p-6 space-y-4 h-max">
      <h2 class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Ringkasan Pembayaran') }}</h2>
      <dl class="text-sm text-neutral-600 dark:text-neutral-300 space-y-2">
        <div class="flex justify-between"><dt>{{ __('Metode') }}</dt><dd>{{ $paymentLabel }}</dd></div>
        <div class="flex justify-between"><dt>{{ __('Subtotal') }}</dt><dd>{{ format_price($itemsTotal) }}</dd></div>
        <div class="flex justify-between"><dt>{{ __('Diskon') }}</dt><dd>{{ $discountAmount > 0 ? '-'.format_price($discountAmount) : format_price(0) }}</dd></div>
        <div class="flex justify-between"><dt>{{ __('Pengiriman') }}</dt><dd>{{ format_price($shippingTotal) }}</dd></div>
        <div class="flex justify-between border-t border-white/60 pt-3 text-base font-semibold text-neutral-900 dark:border-neutral-800 dark:text-neutral-100"><dt>{{ __('Total') }}</dt><dd>{{ format_price($order->total_amount) }}</dd></div>
      </dl>
      <div class="text-xs text-neutral-500 dark:text-neutral-400">{{ __('Informasi pengiriman (alamat, nomor telepon) disembunyikan di halaman publik ini.') }}</div>
    </aside>
  </section>
</main>
@endsection
