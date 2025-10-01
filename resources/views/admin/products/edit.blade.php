@extends('layouts.admin')
@section('title', __('Edit Product'))
@section('content')
  <form action="{{ route('admin.products.update', $product) }}" method="POST" class="max-w-2xl space-y-4 bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
    @csrf
    @method('PUT')
    @include('admin.products.partials.form', ['product' => $product])
    <div>
      <button class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">Update</button>
      <a href="{{ route('admin.products.index') }}" class="ml-2 text-gray-600 hover:underline">Back</a>
    </div>
  </form>
@endsection
