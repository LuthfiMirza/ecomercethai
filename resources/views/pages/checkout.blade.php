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
          'color' => $item->color,
      ];
  })->values();

  $paymentMethods = [
      [
          'id' => 'bank_transfer',
          'label' => __('checkout.payment_bank_transfer'),
          'desc' => __('checkout.payment_bank_transfer_desc'),
      ],
  ];

  $bankAccounts = [
      [
          'bank' => 'ธนาคารไทยพาณิย์ (SCB)',
          'account' => '430-093903-4',
          'holder' => 'หจก.ลุงแป๊ะ เทรดดิ้ง / Lungpae Trading Ltd., Part.',
          'is_primary' => true,
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
          'sectionAddress' => __('checkout.section_address_title'),
          'sectionAddressHelp' => __('checkout.section_address_help'),
          'addressEmpty' => __('checkout.address_empty'),
          'addressFormTitle' => __('checkout.address_form_title'),
          'addressFormHelp' => __('checkout.address_form_help'),
          'addressFormRequiredHint' => __('checkout.address_form_required_hint'),
          'addAddress' => __('checkout.add_address_button'),
          'hideAddressForm' => __('checkout.address_form_hide'),
          'badgePrimary' => __('checkout.badge_primary'),
          'badgeRecommended' => __('checkout.badge_recommended'),
          'selectedLabel' => __('checkout.badge_selected'),
          'deleteAddress' => __('checkout.delete_address'),
          'deleteAddressConfirmTitle' => __('checkout.delete_address_title'),
          'deleteAddressConfirmText' => __('checkout.delete_address_text'),
          'deleteAddressCancel' => __('checkout.delete_address_cancel'),
          'deleteAddressConfirmAction' => __('checkout.delete_address_confirm'),
          'deleteAddressLoading' => __('checkout.delete_address_loading'),
          'deleteAddressError' => __('checkout.delete_address_error'),
          'addressDeleted' => __('checkout.address_deleted'),
          'sectionPayment' => __('checkout.section_payment_title'),
          'sectionPaymentHelp' => __('checkout.section_payment_help'),
          'bankDetailsTitle' => __('checkout.bank_transfer_details_title'),
          'bankDetailsNote' => __('checkout.bank_transfer_details_note'),
          'sectionReview' => __('checkout.section_review_title'),
          'sectionReviewHelp' => __('checkout.section_review_help'),
          'sectionReviewAddress' => __('checkout.section_review_address'),
          'sectionReviewPayment' => __('checkout.section_review_payment'),
          'addressMissing' => __('checkout.address_missing'),
          'paymentProofTitle' => __('checkout.bank_transfer_upload_title'),
          'paymentProofHint' => __('checkout.bank_transfer_upload_hint'),
          'couponEnterCode' => __('checkout.coupon_enter_code'),
          'couponSuccess' => __('checkout.coupon_applied'),
          'couponFailed' => __('checkout.coupon_failed'),
          'couponError' => __('checkout.coupon_error'),
          'couponApply' => __('checkout.coupon_apply'),
          'couponApplying' => __('checkout.coupon_applying'),
          'couponPlaceholder' => __('checkout.coupon_placeholder'),
          'sectionCoupon' => __('checkout.section_coupon'),
          'savingAddress' => __('checkout.saving_address'),
          'processingOrder' => __('checkout.processing_order'),
          'summaryTitle' => __('checkout.summary_title'),
          'summarySubtitle' => __('checkout.summary_subtitle'),
          'summarySubtotal' => __('checkout.summary_subtotal'),
          'summaryShipping' => __('checkout.summary_shipping'),
          'summaryDiscount' => __('checkout.summary_discount'),
          'summaryTotal' => __('checkout.summary_total'),
          'summaryColor' => __('product.color'),
          'securityTitle' => __('checkout.security_title'),
          'securityCopy' => __('checkout.security_copy'),
          'next' => __('checkout.next'),
          'previous' => __('checkout.previous'),
          'placeOrder' => __('checkout.place_order'),
          'saveAddress' => __('checkout.save_address'),
          'makeDefault' => __('checkout.make_default'),
          'fieldName' => __('checkout.field_name_label'),
          'fieldPhone' => __('checkout.field_phone_label'),
          'fieldAddress1' => __('checkout.field_address1_label'),
          'fieldAddress2' => __('checkout.field_address2_label'),
          'fieldCity' => __('checkout.field_city_label'),
          'fieldState' => __('checkout.field_state_label'),
          'fieldPostal' => __('checkout.field_postal_label'),
          'fieldCountry' => __('checkout.field_country_label'),
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
          'stepAddress' => __('checkout.step_address'),
          'stepPayment' => __('checkout.step_payment'),
          'stepReview' => __('checkout.step_review'),
          'paymentNotSelected' => __('checkout.payment_not_selected'),
        ],
      'sessionSuccess' => session('success'),
      'sessionError' => session('error'),
      'validationErrors' => $errors->toArray(),
      'initialStep' => $errors->has('payment_proof') ? 3 : 1,
      'oldInput' => $addressOldInput,
    ];
@endphp

@section('content')
<main class="container max-w-6xl pt-4 pb-10 md:pt-6 md:pb-12" role="main">
  <div
    data-checkout-react
    data-checkout-config='@json($checkoutConfig, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)'
    class="relative"
  >
    <div class="rounded-3xl border border-orange-100 bg-white/80 p-6 text-center text-sm text-slate-500">
      {{ __('checkout.loading') }}
    </div>
  </div>
</main>
@endsection
