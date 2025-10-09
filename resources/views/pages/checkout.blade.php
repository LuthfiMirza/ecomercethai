@extends('layouts.app')

@php
  $locale = app()->getLocale();
  $currency = config('app.currency', 'THB');
  $addressData = $shippingAddresses->map(function ($address) {
      return [
          'id' => $address->id,
          'name' => $address->name,
          'phone' => $address->phone,
          'is_default' => (bool) $address->is_default,
          'lines' => array_values(array_filter([
              $address->address_line1,
              $address->address_line2,
              trim(implode(', ', array_filter([$address->city, $address->state, $address->postal_code]))),
              $address->country,
          ])),
      ];
  });
  $defaultAddress = $addressData->firstWhere('is_default', true) ?? $addressData->first();
  $defaultAddressId = $defaultAddress['id'] ?? null;
  $itemsData = $cartItems->map(function ($item) {
      $product = $item->product;
      $image = null;
      if ($product && $product->image) {
          $image = \Illuminate\Support\Str::startsWith($product->image, ['http://', 'https://'])
              ? $product->image
              : asset('storage/' . ltrim($product->image, '/'));
      }
      $image = $image ?? 'https://source.unsplash.com/160x160/?product,' . urlencode($product->name ?? 'product');

      return [
          'id' => $item->id,
          'name' => $product->name ?? __('Produk tidak tersedia'),
          'brand' => $product->brand ?? null,
          'quantity' => $item->quantity,
          'price' => (float) $item->price,
          'subtotal' => (float) $item->subtotal,
          'image' => $image,
      ];
  });
  $paymentMethods = [
      [
          'id' => 'bank_transfer',
          'label' => __('Transfer Bank Manual'),
          'desc' => __('Transfer ke rekening BCA atau Mandiri, unggah bukti pembayaran.'),
      ],
      [
          'id' => 'credit_card',
          'label' => __('Kartu Kredit / Debit'),
          'desc' => __('Pembayaran instan menggunakan kartu yang tersimpan.'),
      ],
      [
          'id' => 'midtrans',
          'label' => 'Midtrans',
          'desc' => __('Redirect ke Midtrans untuk virtual account, e-money, dan QRIS.'),
      ],
      [
          'id' => 'xendit',
          'label' => 'Xendit',
          'desc' => __('Bayar via Xendit untuk pilihan VA & e-wallet populer.'),
      ],
      [
          'id' => 'stripe',
          'label' => 'Stripe',
          'desc' => __('Kartu internasional aman dengan dukungan 3D Secure.'),
      ],
  ];
@endphp

