@extends('layouts.admin')
@section('title', 'Edit Payment Profile')
@section('content')
  <form action="{{ route('admin.payment-profiles.update', $profile) }}" method="POST" class="max-w-2xl space-y-4 bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
    @csrf
    @method('PUT')
    @include('admin.payment_profiles.partials.form', ['profile' => $profile])
    <div>
      <button class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">Update</button>
      <a href="{{ route('admin.payment-profiles.index') }}" class="ml-2 text-gray-600 hover:underline">Back</a>
    </div>
  </form>
@endsection
