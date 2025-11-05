@props([
  'variant' => 'primary', // primary | secondary | outline | ghost
  'size' => 'md', // sm | md | lg
  'href' => null,
  'as' => null,
  'type' => 'button',
])
@php
  $base = 'inline-flex items-center justify-center font-medium rounded-2xl transition-all focus:outline-none focus-visible:ring-4 focus-visible:ring-orange-200/70 disabled:opacity-50 disabled:cursor-not-allowed';
  $sizes = [
    'sm' => 'px-3.5 py-2 text-sm',
    'md' => 'px-5 py-2.5 text-sm',
    'lg' => 'px-6 py-3 text-base',
  ][$size] ?? 'px-5 py-2.5 text-sm';
  $variants = [
    'primary' => 'bg-gradient-to-r from-orange-400 via-orange-500 to-amber-600 text-white shadow-[0_18px_40px_-25px_rgba(255,112,67,0.65)] hover:-translate-y-0.5',
    'secondary' => 'bg-neutral-900 text-white shadow-md hover:bg-neutral-800 dark:bg-neutral-700 dark:hover:bg-neutral-600',
    'outline' => 'border border-white/60 bg-white/80 text-neutral-700 shadow-inner hover:border-orange-300 backdrop-blur-xl dark:border-neutral-700 dark:bg-neutral-900/60 dark:text-neutral-100',
    'ghost' => 'bg-transparent text-neutral-700 hover:bg-white/70 dark:text-neutral-200 dark:hover:bg-neutral-800/60',
  ][$variant] ?? 'bg-gradient-to-r from-orange-400 via-orange-500 to-amber-600 text-white shadow-[0_18px_40px_-25px_rgba(255,112,67,0.65)] hover:-translate-y-0.5';
  $classes = "$base $sizes $variants ".$attributes->get('class');
  $Tag = $as ?? ($href ? 'a' : 'button');
@endphp
<{{ $Tag }} @if($href) href="{{ $href }}" @endif @if($Tag==='button') type="{{ $type }}" @endif {{ $attributes->merge(['class'=>$classes]) }}>
  {{ $slot }}
</{{ $Tag }}>
