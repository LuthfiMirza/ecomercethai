@php
    $type = $type ?? 'info';

    $styles = [
        'success' => [
            'panel' => 'bg-gradient-to-br from-white via-emerald-50/70 to-white dark:from-slate-900 dark:via-emerald-900/20 dark:to-slate-900',
            'ring' => 'ring-emerald-200/60 dark:ring-emerald-500/30',
            'shadow' => 'shadow-[0_28px_60px_-30px_rgba(16,185,129,0.88)]',
            'icon' => 'bg-emerald-500 text-white',
            'title_class' => 'text-emerald-600 dark:text-emerald-200',
            'headline' => __('Success'),
            'message' => 'text-slate-600 dark:text-slate-200',
            'accent' => 'from-emerald-400/90 via-sky-400/80 to-emerald-500/80',
            'close' => 'text-emerald-500 hover:bg-emerald-100/70 dark:text-emerald-200 dark:hover:bg-emerald-500/20',
            'button' => 'bg-emerald-500 hover:bg-emerald-600 focus-visible:ring-emerald-400',
        ],
        'error' => [
            'panel' => 'bg-gradient-to-br from-white via-rose-50/70 to-white dark:from-slate-900 dark:via-rose-900/20 dark:to-slate-900',
            'ring' => 'ring-rose-200/60 dark:ring-rose-500/30',
            'shadow' => 'shadow-[0_30px_60px_-28px_rgba(244,63,94,0.88)]',
            'icon' => 'bg-rose-500 text-white',
            'title_class' => 'text-rose-600 dark:text-rose-200',
            'headline' => __('Error'),
            'message' => 'text-slate-600 dark:text-slate-200',
            'accent' => 'from-rose-400/90 via-pink-500/80 to-rose-500/80',
            'close' => 'text-rose-500 hover:bg-rose-100/70 dark:text-rose-200 dark:hover:bg-rose-500/20',
            'button' => 'bg-rose-500 hover:bg-rose-600 focus-visible:ring-rose-400',
        ],
        'warning' => [
            'panel' => 'bg-gradient-to-br from-white via-amber-50/70 to-white dark:from-slate-900 dark:via-amber-900/20 dark:to-slate-900',
            'ring' => 'ring-amber-200/60 dark:ring-amber-500/30',
            'shadow' => 'shadow-[0_30px_60px_-28px_rgba(245,158,11,0.8)]',
            'icon' => 'bg-amber-500 text-slate-900',
            'title_class' => 'text-amber-600 dark:text-amber-200',
            'headline' => __('Warning'),
            'message' => 'text-slate-600 dark:text-slate-200',
            'accent' => 'from-amber-400/90 via-orange-400/80 to-amber-500/80',
            'close' => 'text-amber-500 hover:bg-amber-100/70 dark:text-amber-200 dark:hover:bg-amber-500/20',
            'button' => 'bg-amber-500 hover:bg-amber-600 focus-visible:ring-amber-400',
        ],
        'info' => [
            'panel' => 'bg-gradient-to-br from-white via-sky-50/70 to-white dark:from-slate-900 dark:via-blue-900/20 dark:to-slate-900',
            'ring' => 'ring-sky-200/70 dark:ring-blue-500/35',
            'shadow' => 'shadow-[0_32px_70px_-36px_rgba(59,130,246,0.95)]',
            'icon' => 'bg-blue-500 text-white',
            'title_class' => 'text-blue-600 dark:text-blue-200',
            'headline' => __('Information'),
            'message' => 'text-slate-600 dark:text-slate-200',
            'accent' => 'from-sky-400/90 via-blue-500/80 to-sky-500/70',
            'close' => 'text-blue-500 hover:bg-blue-100/70 dark:text-blue-200 dark:hover:bg-blue-500/20',
            'button' => 'bg-blue-500 hover:bg-blue-600 focus-visible:ring-blue-400',
        ],
    ];

    $icons = [
        'success' => 'M5 13l4 4L19 7',
        'error' => 'M6 18L18 6M6 6l12 12',
        'warning' => 'M10.29 3.86l-7.5 13A1 1 0 0 0 3.62 18h16.76a1 1 0 0 0 .83-1.53l-7.5-13a1 1 0 0 0-1.72 0z',
        'info' => 'M12 9v2m0 4h.01M12 5a7 7 0 100 14 7 7 0 000-14z',
    ];

    $style = $styles[$type] ?? $styles['info'];

    $displayMessage = is_iterable($message ?? null)
        ? collect($message)->flatten()->filter(fn ($val) => filled($val))->implode(' ')
        : ($message ?? '');
    $displayMessage = trim($displayMessage);

    $headline = $title ?? $style['headline'];
    $actionLabel = $actionLabel ?? null;
    $actionUrl = $actionUrl ?? null;
@endphp

@if($displayMessage !== '')
    <div
        x-data="{
            open: {{ ($open ?? false) ? 'true' : 'false' }},
            hideAfter: {{ $duration ?? 5200 }},
            init() {
                if (this.open) {
                    this.scheduleHide();
                }
            },
            scheduleHide() {
                if (this.hideAfter > 0) {
                    setTimeout(() => this.open = false, this.hideAfter);
                }
            }
        }"
        x-show="open"
        x-transition.origin.top.right.duration.200ms
        x-cloak
        class="fixed top-8 right-8 z-50 w-full max-w-sm"
        role="status"
        aria-live="polite"
    >
        <div class="relative overflow-hidden rounded-[22px] border border-white/60 px-7 py-6 backdrop-blur-2xl ring-1 {{ $style['panel'] }} {{ $style['ring'] }} {{ $style['shadow'] }} dark:border-white/10">
            <span class="pointer-events-none absolute inset-x-8 top-[10px] h-[2px] rounded-full bg-gradient-to-r {{ $style['accent'] }}"></span>
            <span class="pointer-events-none absolute -top-24 right-0 h-44 w-44 rounded-full bg-gradient-to-br {{ $style['accent'] }} opacity-25 blur-3xl"></span>

            <button
                type="button"
                class="absolute right-5 top-5 inline-flex h-8 w-8 items-center justify-center rounded-full text-xs transition {{ $style['close'] }}"
                @click="open = false"
                aria-label="{{ __('Close notification') }}"
            >
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l8 8M6 14L14 6" />
                </svg>
            </button>

            <div class="flex items-start gap-4">
                <span class="inline-flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-3xl text-base font-semibold shadow-[inset_0_6px_12px_rgba(255,255,255,0.22)] {{ $style['icon'] }}">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$type] ?? $icons['info'] }}" />
                    </svg>
                </span>

                <div class="flex-1">
                    <p class="text-sm font-semibold tracking-wide {{ $style['title_class'] }}">
                        {{ $headline }}
                    </p>
                    <p class="mt-1 text-sm leading-relaxed {{ $style['message'] }}">
                        {{ $displayMessage }}
                    </p>

                    @if($actionLabel && $actionUrl)
                        <div class="mt-5">
                            <a
                                href="{{ $actionUrl }}"
                                class="inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-semibold text-white transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 {{ $style['button'] }} dark:focus-visible:ring-offset-slate-900"
                            >
                                {{ $actionLabel }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
