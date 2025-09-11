@props(['type' => 'info', 'title' => null])
@php
  $base = 'rounded-md p-4 border';
  $styles = [
    'info' => 'bg-blue-50 border-blue-200 text-blue-800',
    'success' => 'bg-green-50 border-green-200 text-green-800',
    'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
    'error' => 'bg-red-50 border-red-200 text-red-800',
  ][$type] ?? 'bg-blue-50 border-blue-200 text-blue-800';
@endphp
<div {{ $attributes->merge(['class'=> "$base $styles"]) }} role="alert">
  @if($title)
    <div class="font-semibold">{{ $title }}</div>
  @endif
  <div class="text-sm">{{ $slot }}</div>
</div>
