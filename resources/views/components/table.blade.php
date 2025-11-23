@props([
  'title' => null,
  'subtitle' => null,
  'exportItems' => [],
  'addUrl' => null,
  'addLabel' => 'Add',
  'action' => null,
  'search' => true,
  'searchName' => 'q',
  'searchPlaceholder' => 'Search…',
  'searchValue' => null,
  'pagination' => null,
])

@php
  $action = $action ?: request()->url();
  $searchValue = $searchValue ?? request($searchName);
@endphp

<div {{ $attributes->merge(['class' => 'soft-card overflow-hidden']) }}>
  <div class="border-b border-white/50 dark:border-slate-800/70 p-5 md:p-6 space-y-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div class="space-y-1">
        @if($title)
          <h3 class="text-xl font-semibold text-slate-700 dark:text-white">{{ $title }}</h3>
        @endif
        @if($subtitle)
          <p class="text-sm text-slate-500 dark:text-slate-300">{{ $subtitle }}</p>
        @endif
      </div>
      <div class="flex flex-wrap items-center gap-2">
        @if(!empty($exportItems))
          <x-export-menu :items="$exportItems" />
        @endif
        @if($addUrl)
          <a href="{{ $addUrl }}" class="btn-primary text-sm">{{ $addLabel }}</a>
        @endif
        {{ $toolbarActions ?? '' }}
      </div>
    </div>

    @if($search || isset($filters))
      <form method="GET" action="{{ $action }}" class="flex flex-col gap-3 lg:flex-row lg:items-end">
        @if($search)
          <label class="relative inline-block lg:flex-1">
            <span class="sr-only">{{ $searchPlaceholder }}</span>
            <input
              type="text"
              name="{{ $searchName }}"
              value="{{ $searchValue }}"
              placeholder="{{ $searchPlaceholder }}"
              class="w-full rounded-2xl border border-white/60 bg-white/80 px-4 py-3 text-sm text-slate-600 shadow-inner focus:border-sky-300 focus:ring-4 focus:ring-sky-200/70 outline-none backdrop-blur-xl dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-100"
            />
          </label>
        @endif

        {{ $filters ?? '' }}

        <div class="flex gap-2">
          <button type="submit" class="btn-outline text-sm">Search</button>
          <a href="{{ $action }}" class="btn-ghost text-sm">Reset</a>
        </div>
      </form>
    @endif
  </div>

  <div class="overflow-x-auto soft-scrollbar">
    <table class="table-modern">
      @isset($head)
        <thead>{{ $head }}</thead>
      @endisset
      @isset($body)
        <tbody>{{ $body }}</tbody>
      @endisset
    </table>
  </div>

  @if($pagination)
    <div class="flex flex-col gap-3 border-t border-white/50 p-4 text-sm text-slate-500 dark:border-slate-800/70 dark:text-slate-300 md:flex-row md:items-center md:justify-between">
      <div>
        {{ __('pagination.showing', [
          'from' => $pagination->firstItem() ?? 0,
          'to' => $pagination->lastItem() ?? 0,
          'total' => $pagination->total() ?? $pagination->count(),
        ]) }}
      </div>
      <div class="self-start md:self-auto">
        {{ $pagination->onEachSide(1)->links() }}
      </div>
    </div>
  @endif
</div>
