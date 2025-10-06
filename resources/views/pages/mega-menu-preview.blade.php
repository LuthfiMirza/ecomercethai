@extends('layouts.app')

@section('content')
<main class="min-h-screen bg-neutral-950 text-neutral-100">
  <section class="container mx-auto px-6 py-12 space-y-6">
    <header class="space-y-2 text-center">
      <h1 class="text-3xl font-semibold">Mega Menu Preview</h1>
      <p class="text-neutral-400">Halaman demonstrasi untuk komponen mega menu dinamis.</p>
    </header>

    <div class="rounded-3xl border border-white/5 bg-white/5 p-10 text-center">
      <p class="text-neutral-300">
        Hover atau klik tombol <strong>Categories</strong> di navbar untuk menampilkan mega menu.
        Konten di bawah ini hanyalah placeholder untuk menampilkan layout halaman.
      </p>
    </div>
  </section>
</main>
@endsection
