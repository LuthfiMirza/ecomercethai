@props(['action', 'title' => __('Delete Confirmation'), 'description' => __('Are you sure you want to delete this item?')])

<div x-data="{ open: false }" class="inline-block">
    <button
        type="button"
        @click="open = true"
        class="inline-flex items-center gap-1 rounded-full border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:border-rose-300 hover:bg-rose-50/80 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:ring-offset-2"
    >
        <svg class="h-3.5 w-3.5" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M12 5v8a2 2 0 01-2 2H8a2 2 0 01-2-2V5m2 0V3.5A1.5 1.5 0 019.5 2h1A1.5 1.5 0 0112 3.5V5" />
        </svg>
        <span>{{ $slot ?? __('Delete') }}</span>
    </button>

    <div
        x-show="open"
        x-transition.opacity
        x-cloak
        class="fixed inset-0 z-[999] flex items-center justify-center px-4"
    >
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="open = false"></div>

        <div
            x-show="open"
            x-transition.origin.bottom.duration.200ms
            class="relative w-full max-w-md overflow-hidden rounded-3xl border border-white/40 bg-gradient-to-br from-white via-slate-50/95 to-slate-100/90 p-6 shadow-[0_40px_80px_-32px_rgba(15,23,42,0.45)] ring-1 ring-slate-200/60 dark:from-slate-900 dark:via-slate-900/95 dark:to-slate-900/90 dark:ring-slate-700/60"
        >
            <span class="pointer-events-none absolute -top-20 right-10 h-32 w-32 rounded-full bg-gradient-to-br from-rose-500/30 via-rose-400/20 to-transparent blur-3xl"></span>

            <div class="flex items-start gap-4">
                <span class="inline-flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-3xl bg-rose-500 text-white shadow-[inset_0_6px_12px_rgba(255,255,255,0.25)]">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <div class="space-y-1.5 text-left">
                    <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">{{ $title }}</h3>
                    <p class="text-sm leading-relaxed text-slate-500 dark:text-slate-300">{{ $description }}</p>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <button
                    type="button"
                    @click="open = false"
                    class="rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-500 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-300 focus:ring-offset-1 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                >
                    {{ __('Cancel') }}
                </button>

                <form method="POST" action="{{ $action }}">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-rose-500 to-rose-600 px-5 py-2 text-sm font-semibold text-white shadow-[0_10px_20px_-8px_rgba(244,63,94,0.65)] transition hover:from-rose-600 hover:to-rose-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-400 focus-visible:ring-offset-2"
                    >
                        {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
