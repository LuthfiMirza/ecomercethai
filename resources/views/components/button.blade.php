@props([
  'variant' => 'primary', // primary | secondary | outline | ghost
  'size' => 'md', // sm | md | lg
  'href' => null,
  'as' => null,
  'type' => 'button',
])
@php
  $base = 'inline-flex items-center justify-center font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-accent-500 disabled:opacity-50 disabled:cursor-not-allowed';
  $sizes = [
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-5 py-3 text-base',
  ][$size] ?? 'px-4 py-2 text-sm';
  $variants = [
    'primary' => 'bg-accent-500 hover:bg-accent-600 text-white',
    'secondary' => 'bg-neutral-800 hover:bg-neutral-900 text-white dark:bg-neutral-700 dark:hover:bg-neutral-600',
    'outline' => 'border border-neutral-300 hover:border-neutral-400 text-neutral-800 dark:text-neutral-100 dark:border-neutral-600',
    'ghost' => 'hover:bg-neutral-100 dark:hover:bg-neutral-800 text-neutral-800 dark:text-neutral-100',
  ][$variant] ?? 'bg-accent-500 hover:bg-accent-600 text-white';
  $classes = "$base $sizes $variants ".$attributes->get('class');
  $Tag = $as ?? ($href ? 'a' : 'button');
@endphp
<{{ $Tag }} @if($href) href="{{ $href }}" @endif @if($Tag==='button') type="{{ $type }}" @endif {{ $attributes->merge(['class'=>$classes]) }}>
  {{ $slot }}
</{{ $Tag }}>
