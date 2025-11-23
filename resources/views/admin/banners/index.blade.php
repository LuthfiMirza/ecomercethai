@extends('layouts.admin')

@section('header', __('admin.nav.banners'))

@section('content')
<div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
    <form action="{{ localized_route('admin.banners.index') }}" method="GET" class="flex items-center gap-2">
        <input type="text"
               name="q"
               value="{{ $q }}"
               placeholder="{{ __('admin.banners.search_placeholder') }}"
               class="h-11 w-64 rounded-2xl border border-slate-200 bg-white/80 px-4 text-sm text-slate-700 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-200 dark:border-slate-700 dark:bg-slate-900/40 dark:text-white">
        @if($q)
            <a href="{{ localized_route('admin.banners.index') }}" class="text-sm text-slate-500 hover:text-slate-700 dark:text-slate-300">{{ __('admin.common.clear') }}</a>
        @endif
    </form>
    <a href="{{ localized_route('admin.banners.create') }}"
       class="inline-flex items-center gap-2 rounded-2xl bg-orange-500 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-orange-400/40 transition hover:bg-orange-400 focus:outline-none focus:ring-4 focus:ring-orange-200">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m7-7H5" />
        </svg>
        {{ __('admin.banners.new') }}
    </a>
</div>

<div class="table-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-white/80 dark:bg-slate-900/60">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('admin.common.preview') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('admin.common.title_subtitle') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('admin.common.order') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('admin.common.status') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">{{ __('admin.common.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white/70 dark:divide-slate-800 dark:bg-slate-900/40">
            @forelse($banners as $banner)
                @php
                    $imgPath = $banner->image_path;
                    $url = $imgPath
                        ? (\Illuminate\Support\Str::startsWith($imgPath, ['http://', 'https://'])
                            ? $imgPath
                            : \Illuminate\Support\Facades\Storage::url($imgPath))
                        : null;
                @endphp
                <tr>
                    <td class="px-6 py-4">
                        @if($url)
                            <img src="{{ $url }}" alt="{{ $banner->title }}" class="h-16 w-36 rounded-xl object-cover shadow-sm">
                        @else
                            <div class="h-16 w-36 rounded-xl border border-dashed border-slate-300 flex items-center justify-center text-xs text-slate-400">{{ __('admin.common.no_image') }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-semibold text-slate-800 dark:text-slate-100">{{ $banner->title }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2">{{ $banner->subtitle }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $banner->sort_order }}</td>
                    <td class="px-6 py-4">
                        @if($banner->is_active)
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-200">{{ __('admin.common.active') }}</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-700/40 dark:text-slate-300">{{ __('admin.common.hidden') }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ localized_route('admin.banners.edit', ['banner' => $banner]) }}" class="text-sm font-medium text-orange-600 hover:text-orange-500">{{ __('admin.common.edit') }}</a>
                            <x-confirm-delete
                                :title="__('admin.common.delete')"
                                :description="__('admin.banners.delete_confirm')"
                                :action="localized_route('admin.banners.destroy', ['banner' => $banner])"
                            >
                                {{ __('admin.common.delete') }}
                            </x-confirm-delete>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-6 text-center text-sm text-slate-500 dark:text-slate-300">
                        {{ __('admin.banners.empty') }}
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-slate-100 bg-white/70 p-4 dark:border-slate-800 dark:bg-slate-900/40">
        {{ $banners->links() }}
    </div>
</div>
@endsection
