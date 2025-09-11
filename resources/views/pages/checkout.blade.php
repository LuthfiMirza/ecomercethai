@extends('layouts.app')

@section('content')
<main id="main" class="container py-8 md:py-10" role="main">
  <h1 class="text-2xl font-semibold mb-6">Checkout</h1>

  <div class="mb-6 grid grid-cols-4 gap-2 text-xs text-center">
    <div class="py-2 rounded-md bg-accent-500 text-white">Alamat</div>
    <div class="py-2 rounded-md border">Pengiriman</div>
    <div class="py-2 rounded-md border">Pembayaran</div>
    <div class="py-2 rounded-md border">Review</div>
  </div>

  <div class="grid lg:grid-cols-3 gap-6">
    <!-- Form -->
    <form class="lg:col-span-2 space-y-6" method="post" action="#" novalidate>
      <section class="border rounded-lg p-4 bg-white dark:bg-neutral-900">
        <h2 class="font-semibold mb-3">Alamat Pengiriman</h2>
        <div class="grid md:grid-cols-2 gap-3">
          <div>
            <label class="text-xs text-neutral-500" for="name">Nama</label>
            <input id="name" class="mt-1 w-full rounded-md border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-sm"/>
          </div>
          <div>
            <label class="text-xs text-neutral-500" for="phone">Telepon</label>
            <input id="phone" class="mt-1 w-full rounded-md border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-sm"/>
          </div>
          <div class="md:col-span-2">
            <label class="text-xs text-neutral-500" for="address">Alamat</label>
            <input id="address" class="mt-1 w-full rounded-md border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-sm"/>
          </div>
          <div>
            <label class="text-xs text-neutral-500" for="city">Kota</label>
            <input id="city" class="mt-1 w-full rounded-md border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-sm"/>
          </div>
          <div>
            <label class="text-xs text-neutral-500" for="zip">Kode Pos</label>
            <input id="zip" class="mt-1 w-full rounded-md border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-sm"/>
          </div>
        </div>
      </section>

      <section class="border rounded-lg p-4 bg-white dark:bg-neutral-900">
        <h2 class="font-semibold mb-3">Metode Pengiriman</h2>
        <label class="flex items-center justify-between border rounded-md p-3 cursor-pointer">
          <span class="text-sm">Reguler (2–3 hari)</span>
          <input type="radio" name="ship" checked>
        </label>
      </section>

      <section x-data="{ method: 'card', showProof:false }" class="border rounded-lg p-4 bg-white dark:bg-neutral-900">
        <h2 class="font-semibold mb-3">Pembayaran</h2>
        <div class="space-y-2">
          <label class="flex items-center justify-between border rounded-md p-3 cursor-pointer">
            <span class="text-sm">Kartu / Transfer</span>
            <input type="radio" name="pay" value="card" x-model="method">
          </label>
          <label class="flex items-center justify-between border rounded-md p-3 cursor-pointer">
            <span class="text-sm">Transfer Manual</span>
            <input type="radio" name="pay" value="manual" x-model="method" @change="showProof=true">
          </label>
        </div>
        <div x-cloak x-show="showProof || method==='manual'" class="mt-4">
          <div class="text-sm font-medium mb-2">Upload Bukti Pembayaran</div>
          <x-payment-proof-upload />
        </div>
      </section>

      <div class="flex justify-end">
        <x-button type="submit">Lanjut</x-button>
      </div>
    </form>

    <!-- Summary -->
    <aside class="space-y-3 lg:sticky lg:top-24 self-start">
      <div class="border rounded-lg p-4 bg-white dark:bg-neutral-900">
        <div class="font-semibold mb-2">Ringkasan Order</div>
        <div class="text-sm flex justify-between"><span>Subtotal</span><span>฿ 1,099.00</span></div>
        <div class="text-sm flex justify-between"><span>Pengiriman</span><span>฿ 0</span></div>
        <div class="mt-2 border-t pt-2 font-semibold flex justify-between"><span>Total</span><span>฿ 1,099.00</span></div>
        <x-button class="w-full mt-4">Buat Pesanan</x-button>
      </div>
      <x-alert type="info">Transaksi Anda aman dan terenkripsi.</x-alert>
    </aside>
  </div>
</main>
@endsection
