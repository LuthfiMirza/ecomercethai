@props(['items' => []])
<div x-data="{ open:false }" class="relative inline-block">
  <button @click="open=!open" type="button" class="btn-outline text-sm">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M7.5 12 12 16.5m0 0L16.5 12M12 16.5V3" /></svg>
    Export
  </button>
  <div
    x-show="open"
    x-cloak
    x-transition
    @click.outside="open=false"
    class="absolute right-0 mt-2 w-40 soft-card py-2 text-sm text-slate-600 dark:text-slate-200"
  >
    @foreach($items as $item)
      <a href="{{ $item['href'] }}" class="block px-4 py-2 hover:bg-white/80 dark:hover:bg-slate-800/70 rounded-xl">{{ $item['label'] }}</a>
    @endforeach
  </div>
</div>
