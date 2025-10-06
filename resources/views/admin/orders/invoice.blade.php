@extends('layouts.admin')

@section('header', 'Invoice')

@section('head')
<style>
  @media print {
    body * { visibility: hidden !important; }
    .print-area, .print-area * { visibility: visible !important; }
    .print-area { position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0; }
    .no-print { display: none !important; }
  }
</style>
@endsection

@section('content')
@php
  $subtotal = $order->orderItems->sum(fn($i)=>($i->price ?? 0) * ($i->quantity ?? 0));
  $discount = (float)($order->discount_amount ?? ($order->discount ?? 0));
  $shipping = (float)($order->shipping_cost ?? 0);
  $total = max(0, $subtotal + $shipping - $discount);
  $paymentStatus = $order->payment_status ?? 'pending';
  $paymentBadge = match($paymentStatus) {
    'paid' => 'badge-success',
    'processing' => 'badge-info',
    'pending' => 'badge-warn',
    default => 'badge-neutral',
  };
  $paymentLabel = ucfirst(str_replace('_', ' ', $paymentStatus));
  $paymentMethodLabel = $order->payment_method ? ucwords(str_replace(['_', '-'], ' ', $order->payment_method)) : 'N/A';
@endphp

<div class="print-area">
<div class="table-card p-6">
  <!-- Header -->
  <div class="flex items-start justify-between border-b border-slate-200 pb-4 dark:border-slate-700/60">
    <div>
      <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Invoice</h2>
      <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Issued: {{ $order->created_at?->format('d M Y') }}</p>
    </div>
    <div class="text-right">
      <p class="text-sm text-slate-500 dark:text-slate-400">ID: <span class="font-semibold">#ORD{{ $order->id }}</span></p>
      <span class="badge badge-info mt-2">{{ ucfirst($order->status) }}</span>
    </div>
  </div>

  <!-- From / To / Payment -->
  <div class="grid grid-cols-1 gap-6 py-6 md:grid-cols-3">
    <div>
      <p class="text-xs text-slate-500">From</p>
      <p class="mt-2 font-semibold">{{ config('app.name') }}</p>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ config('app.url') }}</p>
    </div>
    <div class="md:text-center">
      <p class="text-xs text-slate-500">To</p>
      <p class="mt-2 font-semibold">{{ $order->user->name ?? 'Customer' }}</p>
      <p class="text-sm text-slate-500 dark:text-slate-400">{{ $order->user->email ?? '-' }}</p>
      @if($order->shipping_address)
        <p class="text-sm text-slate-500 dark:text-slate-400">{!! nl2br(e($order->shipping_address)) !!}</p>
      @endif
    </div>
    <div class="md:text-right">
      <p class="text-xs text-slate-500">Payment</p>
      <p class="mt-2 font-semibold text-slate-700 dark:text-slate-200">{{ $paymentMethodLabel }}</p>
      <span class="mt-2 inline-flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-300">
        <span class="badge {{ $paymentBadge }}">{{ $paymentLabel }}</span>
      </span>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Verified: {{ $order->payment_verified_at?->format('d M Y H:i') ?? '—' }}</p>
    </div>
  </div>

  <!-- Items table -->
  <div class="overflow-x-auto">
    <table class="table-modern">
      <thead>
        <tr>
          <th class="w-16">S.No.#</th>
          <th>Products</th>
          <th class="cell-right">Quantity</th>
          <th class="cell-right">Unit Cost</th>
          <th class="cell-right">Discount</th>
          <th class="cell-right">Total</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order->orderItems as $idx=>$item)
          @php $line = ($item->price ?? 0) * ($item->quantity ?? 0); @endphp
          <tr>
            <td>{{ $idx+1 }}</td>
            <td class="font-medium text-slate-900 dark:text-slate-100">{{ $item->product->name ?? 'Product' }}</td>
            <td class="cell-right">{{ $item->quantity }}</td>
            <td class="cell-right">Rp {{ number_format($item->price,0,',','.') }}</td>
            <td class="cell-right">0%</td>
            <td class="cell-right">Rp {{ number_format($line,0,',','.') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <!-- Summary + Actions -->
  <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
    <div class="md:col-span-2"></div>
    <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-700/60">
      <p class="font-semibold mb-2">Order summary</p>
      <div class="space-y-2 text-sm">
        <div class="flex justify-between text-xs">
          <span class="text-slate-500">Payment Via</span>
          <span class="font-medium text-slate-700 dark:text-slate-200">{{ $paymentMethodLabel }}</span>
        </div>
        <div class="flex items-center justify-between text-xs">
          <span class="text-slate-500">Payment Status</span>
          <span class="badge {{ $paymentBadge }}">{{ $paymentLabel }}</span>
        </div>
        <div class="flex justify-between"><span class="text-slate-500">Sub Total</span><span>Rp {{ number_format($subtotal,0,',','.') }}</span></div>
        @if($shipping>0)
        <div class="flex justify-between"><span class="text-slate-500">Shipping</span><span>Rp {{ number_format($shipping,0,',','.') }}</span></div>
        @endif
        @if($discount>0)
        <div class="flex justify-between"><span class="text-slate-500">Discount</span><span>- Rp {{ number_format($discount,0,',','.') }}</span></div>
        @endif
        <div class="flex justify-between border-t border-slate-200 pt-2 dark:border-slate-700/60"><span class="font-medium">Total</span><span class="text-lg font-bold">Rp {{ number_format($total,0,',','.') }}</span></div>
      </div>
    </div>
  </div>

  <div class="mt-6 flex justify-end gap-3 no-print">
    <button type="button" onclick="window.print()" class="btn-primary inline-flex items-center gap-2">
      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2m-2 0H8v-3h8v3z"/></svg>
      Print
    </button>
    <a href="{{ route('admin.orders.invoice.pdf', $order) }}" class="btn-outline">Download PDF</a>
  </div>
</div>
</div>
@endsection
