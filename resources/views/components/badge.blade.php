@props(['variant' => 'accent'])
@php
  $base = 'inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium';
  $variants = [
    'accent' => 'bg-accent-500/10 text-accent-600',
    'neutral' => 'bg-neutral-900/10 text-neutral-800 dark:bg-neutral-100/10 dark:text-neutral-200',
    'success' => 'bg-green-500/10 text-green-700',
    'warning' => 'bg-amber-500/10 text-amber-700',
    'danger'  => 'bg-red-500/10 text-red-700',
  ][$variant] ?? 'bg-accent-500/10 text-accent-600';
@endphp
<span {{ $attributes->merge(['class'=> "$base $variants"]) }}>
  {{ $slot }}
</span>
