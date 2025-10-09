@extends('layouts.app')

@section('content')
<main class="container max-w-5xl py-12 space-y-8" role="main">
  <section class="soft-card p-8 grid gap-8 lg:grid-cols-5">
    <div class="lg:col-span-2 space-y-4">
      <h1 class="text-3xl font-semibold text-neutral-800 dark:text-neutral-100">{{ __('common.contact_us') }}</h1>
      <p class="text-sm text-neutral-500 dark:text-neutral-300">{{ __('Our team is ready to help with your hardware needs. Send us a message or choose one of the channels below.') }}</p>
      <ul class="space-y-3 text-sm text-neutral-600 dark:text-neutral-300">
        <li class="flex items-center gap-3"><span class="icon-circle"><i class="fa-solid fa-phone"></i></span> +62 812-3456-7890</li>
        <li class="flex items-center gap-3"><span class="icon-circle"><i class="fa-solid fa-envelope"></i></span> support@tokothailand.com</li>
        <li class="flex items-center gap-3"><span class="icon-circle"><i class="fa-solid fa-location-dot"></i></span> Jl. Teknologi No. 88, Jakarta</li>
      </ul>
    </div>
    <form class="lg:col-span-3 grid gap-4" method="post" action="#" x-data="{success:false}" x-on:submit.prevent="success=true">
      <div class="grid gap-4 md:grid-cols-2">
        <label class="block" for="name">
          <span class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">{{ __('Name') }}</span>
          <input id="name" type="text" required class="mt-1 w-full rounded-2xl border border-white/60 bg-white/80 px-4 py-3 text-sm text-neutral-700 shadow-inner focus:border-sky-300 focus:ring-4 focus:ring-sky-200/70 outline-none backdrop-blur-xl dark:border-neutral-700 dark:bg-neutral-900/60 dark:text-neutral-100"/>
        </label>
        <label class="block" for="email">
          <span class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">{{ __('Email') }}</span>
          <input id="email" type="email" required class="mt-1 w-full rounded-2xl border border-white/60 bg-white/80 px-4 py-3 text-sm text-neutral-700 shadow-inner focus:border-sky-300 focus:ring-4 focus:ring-sky-200/70 outline-none backdrop-blur-xl dark:border-neutral-700 dark:bg-neutral-900/60 dark:text-neutral-100"/>
        </label>
      </div>
      <label class="block" for="subject">
        <span class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">{{ __('Subject') }}</span>
        <input id="subject" type="text" class="mt-1 w-full rounded-2xl border border-white/60 bg-white/80 px-4 py-3 text-sm text-neutral-700 shadow-inner focus:border-sky-300 focus:ring-4 focus:ring-sky-200/70 outline-none backdrop-blur-xl dark:border-neutral-700 dark:bg-neutral-900/60 dark:text-neutral-100" placeholder="{{ __('Example: Stock inquiry') }}"/>
      </label>
      <label class="block" for="message">
        <span class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">{{ __('Message') }}</span>
        <textarea id="message" rows="5" required class="mt-1 w-full rounded-2xl border border-white/60 bg-white/80 px-4 py-3 text-sm text-neutral-700 shadow-inner focus:border-sky-300 focus:ring-4 focus:ring-sky-200/70 outline-none backdrop-blur-xl dark:border-neutral-700 dark:bg-neutral-900/60 dark:text-neutral-100"></textarea>
      </label>
      <div class="flex items-center justify-between">
        <p class="text-xs text-neutral-400">{{ __('We respond within one business day.') }}</p>
        <x-button type="submit">{{ __('Send Message') }}</x-button>
      </div>
      <p x-show="success" x-transition class="text-sm font-medium text-emerald-600">{{ __('Thank you! We have received your message.') }}</p>
    </form>
  </section>

  <section class="soft-card p-6 md:p-8 grid gap-6 md:grid-cols-2 items-center">
    <div class="space-y-3">
      <h2 class="text-xl font-semibold text-neutral-800 dark:text-neutral-100">{{ __('Need quick assistance?') }}</h2>
      <p class="text-sm text-neutral-500 dark:text-neutral-300">{{ __('Use our live chat in the bottom right during business hours, or visit the help center for hardware guidance.') }}</p>
      <div class="flex gap-3">
        <x-button href="{{ route('faqs') }}" variant="outline">{{ __('Help Center') }}</x-button>
        <x-button href="mailto:support@tokothailand.com">{{ __('common.email_support') }}</x-button>
      </div>
    </div>
    <img src="https://source.unsplash.com/640x480/?customer,service" alt="Customer support" class="w-full rounded-3xl object-cover"/>
  </section>
</main>
@endsection
