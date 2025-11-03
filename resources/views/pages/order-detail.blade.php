@extends('layouts.app')

@php
  $shipping = collect(json_decode($order->shipping_address ?? '[]', true));
  $items = $order->orderItems ?? collect();
  $itemsTotal = $items->sum(fn($item) => $item->price * $item->quantity);
  $discountAmount = $order->discount_amount ?? 0;
  $shippingTotal = max($order->total_amount - $itemsTotal + $discountAmount, 0);
  $paymentRoutes = [
    'bank_transfer' => localized_route('payment.bank-transfer', ['order' => $order->id]),
  ];
  $paymentLabels = [
    'bank_transfer' => __('Transfer Bank Manual'),
  ];
  $paymentLabel = $paymentLabels[$order->payment_method] ?? ucfirst(str_replace('_', ' ', $order->payment_method));
@endphp

@section('content')
<main class="container max-w-5xl py-10 space-y-6" role="main">
  <div class="flex flex-wrap items-start justify-between gap-4">
    <div>
      <h1 class="text-3xl font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Detail Pesanan') }}</h1>
      <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Dibuat pada :date', ['date' => $order->created_at?->format('d M Y, H:i')]) }}</p>
    </div>
    <div class="flex flex-col items-end gap-2">
      <div class="text-xs uppercase tracking-wide text-neutral-400">{{ __('Nomor Pesanan') }}</div>
      <div class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">#{{ $order->id }}</div>
      <div class="flex items-center gap-2 text-xs">
        <x-badge variant="accent">{{ __('Status: :status', ['status' => ucfirst($order->status)]) }}</x-badge>
        <x-badge variant="{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">{{ __('Pembayaran: :status', ['status' => ucfirst($order->payment_status)]) }}</x-badge>
      </div>
    </div>
  </div>

  @if(session('success'))
    <x-alert type="success">{{ session('success') }}</x-alert>
  @endif
  @if(session('error'))
    <x-alert type="error">{{ session('error') }}</x-alert>
  @endif

  @if($order->payment_status === 'pending' && isset($paymentRoutes[$order->payment_method]))
    <x-alert type="warning" class="flex items-center justify-between">
      <div>
        <div class="font-semibold text-sm text-neutral-800 dark:text-neutral-100">{{ __('Pembayaran belum selesai') }}</div>
        <div class="text-xs text-neutral-600 dark:text-neutral-300">{{ __('Selesaikan pembayaran menggunakan metode :method untuk mengaktifkan pemrosesan pesanan.', ['method' => $paymentLabel]) }}</div>
      </div>
      <x-button href="{{ $paymentRoutes[$order->payment_method] }}" class="justify-center">
        {{ __('Lanjutkan Pembayaran') }}
      </x-button>
    </x-alert>
  @endif

  <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_260px]">
    <div class="space-y-6">
      <div class="soft-card p-6 space-y-4">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Produk dalam Pesanan') }}</h2>
        <div class="divide-y divide-white/60 dark:divide-neutral-800">
          @foreach($items as $item)
            <div class="py-4 flex flex-wrap items-center justify-between gap-4 first:pt-0">
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

      <div class="grid gap-6 md:grid-cols-2">
        <div class="soft-card p-6 space-y-3">
          <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Alamat Pengiriman') }}</h2>
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
        <div class="soft-card p-6 space-y-3">
          <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Metode Pembayaran') }}</h2>
          <dl class="text-sm text-neutral-600 dark:text-neutral-300 space-y-2">
            <div>
              <dt class="text-xs uppercase tracking-wide text-neutral-400">{{ __('Metode') }}</dt>
              <dd class="text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $paymentLabel }}</dd>
            </div>
            <div>
              <dt class="text-xs uppercase tracking-wide text-neutral-400">{{ __('Status Pembayaran') }}</dt>
              <dd>{{ ucfirst($order->payment_status) }}</dd>
            </div>
            @if($order->payment_verified_at)
              <div>
                <dt class="text-xs uppercase tracking-wide text-neutral-400">{{ __('Diverifikasi pada') }}</dt>
                <dd>{{ $order->payment_verified_at->format('d M Y, H:i') }}</dd>
              </div>
            @endif
            @if($order->payment_proof_path)
              @php
                $proofUrl = asset('storage/' . ltrim(str_replace('\\', '/', $order->payment_proof_path), '/'));
              @endphp
              <div>
                <dt class="text-xs uppercase tracking-wide text-neutral-400">{{ __('Bukti Pembayaran') }}</dt>
                <dd><a href="{{ $proofUrl }}" target="_blank" class="text-sky-600 hover:text-sky-700">{{ __('Lihat Bukti') }}</a></dd>
              </div>
            @endif
          </dl>
        </div>
      </div>
    </div>

    <aside class="soft-card p-6 space-y-4 h-max">
      <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Ringkasan Pembayaran') }}</h2>
      <dl class="text-sm text-neutral-600 dark:text-neutral-300 space-y-2">
        <div class="flex justify-between"><dt>{{ __('Subtotal') }}</dt><dd>{{ format_price($itemsTotal) }}</dd></div>
        <div class="flex justify-between"><dt>{{ __('Diskon') }}</dt><dd>{{ $discountAmount > 0 ? '-'.format_price($discountAmount) : format_price(0) }}</dd></div>
        <div class="flex justify-between"><dt>{{ __('Pengiriman') }}</dt><dd>{{ format_price($shippingTotal) }}</dd></div>
        <div class="border-t border-white/60 dark:border-neutral-800 pt-3 flex justify-between text-base font-semibold text-neutral-900 dark:text-neutral-100"><dt>{{ __('Total') }}</dt><dd>{{ format_price($order->total_amount) }}</dd></div>
      </dl>
      <x-alert type="info" class="text-xs">{{ __('Status pesanan akan diperbarui secara otomatis setelah pembayaran terkonfirmasi.') }}</x-alert>
    </aside>
  </section>
</main>
@endsection
