@extends('layouts.admin')

@section('header', 'Edit Order')

@section('content')
<div class="mx-auto max-w-5xl space-y-6 p-4 md:p-6">
  <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h3 class="text-2xl font-semibold text-slate-900 dark:text-white">Edit Order</h3>
      <p class="text-sm text-slate-500">Order ID: <span class="font-semibold">#ORD{{ $order->id }}</span></p>
    </div>
    <div class="flex flex-wrap gap-2">
      <a href="{{ route('admin.orders.show', $order) }}" class="btn-outline">Back to Details</a>
      <a href="{{ route('admin.orders.invoice', $order) }}" class="btn-outline">Preview Invoice</a>
    </div>
  </div>

  <form action="{{ route('admin.orders.update', $order) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="form-card form-modern space-y-6">
      <h4 class="text-lg font-semibold text-slate-800 dark:text-slate-200">Order Overview</h4>
      <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div>
          <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Order Status</label>
          <select name="status">
            @foreach($statusOptions as $opt)
              <option value="{{ $opt }}" @selected(old('status', $order->status) === $opt)>{{ ucfirst($opt) }}</option>
            @endforeach
          </select>
          @error('status')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Payment Status</label>
          <select name="payment_status">
            @foreach($paymentStatusOptions as $opt)
              <option value="{{ $opt }}" @selected(old('payment_status', $order->payment_status) === $opt)>{{ ucfirst($opt) }}</option>
            @endforeach
          </select>
          @error('payment_status')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
        </div>
      </div>

      <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div>
          <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Payment Method</label>
          <input type="text" name="payment_method" value="{{ old('payment_method', $order->payment_method) }}" placeholder="e.g. Bank Transfer BCA">
          @error('payment_method')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Total Amount</label>
          <input type="number" step="0.01" name="total_amount" value="{{ old('total_amount', $order->total_amount) }}">
          @error('total_amount')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
        </div>
      </div>

      <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div>
          <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Discount Amount</label>
          <input type="number" step="0.01" name="discount_amount" value="{{ old('discount_amount', $order->discount_amount) }}">
          @error('discount_amount')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
        </div>
        <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4 text-sm dark:border-slate-700/60 dark:bg-slate-900/40">
          <p class="text-xs uppercase tracking-wide text-slate-400">Payment Verified At</p>
          <p class="mt-2 font-medium text-slate-700 dark:text-slate-200">{{ $order->payment_verified_at?->format('d M Y H:i') ?? 'Will auto-fill when payment confirmed' }}</p>
        </div>
      </div>

      <div>
        <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Shipping Address</label>
        <textarea name="shipping_address" rows="4">{{ old('shipping_address', $order->shipping_address) }}</textarea>
        @error('shipping_address')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
      </div>
    </div>

    <div class="form-card space-y-4">
      <div class="flex items-center justify-between">
        <h4 class="text-lg font-semibold text-slate-800 dark:text-slate-200">Payment Proof</h4>
        @if($order->payment_proof_url)
          <span class="text-xs text-slate-500 dark:text-slate-400">Current file shown below</span>
        @endif
      </div>

      @if($order->payment_proof_url)
        <div class="flex flex-col gap-4 rounded-xl border border-slate-200 p-3 dark:border-slate-700/60 md:flex-row">
          <img src="{{ $order->payment_proof_url }}" alt="Payment proof preview" class="h-40 w-full rounded-lg object-contain bg-slate-100 dark:bg-slate-800 md:w-48" loading="lazy">
          <div class="flex-1 space-y-3 text-sm">
            <p class="text-slate-600 dark:text-slate-300">Upload a new file to replace the current proof or remove it entirely.</p>
            <label class="inline-flex items-center gap-2 text-xs font-medium text-rose-600">
              <input type="checkbox" name="remove_payment_proof" value="1" class="check-modern">
              Remove current proof
            </label>
            <a href="{{ $order->payment_proof_url }}" target="_blank" class="btn-outline text-xs">Open Original</a>
          </div>
        </div>
      @endif

      <div class="space-y-2">
        <label class="text-xs font-medium text-slate-500 dark:text-slate-300">Upload New Proof</label>
        <input type="file" name="payment_proof" accept="image/*,.pdf" class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-slate-200 file:px-4 file:py-2 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-300 dark:file:bg-slate-700 dark:file:text-slate-200">
        @error('payment_proof')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
        <p class="text-xs text-slate-500">Supported: JPG, PNG, WEBP, PDF up to 4 MB.</p>
      </div>
    </div>

    <div class="flex flex-wrap items-center justify-end gap-3">
      <a href="{{ route('admin.orders.show', $order) }}" class="btn-outline">Cancel</a>
      <button type="submit" class="btn-primary">Save Changes</button>
    </div>
  </form>
</div>
@endsection
