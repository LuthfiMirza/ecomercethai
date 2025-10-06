@extends('layouts.admin')

@section('header', 'Add New Category')

@section('content')
<div class="bg-white dark:bg-slate-800 shadow-md rounded-lg p-6">
    <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-6">Create New Category</h2>

    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Category Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required 
                   class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-slate-700 dark:border-slate-600 dark:text-white dark:placeholder-slate-400 @error('name') border-red-500 @enderror">
            @error('name')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Description (Optional)</label>
            <textarea id="description" name="description" rows="4"
                      class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-slate-700 dark:border-slate-600 dark:text-white dark:placeholder-slate-400">{{ old('description') }}</textarea>
        </div>

        <div class="flex items-center justify-end space-x-4">
            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:bg-slate-600 dark:hover:bg-slate-500">
                Save Category
            </button>
        </div>
    </form>
</div>
@endsection
