@extends('layouts.admin')

@section('header', 'Edit User')

@section('content')
<div class="bg-white dark:bg-slate-800 shadow-md rounded-lg p-6">
    <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-6">Edit User: {{ $user->name }}</h2>

    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                   class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('name') border-red-500 @enderror">
            @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                   class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('email') border-red-500 @enderror">
            @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">New Password (Optional)</label>
                <input type="password" id="password" name="password"
                       class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('password') border-red-500 @enderror">
                @error('password') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
            </div>
        </div>

        <div class="mb-6">
            <label for="is_admin" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Role</label>
            <select id="is_admin" name="is_admin" required
                    class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                <option value="0" {{ old('is_admin', $user->is_admin) == '0' ? 'selected' : '' }}>Customer</option>
                <option value="1" {{ old('is_admin', $user->is_admin) == '1' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>

        <div class="flex items-center justify-end space-x-4">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:bg-slate-600 dark:hover:bg-slate-500">
                Update User
            </button>
        </div>
    </form>
</div>
@endsection
