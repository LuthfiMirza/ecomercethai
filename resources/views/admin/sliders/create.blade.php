@extends('layouts.admin')

@section('header', __('admin.sliders.form.create_title'))

@section('content')
<div class="rounded-3xl border border-slate-200 bg-white/80 p-6 shadow-xl shadow-slate-900/5 backdrop-blur dark:border-slate-800 dark:bg-slate-900/50">
    <form action="{{ localized_route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @include('admin.sliders.partials.form')

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-sky-500/30 transition hover:bg-sky-500 focus:outline-none focus:ring-4 focus:ring-sky-200">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7" />
                </svg>
                {{ __('admin.sliders.form.save') }}
            </button>
            <a href="{{ localized_route('admin.sliders.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700 dark:text-slate-300 dark:hover:text-white">
                {{ __('admin.common.cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection
