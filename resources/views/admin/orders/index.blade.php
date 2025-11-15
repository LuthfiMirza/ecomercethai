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
  <div
    class="rounded-3xl border border-slate-200 bg-white/70 p-6 shadow-[0_20px_60px_-35px_rgba(15,23,42,0.45)] backdrop-blur dark:border-slate-800 dark:bg-slate-900/40"
    data-order-poll
    data-poll-url="{{ localized_route('admin.orders.poll') }}"
    data-last-id="{{ $latestOrderId ?? 0 }}"
    data-interval="15000"
    data-max-items="5"
    data-total-label="{{ __('admin.orders.realtime.total_label') }}"
    data-payment-label="{{ __('admin.orders.realtime.payment_label') }}"
    data-view-label="{{ __('admin.orders.realtime.view') }}"
    data-empty-label="{{ __('admin.orders.realtime.empty') }}"
    data-customer-label="{{ __('Customer') }}"
  >
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
      <div>
        <p class="text-lg font-semibold text-slate-800 dark:text-slate-100">{{ __('admin.orders.realtime.title') }}</p>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.orders.realtime.subtitle') }}</p>
      </div>
      <div class="flex items-center gap-3">
        <span class="hidden text-xs font-semibold uppercase tracking-wider text-emerald-500" data-order-pulse>{{ __('admin.orders.realtime.badge') }}</span>
        <button type="button" data-order-refresh class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:text-slate-900 dark:border-slate-700 dark:text-slate-200">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v6h6M20 20v-6h-6M5 19A9 9 0 0119 5"/>
          </svg>
          <span>{{ __('admin.orders.realtime.refresh') }}</span>
        </button>
      </div>
    </div>
    <div class="mt-4 space-y-3" data-order-feed aria-live="polite">
      <p class="text-sm text-slate-500 dark:text-slate-400" data-order-empty>{{ __('admin.orders.realtime.empty') }}</p>
    </div>
  </div>

  <div
    data-order-table
    data-refresh-url="{{ request()->fullUrl() }}"
    data-refresh-interval="60000"
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
          <td>{{ format_price($order->total_amount ?? 0) }}</td>
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
                  @if($order->payment_proof_path)
                    @php
                      $proofUrl = asset('storage/' . ltrim(str_replace('\\', '/', $order->payment_proof_path), '/'));
                    @endphp
                    <button
                      type="button"
                      @click="open = false; $dispatch('show-proof', { url: '{{ e($proofUrl) }}', label: 'Order #ORD{{ $order->id }}' })"
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
  </div>

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

