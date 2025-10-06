@extends('layouts.admin')

@section('header', 'Orders')

@section('content')
<div
  x-data="{
    proofOpen: false,
    proofUrl: null,
    proofLabel: null,
  }"
  x-on:show-proof.window="proofOpen = true; proofUrl = $event.detail.url; proofLabel = $event.detail.label"
  @keydown.escape.window="proofOpen = false"
  class="space-y-4"
>
  <x-table 
    title="Orders List"
    :export-items="[
      ['label' => 'CSV', 'href' => route('admin.orders.export.csv')],
      ['label' => 'Excel', 'href' => route('admin.orders.export.excel')],
      ['label' => 'PDF', 'href' => route('admin.orders.export.pdf')],
    ]"
    :pagination="$orders"
    :search="true"
    search-placeholder="Order ID or Customer..."
    :search-value="$q ?? request('q')"
    action="{{ route('admin.orders.index') }}"
  >
    <x-slot:filters>
      <div>
        <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-300">Status</label>
        <select name="status" class="w-full rounded-lg border border-slate-200 bg-white py-2 pl-3 pr-8 text-sm focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
          <option value="">All Statuses</option>
          @foreach(($statusOptions ?? ['pending','processing','shipped','completed','cancelled']) as $opt)
            <option value="{{ $opt }}" @selected(($status ?? request('status')) === $opt)>{{ ucfirst($opt) }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-300">From</label>
        <input type="date" name="from" value="{{ $from ?? request('from') }}" class="w-full rounded-lg border border-slate-200 bg-white py-2 px-3 text-sm focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white" />
      </div>
      <div>
        <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-300">To</label>
        <input type="date" name="to" value="{{ $to ?? request('to') }}" class="w-full rounded-lg border border-slate-200 bg-white py-2 px-3 text-sm focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white" />
      </div>
    </x-slot:filters>

    <x-slot:head>
      <tr>
        <th>Order ID</th>
        <th>Customer</th>
        <th>Total</th>
        <th>Status</th>
        <th>Payment</th>
        <th>Date</th>
        <th></th>
      </tr>
    </x-slot:head>

    <x-slot:body>
      @forelse ($orders as $order)
        @php
          $statusBadge = match($order->status) {
            'pending' => 'badge-warn',
            'processing', 'shipped' => 'badge-info',
            'completed' => 'badge-success',
            'cancelled' => 'badge-danger',
            default => 'badge-neutral',
          };
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
        <tr>
          <td class="font-medium text-slate-900 dark:text-slate-200">#ORD{{ $order->id }}</td>
          <td>
            <p class="font-medium">{{ $order->user->name ?? 'Guest' }}</p>
            <p class="text-xs text-slate-500">{{ $order->user->email ?? '-' }}</p>
          </td>
          <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
          <td><span class="badge {{ $statusBadge }}">{{ ucfirst($order->status) }}</span></td>
          <td>
            <div class="space-y-1">
              <p class="text-xs uppercase tracking-wide text-slate-400">Method</p>
              <p class="font-medium text-slate-700 dark:text-slate-200">{{ $paymentMethodLabel }}</p>
              <span class="badge {{ $paymentBadge }}">{{ $paymentLabel }}</span>
            </div>
          </td>
          <td>{{ $order->created_at?->format('d M Y') }}</td>
          <td class="cell-actions">
            <div class="flex flex-wrap items-center justify-end gap-2">
              <a href="{{ route('admin.orders.show', $order) }}" class="btn-outline text-xs">Detail</a>
              <a href="{{ route('admin.orders.invoice', $order) }}" class="btn-outline text-xs">Invoice</a>
              <div x-data="{ open: false }" class="relative">
                <button
                  type="button"
                  @click="open = !open"
                  @keydown.escape.window="open = false"
                  class="btn-primary text-xs"
                >
                  Manage
                  <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 8l4 4 4-4"/></svg>
                </button>
                <div
                  x-cloak
                  x-show="open"
                  x-transition
                  @click.outside="open = false"
                  class="absolute right-0 z-20 mt-2 w-60 space-y-1 rounded-xl border border-slate-200 bg-white p-2 shadow-lg dark:border-slate-700 dark:bg-slate-900"
                >
                  <a
                    href="{{ route('admin.orders.edit', $order) }}"
                    class="flex w-full items-center justify-between gap-2 rounded-lg px-4 py-2 text-xs font-medium text-slate-600 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800"
                  >
                    Edit Order
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 13v3h3l9-9-3-3-9 9z"/></svg>
                  </a>

                  <p class="px-3 pt-2 text-[10px] font-semibold uppercase tracking-wider text-slate-400">Payment</p>
                  <form action="{{ route('admin.orders.update_payment', $order) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="payment_status" value="processing">
                    <button
                      type="submit"
                      @click="open = false"
                      @class([
                        'flex w-full items-center justify-between gap-2 rounded-lg px-4 py-2 text-xs font-medium transition',
                        'text-slate-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800',
                        'opacity-50 cursor-not-allowed' => $paymentStatus === 'processing',
                      ])
                      @disabled($paymentStatus === 'processing')
                    >
                      Mark Processing
                      <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m-4-4v4m2 4a8 8 0 110-16 8 8 0 010 16z"/></svg>
                    </button>
                  </form>
                  <form action="{{ route('admin.orders.update_payment', $order) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="payment_status" value="paid">
                    <button
                      type="submit"
                      @click="open = false"
                      @class([
                        'flex w-full items-center justify-between gap-2 rounded-lg px-4 py-2 text-xs font-medium transition',
                        'text-slate-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800',
                        'opacity-50 cursor-not-allowed' => $paymentStatus === 'paid',
                      ])
                      @disabled($paymentStatus === 'paid')
                    >
                      Confirm Payment
                      <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l3 3 7-7"/></svg>
                    </button>
                  </form>
                  @if($order->payment_proof_url)
                    <button
                      type="button"
                      @click="open = false; $dispatch('show-proof', { url: '{{ e($order->payment_proof_url) }}', label: 'Order #ORD{{ $order->id }}' })"
                      class="flex w-full items-center justify-between gap-2 rounded-lg px-4 py-2 text-xs font-medium text-slate-600 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800"
                    >
                      View Payment Proof
                      <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  @endif

                  <p class="px-3 pt-2 text-[10px] font-semibold uppercase tracking-wider text-slate-400">Order Status</p>
                  <form action="{{ route('admin.orders.update_status', $order) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="processing">
                    <button
                      type="submit"
                      @click="open = false"
                      @class([
                        'flex w-full items-center justify-between gap-2 rounded-lg px-4 py-2 text-xs font-medium transition',
                        'text-slate-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800',
                        'opacity-50 cursor-not-allowed' => $order->status === 'processing',
                      ])
                      @disabled($order->status === 'processing')
                    >
                      Set Processing
                      <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-2m5-5h-6l-1-2H6l-1 2H3v2h2v6h10v-6h2V7h-2z"/></svg>
                    </button>
                  </form>
                  <form action="{{ route('admin.orders.update_status', $order) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="completed">
                    <button
                      type="submit"
                      @click="open = false"
                      @class([
                        'flex w-full items-center justify-between gap-2 rounded-lg px-4 py-2 text-xs font-medium transition',
                        'text-slate-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800',
                        'opacity-50 cursor-not-allowed' => $order->status === 'completed',
                      ])
                      @disabled($order->status === 'completed')
                    >
                      Mark Completed
                      <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="py-6 text-center text-slate-500 dark:text-slate-400">No orders found.</td>
        </tr>
      @endforelse
    </x-slot:body>
  </x-table>

  <div
    x-cloak
    x-show="proofOpen"
    x-transition.opacity
    class="fixed inset-0 z-30 flex min-h-screen items-center justify-center bg-slate-900/70 p-4"
  >
    <div
      class="table-card w-full max-w-2xl overflow-hidden"
      x-transition.scale
      @click.outside="proofOpen = false"
    >
      <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-slate-700/60">
        <div>
          <p class="text-sm font-semibold text-slate-600 dark:text-slate-300">Payment Proof</p>
          <p class="text-xs text-slate-400" x-text="proofLabel"></p>
        </div>
        <button type="button" class="text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300" @click="proofOpen = false">
          <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l8 8M6 14l8-8"/></svg>
        </button>
      </div>
      <div class="bg-slate-50 p-4 dark:bg-slate-900/60">
        <template x-if="proofUrl">
          <img :src="proofUrl" alt="Payment proof" class="mx-auto max-h-[480px] w-full rounded-lg object-contain" loading="lazy">
        </template>
        <p class="text-center text-sm text-slate-500" x-show="!proofUrl">No payment proof available for this order.</p>
      </div>
      <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-white px-4 py-3 dark:border-slate-700/60 dark:bg-slate-900">
        <template x-if="proofUrl">
          <a :href="proofUrl" target="_blank" class="btn-outline text-xs">Open Original</a>
        </template>
        <button type="button" class="btn-primary text-xs" @click="proofOpen = false">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection
