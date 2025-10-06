@props([
  'title' => '',
  'breadcrumbs' => [],
])

<div class="soft-card p-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
  <div>
    @if(!empty($breadcrumbs))
      <nav class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
        @foreach($breadcrumbs as $i => $bc)
          @if($i > 0)
            <span class="text-gray-400">›</span>
          @endif
          @if(!empty($bc['href'] ?? null))
            <a href="{{ $bc['href'] }}" class="inline-flex items-center rounded-xl bg-white/70 px-3 py-1.5 text-slate-500 shadow-inner hover:bg-white/90">
              {{ $bc['label'] ?? '' }}
            </a>
          @else
            <span class="inline-flex items-center rounded-xl bg-white/70 px-3 py-1.5 text-slate-500 shadow-inner">{{ $bc['label'] ?? '' }}</span>
          @endif
        @endforeach
      </nav>
    @endif
    @if($title)
      <h1 class="text-3xl font-semibold tracking-tight text-slate-700 dark:text-white">{{ $title }}</h1>
    @endif
  </div>
  <div class="flex items-center gap-2">
    {{ $slot }}
  </div>
</div>