@section('content')
<main class="container max-w-6xl py-10" role="main"
      x-data="checkoutApp({
        csrf: '{{ csrf_token() }}',
        locale: '{{ $locale }}',
        currency: '{{ $currency }}',
        subtotal: {{ (float) $subtotal }},
        shipping: {{ (float) $shippingCost }},
        addresses: @json($addressData),
        items: @json($itemsData),
        paymentMethods: @json($paymentMethods),
        defaultAddress: {{ $defaultAddressId ? (int) $defaultAddressId : 'null' }},
        couponUrl: '{{ route('checkout.apply-coupon') }}'
      })">
  <section class="soft-card p-6 md:p-8 space-y-6">
    <header class="space-y-3">
      <a href="{{ route('cart') }}" class="inline-flex items-center gap-2 text-sm text-neutral-500 hover:text-neutral-700">
        <i class="fa-solid fa-chevron-left text-xs"></i>
        <span>{{ __('checkout.back_to_cart') }}</span>
      </a>
      <div>
        <h1 class="text-3xl font-semibold text-neutral-800 dark:text-neutral-100">{{ __('checkout.title') }}</h1>
        <p class="text-sm text-neutral-500 dark:text-neutral-300">{{ __('checkout.subtitle') }}</p>
      </div>
    </header>

    @if(session('error'))
      <x-alert type="error">{{ session('error') }}</x-alert>
    @endif
    @if(session('success'))
      <x-alert type="success">{{ session('success') }}</x-alert>
    @endif
    @if($errors->any())
      <x-alert type="error">{{ $errors->first() }}</x-alert>
    @endif

    <ol class="grid gap-3 sm:grid-cols-3" role="list">
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

    <form method="POST" action="{{ route('checkout.process') }}" x-ref="form" class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
      @csrf
      <input type="hidden" name="shipping_address_id" x-model="form.shipping_address_id">
      <input type="hidden" name="payment_method" x-model="form.payment_method">
      <input type="hidden" name="coupon_code" x-model="form.coupon_code">

      <div class="space-y-6">
        <section x-show="step === 1" x-transition class="soft-card p-6 space-y-4">
          <header class="space-y-1">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Alamat Pengiriman') }}</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Pilih alamat yang akan digunakan untuk pengiriman pesanan.') }}</p>
          </header>

          <template x-if="addresses.length === 0">
            <div class="rounded-xl border border-dashed border-neutral-300 p-6 text-center text-sm text-neutral-500 dark:border-neutral-700 dark:text-neutral-300">
              {{ __('Anda belum memiliki alamat pengiriman. Tambahkan melalui halaman akun Anda terlebih dahulu.') }}
            </div>
          </template>

          <div class="grid gap-3" x-show="addresses.length">
            <template x-for="address in addresses" :key="address.id">
              <label class="flex gap-3 rounded-2xl border border-neutral-200 bg-white/80 p-4 shadow-inner transition hover:border-sky-300 dark:border-neutral-800 dark:bg-neutral-900/70" :class="form.shipping_address_id == address.id ? 'ring-2 ring-sky-200 border-sky-400' : ''">
                <input type="radio" class="mt-1" :value="address.id" x-model="form.shipping_address_id" name="shipping_address_radio">
                <div class="flex-1 space-y-1 text-sm text-neutral-600 dark:text-neutral-300">
                  <div class="flex items-center gap-2 text-neutral-900 dark:text-neutral-100">
                    <span class="font-semibold" x-text="address.name"></span>
                    <template x-if="address.is_default">
                      <x-badge variant="success">{{ __('Utama') }}</x-badge>
                    </template>
                  </div>
                  <div class="text-xs text-neutral-500" x-text="address.phone"></div>
                  <ul class="text-xs text-neutral-500">
                    <template x-for="line in address.lines" :key="line">
                      <li x-text="line"></li>
                    </template>
                  </ul>
                </div>
              </label>
            </template>
          </div>

          <div class="flex items-center justify-between text-xs text-neutral-500">
            <span>{{ __('Perlu mengubah alamat?') }}</span>
            <a href="{{ route('account') }}" class="font-medium text-sky-600 hover:text-sky-700">{{ __('Kelola di halaman Akun') }}</a>
          </div>
        </section>

        <section x-show="step === 2" x-transition class="soft-card p-6 space-y-4">
          <header class="space-y-1">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Metode Pembayaran') }}</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Pilih cara pembayaran yang Anda inginkan.') }}</p>
          </header>

          <div class="space-y-3">
            <template x-for="method in paymentMethods" :key="method.id">
              <label class="block rounded-2xl border border-neutral-200 bg-white/80 p-4 shadow-inner transition hover:border-sky-300 dark:border-neutral-800 dark:bg-neutral-900/70" :class="form.payment_method === method.id ? 'ring-2 ring-sky-200 border-sky-400' : ''">
                <div class="flex items-start gap-3">
                  <input type="radio" :value="method.id" x-model="form.payment_method" name="payment_method_radio" class="mt-1"/>
                  <div class="space-y-1">
                    <div class="flex items-center gap-2">
                      <span class="font-semibold text-neutral-900 dark:text-neutral-100" x-text="method.label"></span>
                    </div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400" x-text="method.desc"></p>
                  </div>
                </div>
              </label>
            </template>
          </div>
        </section>

        <section x-show="step === 3" x-transition class="soft-card p-6 space-y-4">
          <header class="space-y-1">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Review Pesanan') }}</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Pastikan semua informasi sudah benar sebelum menyelesaikan pesanan.') }}</p>
          </header>

          <div class="space-y-4 text-sm text-neutral-600 dark:text-neutral-300">
            <div class="rounded-2xl border border-neutral-200 bg-white/60 p-4 dark:border-neutral-800 dark:bg-neutral-900/50">
              <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 mb-2">{{ __('Alamat Pengiriman') }}</h3>
              <template x-if="selectedAddress">
                <div class="space-y-1">
                  <div class="flex items-center gap-2 text-neutral-900 dark:text-neutral-100">
                    <span x-text="selectedAddress.name" class="font-medium"></span>
                    <template x-if="selectedAddress.is_default">
                      <x-badge variant="success">{{ __('Utama') }}</x-badge>
                    </template>
                  </div>
                  <div class="text-xs" x-text="selectedAddress.phone"></div>
                  <ul class="text-xs">
                    <template x-for="line in selectedAddress.lines" :key="line">
                      <li x-text="line"></li>
                    </template>
                  </ul>
                </div>
              </template>
              <template x-if="!selectedAddress">
                <p class="text-sm text-neutral-400">{{ __('Belum ada alamat dipilih.') }}</p>
              </template>
            </div>

            <div class="rounded-2xl border border-neutral-200 bg-white/60 p-4 dark:border-neutral-800 dark:bg-neutral-900/50">
              <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 mb-2">{{ __('Metode Pembayaran') }}</h3>
              <p class="text-sm" x-text="selectedPaymentLabel"></p>
            </div>

            <div class="rounded-2xl border border-neutral-200 bg-white/60 p-4 dark:border-neutral-800 dark:bg-neutral-900/50">
              <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 mb-2">{{ __('Catatan') }}</h3>
              <p class="text-sm text-neutral-500">{{ __('Anda akan menerima email konfirmasi segera setelah pesanan berhasil dibuat.') }}</p>
            </div>
          </div>
        </section>

        <div class="flex flex-wrap gap-3" x-show="addresses.length">
          <x-button type="button" variant="outline" @click="prev" x-show="step > 1">
            <i class="fa-solid fa-chevron-left text-xs"></i>
            <span>{{ __('Sebelumnya') }}</span>
          </x-button>
          <x-button type="button" class="" @click="next" x-show="step < steps.length" :disabled="!canProceed">
            <span>{{ __('Lanjutkan') }}</span>
            <i class="fa-solid fa-chevron-right text-xs"></i>
          </x-button>
          <x-button type="submit" x-show="step === steps.length" :disabled="!canProceed" @click.prevent="submit">
            {{ __('Buat Pesanan') }}
          </x-button>
        </div>

        <template x-if="!addresses.length">
          <x-alert type="warning">{{ __('Tambahkan alamat terlebih dahulu sebelum melanjutkan checkout.') }}</x-alert>
        </template>
      </div>

      <aside class="space-y-4">
        <div class="soft-card p-6 space-y-4">
          <header>
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Ringkasan Pesanan') }}</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Detail produk dalam keranjang Anda.') }}</p>
          </header>
          <ul class="space-y-4 divide-y divide-white/60 dark:divide-neutral-800">
            <template x-for="item in items" :key="item.id">
              <li class="flex gap-3 pt-2 first:pt-0">
                <img :src="item.image" :alt="item.name" class="h-16 w-16 rounded-xl object-cover" loading="lazy"/>
                <div class="flex-1 space-y-1 text-sm">
                  <p class="font-medium text-neutral-900 dark:text-neutral-100" x-text="item.name"></p>
                  <template x-if="item.brand">
                    <p class="text-xs text-neutral-500" x-text="item.brand"></p>
                  </template>
                  <p class="text-xs text-neutral-500">{{ __('Qty:') }} <span x-text="item.quantity"></span></p>
                  <p class="font-semibold text-neutral-900 dark:text-neutral-100" x-text="format(item.subtotal)"></p>
                </div>
              </li>
            </template>
          </ul>
          <div class="space-y-2 border-t border-white/60 pt-3 text-sm text-neutral-600 dark:text-neutral-300">
            <div class="flex justify-between"><span>{{ __('Subtotal') }}</span><span x-text="format(totals.subtotal)"></span></div>
            <div class="flex justify-between"><span>{{ __('Pengiriman') }}</span><span x-text="format(totals.shipping)"></span></div>
            <div class="flex justify-between" :class="totals.discount > 0 ? 'text-green-600 dark:text-green-400' : ''">
              <span>{{ __('Diskon') }}</span>
              <span x-text="totals.discount > 0 ? '-'+format(totals.discount) : format(0)"></span>
            </div>
            <div class="flex justify-between font-semibold text-neutral-900 dark:text-neutral-100 text-base"><span>{{ __('Total') }}</span><span x-text="format(grandTotal)"></span></div>
          </div>
        </div>

        <div class="soft-card p-4 space-y-3">
          <h3 class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Kode Kupon') }}</h3>
          <div class="flex gap-2">
            <input type="text" x-model="form.coupon_code" placeholder="{{ __('Masukkan kode') }}" class="flex-1 rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm shadow-inner focus:border-sky-300 focus:ring-2 focus:ring-sky-200 outline-none dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100" />
            <x-button type="button" size="sm" @click="applyCoupon" :disabled="!form.coupon_code || isApplyingCoupon">
              <span x-show="!isApplyingCoupon">{{ __('Terapkan') }}</span>
              <span x-show="isApplyingCoupon">{{ __('Memproses...') }}</span>
            </x-button>
          </div>
          <template x-if="couponStatus">
            <p class="text-xs text-green-600" x-text="couponStatus"></p>
          </template>
          <template x-if="couponError">
            <p class="text-xs text-red-600" x-text="couponError"></p>
          </template>
        </div>

        <x-alert type="info">{{ __('Transaksi Anda aman dan terenkripsi.') }}</x-alert>
      </aside>
    </form>
  </section>
