@extends('layouts.admin')
@section('title', 'New Banner')
@section('content')
  <form action="{{ localized_route('admin.banners.store') }}" method="POST" class="max-w-2xl space-y-4 bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
    @csrf
    @include('admin.banners.partials.form', ['banner' => null])
    <div>
      <button class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">Save</button>
      <a href="{{ localized_route('admin.banners.index') }}" class="ml-2 text-gray-600 hover:underline">Cancel</a>
    </div>
  </form>
@endsection
