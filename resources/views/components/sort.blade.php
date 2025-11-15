@props(['options' => null, 'value' => 'newest'])

@php
    $defaultOptions = trans('catalog.sort_options');
    $resolvedOptions = is_array($options) && !empty($options) ? $options : (is_array($defaultOptions) ? $defaultOptions : []);
@endphp

<label class="inline-flex items-center gap-2 text-sm">
  <span class="text-neutral-600 dark:text-neutral-300">{{ __('catalog.sort_label') }}</span>
  <select name="sort" class="rounded-md border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-sm focus:outline-none focus:ring-2 focus:ring-accent-500">
    @foreach($resolvedOptions as $key => $label)
      <option value="{{ $key }}" @selected($value === $key)>{{ $label }}</option>
    @endforeach
  </select>
  <noscript>
    <x-button type="submit" variant="outline">{{ __('catalog.filter.apply') }}</x-button>
  </noscript>
</label>