</main>
@endsection

@push('scripts')
<script>
  function checkoutApp(config) {
    const formatter = new Intl.NumberFormat(config.locale || 'en', { style: 'currency', currency: config.currency || 'THB' });
    return {
      step: 1,
      steps: [
        { id: 1, label: '{{ __('Alamat') }}' },
        { id: 2, label: '{{ __('Pembayaran') }}' },
        { id: 3, label: '{{ __('Review') }}' },
      ],
      addresses: config.addresses || [],
      items: config.items || [],
      paymentMethods: config.paymentMethods || [],
      totals: {
        subtotal: Number(config.subtotal || 0),
        shipping: Number(config.shipping || 0),
        discount: 0,
      },
      form: {
        shipping_address_id: config.defaultAddress || (config.addresses[0]?.id ?? null),
        payment_method: config.paymentMethods[0]?.id ?? 'bank_transfer',
        coupon_code: '',
      },
      couponStatus: null,
      couponError: null,
      isApplyingCoupon: false,
      format(amount) {
        return formatter.format(Number(amount) || 0);
      },
      get grandTotal() {
        const total = this.totals.subtotal - this.totals.discount + this.totals.shipping;
        return total > 0 ? total : 0;
      },
      get selectedAddress() {
        return this.addresses.find(addr => Number(addr.id) === Number(this.form.shipping_address_id)) || null;
      },
      get selectedPaymentLabel() {
        return this.paymentMethods.find(method => method.id === this.form.payment_method)?.label || '{{ __('Belum dipilih') }}';
      },
      get canProceed() {
        if (this.step === 1) {
          return Boolean(this.form.shipping_address_id);
        }
        if (this.step === 2) {
          return Boolean(this.form.payment_method);
        }
        return true;
      },
      next() {
        if (!this.canProceed || this.step >= this.steps.length) return;
        this.step += 1;
      },
      prev() {
        if (this.step <= 1) return;
        this.step -= 1;
      },
      async applyCoupon() {
        if (!this.form.coupon_code) {
          this.couponError = '{{ __('Masukkan kode kupon terlebih dahulu.') }}';
          this.couponStatus = null;
          return;
        }
        this.isApplyingCoupon = true;
        this.couponError = null;
        this.couponStatus = null;
        try {
          const response = await fetch(config.couponUrl, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': config.csrf,
            },
            body: new URLSearchParams({ coupon_code: this.form.coupon_code }).toString(),
          });
          const data = await response.json();
          if (data.success) {
            this.totals.discount = Number(data.discount_amount || 0);
            this.couponStatus = data.message || '{{ __('Kupon berhasil diterapkan.') }}';
            this.couponError = null;
          } else {
            this.totals.discount = 0;
            this.couponError = data.message || '{{ __('Kupon tidak dapat digunakan.') }}';
            this.couponStatus = null;
          }
        } catch (error) {
          this.couponError = '{{ __('Terjadi kesalahan saat memproses kupon.') }}';
          this.couponStatus = null;
        } finally {
          this.isApplyingCoupon = false;
        }
      },
      submit() {
        if (!this.canProceed) return;
        this.$refs.form.submit();
      },
    };
  }
</script>
@endpush
