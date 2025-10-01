@extends('layouts.admin')
@section('title', 'Payments')
@section('content')
<x-admin.header title="Payments" :breadcrumbs="[['label'=>'Admin','href'=>route('admin.dashboard')],['label'=>'Payments']]">
  <form method="GET" action="{{ route('admin.payments.index') }}" class="flex items-center gap-2">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search payments..." class="h-10 w-64 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 text-sm" />
    @if(request('q'))
      <a href="{{ route('admin.payments.index') }}" class="text-sm text-gray-600">Clear</a>
    @endif
    <a href="{{ route('admin.payments.index', array_filter(['q'=>request('q'),'export'=>'csv'])) }}" class="px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-sm">Export CSV</a>
  </form>
</x-admin.header>
<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
  <table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
      <tr>
        <th class="px-4 py-2 text-left">Date</th>
        <th class="px-4 py-2 text-left">User</th>
        <th class="px-4 py-2 text-left">Amount</th>
        <th class="px-4 py-2 text-left">Status</th>
        <th class="px-4 py-2"></th>
      </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
      @foreach($payments as $payment)
      <tr>
        <td class="px-4 py-2">{{ $payment->created_at->format('Y-m-d H:i') }}</td>
        <td class="px-4 py-2">{{ $payment->user->name ?? '-' }}</td>
        <td class="px-4 py-2">${{ number_format($payment->amount, 2) }}</td>
        <td class="px-4 py-2">
          @if($payment->status === 'paid')
            <span class="text-green-600">Paid</span>
          @elseif($payment->status === 'failed')
            <span class="text-red-600">Failed</span>
          @else
            <span class="text-yellow-600">{{ ucfirst($payment->status) }}</span>
          @endif
        </td>
        <td class="px-4 py-2 text-right">
          <a class="text-blue-600 hover:underline mr-3" href="{{ route('admin.payments.show', $payment) }}">View</a>
          <form action="{{ route('admin.payments.destroy', $payment) }}" method="POST" class="inline" onsubmit="return confirm('Delete this payment?')">
            @csrf @method('DELETE')
            <button class="text-red-600 hover:underline">Delete</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $payments->links() }}</div>
@endsection
