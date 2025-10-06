@extends('layouts.admin')

@section('header', 'Categories')

@section('content')
<x-table
  title="Category Management"
  add-url="{{ route('admin.categories.create') }}"
  add-label="Add New Category"
  :search="true"
  search-placeholder="Search categories..."
  :search-value="$q ?? request('q')"
  action="{{ route('admin.categories.index') }}"
  :pagination="$categories"
>
  <x-slot:head>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Created At</th>
      <th></th>
    </tr>
  </x-slot:head>
  <x-slot:body>
    @forelse ($categories as $category)
      <tr>
        <td class="font-medium text-slate-900 dark:text-slate-200">{{ $category->id }}</td>
        <td>{{ $category->name }}</td>
        <td>{{ $category->created_at->format('d M Y') }}</td>
        <td class="cell-actions">
          <a href="{{ route('admin.categories.edit', $category) }}" class="btn-outline text-xs">Edit</a>
          <x-confirm-delete action="{{ route('admin.categories.destroy', $category) }}">Delete</x-confirm-delete>
        </td>
      </tr>
    @empty
      <tr><td colspan="4" class="py-6 text-center text-slate-500 dark:text-slate-400">No categories found.</td></tr>
    @endforelse
  </x-slot:body>
</x-table>
@endsection
