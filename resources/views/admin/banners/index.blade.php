@extends('layouts.admin')
@section('title', 'Banners')
@section('content')
<x-admin.header title="Banners" :breadcrumbs="[['label'=>'Admin','href'=>localized_route('admin.dashboard')],['label'=>'Banners']]">
  <form method="GET" action="{{ localized_route('admin.banners.index') }}" class="flex items-center gap-2">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search banners..." class="h-10 w-64 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 text-sm" />
    @if(request('q'))
      <a href="{{ localized_route('admin.banners.index') }}" class="text-sm text-gray-600">Clear</a>
    @endif
    <a href="{{ localized_route('admin.banners.create') }}" class="px-3 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">New Banner</a>
  </form>
</x-admin.header>
<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
  <table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
      <tr>
        <th class="px-4 py-2 text-left">Title</th>
        <th class="px-4 py-2">Placement</th>
        <th class="px-4 py-2">Active Window</th>
        <th class="px-4 py-2">Active</th>
        <th class="px-4 py-2">Priority</th>
        <th class="px-4 py-2"></th>
      </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
      @foreach($banners as $banner)
      <tr>
        <td class="px-4 py-2">
          <div class="font-medium text-gray-900">{{ $banner->title }}</div>
          <div class="text-gray-500 text-sm line-clamp-1">{{ $banner->image_path }}</div>
        </td>
        <td class="px-4 py-2">{{ $banner->placement }}</td>
        <td class="px-4 py-2">{{ optional($banner->starts_at)->format('Y-m-d') ?? '-' }} — {{ optional($banner->ends_at)->format('Y-m-d') ?? '-' }}</td>
        <td class="px-4 py-2">{!! $banner->is_active ? '<span class="text-green-600">Yes</span>' : '<span class="text-gray-400">No</span>' !!}</td>
        <td class="px-4 py-2">{{ $banner->priority }}</td>
        <td class="px-4 py-2 text-right">
          <a href="{{ localized_route('admin.banners.show', ['id' => $banner->id]) }}" class="text-slate-600 hover:underline mr-3">View</a>
          <a href="{{ localized_route('admin.banners.edit', ['id' => $banner->id]) }}" class="text-blue-600 hover:underline mr-3">Edit</a>
          <form action="{{ localized_route('admin.banners.destroy', ['id' => $banner->id]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this banner?')">
            @csrf @method('DELETE')
            <button class="text-red-600 hover:underline">Delete</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $banners->links() }}</div>
@endsection
