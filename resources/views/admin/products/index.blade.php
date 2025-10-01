@extends('layouts.admin')
@section('title', __('Products'))
@section('content')
<x-admin.header title="Products" :breadcrumbs="[['label'=>'Admin','href'=>route('admin.dashboard')],['label'=>'Products']]">
  <form method="GET" action="{{ route('admin.products.index') }}" class="flex items-center gap-2">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products..." class="h-10 w-64 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 text-sm" />
    @if(request('q'))
      <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600">Clear</a>
    @endif
    <a href="{{ route('admin.products.create') }}" class="px-3 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">New Product</a>
  </form>
</x-admin.header>
<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
  <table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
      <tr>
        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
        <th class="px-4 py-2">Price</th>
        <th class="px-4 py-2">Stock</th>
        <th class="px-4 py-2">Active</th>
        <th class="px-4 py-2"></th>
      </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200/70 dark:divide-gray-700">
      @foreach($products as $product)
      <tr>
        <td class="px-4 py-2">
          <div class="font-medium text-gray-900">{{ $product->name }}</div>
          <div class="text-gray-500 text-sm line-clamp-1">{{ $product->description }}</div>
        </td>
        <td class="px-4 py-2">${{ number_format($product->price, 2) }}</td>
        <td class="px-4 py-2">{{ $product->stock }}</td>
        <td class="px-4 py-2">{!! $product->is_active ? '<span class="text-green-600">Yes</span>' : '<span class="text-gray-400">No</span>' !!}</td>
        <td class="px-4 py-2 text-right">
          <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:underline mr-3">Edit</a>
          <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Delete this product?')">
            @csrf @method('DELETE')
            <button class="text-red-600 hover:underline">Delete</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
