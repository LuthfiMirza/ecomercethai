@props(['paginator'])
@if ($paginator->hasPages())
  <nav class="flex items-center justify-center gap-1" role="navigation" aria-label="Pagination Navigation">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
      <span class="px-3 py-1.5 rounded-md border text-sm text-neutral-400" aria-disabled="true" aria-label="Go to previous page">Prev</span>
    @else
      <a class="px-3 py-1.5 rounded-md border hover:bg-neutral-100 text-sm" href="{{ $paginator->previousPageUrl() }}" rel="prev">Prev</a>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($paginator->elements() as $element)
      @if (is_string($element))
        <span class="px-3 py-1.5 text-sm text-neutral-500">{{ $element }}</span>
      @endif
      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <span class="px-3 py-1.5 rounded-md bg-accent-500 text-white text-sm" aria-current="page">{{ $page }}</span>
          @else
            <a class="px-3 py-1.5 rounded-md border hover:bg-neutral-100 text-sm" href="{{ $url }}">{{ $page }}</a>
          @endif
        @endforeach
      @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
      <a class="px-3 py-1.5 rounded-md border hover:bg-neutral-100 text-sm" href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a>
    @else
      <span class="px-3 py-1.5 rounded-md border text-sm text-neutral-400" aria-disabled="true" aria-label="Go to next page">Next</span>
    @endif
  </nav>
@endif
