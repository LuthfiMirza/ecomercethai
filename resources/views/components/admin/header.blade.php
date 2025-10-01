@props([
  'title' => '',
  'breadcrumbs' => [],
])

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
  <div>
    @if(!empty($breadcrumbs))
      <nav class="mb-2 flex items-center gap-2 text-sm text-gray-500">
        @foreach($breadcrumbs as $i => $bc)
          @if($i > 0)
            <span class="text-gray-400">›</span>
          @endif
          @if(!empty($bc['href'] ?? null))
            <a href="{{ $bc['href'] }}" class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1 text-gray-700 hover:bg-gray-200">
              {{ $bc['label'] ?? '' }}
            </a>
          @else
            <span class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1 text-gray-700">{{ $bc['label'] ?? '' }}</span>
          @endif
        @endforeach
      </nav>
    @endif
    @if($title)
      <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">{{ $title }}</h1>
    @endif
  </div>
  <div class="flex items-center gap-2">
    {{ $slot }}
  </div>
</div>

