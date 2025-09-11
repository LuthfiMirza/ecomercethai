@props(['categories' => [], 'brands' => [], 'min' => 0, 'max' => 10000])
<aside x-data="{ open:true, price:[{{$min}},{{$max}}] }" class="space-y-6">
  <div>
    <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-200">Kategori</h3>
    <div class="mt-2 space-y-1 max-h-48 overflow-auto">
      @foreach($categories as $c)
      <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="category[]" value="{{ $c }}" class="rounded border-neutral-300 text-accent-500 focus:ring-accent-500">
        <span>{{ $c }}</span>
      </label>
      @endforeach
    </div>
  </div>
  <div>
    <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-200">Brand</h3>
    <div class="mt-2 space-y-1 max-h-48 overflow-auto">
      @foreach($brands as $b)
      <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="brand[]" value="{{ $b }}" class="rounded border-neutral-300 text-accent-500 focus:ring-accent-500">
        <span>{{ $b }}</span>
      </label>
      @endforeach
    </div>
  </div>
  <div>
    <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-200">Harga</h3>
    <div class="mt-2 flex items-center gap-2">
      <input type="number" name="min" value="{{ $min }}" class="w-24 rounded-md border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-sm"/>
      <span class="text-neutral-400">—</span>
      <input type="number" name="max" value="{{ $max }}" class="w-24 rounded-md border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-sm"/>
    </div>
  </div>
  <div>
    <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-200">Rating</h3>
    <div class="mt-2 space-y-1">
      @foreach([5,4,3,2,1] as $r)
        <label class="flex items-center gap-2 text-sm">
          <input type="radio" name="rating" value="{{ $r }}" class="text-amber-500 focus:ring-amber-500">
          <span>
            @for($i=1;$i<=5;$i++)
              <i class="fa-solid fa-star {{ $i <= $r ? 'text-amber-500' : 'text-neutral-300' }}"></i>
            @endfor
          </span>
        </label>
      @endforeach
    </div>
  </div>
  <div>
    <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-200">Stok</h3>
    <label class="mt-2 flex items-center gap-2 text-sm">
      <input type="checkbox" name="in_stock" class="rounded border-neutral-300 text-accent-500 focus:ring-accent-500"> Tersedia
    </label>
  </div>
  <div class="pt-2">
    <x-button type="submit" class="w-full">Terapkan</x-button>
  </div>
</aside>
