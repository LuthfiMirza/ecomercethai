@props(['options' => ['popularity'=>'Paling Populer','newest'=>'Terbaru','price_asc'=>'Harga: Rendah ke Tinggi','price_desc'=>'Harga: Tinggi ke Rendah'], 'value' => 'popularity'])
<label class="inline-flex items-center gap-2 text-sm">
  <span class="text-neutral-600 dark:text-neutral-300">Urutkan:</span>
  <select name="sort" class="rounded-md border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-sm focus:outline-none focus:ring-2 focus:ring-accent-500">
    @foreach($options as $key=>$label)
      <option value="{{ $key }}" @selected($value===$key)>{{ $label }}</option>
    @endforeach
  </select>
  <noscript>
    <x-button type="submit" variant="outline">Apply</x-button>
  </noscript>
</label>
