@extends('layouts.app')

@php
  $shipping = collect(json_decode($order->shipping_address ?? '[]', true));
  $items = $order->orderItems ?? collect();
  $itemsTotal = $items->sum(fn($item) => $item->price * $item->quantity);
  $discountAmount = $order->discount_amount ?? 0;
  $shippingTotal = max($order->total_amount - $itemsTotal + $discountAmount, 0);
  $paymentRoutes = [
    'bank_transfer' => localized_route('payment.bank-transfer', ['order' => $order->id])
  ];
  $paymentLabels = [
    'bank_transfer' => __('Transfer Bank Manual')
  ];
  $paymentLabel = $paymentLabels[$order->payment_method] ?? ucfirst(str_replace('_', ' ', $order->payment_method));
  $paymentVerifiedAt = $order->payment_verified_at;
  $statusStages = [
    'pending' => __('Menunggu Konfirmasi'),
    'processing' => __('Sedang Diproses'),
    'shipped' => __('Dikirim'),
    'completed' => __('Selesai'),
  ];
  $stageKeys = array_keys($statusStages);
  $currentStageIndex = array_search($order->status, $stageKeys);
  $currentStageIndex = ($currentStageIndex === false) ? 0 : $currentStageIndex;
  $shippingSummary = $shipping->filter()->implode(', ');
@endphp

@section('content')
<main class="container max-w-5xl py-10 space-y-6" role="main">
  <div class="flex flex-wrap items-start justify-between gap-4">
    <div>
      <h1 class="text-3xl font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Detail Pesanan') }}</h1>
      <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Dibuat pada :date', ['date' => $order->created_at?->format('d M Y H:i')]) }}</p>
    </div>
    <div class="flex flex-col items-end gap-2">
      <div class="text-xs uppercase tracking-wide text-neutral-400">{{ __('Nomor Pesanan') }}</div>
      <div class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">#{{ $order->id }}</div>
      <div class="flex items-center gap-2 text-xs">
        <x-badge id="order-status-badge"
                 variant="accent"
                 data-variant="accent"
                 data-template="{{ __('Status: :status', ['status' => '__STATUS__']) }}">
          {{ __('Status: :status', ['status' => ucfirst($order->status)]) }}
        </x-badge>
        @php
          $paymentBadgeVariant = $order->payment_status === 'paid' ? 'success' : 'warning';
        @endphp
        <x-badge id="order-payment-badge"
                 variant="{{ $paymentBadgeVariant }}"
                 data-variant="{{ $paymentBadgeVariant }}"
                 data-template="{{ __('Pembayaran: :status', ['status' => '__STATUS__']) }}">
          {{ __('Pembayaran: :status', ['status' => ucfirst($order->payment_status)]) }}
        </x-badge>
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
              <dd id="payment-status-text">{{ ucfirst($order->payment_status) }}</dd>
            </div>
            <div id="payment-verified-wrapper" @class(['payment-verified-row', 'hidden' => ! $paymentVerifiedAt])>
              <dt class="text-xs uppercase tracking-wide text-neutral-400">{{ __('Diverifikasi pada') }}</dt>
              <dd id="payment-verified-text">{{ $paymentVerifiedAt?->format('d M Y H:i') }}</dd>
            </div>
            @if($order->payment_proof_path)
              @php
                $proofUrl = asset('storage/' . ltrim(str_replace('\\', '/', $order->payment_proof_path), '/'));
              @endphp
              <div>
                <dt class="text-xs uppercase tracking-wide text-neutral-400">{{ __('Bukti Pembayaran') }}</dt>
                <dd>
                  <button type="button" class="text-sky-600 hover:text-sky-700" data-proof-toggle data-proof-url="{{ $proofUrl }}">
                    {{ __('Lihat Bukti') }}
                  </button>
                </dd>
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

@if($order->payment_proof_path)
  <div x-data="{ open: false }" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center"
       x-show="open"
       x-on:proof-modal.window="open = true; $refs.proofImage.src = $event.detail?.url || '';"
       x-transition.opacity>
    <div class="absolute inset-0 bg-black/50" x-on:click="open = false"></div>
    <div class="relative w-[92vw] max-w-3xl rounded-3xl bg-white p-6 shadow-2xl dark:bg-neutral-900">
      <button type="button" class="absolute right-4 top-4 text-neutral-500 hover:text-neutral-700" x-on:click="open = false">
        <i class="fa-solid fa-xmark text-xl"></i>
      </button>
      <h3 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Bukti Pembayaran') }}</h3>
      <div class="max-h-[70vh] overflow-hidden rounded-2xl border border-neutral-200 dark:border-neutral-700">
        <img x-ref="proofImage" src="{{ $proofUrl ?? '' }}" alt="Payment proof" class="w-full object-contain" loading="lazy">
      </div>
    </div>
  </div>
