@extends('layouts.app')

@section('content')
<main class="container max-w-4xl py-12" x-data="trackOrderForm()" role="main">
  <div class="soft-card p-8 space-y-6">
    <header class="text-center space-y-2">
      <h1 class="text-3xl font-semibold text-neutral-800 dark:text-neutral-100">Track Your Order</h1>
      <p class="text-sm text-neutral-500 dark:text-neutral-300">Masukkan nomor order dan email untuk melihat status pengiriman terbaru.</p>
    </header>

    <form x-on:submit.prevent="submit" class="grid gap-4 md:grid-cols-2">
      <div class="md:col-span-1">
        <label class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400" for="order_id">Nomor Order</label>
        <input id="order_id" name="order" type="text" x-model="order" required placeholder="contoh: ORD-20250101"
               class="mt-1 w-full rounded-2xl border border-white/60 bg-white/80 px-4 py-3 text-sm text-neutral-700 shadow-inner focus:border-sky-300 focus:ring-4 focus:ring-sky-200/70 outline-none backdrop-blur-xl dark:border-neutral-700 dark:bg-neutral-900/60 dark:text-neutral-100"/>
      </div>
      <div class="md:col-span-1">
        <label class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400" for="email">Email</label>
        <input id="email" name="email" type="email" x-model="email" required placeholder="nama@email.com"
               class="mt-1 w-full rounded-2xl border border-white/60 bg-white/80 px-4 py-3 text-sm text-neutral-700 shadow-inner focus:border-sky-300 focus:ring-4 focus:ring-sky-200/70 outline-none backdrop-blur-xl dark:border-neutral-700 dark:bg-neutral-900/60 dark:text-neutral-100"/>
      </div>
      <div class="md:col-span-2 flex flex-wrap items-center gap-3 justify-end">
        <x-button type="submit">Lacak Pesanan</x-button>
        <p class="text-xs text-neutral-400">Informasi pelacakan bersifat simulasi untuk demo.</p>
      </div>
    </form>

    <template x-if="status">
      <section class="soft-card bg-white/90 dark:bg-neutral-900/70 p-4 md:p-6">
        <header class="flex items-center justify-between">
          <div>
            <p class="text-sm text-neutral-400">Pesanan</p>
            <p class="text-lg font-semibold text-neutral-800 dark:text-neutral-100" x-text="order"></p>
          </div>
          <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold" :class="badgeClass">
            <span class="h-2 w-2 rounded-full" :class="dotClass"></span>
            <span x-text="status"></span>
          </span>
        </header>
        <dl class="mt-4 grid gap-3 text-sm text-neutral-600 dark:text-neutral-300 md:grid-cols-2">
          <div>
            <dt class="font-medium">Kurir</dt>
            <dd x-text="courier"></dd>
          </div>
          <div>
            <dt class="font-medium">Estimasi Tiba</dt>
            <dd x-text="eta"></dd>
          </div>
        </dl>
        <ol class="mt-6 space-y-3">
          <template x-for="(step, idx) in timeline" :key="idx">
            <li class="flex items-start gap-3">
              <div class="mt-1 h-2 w-2 rounded-full" :class="idx === 0 ? 'bg-emerald-500' : 'bg-neutral-300 dark:bg-neutral-600'"></div>
              <div>
                <p class="font-medium text-neutral-700 dark:text-neutral-100" x-text="step.title"></p>
                <p class="text-xs text-neutral-500" x-text="step.time"></p>
              </div>
            </li>
          </template>
        </ol>
      </section>
    </template>
  </div>
</main>
@endsection

@push('scripts')
<script>
  function trackOrderForm(){
    return {
      order: '',
      email: '',
      status: '',
      courier: '',
      eta: '',
      timeline: [],
      get badgeClass(){
        return {
          'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300': this.status === 'Dalam Pengiriman',
          'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300': this.status === 'Diproses',
          'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300': this.status === 'Menunggu Konfirmasi',
        };
      },
      get dotClass(){
        return {
          'bg-emerald-500': this.status === 'Dalam Pengiriman',
          'bg-sky-500': this.status === 'Diproses',
          'bg-amber-500': this.status === 'Menunggu Konfirmasi',
        };
      },
      submit(){
        // Dummy response to simulate tracking result
        this.status = 'Dalam Pengiriman';
        this.courier = 'JNE Express';
        this.eta = 'Estimasi tiba: 2 hari lagi';
        const now = new Date();
        const format = (offset) => new Date(now.getTime() - offset).toLocaleString('id-ID');
        this.timeline = [
          { title: 'Paket sedang dalam perjalanan ke alamat tujuan.', time: format(2 * 60 * 60 * 1000) },
          { title: 'Paket diambil oleh kurir dari gudang.', time: format(18 * 60 * 60 * 1000) },
          { title: 'Pembayaran terkonfirmasi.', time: format(26 * 60 * 60 * 1000) },
        ];
      }
    }
  }
</script>
@endpush