@push('scripts')
<script>
(() => {
  const panel = document.querySelector('[data-order-poll]');
  const hasHttpClient = typeof window.axios !== 'undefined'
    || typeof window.fetch !== 'undefined'
    || typeof window.XMLHttpRequest !== 'undefined';
  if (!panel || !hasHttpClient) {
    return;
  }

  const buildUrl = (url, params = {}) => {
    const entries = Object.entries(params).filter(([, value]) => value !== undefined && value !== null && value !== '');
    if (!entries.length) {
      return url;
    }
    const search = new URLSearchParams(entries).toString();
    return url.includes('?') ? `${url}&${search}` : `${url}?${search}`;
  };

  const baseHeaders = {
    'X-Requested-With': 'XMLHttpRequest',
    Accept: 'application/json',
  };

  const requestWithXHR = (url, options = {}) => {
    const target = buildUrl(url, options.params || {});
    return new Promise((resolve, reject) => {
      const xhr = new XMLHttpRequest();
      xhr.open('GET', target, true);
      xhr.withCredentials = true;
      const headers = { ...baseHeaders, ...(options.headers || {}) };
      Object.keys(headers).forEach((key) => {
        const value = headers[key];
        if (value !== undefined && value !== null) {
          xhr.setRequestHeader(key, value);
        }
      });
      xhr.onreadystatechange = () => {
        if (xhr.readyState !== XMLHttpRequest.DONE) {
          return;
        }
        if (xhr.status >= 200 && xhr.status < 300) {
          try {
            const data = xhr.responseText ? JSON.parse(xhr.responseText) : null;
            resolve({ data });
          } catch (error) {
            reject(error);
          }
        } else {
          reject(new Error(`Request failed with status ${xhr.status}`));
        }
      };
      xhr.send();
    });
  };

  const httpGet = (url, options = {}) => {
    if (typeof window.axios !== 'undefined') {
      return window.axios.get(url, options);
    }
    if (typeof window.fetch !== 'undefined') {
      const target = buildUrl(url, options.params || {});
      const headers = { ...baseHeaders, ...(options.headers || {}) };
      return fetch(target, {
        method: 'GET',
        credentials: 'same-origin',
        headers,
      }).then(async (response) => {
        if (!response.ok) {
          throw new Error(`Request failed with status ${response.status}`);
        }
        return { data: await response.json() };
      });
    }

    return requestWithXHR(url, options);
  };

  const feed = panel.querySelector('[data-order-feed]');
  const emptyState = panel.querySelector('[data-order-empty]');
  const refreshBtn = panel.querySelector('[data-order-refresh]');
  const badge = panel.querySelector('[data-order-pulse]');

  const pollUrl = panel.dataset.pollUrl;
  const interval = Number(panel.dataset.interval || 15000);
  const maxItems = Number(panel.dataset.maxItems || 5);

  let cursor = Number(panel.dataset.lastId || 0) || 0;
  let loading = false;
  let timer = null;

  const labels = {
    total: panel.dataset.totalLabel || 'Total',
    payment: panel.dataset.paymentLabel || 'Payment',
    view: panel.dataset.viewLabel || 'View',
    empty: panel.dataset.emptyLabel || '',
    customer: panel.dataset.customerLabel || 'Customer',
  };

  const escapeHtml = (value = '') => String(value)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');

  const timeLabel = (iso) => {
    if (!iso) return '';
    const date = new Date(iso);
    if (Number.isNaN(date.getTime())) return '';
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  };

  const statusBadge = (status = '') => {
    switch (status.toLowerCase()) {
      case 'pending':
        return 'badge-warn';
      case 'processing':
      case 'shipped':
        return 'badge-info';
      case 'completed':
        return 'badge-success';
      case 'cancelled':
      case 'canceled':
        return 'badge-danger';
      default:
        return 'badge-neutral';
    }
  };

  const paymentBadge = (status = '') => {
    switch (status.toLowerCase()) {
      case 'paid':
        return 'badge-success';
      case 'processing':
        return 'badge-info';
      case 'pending':
        return 'badge-warn';
      default:
        return 'badge-neutral';
    }
  };

  const entryNodes = () => (feed ? Array.from(feed.querySelectorAll('[data-order-id]')) : []);

  const setEmptyState = (visible) => {
    if (!emptyState) return;
    emptyState.classList.toggle('hidden', !visible);
  };

  const pruneFeed = () => {
    if (!feed) return;
    const nodes = entryNodes();
    while (nodes.length > maxItems) {
      const last = nodes.pop();
      last?.remove();
    }
  };

  const pulsePanel = () => {
    if (!badge) return;
    badge.classList.remove('hidden');
    if (badge.dataset.timeout) {
      window.clearTimeout(Number(badge.dataset.timeout));
    }
    const handle = window.setTimeout(() => {
      badge.classList.add('hidden');
      badge.dataset.timeout = '';
    }, 2000);
    badge.dataset.timeout = String(handle);

    panel.animate(
      [
        { boxShadow: '0 0 0 rgba(79,70,229,0)' },
        { boxShadow: '0 0 0 18px rgba(79,70,229,0.15)' },
        { boxShadow: '0 0 0 rgba(79,70,229,0)' },
      ],
      { duration: 600, easing: 'ease-out' }
    );
  };

  const renderOrder = (order) => {
    const node = document.createElement('article');
    node.dataset.orderId = String(order?.id ?? '');
    node.className = 'rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 shadow-sm dark:border-slate-800 dark:bg-slate-900/40';
    const customerName = order?.customer?.name || labels.customer;
    const created = order?.created_label || timeLabel(order?.created_at);
    node.innerHTML = `
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
          <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">${escapeHtml(order?.code || '')}</p>
          <p class="text-xs text-slate-500 dark:text-slate-400">${escapeHtml(customerName)} • ${escapeHtml(created)}</p>
        </div>
        <span class="badge ${statusBadge(order?.status)}">${escapeHtml(order?.status_label || order?.status || '')}</span>
      </div>
      <div class="mt-2 flex flex-wrap items-center gap-4 text-sm text-slate-600 dark:text-slate-300">
        <span>${escapeHtml(labels.total)}: <strong class="text-slate-900 dark:text-white">${escapeHtml(order?.total_formatted || order?.total_amount || '')}</strong></span>
        <span>${escapeHtml(labels.payment)}: <span class="badge ${paymentBadge(order?.payment_status)}">${escapeHtml(order?.payment_status_label || order?.payment_status || '')}</span></span>
      </div>
      <div class="mt-3 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
        <span>${escapeHtml(created)}</span>
        <a href="${escapeHtml(order?.url || '#')}" class="inline-flex items-center gap-1 text-primary-600 hover:text-primary-700">
          ${escapeHtml(labels.view)}
          <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 5h8v8M5 15l10-10"/>
          </svg>
        </a>
      </div>
    `;
    return node;
  };

  const handleOrders = (list) => {
    if (!feed) return;
    if (!Array.isArray(list) || !list.length) {
      setEmptyState(entryNodes().length === 0);
      return;
    }
    pulsePanel();
    setEmptyState(false);
    const ordered = [...list].sort((a, b) => (Number(a?.id) || 0) - (Number(b?.id) || 0));
    ordered.forEach((order) => {
      const node = renderOrder(order);
      feed.prepend(node);
    });
    pruneFeed();
  };

  const fetchOrders = async () => {
    if (!pollUrl || loading) return;
    loading = true;
    try {
      const params = {};
      if (cursor > 0) {
        params.after = cursor;
      }
      const { data } = await httpGet(pollUrl, { params });
      if (!data?.ok) {
        throw new Error('Unable to poll orders');
      }
      const incoming = Array.isArray(data.orders) ? data.orders : [];
      if (incoming.length) {
        const nextCursor = incoming.reduce((acc, order) => {
          const id = Number(order?.id) || 0;
          return id > acc ? id : acc;
        }, cursor);
        cursor = nextCursor;
      }
      if (data.latest_id) {
        const latest = Number(data.latest_id);
        if (!Number.isNaN(latest) && latest > cursor) {
          cursor = latest;
        }
      }
      panel.dataset.lastId = String(cursor);
      handleOrders(incoming);
    } catch (error) {
      console.debug('Order poll skipped', error);
    } finally {
      loading = false;
    }
  };

  const start = () => {
    if (timer || !pollUrl) return;
    timer = window.setInterval(fetchOrders, interval);
  };

  const stop = () => {
    if (!timer) return;
    window.clearInterval(timer);
    timer = null;
  };

  refreshBtn?.addEventListener('click', () => {
    fetchOrders();
  });

  document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
      stop();
      return;
    }
    start();
    fetchOrders();
  });

  start();
  fetchOrders();
})();
</script>

<script>
(() => {
  const container = document.querySelector('[data-order-table]');
  if (!container || typeof window.fetch === 'undefined') {
    return;
  }

  const refreshUrl = container.dataset.refreshUrl || window.location.href;
  const interval = Number(container.dataset.refreshInterval || 60000);
  let loading = false;

  const refreshTable = async () => {
    if (loading || document.hidden) {
      return;
    }
    loading = true;
    try {
      const response = await fetch(refreshUrl, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
      });
      if (!response.ok) {
        throw new Error(`Failed to refresh orders (${response.status})`);
      }
      const html = await response.text();
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const next = doc.querySelector('[data-order-table]');
      if (next) {
        container.innerHTML = next.innerHTML;
      }
    } catch (error) {
      console.debug('Order table refresh skipped', error);
    } finally {
      loading = false;
    }
  };

  window.setInterval(refreshTable, interval);
})();
</script>
@endpush
@endsection
