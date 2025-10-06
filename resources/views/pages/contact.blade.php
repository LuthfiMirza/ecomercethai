@extends('layouts.app')

@section('content')
<main class="container max-w-5xl py-12 space-y-8" role="main">
  <section class="soft-card p-8 grid gap-8 lg:grid-cols-5">
    <div class="lg:col-span-2 space-y-4">
      <h1 class="text-3xl font-semibold text-neutral-800 dark:text-neutral-100">Hubungi Kami</h1>
      <p class="text-sm text-neutral-500 dark:text-neutral-300">Tim kami siap membantu kebutuhan belanja hardware Anda. Kirimkan pesan atau gunakan salah satu kanal di bawah.</p>
      <ul class="space-y-3 text-sm text-neutral-600 dark:text-neutral-300">
        <li class="flex items-center gap-3"><span class="icon-circle"><i class="fa-solid fa-phone"></i></span> +62 812-3456-7890</li>
        <li class="flex items-center gap-3"><span class="icon-circle"><i class="fa-solid fa-envelope"></i></span> support@tokothailand.com</li>
        <li class="flex items-center gap-3"><span class="icon-circle"><i class="fa-solid fa-location-dot"></i></span> Jl. Teknologi No. 88, Jakarta</li>
      </ul>
    </div>
    <form class="lg:col-span-3 grid gap-4" method="post" action="#" x-data="{success:false}" x-on:submit.prevent="success=true">
      <div class="grid gap-4 md:grid-cols-2">
        <label class="block" for="name">
          <span class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Nama</span>
          <input id="name" type="text" required class="mt-1 w-full rounded-2xl border border-white/60 bg-white/80 px-4 py-3 text-sm text-neutral-700 shadow-inner focus:border-sky-300 focus:ring-4 focus:ring-sky-200/70 outline-none backdrop-blur-xl dark:border-neutral-700 dark:bg-neutral-900/60 dark:text-neutral-100"/>
        </label>
        <label class="block" for="email">
          <span class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Email</span>
          <input id="email" type="email" required class="mt-1 w-full rounded-2xl border border-white/60 bg-white/80 px-4 py-3 text-sm text-neutral-700 shadow-inner focus:border-sky-300 focus:ring-4 focus:ring-sky-200/70 outline-none backdrop-blur-xl dark:border-neutral-700 dark:bg-neutral-900/60 dark:text-neutral-100"/>
        </label>
      </div>
      <label class="block" for="subject">
        <span class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Subjek</span>
        <input id="subject" type="text" class="mt-1 w-full rounded-2xl border border-white/60 bg-white/80 px-4 py-3 text-sm text-neutral-700 shadow-inner focus:border-sky-300 focus:ring-4 focus:ring-sky-200/70 outline-none backdrop-blur-xl dark:border-neutral-700 dark:bg-neutral-900/60 dark:text-neutral-100" placeholder="Contoh: Pertanyaan stok"/>
      </label>
      <label class="block" for="message">
        <span class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Pesan</span>
        <textarea id="message" rows="5" required class="mt-1 w-full rounded-2xl border border-white/60 bg-white/80 px-4 py-3 text-sm text-neutral-700 shadow-inner focus:border-sky-300 focus:ring-4 focus:ring-sky-200/70 outline-none backdrop-blur-xl dark:border-neutral-700 dark:bg-neutral-900/60 dark:text-neutral-100"></textarea>
      </label>
      <div class="flex items-center justify-between">
        <p class="text-xs text-neutral-400">Kami akan membalas dalam 1×24 jam kerja.</p>
        <x-button type="submit">Kirim Pesan</x-button>
      </div>
      <p x-show="success" x-transition class="text-sm font-medium text-emerald-600">Terima kasih! Pesan Anda telah kami terima.</p>
    </form>
  </section>

  <section class="soft-card p-6 md:p-8 grid gap-6 md:grid-cols-2 items-center">
    <div class="space-y-3">
      <h2 class="text-xl font-semibold text-neutral-800 dark:text-neutral-100">Butuh bantuan cepat?</h2>
      <p class="text-sm text-neutral-500 dark:text-neutral-300">Gunakan live chat kami di pojok kanan bawah saat jam kerja, atau kunjungi pusat bantuan untuk panduan perawatan hardware.</p>
      <div class="flex gap-3">
        <x-button href="{{ route('faqs') }}" variant="outline">Pusat Bantuan</x-button>
        <x-button href="mailto:support@tokothailand.com">Email Support</x-button>
      </div>
    </div>
    <img src="https://source.unsplash.com/640x480/?customer,service" alt="Customer support" class="w-full rounded-3xl object-cover"/>
  </section>
</main>
@endsection
