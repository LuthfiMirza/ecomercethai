@extends('layouts.admin')
@section('title', 'Payment Detail')
@section('content')
<div class="max-w-3xl space-y-4">
  <a href="{{ route('admin.payments.index') }}" class="text-blue-600 hover:underline">← Back to payments</a>
  <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
    <h2 class="text-lg font-semibold mb-2">Payment #{{ $payment->id }}</h2>
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <dt class="text-sm text-gray-500">User</dt>
        <dd class="font-medium">{{ $payment->user->name ?? '-' }}</dd>
      </div>
      <div>
        <dt class="text-sm text-gray-500">Amount</dt>
        <dd class="font-medium">${{ number_format($payment->amount, 2) }}</dd>
      </div>
      <div>
        <dt class="text-sm text-gray-500">Status</dt>
        <dd class="font-medium">{{ ucfirst($payment->status) }}</dd>
      </div>
      <div>
        <dt class="text-sm text-gray-500">Method</dt>
        <dd class="font-medium">{{ $payment->method ?? '-' }}</dd>
      </div>
      <div>
        <dt class="text-sm text-gray-500">Reference</dt>
        <dd class="font-medium">{{ $payment->reference ?? '-' }}</dd>
      </div>
      <div>
        <dt class="text-sm text-gray-500">Paid At</dt>
        <dd class="font-medium">{{ optional($payment->paid_at)->format('Y-m-d H:i') ?? '-' }}</dd>
      </div>
    </dl>
    @if($payment->meta)
    <div class="mt-4">
      <h3 class="text-sm text-gray-500 mb-1">Meta</h3>
      <pre class="text-sm bg-gray-50 p-3 rounded border border-gray-200">{{ json_encode($payment->meta, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
    </div>
    @endif
  </div>
</div>
@endsection
