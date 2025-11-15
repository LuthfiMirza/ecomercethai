@extends('layouts.admin')

@section('header', 'Promo Detail')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
  <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
    <div class="space-y-1">
      <h3 class="text-2xl font-bold text-black dark:text-white">{{ $coupon->code }}</h3>
      <p class="text-sm text-slate-500 dark:text-slate-300">
        {{ ucfirst($coupon->discount_type) }} discount &bull; Created {{ $coupon->created_at?->format('d M Y H:i') }}
      </p>
    </div>
    <div class="flex flex-wrap gap-2">
      <a href="{{ localized_route('admin.promos.index') }}" class="btn-ghost text-sm">Back to list</a>
      <a href="{{ localized_route('admin.promos.create') }}" class="btn-outline text-sm">Add Promo</a>
      <a href="{{ localized_route('admin.promos.edit', ['id' => $coupon->id]) }}" class="btn-primary text-sm">Edit Promo</a>
    </div>
  </div>

  <div class="mt-6 grid grid-cols-12 gap-4 md:gap-6 2xl:gap-7.5">
    <div class="col-span-12 xl:col-span-6 space-y-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <h4 class="text-lg font-semibold text-black dark:text-white mb-3">Promo Overview</h4>
        <dl class="space-y-3 text-sm text-slate-600 dark:text-slate-300">
          <div class="flex justify-between">
            <dt class="font-medium text-slate-500 dark:text-slate-400">Discount Value</dt>
            <dd>
              @if($coupon->discount_type === 'percent')
                {{ $coupon->discount_value }}%
              @else
                {{ format_price($coupon->discount_value ?? 0) }}
              @endif
            </dd>
          </div>
          <div class="flex justify-between">
            <dt class="font-medium text-slate-500 dark:text-slate-400">Usage</dt>
            <dd>{{ $coupon->used_count ?? 0 }} / {{ $coupon->usage_limit ?? '∞' }}</dd>
          </div>
          <div class="flex justify-between">
            <dt class="font-medium text-slate-500 dark:text-slate-400">Status</dt>
            <dd><span class="badge {{ ($coupon->status ?? 'inactive') === 'active' ? 'badge-success' : 'badge-neutral' }}">{{ ucfirst($coupon->status ?? 'inactive') }}</span></dd>
          </div>
          <div>
            <dt class="font-medium text-slate-500 dark:text-slate-400">Validity Period</dt>
            <dd>{{ optional($coupon->starts_at)->format('d M Y') ?? '—' }} – {{ optional($coupon->ends_at)->format('d M Y') ?? '—' }}</dd>
          </div>
        </dl>
      </div>
    </div>
    <div class="col-span-12 xl:col-span-6 space-y-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <h4 class="text-lg font-semibold text-black dark:text-white mb-3">Conditions</h4>
        <dl class="space-y-3 text-sm text-slate-600 dark:text-slate-300">
          <div>
            <dt class="font-medium text-slate-500 dark:text-slate-400">Minimum Purchase</dt>
            <dd>{{ $coupon->min_purchase ? format_price($coupon->min_purchase) : 'Not set' }}</dd>
          </div>
          <div>
            <dt class="font-medium text-slate-500 dark:text-slate-400">Maximum Discount</dt>
            <dd>{{ $coupon->max_discount ? format_price($coupon->max_discount) : 'Unlimited' }}</dd>
          </div>
          <div>
            <dt class="font-medium text-slate-500 dark:text-slate-400">Notes</dt>
            <dd>{!! $coupon->description ? nl2br(e($coupon->description)) : '<span class="text-slate-400">No additional notes.</span>' !!}</dd>
          </div>
        </dl>
      </div>
    </div>
  </div>
</div>
@endsection
