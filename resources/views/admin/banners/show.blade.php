@extends('layouts.admin')

@section('header', 'Banner Detail')

@section('content')
<div class="mx-auto max-w-screen-xl p-4 md:p-6 2xl:p-10">
  <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
    <div class="space-y-1">
      <h3 class="text-2xl font-bold text-black dark:text-white">{{ $banner->title }}</h3>
      <p class="text-sm text-slate-500 dark:text-slate-300">
        Placement: {{ ucfirst(str_replace('_', ' ', $banner->placement)) }} &bull; Priority {{ $banner->priority ?? 0 }}
      </p>
    </div>
    <div class="flex flex-wrap gap-2">
      <a href="{{ localized_route('admin.banners.index') }}" class="btn-ghost text-sm">Back to list</a>
      <a href="{{ localized_route('admin.banners.create') }}" class="btn-outline text-sm">Add Banner</a>
      <a href="{{ localized_route('admin.banners.edit', ['id' => $banner->id]) }}" class="btn-primary text-sm">Edit Banner</a>
    </div>
  </div>

  <div class="mt-6 grid grid-cols-12 gap-4 md:gap-6 2xl:gap-7.5">
    <div class="col-span-12 xl:col-span-7 space-y-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <h4 class="text-lg font-semibold text-black dark:text-white mb-3">Preview</h4>
        <div class="rounded-lg border border-dashed border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/50">
          <img src="{{ $banner->image_path }}" alt="{{ $banner->title }}" class="mx-auto max-h-72 w-full rounded-md object-cover" onerror="this.src='https://via.placeholder.com/640x300?text=Banner';">
        </div>
      </div>
    </div>
    <div class="col-span-12 xl:col-span-5 space-y-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <h4 class="text-lg font-semibold text-black dark:text-white mb-3">Banner Info</h4>
        <dl class="space-y-3 text-sm text-slate-600 dark:text-slate-300">
          <div class="flex justify-between">
            <dt class="font-medium text-slate-500 dark:text-slate-400">Status</dt>
            <dd><span class="badge {{ $banner->is_active ? 'badge-success' : 'badge-neutral' }}">{{ $banner->is_active ? 'Active' : 'Inactive' }}</span></dd>
          </div>
          <div class="flex justify-between">
            <dt class="font-medium text-slate-500 dark:text-slate-400">Link URL</dt>
            <dd class="text-right">
              @if($banner->link_url)
                <a class="text-indigo-600 hover:underline" href="{{ $banner->link_url }}" target="_blank" rel="noopener">Open link</a>
              @else
                <span class="text-slate-400">Not set</span>
              @endif
            </dd>
          </div>
          <div class="flex justify-between">
            <dt class="font-medium text-slate-500 dark:text-slate-400">Active Window</dt>
            <dd>{{ optional($banner->starts_at)->format('d M Y') ?? '—' }} – {{ optional($banner->ends_at)->format('d M Y') ?? '—' }}</dd>
          </div>
          <div class="flex justify-between">
            <dt class="font-medium text-slate-500 dark:text-slate-400">Updated</dt>
            <dd>{{ $banner->updated_at?->format('d M Y H:i') }}</dd>
          </div>
        </dl>
      </div>
    </div>
  </div>
</div>
@endsection
