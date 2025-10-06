@extends('layouts.app')

@section('content')
<main class="container py-12" x-data="checkoutFlow()" role="main">
  <section class="soft-card p-6 md:p-8 space-y-6">
    <header class="space-y-3">
      <h1 class="text-3xl font-semibold text-neutral-800 dark:text-neutral-100">Checkout</h1>
      <p class="text-sm text-neutral-500 dark:text-neutral-300">Lengkapi setiap langkah di bawah untuk menyelesaikan pesanan Anda.</p>
    </header>

    <!-- Stepper -->
    <ol class="grid gap-3 sm:grid-cols-4" role="list">
      <template x-for="(item, index) in steps" :key="item.id">
        <li class="flex flex-col gap-2" :aria-current="step === item.id ? 'step' : null">
          <div class="flex items-center gap-2">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold"
                  :class="step >= item.id ? 'bg-gradient-to-r from-sky-500 to-indigo-500 text-white shadow-lg' : 'bg-white/70 text-neutral-400 border border-neutral-200'">@{{ index + 1 }}</span>
            <span class="text-sm font-medium" :class="step >= item.id ? 'text-neutral-800 dark:text-neutral-100' : 'text-neutral-400 dark:text-neutral-500'" x-text="item.label"></span>
          </div>
        </li>
      </template>
    </ol>

    <!-- Step panels -->
    <form x-on:submit.prevent="next" class="grid gap-6 md:grid-cols-[minmax(0,1fr)_360px]">
      <div class="space-y-6">
        <div x-cloak x-show="error" class="soft-card p-4 text-sm text-red-600 dark:text-red-300" x-text="error"></div>
        <!-- Step 1: Address -->
        <section x-show="step === 1" x-transition class="soft-card p-6 space-y-4">
          <header>
            <h2 class="text-lg font-semibold text-neutral-800 dark:text-neutral-100">Alamat Pengiriman</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Pastikan informasi pengiriman sudah benar.</p>
          </header>
          <div class="grid gap-4 md:grid-cols-2">
            <label class="md:col-span-1">
              <span class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Nama Lengkap</span>
              <input id="shipping_name" type="text" x-model="form.name" required class="mt-1 w-full rounded-2xl border border-white/60 bg-white/80 px-4 py-3 text-sm text-neutral-700 shadow-inner focus:border-sky-300 focus:ring-4 focus:ring-sky-200/70 outline-none backdrop-blur-xl dark:border-neutral-700 dark:bg-neutral-900/60 dark:text-neutral-100" />
            </label>
            <label class="md:col-span-1">
              <span class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Nomor Telepon</span>
              <input id="shipping_phone" type="text" x-model="form.phone" required class="mt-1 w-full rounded-2xl border border-white/60 bg-white/80 px-4 py-3 text-sm text-neutral-700 shadow-inner focus:border-sky-300 focus:ring-4 focus:ring-sky-200/70 outline-none backdrop-blur-xl dark:border-neutral-700 dark:bg-neutral-900/60 dark:text-neutral-100" />
            </label>
            <label class="md:col-span-2">
              <span class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Email</span>
              <input id="shipping_email" type="email" x-model="form.email" required class="mt-1 w-full rounded-2xl border border-white/60 bg-white/80 px-4 py-3 text-sm text-neutral-700 shadow-inner focus:border-sky-300 focus:ring-4 focus:ring-sky-200/70 outline-none backdrop-blur-xl dark:border-neutral-700 dark:bg-neutral-900/60 dark:text-neutral-100" />
            </label>
            <label class="md:col-span-2">
              <span class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Alamat Lengkap</span>
              <input id="shipping_address" type="text" x-model="form.address" required class="mt-1 w-full rounded-2xl border border-white/60 bg-white/80 px-4 py-3 text-sm text-neutral-700 shadow-inner focus:border-sky-300 focus:ring-4 focus:ring-sky-200/70 outline-none backdrop-blur-xl dark:border-neutral-700 dark:bg-neutral-900/60 dark:text-neutral-100" />
            </label>
            <label class="md:col-span-1">
              <span class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Kota</span>
              <input id="shipping_city" type="text" x-model="form.city" required class="mt-1 w-full rounded-2xl border border-white/60 bg-white/80 px-4 py-3 text-sm text-neutral-700 shadow-inner focus:border-sky-300 focus:ring-4 focus:ring-sky-200/70 outline-none backdrop-blur-xl dark:border-neutral-700 dark:bg-neutral-900/60 dark:text-neutral-100" />
            </label>
            <label class="md:col-span-1">
              <span class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Kode Pos</span>
              <input id="shipping_zip" type="text" x-model="form.zip" required class="mt-1 w-full rounded-2xl border border-white/60 bg-white/80 px-4 py-3 text-sm text-neutral-700 shadow-inner focus:border-sky-300 focus:ring-4 focus:ring-sky-200/70 outline-none backdrop-blur-xl dark:border-neutral-700 dark:bg-neutral-900/60 dark:text-neutral-100" />
            </label>
          </div>
        </section>

        <!-- Step 2: Shipping -->
        <section x-show="step === 2" x-transition class="soft-card p-6 space-y-4">
          <header>
            <h2 class="text-lg font-semibold text-neutral-800 dark:text-neutral-100">Jasa Pengiriman</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Pilih opsi pengiriman yang tersedia.</p>
          </header>
          <div class="space-y-3">
            <template x-for="option in shippingOptions" :key="option.id">
              <label class="flex items-center justify-between rounded-2xl border border-white/60 bg-white/80 px-4 py-3 shadow-inner transition hover:border-sky-300 dark:border-neutral-700 dark:bg-neutral-900/60" :class="form.shipping === option.id ? 'ring-4 ring-sky-200/70 border-sky-300' : ''">
                <div>
                  <p class="font-medium text-sm text-neutral-700 dark:text-neutral-100" x-text="option.label"></p>
                  <p class="text-xs text-neutral-400" x-text="option.desc"></p>
                </div>
                <div class="text-right">
                  <p class="text-sm font-semibold text-neutral-800 dark:text-neutral-100" x-text="option.priceLabel"></p>
                  <input type="radio" name="shipping" class="sr-only" :value="option.id" x-model="form.shipping">
                </div>
              </label>
            </template>
          </div>
        </section>

        <!-- Step 3: Payment -->
        <section x-show="step === 3" x-transition class="soft-card p-6 space-y-4">
          <header>
            <h2 class="text-lg font-semibold text-neutral-800 dark:text-neutral-100">Metode Pembayaran</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Pilih metode pembayaran yang diinginkan.</p>
          </header>
          <div class="space-y-3">
            <template x-for="method in paymentOptions" :key="method.id">
              <label class="flex items-center justify-between rounded-2xl border border-white/60 bg-white/80 px-4 py-3 shadow-inner transition hover:border-sky-300 dark:border-neutral-700 dark:bg-neutral-900/60" :class="form.payment === method.id ? 'ring-4 ring-sky-200/70 border-sky-300' : ''">
                <div>
                  <p class="text-sm font-semibold text-neutral-700 dark:text-neutral-100" x-text="method.label"></p>
                  <p class="text-xs text-neutral-400" x-text="method.desc"></p>
                </div>
                <input type="radio" class="sr-only" name="payment" :value="method.id" x-model="form.payment">
              </label>
            </template>
          </div>
          <div x-show="form.payment === 'manual'" x-cloak class="mt-4 space-y-2">
            <p class="text-sm font-medium text-neutral-600 dark:text-neutral-200">Upload bukti transfer (opsional)</p>
            <x-payment-proof-upload />
          </div>
        </section>

        <!-- Step 4: Review -->
        <section x-show="step === 4" x-transition class="soft-card p-6 space-y-4">
          <header class="space-y-2">
            <h2 class="text-lg font-semibold text-neutral-800 dark:text-neutral-100">Tinjau Pesanan</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Pastikan detail berikut sesuai sebelum membuat pesanan.</p>
          </header>
          <dl class="grid gap-4 text-sm text-neutral-600 dark:text-neutral-300 md:grid-cols-2">
            <div><dt class="font-medium">Nama</dt><dd x-text="form.name"></dd></div>
            <div><dt class="font-medium">Email</dt><dd x-text="form.email"></dd></div>
            <div><dt class="font-medium">Alamat</dt><dd x-text="form.address"></dd></div>
            <div><dt class="font-medium">Pengiriman</dt><dd x-text="selectedShipping"></dd></div>
            <div><dt class="font-medium">Pembayaran</dt><dd x-text="selectedPayment"></dd></div>
          </dl>
          <x-alert type="info">Setelah menekan tombol "Buat Pesanan" Anda akan menerima email konfirmasi.</x-alert>
        </section>

        <div class="flex flex-wrap gap-3">
          <x-button type="button" variant="outline" x-show="step > 1" x-on:click="prev">Kembali</x-button>
          <x-button type="submit" x-show="step < steps[steps.length - 1].id" x-text="'Lanjut (' + (step + 1) + '/' + steps.length + ')'"/>
          <x-button type="button" x-show="step === steps[steps.length - 1].id" x-on:click="complete" class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white">Buat Pesanan</x-button>
        </div>
      </div>

      <!-- Summary -->
      <aside class="space-y-4">
        <div class="soft-card p-6 space-y-3">
          <h2 class="text-lg font-semibold text-neutral-800 dark:text-neutral-100">Ringkasan Pesanan</h2>
          <ul class="space-y-3 text-sm text-neutral-600 dark:text-neutral-300">
            <template x-for="item in summary" :key="item.name">
              <li class="flex items-center gap-3">
                <img :src="item.image" class="h-12 w-12 rounded-xl object-cover" alt=""/>
                <div class="flex-1">
                  <p class="font-medium text-neutral-700 dark:text-neutral-100" x-text="item.name"></p>
                  <p class="text-xs" x-text="item.desc"></p>
                </div>
                <p class="font-semibold text-neutral-800 dark:text-neutral-100" x-text="item.price"></p>
              </li>
            </template>
          </ul>
          <div class="space-y-2 border-t border-white/60 pt-3 text-sm text-neutral-600 dark:text-neutral-300">
            <div class="flex justify-between"><span>Subtotal</span><span x-text="totals.subtotal"></span></div>
            <div class="flex justify-between"><span>Pengiriman</span><span x-text="totals.shipping"></span></div>
            <div class="flex justify-between font-semibold text-neutral-800 dark:text-neutral-100"><span>Total</span><span x-text="totals.total"></span></div>
          </div>
        </div>
        <x-alert type="info">Transaksi Anda aman dan terenkripsi.</x-alert>
        <x-alert type="success" x-show="orderPlaced" x-transition>Pesanan berhasil dibuat! Kami telah mengirim email konfirmasi.</x-alert>
      </aside>
    </form>
  </section>