@endif

@endsection

@push('scripts')
<script>
  (function () {
    const pollUrl = @json(localized_route('orders.status', ['order' => $order->id]));
    if (!pollUrl) {
      return;
    }

    const statusBadge = document.getElementById('order-status-badge');
    const paymentBadge = document.getElementById('order-payment-badge');
    const paymentStatusText = document.getElementById('payment-status-text');
    const paymentVerifiedWrapper = document.getElementById('payment-verified-wrapper');
    const paymentVerifiedText = document.getElementById('payment-verified-text');
    const placeholder = '__STATUS__';
    const badgeVariants = {
      accent: 'bg-accent-500/10 text-accent-600',
      neutral: 'bg-neutral-900/10 text-neutral-800 dark:bg-neutral-100/10 dark:text-neutral-200',
      success: 'bg-green-500/10 text-green-700',
      warning: 'bg-amber-500/10 text-amber-700',
      danger: 'bg-red-500/10 text-red-700',
    };

    const locale = document.documentElement?.lang || 'en';
    const dateFormatter = typeof Intl !== 'undefined'
      ? new Intl.DateTimeFormat(locale, {
          day: '2-digit',
          month: 'short',
          year: 'numeric',
          hour: '2-digit',
          minute: '2-digit',
        })
      : null;

    const formatLabel = (value) => {
      if (!value) {
        return '';
      }

      return value.toString().split('_').map((part) => {
        return part.charAt(0).toUpperCase() + part.slice(1);
      }).join(' ');
    };

    const fillTemplate = (el, label) => {
      const template = el?.dataset?.template;
      if (!template) {
        return label;
      }

      return template.replace(placeholder, label);
    };

    const applyBadgeVariant = (el, variant) => {
      if (!el) {
        return;
      }

      const target = badgeVariants[variant] ? variant : 'accent';

      Object.values(badgeVariants).forEach((classes) => {
        classes.split(' ').forEach((cls) => cls && el.classList.remove(cls));
      });

      badgeVariants[target].split(' ').forEach((cls) => cls && el.classList.add(cls));
      el.dataset.variant = target;
    };

    const updatePaymentVerified = (timestamp) => {
      if (!paymentVerifiedWrapper || !paymentVerifiedText) {
        return;
      }

      if (!timestamp) {
        paymentVerifiedWrapper.classList.add('hidden');
        paymentVerifiedText.textContent = '';
        return;
      }

      const date = new Date(timestamp);
      let formatted = timestamp;

      if (dateFormatter && !Number.isNaN(date.getTime())) {
        formatted = dateFormatter.format(date);
      }

      paymentVerifiedText.textContent = formatted;
      paymentVerifiedWrapper.classList.remove('hidden');
    };

    const updateData = (payload) => {
      if (payload.status && statusBadge) {
        const label = payload.status_label || formatLabel(payload.status);
        statusBadge.textContent = fillTemplate(statusBadge, label);
      }

      if (payload.payment_status && paymentBadge) {
        const label = payload.payment_status_label || formatLabel(payload.payment_status);
        paymentBadge.textContent = fillTemplate(paymentBadge, label);

        const variant = payload.payment_status_variant
          || (payload.payment_status === 'paid' ? 'success'
            : ['failed', 'canceled', 'cancelled', 'refunded', 'expired'].includes(payload.payment_status)
              ? 'danger'
              : 'warning');

        applyBadgeVariant(paymentBadge, variant);
      }

      if (payload.payment_status && paymentStatusText) {
        paymentStatusText.textContent = payload.payment_status_label || formatLabel(payload.payment_status);
      }

      updatePaymentVerified(payload.payment_verified_at || null);
    };

    let intervalId;

    const poll = async () => {
      try {
        const response = await fetch(pollUrl, {
          headers: {
            'Accept': 'application/json',
          },
          cache: 'no-store',
        });

        if (!response.ok) {
          throw new Error('Failed to fetch order status');
        }

        const payload = await response.json();
        updateData(payload);
      } catch (error) {
        console.warn('[order-status-poll]', error);
      }
    };

    const startPolling = () => {
      clearInterval(intervalId);
      intervalId = setInterval(poll, 10000);
    };

    poll();
    startPolling();

    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        clearInterval(intervalId);
      } else {
        poll();
        startPolling();
      }
    });

    window.addEventListener('beforeunload', () => clearInterval(intervalId));
    document.querySelectorAll('[data-proof-toggle]').forEach((button) => {
      button.addEventListener('click', () => {
        const url = button.getAttribute('data-proof-url');
        window.dispatchEvent(new CustomEvent('proof-modal', { detail: { url } }));
      });
    });
  })();
</script>
@endpush
