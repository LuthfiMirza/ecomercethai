@extends('layouts.admin')

@section('header', 'Product Detail')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
  <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
    <div class="space-y-1">
      <h3 class="text-2xl font-bold text-black dark:text-white">{{ $product->name }}</h3>
      <p class="text-sm text-slate-500 dark:text-slate-300">
        SKU #{{ $product->id }} &bull; Last updated {{ $product->updated_at?->format('d M Y H:i') }}
      </p>
    </div>
    <div class="flex flex-wrap gap-2">
      <a href="{{ localized_route('admin.products.index') }}" class="btn-ghost text-sm">Back to list</a>
      <a href="{{ localized_route('admin.products.create') }}" class="btn-outline text-sm">Add Product</a>
      <a href="{{ localized_route('admin.products.edit', ['id' => $product->id]) }}" class="btn-primary text-sm">Edit Product</a>
    </div>
  </div>

  <div class="mt-6 grid grid-cols-12 gap-4 md:gap-6 2xl:gap-7.5">
    <div class="col-span-12 xl:col-span-4 space-y-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <div class="flex flex-col items-center text-center space-y-3">
          <img src="{{ $product->image_url ?? $product->image ?? 'https://via.placeholder.com/320x200?text=Product' }}"
               alt="{{ $product->name }}"
               class="h-40 w-full rounded-lg object-cover" />
          <div class="flex flex-wrap gap-2">
            <span class="badge {{ $product->status === 'active' ? 'badge-success' : 'badge-neutral' }}">{{ ucfirst($product->status ?? ($product->is_active ? 'active' : 'inactive')) }}</span>
            @if($product->category)
              <span class="badge badge-info">{{ $product->category->name }}</span>
            @endif
          </div>
        </div>
        <dl class="mt-6 space-y-3 text-sm text-slate-600 dark:text-slate-300">
          <div class="flex justify-between">
            <dt class="font-medium text-slate-500 dark:text-slate-400">Price</dt>
            <dd>{{ format_price($product->price ?? 0) }}</dd>
          </div>
          <div class="flex justify-between">
            <dt class="font-medium text-slate-500 dark:text-slate-400">Stock</dt>
            <dd>{{ $product->stock }} unit(s)</dd>
          </div>
          <div class="flex justify-between">
            <dt class="font-medium text-slate-500 dark:text-slate-400">Brand</dt>
            <dd>{{ $product->brand ?? '-' }}</dd>
          </div>
          <div class="flex justify-between">
            <dt class="font-medium text-slate-500 dark:text-slate-400">Created</dt>
            <dd>{{ $product->created_at?->format('d M Y H:i') }}</dd>
          </div>
        </dl>
      </div>
    </div>
    <div class="col-span-12 xl:col-span-8 space-y-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <h4 class="text-lg font-semibold text-black dark:text-white mb-3">Description</h4>
        <div class="prose max-w-none text-sm text-slate-600 dark:prose-invert dark:text-slate-200">
          {!! $product->description ? nl2br(e($product->description)) : '<p class="text-slate-400">No description provided.</p>' !!}
        </div>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <h4 class="text-lg font-semibold text-black dark:text-white mb-3">Inventory</h4>
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
          <div class="rounded-xl border border-slate-200 px-4 py-3 dark:border-slate-700">
            <p class="text-xs uppercase tracking-wide text-slate-400">Status</p>
            <p class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ ucfirst($product->status ?? ($product->is_active ? 'active' : 'inactive')) }}</p>
          </div>
          <div class="rounded-xl border border-slate-200 px-4 py-3 dark:border-slate-700">
            <p class="text-xs uppercase tracking-wide text-slate-400">Total Sold</p>
            <p class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ number_format($totalSold) }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
