@extends('layouts.admin')

@section('header', 'Category Detail')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
  <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
    <div class="space-y-1">
      <h3 class="text-2xl font-bold text-black dark:text-white">{{ $category->name }}</h3>
      <p class="text-sm text-slate-500 dark:text-slate-300">
        {{ $category->products_count }} product(s) &bull; Created {{ $category->created_at?->format('d M Y H:i') }}
      </p>
    </div>
    <div class="flex flex-wrap gap-2">
      <a href="{{ localized_route('admin.categories.index') }}" class="btn-ghost text-sm">Back to list</a>
      <a href="{{ localized_route('admin.categories.create') }}" class="btn-outline text-sm">Add Category</a>
      <a href="{{ localized_route('admin.products.create', ['category_id' => $category->id]) }}" class="btn-outline text-sm">Add Product</a>
      <a href="{{ localized_route('admin.categories.edit', ['id' => $category->id]) }}" class="btn-primary text-sm">Edit Category</a>
    </div>
  </div>

  <div class="mt-6 grid grid-cols-12 gap-4 md:gap-6 2xl:gap-7.5">
    <div class="col-span-12 xl:col-span-4 space-y-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <h4 class="text-lg font-semibold text-black dark:text-white mb-3">Category Info</h4>
        <dl class="space-y-3 text-sm text-slate-600 dark:text-slate-300">
          <div>
            <dt class="font-medium text-slate-500 dark:text-slate-400">Slug</dt>
            <dd>{{ $category->slug ?? '—' }}</dd>
          </div>
          <div>
            <dt class="font-medium text-slate-500 dark:text-slate-400">Description</dt>
            <dd>{!! $category->description ? nl2br(e($category->description)) : '<span class="text-slate-400">Not provided.</span>' !!}</dd>
          </div>
          <div>
            <dt class="font-medium text-slate-500 dark:text-slate-400">Last Updated</dt>
            <dd>{{ $category->updated_at?->format('d M Y H:i') }}</dd>
          </div>
        </dl>
      </div>
    </div>
    <div class="col-span-12 xl:col-span-8">
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <div class="flex items-center justify-between mb-4">
          <h4 class="text-lg font-semibold text-black dark:text-white">Products</h4>
          <span class="text-xs uppercase tracking-wide text-slate-400">Showing recent items</span>
        </div>
        @if($category->products->isEmpty())
          <p class="text-sm text-slate-500">No products in this category yet.</p>
        @else
          <div class="overflow-x-auto soft-scrollbar">
            <table class="table-modern">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Price</th>
                  <th>Stock</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @foreach($category->products as $product)
                  <tr>
                    <td class="font-medium text-slate-900 dark:text-slate-200">{{ $product->name }}</td>
                    <td>{{ format_price($product->price ?? 0) }}</td>
                    <td>{{ $product->stock }}</td>
                    <td class="cell-actions">
                      <a href="{{ localized_route('admin.products.show', ['id' => $product->id]) }}" class="btn-ghost text-xs">View</a>
                      <a href="{{ localized_route('admin.products.edit', ['id' => $product->id]) }}" class="btn-outline text-xs">Edit</a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
