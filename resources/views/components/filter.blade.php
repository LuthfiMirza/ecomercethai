@props([
    'categories' => [],
    'brands' => [],
    'minPrice' => null,
    'maxPrice' => null,
    'inStock' => false,
])

@php
    $minValue = is_numeric($minPrice) ? (float) $minPrice : null;
    $maxValue = is_numeric($maxPrice) ? (float) $maxPrice : null;
@endphp

<aside class="space-y-6">
  <div>
    <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-200">{{ __('catalog.filter.categories') }}</h3>
    <div class="mt-2 space-y-1 max-h-48 overflow-auto pr-1">
      @forelse($categories as $category)
        <label class="flex items-center gap-2 text-sm">
          <input
            type="checkbox"
            name="category[]"
            value="{{ $category['value'] }}"
            @checked($category['checked'] ?? false)
            class="rounded border-neutral-300 text-accent-500 focus:ring-accent-500 dark:border-neutral-700"
          >
          <span>{{ $category['label'] }}</span>
        </label>
      @empty
        <p class="text-xs text-neutral-500">{{ __('catalog.filter.categories_empty') }}</p>
      @endforelse
    </div>
  </div>

  <div>
    <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-200">{{ __('catalog.filter.brands') }}</h3>
    <div class="mt-2 space-y-1 max-h-48 overflow-auto pr-1">
      @forelse($brands as $brand)
        <label class="flex items-center gap-2 text-sm">
          <input
            type="checkbox"
            name="brand[]"
            value="{{ $brand['value'] }}"
            @checked($brand['checked'] ?? false)
            class="rounded border-neutral-300 text-accent-500 focus:ring-accent-500 dark:border-neutral-700"
          >
          <span>{{ $brand['label'] }}</span>
        </label>
      @empty
        <p class="text-xs text-neutral-500">{{ __('catalog.filter.brands_empty') }}</p>
      @endforelse
    </div>
  </div>

  <div>
    <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-200">{{ __('catalog.filter.price') }}</h3>
    <div class="mt-2 flex items-center gap-2">
      <input
        type="number"
        name="min_price"
        value="{{ $minValue ?? '' }}"
        min="0"
        step="0.01"
        placeholder="{{ __('catalog.filter.price_min') }}"
        class="w-24 rounded-md border-neutral-300 text-sm focus:border-accent-500 focus:ring-accent-500 dark:border-neutral-700 dark:bg-neutral-800"
      >
      <span class="text-neutral-400">—</span>
      <input
        type="number"
        name="max_price"
        value="{{ $maxValue ?? '' }}"
        min="0"
        step="0.01"
        placeholder="{{ __('catalog.filter.price_max') }}"
        class="w-24 rounded-md border-neutral-300 text-sm focus:border-accent-500 focus:ring-accent-500 dark:border-neutral-700 dark:bg-neutral-800"
      >
    </div>
  </div>

  <div>
    <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-200">{{ __('catalog.filter.stock') }}</h3>
    <label class="mt-2 flex items-center gap-2 text-sm">
      <input
        type="checkbox"
        name="in_stock"
        value="1"
        @checked($inStock)
        class="rounded border-neutral-300 text-accent-500 focus:ring-accent-500 dark:border-neutral-700"
      >
      <span>{{ __('catalog.filter.stock_only') }}</span>
    </label>
  </div>

  <div class="pt-2">
    <x-button type="submit" class="w-full">{{ __('catalog.filter.apply') }}</x-button>
  </div>
</aside>
