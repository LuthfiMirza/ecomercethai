@props([
    'title' => null,
    'padding' => 'p-6',
])

<div {{ $attributes->class([
    'rounded-2xl border border-white/70 bg-white/95 shadow-sm backdrop-blur dark:border-neutral-800 dark:bg-neutral-900/80',
    $padding,
]) }}>
    @if($title)
        <div class="mb-4 border-b border-white/60 pb-3 dark:border-neutral-800">
            <h3 class="text-base font-semibold text-neutral-900 dark:text-neutral-100">{{ $title }}</h3>
        </div>
    @endif

    {{ $slot }}
</div>
