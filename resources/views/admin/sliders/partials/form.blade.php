@php
    $existingPath = $slider->image_path ?? null;
    $imageUrl = $existingPath
        ? (\Illuminate\Support\Str::startsWith($existingPath, ['http://', 'https://'])
            ? $existingPath
            : \Illuminate\Support\Facades\Storage::url($existingPath))
        : null;
@endphp

<div class="space-y-6">
    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label class="text-sm font-semibold text-slate-700 dark:text-slate-100">{{ __('admin.sliders.form.title') }}</label>
            <input type="text"
                   name="title"
                   value="{{ old('title', $slider->title) }}"
                   required
                   class="mt-1 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-900 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-900/40 dark:text-white">
            @error('title')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700 dark:text-slate-100">{{ __('admin.sliders.form.link') }}</label>
            <input type="url"
                   name="link_url"
                   value="{{ old('link_url', $slider->link_url) }}"
                   class="mt-1 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-900 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-900/40 dark:text-white"
                   placeholder="https://example.com/promo">
            @error('link_url')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label class="text-sm font-semibold text-slate-700 dark:text-slate-100">{{ __('admin.sliders.form.subtitle') }}</label>
        <textarea name="subtitle"
                  rows="3"
                  class="mt-1 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-900 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-900/40 dark:text-white"
                  placeholder="{{ __('admin.sliders.form.subtitle_placeholder') }}">{{ old('subtitle', $slider->subtitle) }}</textarea>
        @error('subtitle')
            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label class="text-sm font-semibold text-slate-700 dark:text-slate-100">{{ __('admin.sliders.form.sort_order') }}</label>
            <input type="number"
                   name="sort_order"
                   value="{{ old('sort_order', $slider->sort_order ?? 0) }}"
                   class="mt-1 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-900 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-700 dark:bg-slate-900/40 dark:text-white">
            @error('sort_order')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex items-center gap-3 pt-6 md:pt-0">
            <input type="hidden" name="is_active" value="0">
            <label class="flex items-center gap-3 text-sm font-semibold text-slate-700 dark:text-slate-100">
                <input type="checkbox"
                       name="is_active"
                       value="1"
                       class="h-5 w-5 rounded border-slate-300 text-sky-500 focus:ring-sky-400"
                       {{ old('is_active', $slider->is_active ?? true) ? 'checked' : '' }}>
                {{ __('admin.sliders.form.is_active') }}
            </label>
        </div>
    </div>

    <div>
        <label class="text-sm font-semibold text-slate-700 dark:text-slate-100">{{ __('admin.sliders.form.image') }}</label>
        <input type="file"
               name="image"
               accept="image/jpeg,image/png,image/webp"
               class="mt-1 block w-full rounded-2xl border border-dashed border-slate-300 bg-white/60 px-4 py-3 text-sm text-slate-600 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200 dark:border-slate-600 dark:bg-slate-900/30 dark:text-slate-200">
        <p class="mt-2 text-xs text-slate-500">{{ __('admin.sliders.form.image_hint') }}</p>
        @error('image')
            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
        @enderror

        @if($imageUrl)
            <div class="mt-3 rounded-2xl border border-slate-200 bg-white/60 p-3 dark:border-slate-700 dark:bg-slate-900/40">
                <p class="text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2">{{ __('admin.sliders.form.current_preview') }}</p>
                <img src="{{ $imageUrl }}" alt="{{ $slider->title }}" class="w-full rounded-xl object-cover max-h-64">
            </div>
        @endif
    </div>
</div>
