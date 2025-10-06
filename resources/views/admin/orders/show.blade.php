@extends('layouts.admin')

@section('header', 'Order Details')

@section('content')
  <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h3 class="text-2xl font-bold text-black dark:text-white">Order Details</h3>
        <p class="text-sm text-slate-500">Order ID: <span class="font-semibold">#ORD{{ $order->id }}</span></p>
      </div>
      <div class="flex gap-2 items-center flex-wrap">
        <a href="{{ route('admin.orders.index') }}" class="btn-outline rounded-full inline-flex items-center gap-2">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7m-7 7h18"/></svg>
          Back to Orders
        </a>
        <a href="{{ route('admin.orders.edit', $order) }}" class="btn-outline">Edit Order</a>
        <a href="{{ route('admin.orders.invoice', $order) }}" class="btn-outline">View Invoice</a>
        <a href="{{ route('admin.orders.invoice.pdf', $order) }}" class="btn-primary">Download PDF</a>
      </div>
    </div>

    <div class="mt-6 grid grid-cols-12 gap-6">
      <!-- Left: Items + Summary -->
      <div class="col-span-12 xl:col-span-8 space-y-6">
        <div class="table-card p-6">
          @php
            $statusBadge = match($order->status){
              'pending' => 'badge-warn',
              'processing','shipped' => 'badge-info',
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

          <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
            <div class="flex items-center gap-2">
              <span class="badge {{ $statusBadge }}">{{ ucfirst($order->status) }}</span>
              <span class="badge {{ $paymentBadge }}">Payment: {{ $paymentLabel }}</span>
            </div>
            <span class="text-sm text-slate-500">Created: {{ $order->created_at?->format('d M Y H:i') }}</span>
          </div>

          <div class="mb-6 grid gap-4 rounded-xl border border-slate-200 p-4 text-sm dark:border-slate-700/60 sm:grid-cols-2">
            <div>
              <p class="text-xs uppercase tracking-wide text-slate-400">Payment Method</p>
              <p class="mt-1 font-semibold text-slate-700 dark:text-slate-200">{{ $paymentMethodLabel }}</p>
            </div>
            <div>
              <p class="text-xs uppercase tracking-wide text-slate-400">Payment Verified</p>
              <p class="mt-1 text-slate-600 dark:text-slate-300">{{ $order->payment_verified_at?->format('d M Y H:i') ?? '—' }}</p>
            </div>
          </div>

          <h5 class="text-lg font-medium text-black dark:text-white mb-4">Products</h5>
          <div class="overflow-x-auto">
            <table class="table-modern">
              <thead>
                <tr>
                  <th>Product</th>
                  <th class="cell-right">Qty</th>
                  <th class="cell-right">Price</th>
                  <th class="cell-right">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                @foreach($order->orderItems as $item)
                  <tr>
                    <td class="font-medium">{{ $item->product->name ?? 'Product' }}</td>
                    <td class="cell-right">{{ $item->quantity }}</td>
                    <td class="cell-right">Rp {{ number_format($item->price,0,',','.') }}</td>
                    <td class="cell-right">Rp {{ number_format(($item->price ?? 0) * ($item->quantity ?? 0),0,',','.') }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          @php 
            $subtotal = $order->orderItems->sum(fn($i)=>($i->price??0)*($i->quantity??0));
            $discount = (float)($order->discount_amount ?? 0);
            $shipping = (float)($order->shipping_cost ?? 0);
            $total = max(0,$subtotal + $shipping - $discount);
          @endphp

          <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div class="md:col-span-2 space-y-4">
              <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-700/60">
                <p class="text-xs uppercase tracking-wide text-slate-400">Payment Proof</p>
                @if($order->payment_proof_url)
                  <div class="mt-3 space-y-3">
                    <img src="{{ $order->payment_proof_url }}" alt="Payment proof" class="w-full max-h-64 rounded-lg object-contain bg-slate-100 dark:bg-slate-800" loading="lazy">
                    <div class="flex flex-wrap gap-2">
                      <a href="{{ $order->payment_proof_url }}" target="_blank" class="btn-outline text-xs">Open Original</a>
                      <a href="{{ route('admin.orders.edit', $order) }}" class="btn-outline text-xs">Manage Proof</a>
                    </div>
                  </div>
                @else
                  <p class="mt-2 text-sm text-slate-500">No payment proof uploaded yet.</p>
                @endif
              </div>
            </div>
            <div class="rounded-xl border border-slate-200 p-6 dark:border-slate-700/60">
              <div class="space-y-2 text-sm">
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
        </div>
      </div>

      <!-- Right: Customer & Update -->
      <div class="col-span-12 xl:col-span-4 space-y-6">
        <div class="table-card p-6">
          <h4 class="text-xl font-semibold text-black dark:text-white mb-4">Customer Information</h4>
          <div class="space-y-3 text-sm">
            <div><p class="text-slate-500">Name</p><p class="font-medium">{{ $order->user->name ?? '-' }}</p></div>
            <div><p class="text-slate-500">Email</p><p class="font-medium">{{ $order->user->email ?? '-' }}</p></div>
            <div><p class="text-slate-500">Phone</p><p class="font-medium">{{ $order->phone ?? '-' }}</p></div>
            <div><p class="text-slate-500">Address</p><p class="font-medium">{!! nl2br(e($order->shipping_address ?? '-')) !!}</p></div>
          </div>
        </div>

        <div class="table-card p-6">
          <h4 class="text-xl font-semibold text-black dark:text-white mb-4">Payment Status</h4>
          <form action="{{ route('admin.orders.update_payment', $order) }}" method="POST" class="space-y-3">
            @csrf
            @method('PATCH')
            <div>
              <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Payment Status</label>
              <select name="payment_status" class="w-full rounded-lg border border-slate-200 bg-white py-2 px-3 text-sm focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                @foreach($paymentStatusOptions as $opt)
                  <option value="{{ $opt }}" @selected($paymentStatus === $opt)>{{ ucfirst($opt) }}</option>
                @endforeach
              </select>
            </div>
            <button type="submit" class="btn-primary w-full justify-center">Update Payment Status</button>
            <p class="text-xs text-slate-500">Changing to <strong>Paid</strong> will automatically timestamp the verification time.</p>
          </form>
        </div>

        <div class="table-card p-6">
          <h4 class="text-xl font-semibold text-black dark:text-white mb-4">Update Order Status</h4>
          <form action="{{ route('admin.orders.update_status', $order) }}" method="POST" class="space-y-3">
            @csrf
            @method('PATCH')
            <select name="status" class="w-full rounded-lg border border-slate-200 bg-white py-2 px-3 text-sm focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
              @foreach($statusOptions as $statusOption)
                <option value="{{ $statusOption }}" @selected($order->status === $statusOption)>{{ ucfirst($statusOption) }}</option>
              @endforeach
            </select>
            <button type="submit" class="btn-primary w-full justify-center">Update Status</button>
          </form>

          <div class="mt-6">
            <h5 class="mb-2 text-sm font-medium">Apply Coupon</h5>
            <form action="{{ route('admin.orders.apply_coupon', $order) }}" method="POST" class="flex gap-2">
              @csrf
              <input type="text" name="coupon_code" placeholder="Coupon code" class="flex-1 rounded-lg border border-slate-200 bg-white py-2 px-3 text-sm focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white" />
              <button type="submit" class="btn-outline">Apply</button>
            </form>
            @if($order->coupon_code)
              <p class="mt-2 text-xs text-slate-500">Applied: {{ $order->coupon_code }} (Discount: Rp {{ number_format($order->discount_amount,0,',','.') }})</p>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
