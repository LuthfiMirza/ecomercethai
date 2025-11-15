import React, { useMemo, useRef, useState } from 'react';

const classNames = (...classes) => classes.filter(Boolean).join(' ');

const firstError = (errors, key) => {
  if (!errors || !errors[key]) return null;
  const value = errors[key];
  return Array.isArray(value) ? value[0] : value;
};

const createFormatter = (locale, currency) => {
  try {
    return new Intl.NumberFormat(locale || 'en', {
      style: 'currency',
      currency: currency || 'THB',
    });
  } catch (error) {
    return new Intl.NumberFormat('en', { style: 'currency', currency: 'THB' });
  }
};

const buildAddressForm = (oldInput = {}) => ({
  name: oldInput.name || '',
  phone: oldInput.phone || '',
  address_line1: oldInput.address_line1 || '',
  address_line2: oldInput.address_line2 || '',
  city: oldInput.city || '',
  state: oldInput.state || '',
  postal_code: oldInput.postal_code || '',
  country: oldInput.country || '',
  is_default: Boolean(oldInput.is_default),
});

const CheckoutApp = ({ initialData = {} }) => {

  const {
    steps = [],
    addresses: initialAddresses = [],
    items: initialItems = [],
    paymentMethods = [],
    bankAccounts = [],
    totals: initialTotals = {},
    form: initialForm = {},
    translations = {},
    couponUrl,
    addressStoreUrl,
    processUrl,
    csrf,
    locale = 'en',
    currency = 'THB',
    addressDeleteUrl,
  } = initialData;

  const t = (key, fallback) => translations[key] || fallback || key;

  const formatter = useMemo(() => createFormatter(locale, currency), [locale, currency]);
  const formatCurrency = (value) => formatter.format(Number(value) || 0);

  const hasAddressPrefill = useMemo(() => {
    if (initialData.forceShowAddressForm) return true;
    const entries = Object.entries(initialData.oldInput || {});
    if (entries.length === 0) {
      return false;
    }
    return entries.some(([key, value]) => {
      if (key === 'is_default') {
        return Boolean(value);
      }
      if (typeof value === 'number') return true;
      if (typeof value === 'string') return value.trim().length > 0;
      return Boolean(value);
    });
  }, [initialData.forceShowAddressForm, initialData.oldInput]);

  const formRef = useRef(null);
  const fileInputRef = useRef(null);

  const [step, setStep] = useState(initialData.initialStep || 1);
  const [addresses, setAddresses] = useState(initialAddresses);
  const [selectedAddressId, setSelectedAddressId] = useState(
    initialForm.shipping_address_id || initialAddresses[0]?.id || null,
  );
  const [showAddressForm, setShowAddressForm] = useState(initialAddresses.length === 0 || hasAddressPrefill);
  const [addressForm, setAddressForm] = useState(buildAddressForm(initialData.oldInput));
  const [addressErrors, setAddressErrors] = useState({});
  const [savingAddress, setSavingAddress] = useState(false);
  const [addressPendingDelete, setAddressPendingDelete] = useState(null);
  const [deletingAddress, setDeletingAddress] = useState(false);

  const [paymentMethod, setPaymentMethod] = useState(
    initialForm.payment_method || paymentMethods[0]?.id || '',
  );
  const [paymentProofName, setPaymentProofName] = useState(null);

  const [couponCode, setCouponCode] = useState(initialForm.coupon_code || '');
  const [discount, setDiscount] = useState(Number(initialTotals.discount) || 0);
  const [couponFeedback, setCouponFeedback] = useState({ status: null, message: null, loading: false });

  const [validationErrors, setValidationErrors] = useState(initialData.validationErrors || {});
  const [submitting, setSubmitting] = useState(false);
  const [globalMessage, setGlobalMessage] = useState(() => {
    if (initialData.sessionSuccess) {
      return { type: 'success', text: initialData.sessionSuccess };
    }
    if (initialData.sessionError) {
      return { type: 'error', text: initialData.sessionError };
    }
    return null;
  });

  const subtotal = Number(initialTotals.subtotal) || 0;
  const shipping = Number(initialTotals.shipping) || 0;
  const discountAmount = Math.min(Number(discount) || 0, subtotal);
  const total = Math.max(0, subtotal - discountAmount + shipping);

  const selectedAddress = addresses.find((address) => address.id === selectedAddressId) || null;

  const resolveDeleteUrl = (addressId) => {
    if (!addressDeleteUrl || !addressId) return null;
    return addressDeleteUrl.replace('ADDRESS_ID_PLACEHOLDER', addressId);
  };

  const inputBaseClass =
    'mt-2 w-full rounded-2xl border border-neutral-200 bg-white px-4 py-3 text-sm text-neutral-900 shadow-sm transition placeholder:text-neutral-400 focus:border-orange-400 focus:ring-4 focus:ring-orange-200/60 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 dark:placeholder:text-neutral-500';
  const helperTextClass = 'mt-1 text-xs text-neutral-500 dark:text-neutral-400';
  const badgeClass = (optional = false) =>
    classNames(
      'rounded-full px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wide',
      optional
        ? 'bg-neutral-100 text-neutral-500 dark:bg-neutral-800 dark:text-neutral-300'
        : 'bg-orange-100 text-orange-600 dark:bg-orange-500/20 dark:text-orange-200',
    );


  const handleNext = () => {
    if (step === 1 && !selectedAddressId) {
      const message = t('addressMissing', 'Please choose a shipping address.');
      setGlobalMessage({ type: 'error', text: message });
      setValidationErrors((prev) => ({ ...prev, shipping_address_id: [message] }));
      return;
    }
    if (step === 2 && !paymentMethod) {
      const message = t('paymentNotSelected', 'Choose a payment method.');
      setGlobalMessage({ type: 'error', text: message });
      setValidationErrors((prev) => ({ ...prev, payment_method: [message] }));
      return;
    }
    setGlobalMessage(null);
    setStep((current) => Math.min(current + 1, 3));
  };

  const handlePrevious = () => {
    setGlobalMessage(null);
    setStep((current) => Math.max(current - 1, 1));
  };

  const resetAddressForm = () => {
    setAddressForm(buildAddressForm(initialData.oldInput));
    setAddressErrors({});
  };

  const handleAddressFieldChange = (event) => {
    const { name, value, type, checked } = event.target;
    setAddressForm((current) => ({
      ...current,
      [name]: type === 'checkbox' ? checked : value,
    }));
    setAddressErrors((current) => ({ ...current, [name]: undefined }));
  };

  const handleAddressKeyDown = (event) => {
    if (event.key !== 'Enter') return;
    const tagName = event.target?.tagName?.toUpperCase();
    if (!['INPUT', 'TEXTAREA', 'SELECT'].includes(tagName)) return;
    event.preventDefault();
    handleAddressSubmit();
  };

  const handleAddressSubmit = async (event) => {
    if (event) {
      event.preventDefault();
      event.stopPropagation();
    }
    if (!addressStoreUrl || savingAddress) return;

    setSavingAddress(true);
    setAddressErrors({});
    setGlobalMessage(null);

    const formData = new FormData();
    Object.entries(addressForm).forEach(([key, value]) => {
      if (key === 'is_default') {
        if (value) formData.append(key, '1');
      } else {
        formData.append(key, value ?? '');
      }
    });

    try {
      const response = await fetch(addressStoreUrl, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf,
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData,
      });

      if (response.status === 422) {
        const payload = await response.json();
        setAddressErrors(payload.errors || {});
        return;
      }

      if (!response.ok) {
        throw new Error('Unable to save address');
      }

      const payload = await response.json();
      if (payload?.success && payload.address) {
        setAddresses((current) => {
          const normalized = payload.address;
          const base = normalized.is_default
            ? current.map((address) => ({ ...address, is_default: false }))
            : [...current];
          return normalized.is_default ? [normalized, ...base] : [...base, normalized];
        });
        setSelectedAddressId(payload.address.id);
        setAddressForm(buildAddressForm());
        setShowAddressForm(false);
        setGlobalMessage({
          type: 'success',
          text: payload.message || t('addressAdded', 'Address saved successfully.'),
        });
      } else {
        throw new Error(payload?.message || 'Unexpected response');
      }
    } catch (error) {
      setGlobalMessage({
        type: 'error',
        text: error.message || t('couponError', 'Something went wrong. Please try again.'),
      });
    } finally {
      setSavingAddress(false);
    }
  };

  const handleDeleteAddress = async () => {
    if (!addressPendingDelete) return;
    const addressId = addressPendingDelete.id;
    const deleteUrl = resolveDeleteUrl(addressId);
    if (!deleteUrl) {
      setGlobalMessage({
        type: 'error',
        text: t('deleteAddressError', 'Unable to delete this address.'),
      });
      return;
    }

    setDeletingAddress(true);
    setGlobalMessage(null);

    try {
      const response = await fetch(deleteUrl, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrf,
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      });

      const payload = await response.json().catch(() => null);

      if (!response.ok || !payload?.success) {
        throw new Error(payload?.message || 'Unable to delete address');
      }

      setAddresses((current) => {
        const next = current.filter((item) => item.id !== addressId);
        const nextDefaultId = payload.next_default_id ? String(payload.next_default_id) : null;
        const finalList = nextDefaultId
          ? next.map((item) => ({ ...item, is_default: item.id === nextDefaultId }))
          : next;

        if (selectedAddressId === addressId) {
          const fallback = finalList.find((item) => item.is_default) || finalList[0] || null;
          setSelectedAddressId(fallback ? fallback.id : null);
        }

        if (finalList.length === 0) {
          setShowAddressForm(true);
        }

        return finalList;
      });

      setGlobalMessage({
        type: 'success',
        text: payload?.message || t('addressDeleted', 'Address deleted successfully.'),
      });
      setAddressPendingDelete(null);
    } catch (error) {
      setGlobalMessage({
        type: 'error',
        text: error.message || t('deleteAddressError', 'Unable to delete this address.'),
      });
    } finally {
      setDeletingAddress(false);
    }
  };


  const handleApplyCoupon = async (event) => {
    event.preventDefault();
    if (!couponUrl || couponFeedback.loading) return;

    const code = couponCode.trim();
    if (!code) {
      setCouponFeedback({
        status: 'error',
        message: t('couponEnterCode', 'Enter a coupon code first.'),
        loading: false,
      });
      return;
    }

    setCouponFeedback({ status: null, message: null, loading: true });

    try {
      const response = await fetch(couponUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ coupon_code: code }),
      });

      const payload = await response.json();
      if (!response.ok || !payload.success) {
        setDiscount(0);
        setCouponFeedback({
          status: 'error',
          message: payload?.message || t('couponFailed', 'Coupon cannot be applied.'),
          loading: false,
        });
        return;
      }

      setDiscount(Number(payload.discount_amount) || 0);
      setCouponFeedback({
        status: 'success',
        message: payload?.message || t('couponSuccess', 'Coupon applied successfully.'),
        loading: false,
      });
    } catch (error) {
      setDiscount(0);
      setCouponFeedback({
        status: 'error',
        message: t('couponError', 'There was an error applying the coupon.'),
        loading: false,
      });
    }
  };

  const handleSubmit = async (event) => {
    event.preventDefault();
    
    if (!processUrl || submitting) {
      return;
    }

    if (!selectedAddressId) {
      const message = t('addressMissing', 'Please choose a shipping address.');
      setStep(1);
      setGlobalMessage({ type: 'error', text: message });
      setValidationErrors({ shipping_address_id: [message] });
      return;
    }

    if (!paymentMethod) {
      const message = t('paymentNotSelected', 'Choose a payment method.');
      setStep(2);
      setGlobalMessage({ type: 'error', text: message });
      setValidationErrors({ payment_method: [message] });
      return;
    }

    setSubmitting(true);
    setGlobalMessage(null);
    setValidationErrors({});

    // Create FormData with explicit fields
    const formData = new FormData();
    formData.append('_token', csrf);
    formData.append('shipping_address_id', selectedAddressId);
    formData.append('payment_method', paymentMethod);
    formData.append('coupon_code', couponCode.trim());
    
    // Add payment proof file if selected
    if (fileInputRef.current?.files?.[0]) {
      formData.append('payment_proof', fileInputRef.current.files[0]);
    }

    try {
      const response = await fetch(processUrl, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf,
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData,
      });

      if (response.status === 422) {
        const payload = await response.json();
        setValidationErrors(payload.errors || {});
        if (payload.errors?.shipping_address_id) setStep(1);
        else if (payload.errors?.payment_method) setStep(2);
        else setStep(3);
        setGlobalMessage({ type: 'error', text: payload.message || JSON.stringify(payload.errors) || 'Validation error' });
        return;
      }

      if (!response.ok) {
        const payload = await response.json().catch(() => null);
        throw new Error(payload?.message || 'Unable to complete checkout');
      }

      const payload = await response.json().catch(() => null);
      
      if (payload?.redirect) {
        window.location.assign(payload.redirect);
        return;
      }

      if (response.redirected && response.url) {
        window.location.assign(response.url);
        return;
      }

      setGlobalMessage({ type: 'success', text: payload?.message || t('placeOrder', 'Order placed successfully.') });
    } catch (error) {
      setGlobalMessage({ type: 'error', text: error.message || t('checkout.error', 'Unable to complete checkout.') });
    } finally {
      setSubmitting(false);
    }
  };

  const statusTone = (type) => {
    if (type === 'success') return 'bg-emerald-100 text-emerald-700 border-emerald-200';
    if (type === 'error') return 'bg-rose-100 text-rose-700 border-rose-200';
    return 'bg-neutral-100 text-neutral-600 border-neutral-200';
  };

  return (

    <form
      ref={formRef}
      onSubmit={handleSubmit}
      className="grid gap-8 lg:grid-cols-[minmax(0,1.6fr)_minmax(280px,1fr)]"
      encType="multipart/form-data"
    >
      <input type="hidden" name="_token" value={csrf} />

      <div className="space-y-6">
        <div className="rounded-3xl border border-neutral-200 bg-white p-5 shadow-lg dark:border-neutral-800 dark:bg-neutral-900">
          <div className="flex flex-wrap items-center gap-4">
            {steps.map((item, index) => {
              const isCurrent = step === item.id;
              const isCompleted = step > item.id;
              return (
                <div key={item.id} className="flex items-center gap-3">
                  <div
                    className={classNames(
                      'flex h-10 w-10 items-center justify-center rounded-full text-sm font-semibold transition',
                      isCurrent
                        ? 'bg-orange-500 text-white shadow-lg'
                        : isCompleted
                        ? 'bg-emerald-500 text-white'
                        : 'bg-neutral-200 text-neutral-600 dark:bg-neutral-700 dark:text-neutral-200',
                    )}
                  >
                    {index + 1}
                  </div>
                  <div className="flex flex-col leading-tight">
                    <span className="text-xs uppercase tracking-wide text-neutral-400">
                      {t(`step${item.id}`, item.label)}
                    </span>
                    <span className="font-semibold text-neutral-900 dark:text-neutral-100">{item.label}</span>
                  </div>
                </div>
              );
            })}
          </div>
        </div>

        {globalMessage && (
          <div className={classNames('rounded-2xl border px-4 py-3 text-sm', statusTone(globalMessage.type))}>
            {globalMessage.text}
          </div>
        )}
        <section
          className={classNames(
            'rounded-3xl border border-neutral-200 bg-white shadow-xl transition dark:border-neutral-800 dark:bg-neutral-900',
            step === 1 ? 'opacity-100' : 'pointer-events-none absolute opacity-0 -z-10',
          )}
        >
          <header className="border-b border-neutral-200 px-6 py-5 dark:border-neutral-800">
            <h2 className="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
              {t('sectionAddress', 'Shipping Address')}
            </h2>
            <p className="text-sm text-neutral-600 dark:text-neutral-300">
              {t('sectionAddressHelp', 'Choose where you would like us to deliver your order.')}
            </p>
          </header>
          <div className="space-y-6 px-6 py-6">
              <div className="rounded-3xl border border-neutral-100 bg-white/90 p-5 shadow-lg ring-1 ring-black/5 dark:border-neutral-700/80 dark:bg-neutral-900/50 dark:ring-white/5">
              {addresses.length === 0 ? (
                <div className="rounded-2xl border border-dashed border-neutral-300 bg-neutral-50/80 px-4 py-6 text-center text-sm text-neutral-500 dark:border-neutral-700 dark:bg-neutral-800/60 dark:text-neutral-300">
                  {t('addressEmpty', 'You do not have any saved addresses yet.')}
                </div>
              ) : (
                <div className="grid gap-4 md:grid-cols-2">
                  {addresses.map((address) => {
                    const isActive = selectedAddressId === address.id;
                    return (
                      <label
                        key={address.id}
                        className={classNames(
                          'group relative block cursor-pointer rounded-2xl border px-5 py-4 shadow-sm transition focus-within:ring-2 focus-within:ring-orange-300 focus:outline-none',
                          isActive
                            ? 'border-orange-400 bg-orange-50/70 shadow-lg dark:border-orange-400/80 dark:bg-orange-500/10'
                            : 'border-neutral-200 bg-white hover:border-orange-200 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-900/70',
                        )}
                      >
                        <input
                          type="radio"
                          name="shipping_address_id"
                          value={address.id}
                          checked={isActive}
                          onChange={() => {
                            setSelectedAddressId(address.id);
                            setValidationErrors((prev) => ({ ...prev, shipping_address_id: undefined }));
                            setGlobalMessage(null);
                          }}
                          className="sr-only"
                          aria-checked={isActive}
                        />
                        {isActive && (
                          <span className="absolute right-4 top-4 inline-flex items-center gap-1 rounded-full bg-white/95 px-2 py-1 text-xs font-semibold text-orange-600 shadow">
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                              aria-hidden="true"
                              className="h-3.5 w-3.5"
                            >
                              <path
                                fillRule="evenodd"
                                d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.25 7.3a1 1 0 0 1-1.42.01L3.29 9.29a1 1 0 1 1 1.42-1.41l3.08 3.08 6.54-6.58a1 1 0 0 1 1.374-.09z"
                                clipRule="evenodd"
                              />
                            </svg>
                            <span>{t('selectedLabel', 'Selected')}</span>
                          </span>
                        )}
                        <div className="flex items-start justify-between gap-3">
                          <div>
                            <p className="font-semibold text-neutral-900 dark:text-neutral-100">{address.name}</p>
                            <p className="text-sm text-neutral-600 dark:text-neutral-300">{address.phone}</p>
                            <ul className="mt-2 space-y-1 text-sm text-neutral-500 dark:text-neutral-300">
                              {(address.lines || []).map((line, index) => (
                                <li key={index}>{line}</li>
                              ))}
                            </ul>
                          </div>
                          {address.is_default && (
                            <span className="rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-600 dark:text-emerald-300">
                              {t('badgePrimary', 'Primary')}
                            </span>
                          )}
                        </div>
                        <div className="mt-4 flex justify-end">
                          <button
                            type="button"
                            onClick={(event) => {
                              event.preventDefault();
                              event.stopPropagation();
                              setAddressPendingDelete(address);
                            }}
                            className="rounded-full px-3 py-1 text-xs font-semibold text-rose-600 transition hover:text-rose-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-400/60"
                          >
                            {t('deleteAddress', 'Delete address')}
                          </button>
                        </div>
                      </label>
                    );
                  })}
                </div>
              )}
            </div>

            {firstError(validationErrors, 'shipping_address_id') && (
              <p className="text-sm text-rose-600">{firstError(validationErrors, 'shipping_address_id')}</p>
            )}

            {showAddressForm && (
              <div
                role="group"
                aria-label={t('addAddress', 'Add new address')}
                onKeyDown={handleAddressKeyDown}
                className="space-y-6 rounded-2xl border border-neutral-200 bg-white px-5 py-5 shadow-inner dark:border-neutral-700 dark:bg-neutral-900/70"
              >
                <div className="rounded-2xl bg-gradient-to-r from-orange-50 to-orange-100/70 px-5 py-4 dark:from-orange-500/10 dark:to-orange-500/5">
                  <div className="flex flex-wrap items-start gap-4">
                    <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-orange-500 shadow-sm dark:bg-neutral-900">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        className="h-6 w-6"
                        aria-hidden="true"
                      >
                        <path d="M12 2a7 7 0 0 0-7 7c0 4.13 5.59 10.2 6.23 10.88a1.05 1.05 0 0 0 1.54 0C13.41 19.2 19 13.13 19 9a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 14.5 9 2.5 2.5 0 0 1 12 11.5Z" />
                      </svg>
                    </div>
                    <div className="flex-1 space-y-1">
                      <p className="text-sm font-semibold text-orange-700 dark:text-orange-300">
                        {t('addressFormTitle', 'Add shipping address')}
                      </p>
                      <p className="text-sm text-neutral-600 dark:text-neutral-200">
                        {t('addressFormHelp', 'Fill in your shipping details below.')}
                      </p>
                    </div>
                  </div>
                  <p className="mt-4 text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                    {t('addressFormRequiredHint', 'All fields are required unless marked optional.')}
                  </p>
                </div>
                <div className="grid gap-5 md:grid-cols-2">
                  <div className="space-y-2">
                    <label className="flex items-center justify-between text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                      <span>{t('fieldName', 'Recipient Name')}</span>
                      <span className={badgeClass(false)}>{t('fieldRequiredLabel', 'Required')}</span>
                    </label>
                    <input
                      type="text"
                      name="name"
                      required
                      placeholder={t('fieldNamePlaceholder', 'e.g., John Doe')}
                      value={addressForm.name}
                      onChange={handleAddressFieldChange}
                      className={inputBaseClass}
                      autoComplete="name"
                      aria-required="true"
                      aria-invalid={Boolean(firstError(addressErrors, 'name'))}
                    />
                    {firstError(addressErrors, 'name') && (
                      <p className="text-xs text-rose-600">{firstError(addressErrors, 'name')}</p>
                    )}
                  </div>
                  <div className="space-y-2">
                    <label className="flex items-center justify-between text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                      <span>{t('fieldPhone', 'Phone Number')}</span>
                      <span className={badgeClass(false)}>{t('fieldRequiredLabel', 'Required')}</span>
                    </label>
                    <input
                      type="tel"
                      name="phone"
                      required
                      value={addressForm.phone}
                      onChange={handleAddressFieldChange}
                      className={inputBaseClass}
                      placeholder={t('fieldPhonePlaceholder', '+66 812-345-678')}
                      autoComplete="tel"
                      inputMode="tel"
                      aria-required="true"
                      aria-invalid={Boolean(firstError(addressErrors, 'phone'))}
                    />
                    <p className={helperTextClass}>
                      {t('fieldPhoneHint', 'Include country code, e.g., +66 812-345-678.')}
                    </p>
                    {firstError(addressErrors, 'phone') && (
                      <p className="text-xs text-rose-600">{firstError(addressErrors, 'phone')}</p>
                    )}
                  </div>
                  <div className="md:col-span-2 space-y-2">
                    <label className="flex items-center justify-between text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                      <span>{t('fieldAddress1', 'Address Line 1')}</span>
                      <span className={badgeClass(false)}>{t('fieldRequiredLabel', 'Required')}</span>
                    </label>
                    <input
                      type="text"
                      name="address_line1"
                      required
                      value={addressForm.address_line1}
                      onChange={handleAddressFieldChange}
                      className={inputBaseClass}
                      placeholder={t('fieldAddress1Placeholder', '123 Sukhumvit Rd, Building A')}
                      autoComplete="address-line1"
                      aria-required="true"
                      aria-invalid={Boolean(firstError(addressErrors, 'address_line1'))}
                    />
                    <p className={helperTextClass}>
                      {t('fieldAddress1Hint', 'Street, building, or house number.')}
                    </p>
                    {firstError(addressErrors, 'address_line1') && (
                      <p className="text-xs text-rose-600">{firstError(addressErrors, 'address_line1')}</p>
                    )}
                  </div>
                  <div className="space-y-2">
                    <label className="flex items-center justify-between text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                      <span>{t('fieldCity', 'City')}</span>
                      <span className={badgeClass(false)}>{t('fieldRequiredLabel', 'Required')}</span>
                    </label>
                    <input
                      type="text"
                      name="city"
                      required
                      value={addressForm.city}
                      onChange={handleAddressFieldChange}
                      className={inputBaseClass}
                      placeholder={t('fieldCityPlaceholder', 'e.g., Bangkok')}
                      autoComplete="address-level2"
                      aria-required="true"
                      aria-invalid={Boolean(firstError(addressErrors, 'city'))}
                    />
                    {firstError(addressErrors, 'city') && (
                      <p className="text-xs text-rose-600">{firstError(addressErrors, 'city')}</p>
                    )}
                  </div>
                  <div className="space-y-2">
                    <label className="flex items-center justify-between text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                      <span>{t('fieldState', 'State / Province (Optional)')}</span>
                      <span className={badgeClass(true)}>{t('fieldOptionalLabel', 'Optional')}</span>
                    </label>
                    <input
                      type="text"
                      name="state"
                      value={addressForm.state}
                      onChange={handleAddressFieldChange}
                      className={inputBaseClass}
                      placeholder={t('fieldStatePlaceholder', 'e.g., Bangkok')}
                      autoComplete="address-level1"
                      aria-required="false"
                      aria-invalid={Boolean(firstError(addressErrors, 'state'))}
                    />
                    {firstError(addressErrors, 'state') && (
                      <p className="text-xs text-rose-600">{firstError(addressErrors, 'state')}</p>
                    )}
                  </div>
                  <div className="space-y-2">
                    <label className="flex items-center justify-between text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                      <span>{t('fieldCountry', 'Country')}</span>
                      <span className={badgeClass(false)}>{t('fieldRequiredLabel', 'Required')}</span>
                    </label>
                    <input
                      type="text"
                      name="country"
                      required
                      value={addressForm.country}
                      onChange={handleAddressFieldChange}
                      className={inputBaseClass}
                      placeholder={t('fieldCountryPlaceholder', 'e.g., Thailand')}
                      autoComplete="country-name"
                      aria-required="true"
                      aria-invalid={Boolean(firstError(addressErrors, 'country'))}
                    />
                    <p className={helperTextClass}>
                      {t('fieldCountryHint', 'Use the full country name.')}
                    </p>
                    {firstError(addressErrors, 'country') && (
                      <p className="text-xs text-rose-600">{firstError(addressErrors, 'country')}</p>
                    )}
                  </div>
                  <div className="space-y-2">
                    <label className="flex items-center justify-between text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                      <span>{t('fieldPostal', 'Postal Code')}</span>
                      <span className={badgeClass(false)}>{t('fieldRequiredLabel', 'Required')}</span>
                    </label>
                    <input
                      type="text"
                      name="postal_code"
                      required
                      value={addressForm.postal_code}
                      onChange={handleAddressFieldChange}
                      className={inputBaseClass}
                      placeholder={t('fieldPostalPlaceholder', '10110')}
                      autoComplete="postal-code"
                      inputMode="numeric"
                      aria-required="true"
                      aria-invalid={Boolean(firstError(addressErrors, 'postal_code'))}
                    />
                    <p className={helperTextClass}>
                      {t('fieldPostalHint', 'Numbers only, e.g., 10110.')}
                    </p>
                    {firstError(addressErrors, 'postal_code') && (
                      <p className="text-xs text-rose-600">{firstError(addressErrors, 'postal_code')}</p>
                    )}
                  </div>
                  <div className="md:col-span-2 space-y-2">
                    <label className="flex items-center justify-between text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                      <span>{t('fieldAddress2', 'Additional Details (Optional)')}</span>
                      <span className={badgeClass(true)}>{t('fieldOptionalLabel', 'Optional')}</span>
                    </label>
                    <input
                      type="text"
                      name="address_line2"
                      onChange={handleAddressFieldChange}
                      value={addressForm.address_line2}
                      className={inputBaseClass}
                      placeholder={t('fieldAddress2Placeholder', 'Apartment or delivery details')}
                      autoComplete="address-line2"
                      aria-required="false"
                      aria-invalid={Boolean(firstError(addressErrors, 'address_line2'))}
                    />
                    <p className={helperTextClass}>
                      {t('fieldAddress2Hint', 'Apartment, floor, or delivery notes (optional).')}
                    </p>
                    {firstError(addressErrors, 'address_line2') && (
                      <p className="text-xs text-rose-600">{firstError(addressErrors, 'address_line2')}</p>
                    )}
                  </div>
                  <div className="md:col-span-2 rounded-2xl border border-neutral-100 bg-neutral-50/80 px-4 py-3 dark:border-neutral-700 dark:bg-neutral-900/60">
                    <label htmlFor="address-is-default" className="flex items-start gap-3">
                      <input
                        id="address-is-default"
                        type="checkbox"
                        name="is_default"
                        checked={addressForm.is_default}
                        onChange={handleAddressFieldChange}
                        className="mt-1 h-5 w-5 rounded border-neutral-300 text-orange-500 focus:ring-orange-400"
                      />
                      <div>
                        <span className="text-sm font-semibold text-neutral-700 dark:text-neutral-200">
                          {t('makeDefault', 'Set as default address')}
                        </span>
                        <p className={helperTextClass}>
                          {t('makeDefaultHint', 'We will preselect this address for your next checkout.')}
                        </p>
                      </div>
                    </label>
                  </div>
                </div>
              </div>
            )}
            <div className="mt-8 space-y-4">
              {showAddressForm ? (
                <div className="flex flex-col gap-3 sm:flex-row">
                  <button
                    type="button"
                    className="btn-outline w-full rounded-xl px-6 py-4 text-base font-semibold transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl active:translate-y-0.5 sm:flex-1"
                    onClick={() => setShowAddressForm(false)}
                  >
                    {t('hideAddressForm', 'Hide address form')}
                  </button>
                  <button
                    type="button"
                    onClick={handleAddressSubmit}
                    className="btn-primary w-full rounded-xl px-6 py-4 text-base font-semibold transition-all duration-200 hover:-translate-y-0.5 hover:shadow-2xl active:translate-y-0.5 sm:flex-1"
                    disabled={savingAddress}
                  >
                    {savingAddress ? t('savingAddress', 'Saving...') : t('saveAddress', 'Save Address')}
                  </button>
                </div>
              ) : (
                <div className="flex flex-col gap-3 sm:flex-row">
                  <button
                    type="button"
                    className="btn-outline w-full rounded-xl px-6 py-4 text-base font-semibold transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl active:translate-y-0.5 sm:flex-1"
                    onClick={() => {
                      resetAddressForm();
                      setShowAddressForm(true);
                    }}
                  >
                    {t('addAddress', 'Add new address')}
                  </button>
                  <button
                    type="button"
                    className="btn-primary w-full rounded-xl px-6 py-4 text-base font-semibold transition-all duration-200 hover:-translate-y-0.5 hover:shadow-2xl active:translate-y-0.5 sm:flex-1"
                    onClick={handleNext}
                  >
                    {t('next', 'Next')}
                  </button>
                </div>
              )}
              <div className="text-center">
                <a
                  href={initialData.backToCartUrl}
                  className="inline-flex items-center justify-center rounded-xl px-6 py-3 text-base font-semibold text-neutral-600 transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 dark:text-neutral-200"
                >
                  {t('backToCart', 'Back to cart')}
                </a>
              </div>
              <div className="rounded-2xl border border-neutral-200 bg-neutral-50/70 px-5 py-5 text-sm text-neutral-600 shadow-inner dark:border-neutral-700 dark:bg-neutral-900/60 dark:text-neutral-200">
                <h3 className="text-sm font-semibold uppercase tracking-wide text-neutral-400 dark:text-neutral-400">
                  {t('sectionCoupon', 'Coupon Code')}
                </h3>
                <form onSubmit={handleApplyCoupon} className="mt-3 flex flex-col gap-2 sm:flex-row">
                  <input
                    type="text"
                    value={couponCode}
                    onChange={(event) => setCouponCode(event.target.value)}
                    placeholder={t('couponPlaceholder', 'Enter code')}
                    className="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm shadow-sm focus:border-orange-400 focus:ring-4 focus:ring-orange-200/60 dark:border-neutral-700 dark:bg-neutral-900"
                  />
                  <button type="submit" className="btn-primary text-sm" disabled={couponFeedback.loading}>
                    {couponFeedback.loading ? t('couponApplying', 'Applying...') : t('couponApply', 'Apply')}
                  </button>
                </form>
                {couponFeedback.message && (
                  <p
                    className={classNames(
                      'mt-2 text-sm',
                      couponFeedback.status === 'success' ? 'text-emerald-600' : 'text-rose-600',
                    )}
                  >
                    {couponFeedback.message}
                  </p>
                )}
              </div>
            </div>
          </div>
        </section>
        <section
          className={classNames(
            'rounded-3xl border border-neutral-200 bg-white shadow-xl transition dark:border-neutral-800 dark:bg-neutral-900',
            step === 2 ? 'opacity-100' : 'pointer-events-none absolute opacity-0 -z-10',
          )}
        >
          <header className="border-b border-neutral-200 px-6 py-5 dark:border-neutral-800">
            <h2 className="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
              {t('sectionPayment', 'Payment Method')}
            </h2>
            <p className="text-sm text-neutral-600 dark:text-neutral-300">
              {t('sectionPaymentHelp', 'Choose how you would like to pay for this order.')}
            </p>
          </header>
          <div className="space-y-6 px-6 py-6">
            <div className="grid gap-4 md:grid-cols-2">
              {paymentMethods.map((method) => {
                const isActive = paymentMethod === method.id;
                return (
                  <label
                    key={method.id}
                    className={classNames(
                      'block cursor-pointer rounded-2xl border px-5 py-4 shadow-sm transition',
                      isActive
                        ? 'border-orange-400 bg-orange-50/70 shadow-lg dark:border-orange-400/80 dark:bg-orange-500/10'
                        : 'border-neutral-200 bg-white/70 hover:border-orange-200 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-900/70',
                    )}
                  >
                    <input
                      type="radio"
                      name="payment_method"
                      value={method.id}
                      checked={isActive}
                      onChange={(event) => {
                        setPaymentMethod(event.target.value);
                        setValidationErrors((prev) => ({ ...prev, payment_method: undefined }));
                        setGlobalMessage(null);
                      }}
                      className="sr-only"
                    />
                    <h3 className="text-base font-semibold text-neutral-900 dark:text-neutral-100">{method.label}</h3>
                    <p className="mt-1 text-sm text-neutral-600 dark:text-neutral-300">{method.desc}</p>
                    {isActive && method.id === 'bank_transfer' && bankAccounts.length > 0 && (
                      <div className="mt-4 space-y-3 rounded-xl border border-neutral-200 bg-neutral-50/80 p-4 text-sm text-neutral-600 dark:border-neutral-700 dark:bg-neutral-900/60 dark:text-neutral-200">
                        <p className="text-xs font-semibold uppercase tracking-wide text-neutral-400 dark:text-neutral-400">
                          {t('bankDetailsTitle', 'Bank Transfer Details')}
                        </p>
                        <ul className="space-y-2">
                          {bankAccounts.map((account, index) => (
                            <li
                              key={index}
                              className="flex flex-col rounded-lg bg-white/70 px-3 py-2 shadow-inner dark:bg-neutral-900/70"
                            >
                              <span className="font-semibold text-neutral-900 dark:text-neutral-100">{account.bank}</span>
                              <span className="text-sm text-neutral-600 dark:text-neutral-300">{account.account}</span>
                              <span className="text-xs text-neutral-400 dark:text-neutral-400">{account.holder}</span>
                              {account.is_primary && (
                                <span className="mt-1 inline-flex w-fit rounded-full bg-emerald-500/20 px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wide text-emerald-600 dark:text-emerald-300">
                                  {t('badgeRecommended', 'Recommended')}
                                </span>
                              )}
                            </li>
                          ))}
                        </ul>
                        <p className="text-xs text-neutral-500 dark:text-neutral-400">
                          {t('bankDetailsNote', 'Upload the transfer receipt so we can verify your payment quickly.')}
                        </p>
                      </div>
                    )}
                  </label>
                );
              })}
            </div>

            {firstError(validationErrors, 'payment_method') && (
              <p className="text-sm text-rose-600">{firstError(validationErrors, 'payment_method')}</p>
            )}

            <div className="space-y-3 rounded-2xl border border-neutral-200 bg-neutral-50/80 px-5 py-5 shadow-inner dark:border-neutral-700 dark:bg-neutral-900/60">
              <label className="text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                {t('paymentProofTitle', 'Upload payment proof (Optional)')}
              </label>
              <input
                ref={fileInputRef}
                type="file"
                name="payment_proof"
                accept="image/*"
                onChange={(event) => {
                  const file = event.target.files?.[0];
                  setPaymentProofName(file ? file.name : null);
                  setValidationErrors((prev) => ({ ...prev, payment_proof: undefined }));
                }}
                className="block w-full cursor-pointer rounded-xl border border-neutral-300 bg-white px-4 py-3 text-sm shadow-sm file:mr-4 file:rounded-lg file:border-none file:bg-orange-500 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-orange-600 dark:border-neutral-700 dark:bg-neutral-900"
              />
              <p className="text-xs text-neutral-500 dark:text-neutral-400">
                {t('paymentProofHint', 'Accepted formats: JPG or PNG up to 2 MB.')}
              </p>
              {paymentProofName && (
                <p className="text-xs text-neutral-500 dark:text-neutral-300">
                  {t('paymentProofName', 'Selected file')}: {paymentProofName}
                </p>
              )}
              {firstError(validationErrors, 'payment_proof') && (
                <p className="text-xs text-rose-600">{firstError(validationErrors, 'payment_proof')}</p>
              )}
            </div>

            <div className="pt-2 space-y-4">
              <div className="flex flex-col gap-3 sm:flex-row">
                <button
                  type="button"
                  onClick={handlePrevious}
                  className="btn-outline w-full rounded-xl px-6 py-4 text-base font-semibold transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl active:translate-y-0.5 sm:flex-1"
                >
                  {t('previous', 'Previous')}
                </button>
                <button
                  type="button"
                  onClick={handleNext}
                  className="btn-primary w-full rounded-xl px-6 py-4 text-base font-semibold transition-all duration-200 hover:-translate-y-0.5 hover:shadow-2xl active:translate-y-0.5 sm:flex-1"
                >
                  {t('next', 'Next')}
                </button>
              </div>
              <div className="text-center">
                <a
                  href={initialData.backToCartUrl}
                  className="inline-flex items-center justify-center rounded-xl px-6 py-3 text-base font-semibold text-neutral-600 transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 dark:text-neutral-200"
                >
                  {t('backToCart', 'Back to cart')}
                </a>
              </div>
            </div>
          </div>
        </section>
        <section
          className={classNames(
            'rounded-3xl border border-neutral-200 bg-white shadow-xl transition dark:border-neutral-800 dark:bg-neutral-900',
            step === 3 ? 'opacity-100' : 'pointer-events-none absolute opacity-0 -z-10',
          )}
        >
          <header className="border-b border-neutral-200 px-6 py-5 dark:border-neutral-800">
            <h2 className="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
              {t('sectionReview', 'Review Order')}
            </h2>
            <p className="text-sm text-neutral-600 dark:text-neutral-300">
              {t('sectionReviewHelp', 'Make sure all information is correct before placing your order.')}
            </p>
          </header>
          <div className="space-y-6 px-6 py-6">
            <div className="rounded-2xl border border-neutral-200 bg-white px-5 py-4 shadow-inner dark:border-neutral-700 dark:bg-neutral-900/60">
              <h3 className="text-sm font-semibold uppercase tracking-wide text-neutral-400 dark:text-neutral-400">
                {t('sectionReviewAddress', 'Shipping Address')}
              </h3>
              {selectedAddress ? (
                <div className="mt-2 text-sm text-neutral-600 dark:text-neutral-200">
                  <p className="font-medium text-neutral-900 dark:text-neutral-100">{selectedAddress.name}</p>
                  <p>{selectedAddress.phone}</p>
                  <ul className="mt-1 space-y-1">
                    {(selectedAddress.lines || []).map((line, index) => (
                      <li key={index}>{line}</li>
                    ))}
                  </ul>
                </div>
              ) : (
                <p className="mt-2 text-sm text-rose-600">{t('addressMissing', 'No address selected.')}</p>
              )}
            </div>

            <div className="rounded-2xl border border-neutral-200 bg-white px-5 py-4 shadow-inner dark:border-neutral-700 dark:bg-neutral-900/60">
              <h3 className="text-sm font-semibold uppercase tracking-wide text-neutral-400 dark:text-neutral-400">
                {t('sectionReviewPayment', 'Payment Method')}
              </h3>
              <p className="mt-2 text-sm text-neutral-600 dark:text-neutral-200">
                {paymentMethods.find((method) => method.id === paymentMethod)?.label ||
                  t('paymentNotSelected', 'Not selected')}
              </p>
            </div>

            <div className="pt-2 space-y-4">
              <div className="flex flex-col gap-3 sm:flex-row">
                <button
                  type="button"
                  onClick={handlePrevious}
                  className="btn-outline w-full rounded-xl px-6 py-4 text-base font-semibold transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl active:translate-y-0.5 sm:flex-1"
                >
                  {t('previous', 'Previous')}
                </button>
                <button
                  type="submit"
                  className="btn-primary w-full rounded-xl px-6 py-4 text-base font-semibold transition-all duration-200 hover:-translate-y-0.5 hover:shadow-2xl active:translate-y-0.5 sm:flex-1"
                  disabled={submitting}
                >
                  {submitting ? t('processingOrder', 'Processing...') : t('placeOrder', 'Place Order')}
                </button>
              </div>
              <div className="text-center">
                <a
                  href={initialData.backToCartUrl}
                  className="inline-flex items-center justify-center rounded-xl px-6 py-3 text-base font-semibold text-neutral-600 transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 dark:text-neutral-200"
                >
                  {t('backToCart', 'Back to cart')}
                </a>
              </div>
            </div>
          </div>
        </section>
      </div>

      <aside className="space-y-5">
        <div className="rounded-3xl border border-neutral-200 bg-white shadow-xl dark:border-neutral-800 dark:bg-neutral-900">
          <div className="border-b border-neutral-200 px-6 py-5 dark:border-neutral-800">
            <h3 className="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
              {t('summaryTitle', 'Order Summary')}
            </h3>
            <p className="text-sm text-neutral-600 dark:text-neutral-300">
              {t('summarySubtitle', 'Review the items currently in your cart.')}
            </p>
          </div>
          <div className="divide-y divide-neutral-200/70 px-6 dark:divide-neutral-700/80">
            {initialItems.map((item) => (
              <div key={item.id} className="flex gap-4 py-4">
                <img
                  src={item.image}
                  alt={item.name}
                  className="h-16 w-16 rounded-2xl border border-neutral-200 object-cover dark:border-neutral-700"
                  loading="lazy"
                />
                <div className="flex-1 space-y-1 text-sm">
                  <p className="font-semibold text-neutral-900 dark:text-neutral-100">{item.name}</p>
                  {item.brand && (
                    <p className="text-xs uppercase tracking-wide text-neutral-400 dark:text-neutral-400">{item.brand}</p>
                  )}
                  {item.color && (
                    <p className="text-xs text-neutral-500 dark:text-neutral-400">
                      {t('summaryColor', 'Color')}: {item.color}
                    </p>
                  )}
                  <p className="text-sm text-neutral-600 dark:text-neutral-300">
                    {item.quantity} × {formatCurrency(item.price)}
                  </p>
                </div>
                <div className="font-semibold text-neutral-900 dark:text-neutral-100">
                  {formatCurrency(item.subtotal)}
                </div>
              </div>
            ))}
          </div>
          <div className="space-y-3 border-t border-neutral-200 px-6 py-5 text-sm dark:border-neutral-800">
            <div className="flex items-center justify-between text-neutral-600 dark:text-neutral-300">
              <span>{t('summarySubtotal', 'Subtotal')}</span>
              <span>{formatCurrency(subtotal)}</span>
            </div>
            <div className="flex items-center justify-between text-neutral-600 dark:text-neutral-300">
              <span>{t('summaryShipping', 'Shipping')}</span>
              <span>{formatCurrency(shipping)}</span>
            </div>
            <div className="flex items-center justify-between text-emerald-600 dark:text-emerald-300">
              <span>{t('summaryDiscount', 'Discount')}</span>
              <span>-{formatCurrency(discountAmount)}</span>
            </div>
            <div className="flex items-center justify-between border-t border-neutral-200 pt-4 text-base font-semibold text-neutral-900 dark:border-neutral-800 dark:text-neutral-100">
              <span>{t('summaryTotal', 'Total')}</span>
              <span>{formatCurrency(total)}</span>
            </div>
          </div>
        </div>

        <div className="rounded-3xl border border-neutral-200 bg-white px-6 py-6 mt-6 text-neutral-900 shadow-xl dark:border-neutral-800 dark:bg-gradient-to-br dark:from-neutral-900 dark:via-neutral-950 dark:to-neutral-900 dark:text-white">
          <h4 className="text-base font-semibold">{t('securityTitle', 'Secure Checkout')}</h4>
          <p className="mt-2 text-sm text-neutral-600 dark:text-white/70">
            {t('securityCopy', 'Your transaction is protected with enterprise-grade security.')}
          </p>
          <ul className="mt-4 space-y-2 text-xs text-neutral-600 dark:text-white/60">
            <li>- 256-bit SSL encryption</li>
            <li>- Fraud monitoring & verification</li>
            <li>- Dedicated customer support</li>
          </ul>
        </div>
      </aside>

      {addressPendingDelete && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
          <div
            role="dialog"
            aria-modal="true"
            className="w-full max-w-md rounded-2xl border border-neutral-200 bg-white p-6 shadow-2xl dark:border-neutral-700 dark:bg-neutral-900"
          >
            <h3 className="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
              {t('deleteAddressConfirmTitle', 'Delete this address?')}
            </h3>
            <p className="mt-2 text-sm text-neutral-600 dark:text-neutral-300">
              {t(
                'deleteAddressConfirmText',
                'This address will be removed permanently and cannot be used again.',
              )}
            </p>
            <div className="mt-4 rounded-2xl border border-neutral-100 bg-neutral-50/80 px-4 py-3 text-sm text-neutral-600 dark:border-neutral-700 dark:bg-neutral-900/60 dark:text-neutral-200">
              <p className="font-semibold text-neutral-900 dark:text-neutral-100">{addressPendingDelete.name}</p>
              <p>{addressPendingDelete.phone}</p>
              <ul className="mt-1 space-y-1">
                {(addressPendingDelete.lines || []).map((line, index) => (
                  <li key={index}>{line}</li>
                ))}
              </ul>
            </div>
            <div className="mt-6 flex justify-end gap-3">
              <button
                type="button"
                className="btn-ghost text-sm"
                onClick={() => {
                  if (deletingAddress) return;
                  setAddressPendingDelete(null);
                }}
              >
                {t('deleteAddressCancel', 'Cancel')}
              </button>
              <button
                type="button"
                className="btn-outline text-sm text-rose-600 hover:border-rose-600 hover:text-rose-700"
                onClick={handleDeleteAddress}
                disabled={deletingAddress}
              >
                {deletingAddress ? t('deleteAddressLoading', 'Deleting...') : t('deleteAddressConfirmAction', 'Delete')}
              </button>
            </div>
          </div>
        </div>
      )}
    </form>
  );
};

export default CheckoutApp;
