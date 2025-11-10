@extends('layouts.app')

@php
  $locale = app()->getLocale();
  $brandName = config('app.name', 'Lungpaeit');
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
          'holder' => 'PT ' . $brandName,
          'is_primary' => true,
      ],
      [
          'bank' => 'Mandiri',
          'account' => '987 654 3210',
          'holder' => 'PT ' . $brandName,
          'is_primary' => false,
      ],
  ];

  $requestInstance = request();
  $addressFormFields = ['name', 'phone', 'address_line1', 'address_line2', 'city', 'state', 'postal_code', 'country', 'is_default'];
  $addressOldInput = collect($addressFormFields)->mapWithKeys(function ($field) use ($requestInstance) {
      $value = old($field, $requestInstance->query($field));
      if ($field === 'is_default') {
          $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
      }
      return [$field => $value];
  });
  $addressPrefillPresent = $addressOldInput
      ->some(fn ($value, $key) => $key === 'is_default' ? (bool) $value : filled($value));

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
      'couponUrl' => localized_route('checkout.apply-coupon'),
      'csrf' => csrf_token(),
      'processUrl' => localized_route('checkout.process'),
      'addressStoreUrl' => localized_route('checkout.address.store'),
      'backToCartUrl' => localized_route('cart'),
      'addressDeleteUrl' => localized_route('checkout.address.destroy', ['address' => 'ADDRESS_ID_PLACEHOLDER']),
      'forceShowAddressForm' => $addressPrefillPresent,
      'translations' => [
          'title' => __('checkout.title'),
          'subtitle' => __('checkout.subtitle'),
          'backToCart' => __('checkout.back_to_cart'),
          'sectionAddress' => __('Alamat Pengiriman'),
          'sectionAddressHelp' => __('Pilih alamat yang akan digunakan untuk pengiriman pesanan.'),
          'addressEmpty' => __('checkout.address_empty'),
          'addressFormTitle' => __('checkout.address_form_title'),
          'addressFormHelp' => __('checkout.address_form_help'),
          'addressFormRequiredHint' => __('checkout.address_form_required_hint'),
          'addAddress' => __('checkout.add_address_button'),
          'hideAddressForm' => __('Sembunyikan formulir alamat'),
          'badgePrimary' => __('Utama'),
          'badgeRecommended' => __('Direkomendasikan'),
          'selectedLabel' => __('Terpilih'),
          'deleteAddress' => __('Hapus Alamat'),
          'deleteAddressConfirmTitle' => __('Hapus alamat ini?'),
          'deleteAddressConfirmText' => __('Alamat akan dihapus permanen dan tidak bisa digunakan lagi.'),
          'deleteAddressCancel' => __('Batal'),
          'deleteAddressConfirmAction' => __('Hapus'),
          'deleteAddressLoading' => __('Menghapus…'),
          'deleteAddressError' => __('Tidak dapat menghapus alamat.'),
          'addressDeleted' => __('checkout.address_deleted'),
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
          'fieldRequiredLabel' => __('checkout.field_required_label'),
          'fieldOptionalLabel' => __('checkout.field_optional_label'),
          'fieldNamePlaceholder' => __('checkout.field_name_placeholder'),
          'fieldPhoneHint' => __('checkout.field_phone_hint'),
          'fieldPhonePlaceholder' => __('checkout.field_phone_placeholder'),
          'fieldAddress1Hint' => __('checkout.field_address1_hint'),
          'fieldAddress1Placeholder' => __('checkout.field_address1_placeholder'),
          'fieldAddress2Hint' => __('checkout.field_address2_hint'),
          'fieldAddress2Placeholder' => __('checkout.field_address2_placeholder'),
          'fieldCityPlaceholder' => __('checkout.field_city_placeholder'),
          'fieldStatePlaceholder' => __('checkout.field_state_placeholder'),
          'fieldPostalHint' => __('checkout.field_postal_hint'),
          'fieldPostalPlaceholder' => __('checkout.field_postal_placeholder'),
          'fieldCountryPlaceholder' => __('checkout.field_country_placeholder'),
          'fieldCountryHint' => __('checkout.field_country_hint'),
          'makeDefaultHint' => __('checkout.make_default_hint'),
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
