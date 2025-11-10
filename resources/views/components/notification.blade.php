@php
    $type = $type ?? 'info';

    $styles = [
        'success' => [
            'container' => 'border-emerald-200/70 bg-white/95 text-emerald-800 dark:border-emerald-500/40 dark:bg-slate-900/95 dark:text-emerald-100',
            'shadow' => 'shadow-[0_22px_55px_-30px_rgba(16,185,129,0.75)]',
            'icon' => 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-200',
            'title' => 'text-emerald-700 dark:text-emerald-200',
            'bar' => 'from-emerald-400 via-emerald-500 to-emerald-400',
            'button' => 'text-emerald-600 hover:bg-emerald-500/10 dark:text-emerald-200 dark:hover:bg-emerald-500/20',
            'headline' => __('Success'),
            'cta' => 'bg-emerald-500 hover:bg-emerald-600 focus-visible:ring-emerald-400',
        ],
        'error' => [
            'container' => 'border-rose-200/70 bg-white/95 text-rose-800 dark:border-rose-500/45 dark:bg-slate-900/95 dark:text-rose-200',
            'shadow' => 'shadow-[0_24px_55px_-28px_rgba(244,63,94,0.78)]',
            'icon' => 'bg-rose-500/15 text-rose-600 dark:text-rose-200',
            'title' => 'text-rose-600 dark:text-rose-200',
            'bar' => 'from-rose-400 via-rose-500 to-rose-400',
            'button' => 'text-rose-600 hover:bg-rose-500/10 dark:text-rose-200 dark:hover:bg-rose-500/25',
            'headline' => __('Error'),
            'cta' => 'bg-rose-500 hover:bg-rose-600 focus-visible:ring-rose-400',
        ],
        'warning' => [
            'container' => 'border-amber-200/80 bg-white/95 text-amber-800 dark:border-amber-400/35 dark:bg-slate-900/95 dark:text-amber-200',
            'shadow' => 'shadow-[0_22px_55px_-28px_rgba(245,158,11,0.68)]',
            'icon' => 'bg-amber-400/20 text-amber-600 dark:text-amber-200',
            'title' => 'text-amber-600 dark:text-amber-200',
            'bar' => 'from-amber-400 via-orange-400 to-amber-400',
            'button' => 'text-amber-600 hover:bg-amber-500/10 dark:text-amber-200 dark:hover:bg-amber-500/20',
            'headline' => __('Warning'),
            'cta' => 'bg-amber-500 hover:bg-amber-600 focus-visible:ring-amber-400',
        ],
        'info' => [
            'container' => 'border-blue-200/70 bg-white/95 text-blue-800 dark:border-blue-500/40 dark:bg-slate-900/95 dark:text-blue-200',
            'shadow' => 'shadow-[0_24px_55px_-28px_rgba(59,130,246,0.7)]',
            'icon' => 'bg-blue-500/15 text-blue-600 dark:text-blue-200',
            'title' => 'text-blue-600 dark:text-blue-200',
            'bar' => 'from-sky-400 via-blue-500 to-sky-400',
            'button' => 'text-blue-600 hover:bg-blue-500/10 dark:text-blue-200 dark:hover:bg-blue-500/25',
            'headline' => __('Information'),
            'cta' => 'bg-blue-500 hover:bg-blue-600 focus-visible:ring-blue-400',
        ],
    ];

    $icons = [
        'success' => 'M5 13l4 4L19 7',
        'error' => 'M6 18L18 6M6 6l12 12',
        'warning' => 'M10.29 3.86l-7.5 13A1 1 0 0 0 3.62 18h16.76a1 1 0 0 0 .83-1.53l-7.5-13a1 1 0 0 0-1.72 0z',
        'info' => 'M12 9v2m0 4h.01M12 5a7 7 0 100 14 7 7 0 000-14z',
    ];

    $style = $styles[$type] ?? $styles['info'];

    $rawMessages = $message ?? null;
    $messages = collect(is_iterable($rawMessages) ? $rawMessages : [$rawMessages])
        ->flatten()
        ->map(fn ($val) => trim((string) $val))
        ->filter(fn ($val) => $val !== '')
        ->values();

    $headline = $title ?? $style['headline'];
    $actionLabel = $actionLabel ?? null;
    $actionUrl = $actionUrl ?? null;
@endphp

@if($messages->isNotEmpty())
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
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-y-3 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="translate-y-2 opacity-0"
        x-cloak
        class="pointer-events-none fixed top-6 right-6 z-50 w-full max-w-sm sm:top-8 sm:right-8"
        role="status"
        aria-live="polite"
    >
        <div class="pointer-events-auto relative overflow-hidden rounded-2xl border px-5 py-4 backdrop-blur-xl {{ $style['container'] }} {{ $style['shadow'] }}">
            <span class="pointer-events-none absolute inset-x-4 top-0 h-1 rounded-full bg-gradient-to-r opacity-80 {{ $style['bar'] }}"></span>

            <button
                type="button"
                class="absolute right-3 top-3 inline-flex h-8 w-8 items-center justify-center rounded-full text-xs transition {{ $style['button'] }}"
                @click="open = false"
                aria-label="{{ __('Close notification') }}"
            >
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l8 8M6 14L14 6" />
                </svg>
            </button>

            <div class="flex items-start gap-3 pe-6 sm:pe-8">
                <span class="mt-0.5 inline-flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl text-base font-semibold {{ $style['icon'] }}">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$type] ?? $icons['info'] }}" />
                    </svg>
                </span>

                <div class="flex-1">
                    <p class="text-sm font-semibold {{ $style['title'] }}">
                        {{ $headline }}
                    </p>
                    @if($messages->count() > 1)
                        <ul class="mt-2 space-y-1 text-sm text-slate-600 dark:text-slate-300">
                            @foreach($messages as $item)
                                <li class="flex items-start gap-2">
                                    <span class="mt-1 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-current opacity-60"></span>
                                    <span class="leading-relaxed">{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mt-1 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                            {{ $messages->first() }}
                        </p>
                    @endif

                    @if($actionLabel && $actionUrl)
                        <div class="mt-5">
                            <a
                                href="{{ $actionUrl }}"
                                class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold text-white transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 {{ $style['cta'] }} dark:focus-visible:ring-offset-slate-900"
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
