@extends('layouts.admin')

@section('header', 'Payment Profile Detail')

@section('content')
<div class="mx-auto max-w-screen-xl p-4 md:p-6 2xl:p-10">
  <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
    <div class="space-y-1">
      <h3 class="text-2xl font-bold text-black dark:text-white">{{ $profile->provider }}</h3>
      <p class="text-sm text-slate-500 dark:text-slate-300">
        For {{ $profile->user->name ?? 'Unknown user' }} &bull; Created {{ $profile->created_at?->format('d M Y H:i') }}
      </p>
    </div>
    <div class="flex flex-wrap gap-2">
      <a href="{{ route('admin.payment-profiles.index') }}" class="btn-ghost text-sm">Back to list</a>
      <a href="{{ route('admin.payment-profiles.create') }}" class="btn-outline text-sm">Add Profile</a>
      <a href="{{ route('admin.payment-profiles.edit', $profile) }}" class="btn-primary text-sm">Edit Profile</a>
    </div>
  </div>

  <div class="mt-6 grid grid-cols-12 gap-4 md:gap-6 2xl:gap-7.5">
    <div class="col-span-12 xl:col-span-6 space-y-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <h4 class="text-lg font-semibold text-black dark:text-white mb-3">Profile Info</h4>
        <dl class="space-y-3 text-sm text-slate-600 dark:text-slate-300">
          <div>
            <dt class="font-medium text-slate-500 dark:text-slate-400">Account Holder</dt>
            <dd>{{ $profile->account_name ?? $profile->user->name ?? '—' }}</dd>
          </div>
          <div>
            <dt class="font-medium text-slate-500 dark:text-slate-400">Account Number</dt>
            <dd>{{ $profile->account_number ?? '—' }}</dd>
          </div>
          <div>
            <dt class="font-medium text-slate-500 dark:text-slate-400">Default</dt>
            <dd><span class="badge {{ $profile->is_default ? 'badge-success' : 'badge-neutral' }}">{{ $profile->is_default ? 'Primary method' : 'Secondary' }}</span></dd>
          </div>
        </dl>
      </div>
    </div>
    <div class="col-span-12 xl:col-span-6 space-y-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <h4 class="text-lg font-semibold text-black dark:text-white mb-3">Customer Snapshot</h4>
        @if($profile->user)
          <div class="flex items-center gap-3">
            <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($profile->user->name) }}&background=random" alt="{{ $profile->user->name }}">
            <div>
              <p class="font-medium text-slate-800 dark:text-slate-100">{{ $profile->user->name }}</p>
              <p class="text-sm text-slate-500">{{ $profile->user->email }}</p>
            </div>
          </div>
          <div class="mt-4 grid grid-cols-2 gap-3">
            <div class="rounded-xl border border-slate-200 px-4 py-3 text-sm dark:border-slate-700">
              <p class="text-xs uppercase tracking-wide text-slate-400">Joined</p>
              <p class="font-medium text-slate-700 dark:text-slate-200">{{ $profile->user->created_at?->format('d M Y') }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 px-4 py-3 text-sm dark:border-slate-700">
              <p class="text-xs uppercase tracking-wide text-slate-400">Payments</p>
              <p class="font-medium text-slate-700 dark:text-slate-200">{{ $paymentsCount }}</p>
            </div>
          </div>
        @else
          <p class="text-sm text-slate-500">User record no longer exists.</p>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
