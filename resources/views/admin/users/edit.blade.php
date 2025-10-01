@extends('layouts.admin')
@section('title', 'Edit User')
@section('content')
  <form action="{{ route('admin.users.update', $user) }}" method="POST" class="max-w-2xl space-y-4 bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
    @csrf
    @method('PUT')
    <div class="space-y-1">
      <label class="block text-sm font-medium">Name</label>
      <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 rounded" required>
    </div>
    <div class="space-y-1">
      <label class="block text-sm font-medium">Email</label>
      <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 rounded" required>
    </div>
    <div class="space-y-1">
      <label class="block text-sm font-medium">Roles</label>
      <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
        @foreach($roles as $role)
          <label class="flex items-center gap-2">
            <input type="checkbox" name="roles[]" value="{{ $role->name }}" @checked($user->roles->pluck('name')->contains($role->name))>
            <span>{{ ucfirst($role->name) }}</span>
          </label>
        @endforeach
      </div>
    </div>
    <div>
      <button class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">Save</button>
      <a href="{{ route('admin.users.index') }}" class="ml-2 text-gray-600 hover:underline">Back</a>
    </div>
  </form>
@endsection
