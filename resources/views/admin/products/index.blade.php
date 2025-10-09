@extends('layouts.admin')

@section('header', 'Products')

@section('content')
<x-table 
  title="Products List"
  :export-items="[
    ['label' => 'CSV', 'href' => localized_route('admin.products.export.csv')],
    ['label' => 'Excel', 'href' => localized_route('admin.products.export.excel')],
    ['label' => 'PDF', 'href' => localized_route('admin.products.export.pdf')],
  ]"
  add-url="{{ localized_route('admin.products.create') }}"
  add-label="Add Product"
  :search="true"
  search-placeholder="Search by name..."
  :search-value="$q ?? request('q')"
  action="{{ localized_route('admin.products.index') }}"
  :pagination="$products"
>
  <x-slot:filters>
    <div>
      <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-300">Category</label>
      <select name="category_id" class="w-full rounded-lg border border-slate-200 bg-white py-2 pl-3 pr-8 text-sm focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
        <option value="">All Categories</option>
        @foreach(($categories ?? []) as $cat)
          <option value="{{ $cat->id }}" @selected(($category_id ?? request('category_id')) == $cat->id)>{{ $cat->name }}</option>
        @endforeach
      </select>
    </div>
  </x-slot:filters>

  <x-slot:head>
    <tr>
      <th>Product</th>
      <th>Category</th>
      <th>Price</th>
      <th>Stock</th>
      <th></th>
    </tr>
  </x-slot:head>

  <x-slot:body>
    @forelse ($products as $product)
      <tr>
        <td>
          <div class="flex items-center">
            <div class="h-10 w-10 flex-shrink-0">
              <img class="h-10 w-10 rounded-md object-cover" src="{{ $product->image_url ?? 'https://via.placeholder.com/40' }}" alt="">
            </div>
            <div class="ml-4">
              <div class="font-medium text-slate-900 dark:text-slate-200">{{ $product->name }}</div>
            </div>
          </div>
        </td>
        <td>{{ $product->category->name ?? 'N/A' }}</td>
        <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
        <td>{{ $product->stock }}</td>
        <td class="cell-actions">
          <a href="{{ localized_route('admin.products.show', ['id' => $product->id]) }}" class="btn-ghost text-xs">View</a>
          <a href="{{ localized_route('admin.products.edit', ['id' => $product->id]) }}" class="btn-outline text-xs">Edit</a>
          <x-confirm-delete action="{{ localized_route('admin.products.destroy', ['id' => $product->id]) }}">Delete</x-confirm-delete>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="5" class="py-6 text-center text-slate-500 dark:text-slate-400">No products found.</td>
      </tr>
    @endforelse
  </x-slot:body>
</x-table>
@endsection
