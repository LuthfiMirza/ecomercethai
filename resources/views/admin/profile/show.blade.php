@extends('layouts.admin')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 dark:text-white">Admin Profile</h1>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 dark:bg-green-900 dark:border-green-700 dark:text-green-200" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 dark:bg-red-900 dark:border-red-700 dark:text-red-200" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white p-6 rounded-lg shadow-lg admin-card dark:bg-gray-800">
        <form action="{{ route('admin.profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="input-field shadow appearance-none rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:text-white @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-xs italic mt-2 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="input-field shadow appearance-none rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:text-white @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-xs italic mt-2 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">New Password</label>
                <input type="password" id="password" name="password" class="input-field shadow appearance-none rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:text-white @error('password') border-red-500 @enderror" placeholder="Leave blank to keep current password">
                @error('password')
                    <p class="text-red-500 text-xs italic mt-2 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="input-field shadow appearance-none rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:text-white">
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="neon-button font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline">
                    Update Profile
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