</main>
@endsection

@push('scripts')
<script>
  function checkoutFlow(){
    return {
      step: 1,
      steps: [
        { id: 1, label: 'Alamat' },
        { id: 2, label: 'Pengiriman' },
        { id: 3, label: 'Pembayaran' },
        { id: 4, label: 'Review' },
      ],
      form: {
        name: '',
        phone: '',
        email: '',
        address: '',
        city: '',
        zip: '',
        shipping: 'reg',
        payment: 'card',
      },
      error: '',
      orderPlaced: false,
      shippingOptions: [
        { id: 'reg', label: 'Reguler (2-3 hari)', desc: 'Gratis ongkir untuk pesanan di atas Rp500.000', priceLabel: 'Gratis' },
        { id: 'express', label: 'Express (1 hari)', desc: 'Pengiriman prioritas same-day untuk kota besar', priceLabel: 'Rp35.000' },
      ],
      paymentOptions: [
        { id: 'card', label: 'Kartu Kredit / Virtual Account', desc: 'Pembayaran otomatis dengan konfirmasi instan' },
        { id: 'manual', label: 'Transfer Bank Manual', desc: 'Transfer ke rekening BCA / Mandiri dan upload bukti' },
      ],
      summary: [
        { name:'MSI MAG B650 Tomahawk WiFi', desc:'Mainboard AM5', price:'Rp4.299.000', image:'https://source.unsplash.com/200x200/?motherboard' },
        { name:'AMD Ryzen 7 7800X3D', desc:'Gaming CPU', price:'Rp7.399.000', image:'https://source.unsplash.com/200x200/?cpu' },
      ],
      totals: {
        subtotal: 'Rp11.698.000',
        shipping: 'Rp0',
        total: 'Rp11.698.000',
      },
      get selectedShipping(){
        const opt = this.shippingOptions.find(o => o.id === this.form.shipping);
        return opt ? `${opt.label} — ${opt.priceLabel}` : '-';
      },
      get selectedPayment(){
        const opt = this.paymentOptions.find(o => o.id === this.form.payment);
        return opt ? opt.label : '-';
      },
      validate(){
        this.error = '';
        if(this.step === 1){
          if(!this.form.name || !this.form.phone || !this.form.email || !this.form.address || !this.form.city || !this.form.zip){
            this.error = 'Lengkapi semua detail alamat sebelum melanjutkan.';
            return false;
          }
        }
        return true;
      },
      next(){
        if(!this.validate()) return;
        if(this.step < this.steps.length){
          this.step += 1;
          this.orderPlaced = false;
        }
      },
      prev(){
        if(this.step > 1){
          this.step -= 1;
          this.error = '';
        }
      },
      complete(){
        if(!this.validate()) return;
        this.orderPlaced = true;
        this.error = '';
      }
    }
  }
</script>
@endpush
