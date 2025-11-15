@extends('layouts.app')

@section('content')
<main id="faqs" class="container max-w-5xl py-12 space-y-10" role="main">
  <header class="text-center space-y-4">
    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-accent-500">{{ __('common.faqs') }}</p>
    <h1 class="text-3xl font-bold text-neutral-800 dark:text-neutral-100">{{ __('faq.intro_title') }}</h1>
    <p class="mx-auto max-w-3xl text-sm text-neutral-600 dark:text-neutral-300">{{ __('faq.intro_text') }}</p>
  </header>

  <section class="grid gap-6 md:grid-cols-[220px_minmax(0,1fr)] lg:gap-10">
    <aside class="space-y-3">
      <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">{{ __('common.categories') }}</h2>
      <nav class="flex flex-wrap gap-2" aria-label="FAQ categories">
        @foreach($faqs as $index => $group)
          <a href="#faq-{{ $index }}" class="inline-flex items-center gap-2 rounded-full border border-neutral-200 bg-white px-4 py-2 text-sm font-medium text-neutral-700 transition hover:border-accent-400 hover:text-accent-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200">
            <i class="fa-solid fa-circle-question text-accent-500"></i>
            <span>{{ $group['category'] }}</span>
          </a>
        @endforeach
      </nav>
      <div class="rounded-2xl border border-neutral-200 bg-white p-4 text-sm shadow-sm dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-200">
        <p class="font-medium text-neutral-800 dark:text-neutral-100">{{ __('faq.support_title') }}</p>
        <p class="mt-1 text-neutral-500 dark:text-neutral-300">{{ __('faq.support_text') }}</p>
        <div class="mt-3 flex flex-wrap gap-2">
          <a href="mailto:support@tokothailand.com" class="inline-flex items-center gap-2 rounded-full bg-accent-500 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white transition hover:bg-accent-600">{{ __('common.email_support') }}</a>
          <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 rounded-full border border-accent-400 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-accent-500 transition hover:bg-accent-50 dark:hover:bg-neutral-800">{{ __('common.contact_us') }}</a>
        </div>
      </div>
    </aside>

    <div class="space-y-8">
      @foreach($faqs as $index => $group)
        <section id="faq-{{ $index }}" class="scroll-mt-32 space-y-4">
          <div class="flex items-center gap-3">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-accent-500/10 text-accent-500">
              <i class="fa-solid fa-layer-group"></i>
            </span>
            <h2 class="text-xl font-semibold text-neutral-800 dark:text-neutral-100">{{ $group['category'] }}</h2>
          </div>

          <ul class="space-y-3" role="list">
            @foreach($group['items'] as $itemIndex => $item)
              <li>
                <details class="group rounded-2xl border border-neutral-200 bg-white p-4 shadow-sm transition dark:border-neutral-800 dark:bg-neutral-900">
                  <summary class="flex cursor-pointer items-center justify-between gap-3 text-left text-sm font-semibold text-neutral-800 marker:hidden dark:text-neutral-100">
                    <span>{{ $item['question'] }}</span>
                    <i class="fa-solid fa-chevron-down text-xs text-neutral-400 transition group-open:rotate-180"></i>
                  </summary>
                  <p class="mt-3 text-sm leading-relaxed text-neutral-600 dark:text-neutral-300">{{ $item['answer'] }}</p>
                </details>
              </li>
            @endforeach
          </ul>
        </section>
      @endforeach
    </div>
  </section>
</main>
@endsection
