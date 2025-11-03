@extends('layouts.app')

@php
  $locale = app()->getLocale();
  $currency = config('app.currency', 'THB');
  $addressData = $shippingAddresses->map(function ($address) {
      return [
          'id' => (string) $address->id,
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
  })->values();
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
          'id' => (string) $item->id,
          'name' => $product->name ?? __('Produk tidak tersedia'),
          'brand' => $product->brand ?? null,
          'quantity' => (int) $item->quantity,
          'price' => (float) $item->price,
          'subtotal' => (float) $item->subtotal,
          'image' => $image,
      ];
  })->values();

  $paymentMethods = [
      [
          'id' => 'bank_transfer',
          'label' => __('Transfer Bank Manual'),
          'desc' => __('Transfer ke rekening BCA atau Mandiri, unggah bukti pembayaran.'),
      ],
  ];

  $bankAccounts = [
      [
          'bank' => 'BCA',
          'account' => '123 456 7890',
          'holder' => 'PT Toko Thailand',
          'is_primary' => true,
      ],
      [
          'bank' => 'Mandiri',
          'account' => '987 654 3210',
          'holder' => 'PT Toko Thailand',
          'is_primary' => false,
      ],
  ];

  $addressFormFields = ['name', 'phone', 'address_line1', 'address_line2', 'city', 'state', 'postal_code', 'country', 'is_default'];
  $addressOldInput = collect($addressFormFields)->mapWithKeys(fn ($field) => [$field => old($field)]);

  $checkoutConfig = [
      'locale' => $locale,
      'currency' => $currency,
      'steps' => [
          ['id' => 1, 'label' => __('Alamat')],
          ['id' => 2, 'label' => __('Pembayaran')],
          ['id' => 3, 'label' => __('Review')],
      ],
      'addresses' => $addressData,
      'items' => $itemsData,
      'paymentMethods' => $paymentMethods,
      'bankAccounts' => $bankAccounts,
      'totals' => [
          'subtotal' => (float) $subtotal,
          'shipping' => (float) $shippingCost,
          'discount' => 0,
      ],
      'form' => [
          'shipping_address_id' => old('shipping_address_id', $defaultAddressId),
          'payment_method' => old('payment_method', $paymentMethods[0]['id'] ?? 'bank_transfer'),
          'coupon_code' => old('coupon_code', ''),
      ],
      'couponUrl' => route('checkout.apply-coupon'),
      'csrf' => csrf_token(),
      'processUrl' => route('checkout.process'),
      'addressStoreUrl' => route('checkout.address.store'),
      'backToCartUrl' => route('cart'),
      'translations' => [
          'title' => __('checkout.title'),
          'subtitle' => __('checkout.subtitle'),
          'backToCart' => __('checkout.back_to_cart'),
          'sectionAddress' => __('Alamat Pengiriman'),
          'sectionAddressHelp' => __('Pilih alamat yang akan digunakan untuk pengiriman pesanan.'),
          'addressEmpty' => __('checkout.address_empty'),
          'addressFormHelp' => __('checkout.address_form_help'),
          'addAddress' => __('checkout.add_address_button'),
          'hideAddressForm' => __('Sembunyikan formulir alamat'),
          'badgePrimary' => __('Utama'),
          'badgeRecommended' => __('Direkomendasikan'),
          'sectionPayment' => __('Metode Pembayaran'),
          'sectionPaymentHelp' => __('Pilih cara pembayaran yang Anda inginkan.'),
          'bankDetailsTitle' => __('checkout.bank_transfer_details_title'),
          'bankDetailsNote' => __('checkout.bank_transfer_details_note'),
          'sectionReview' => __('Review Pesanan'),
          'sectionReviewHelp' => __('Pastikan semua informasi sudah benar sebelum menyelesaikan pesanan.'),
          'sectionReviewAddress' => __('Alamat Pengiriman'),
          'sectionReviewPayment' => __('Metode Pembayaran'),
          'addressMissing' => __('Alamat belum dipilih.'),
          'paymentProofTitle' => __('checkout.bank_transfer_upload_title'),
          'paymentProofHint' => __('checkout.bank_transfer_upload_hint'),
          'couponEnterCode' => __('Masukkan kode kupon terlebih dahulu.'),
          'couponSuccess' => __('Kupon berhasil diterapkan.'),
          'couponFailed' => __('Kupon tidak dapat digunakan.'),
          'couponError' => __('Terjadi kesalahan saat memproses kupon.'),
          'couponApply' => __('Terapkan'),
          'couponApplying' => __('Memproses…'),
          'couponPlaceholder' => __('Masukkan kode'),
          'sectionCoupon' => __('Kode Kupon'),
          'summaryTitle' => __('Ringkasan Pesanan'),
          'summarySubtitle' => __('Detail produk dalam keranjang Anda.'),
          'summarySubtotal' => __('Subtotal'),
          'summaryShipping' => __('Pengiriman'),
          'summaryDiscount' => __('Diskon'),
          'summaryTotal' => __('Total'),
          'securityTitle' => 'Transaksi Aman',
          'securityCopy' => __('Transaksi Anda aman dan terenkripsi.'),
          'next' => __('Lanjutkan'),
          'previous' => __('Sebelumnya'),
          'placeOrder' => __('Buat Pesanan'),
          'saveAddress' => __('Simpan Alamat'),
          'makeDefault' => __('checkout.make_default'),
          'fieldName' => __('Nama Penerima'),
          'fieldPhone' => __('Nomor Telepon'),
          'fieldAddress1' => __('Alamat'),
          'fieldAddress2' => __('Detail Tambahan (Opsional)'),
          'fieldCity' => __('Kota'),
          'fieldState' => __('Provinsi (Opsional)'),
          'fieldPostal' => __('Kode Pos'),
          'fieldCountry' => __('Negara'),
          'stepAddress' => __('Alamat'),
          'stepPayment' => __('Pembayaran'),
          'stepReview' => __('Review'),
          'paymentNotSelected' => __('Belum dipilih'),
        ],
      'sessionSuccess' => session('success'),
      'sessionError' => session('error'),
      'validationErrors' => $errors->toArray(),
      'initialStep' => $errors->has('payment_proof') ? 3 : 1,
      'oldInput' => $addressOldInput,
    ];
@endphp

@section('content')
<main class="container max-w-6xl py-10" role="main">
  <div
    data-checkout-react
    data-checkout-config='@json($checkoutConfig, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)'
    class="relative"
  >
    <div class="rounded-3xl border border-orange-100 bg-white/80 p-6 text-center text-sm text-slate-500">
      {{ __('Memuat checkout…') }}
    </div>
  </div>
</main>
@endsection
