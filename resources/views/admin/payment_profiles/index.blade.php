@extends('layouts.admin')
@section('title', 'Payment Profiles')
@section('content')
<x-admin.header title="Payment Profiles" :breadcrumbs="[['label'=>'Admin','href'=>route('admin.dashboard')],['label'=>'Payment Profiles']]">
  <form method="GET" action="{{ route('admin.payment-profiles.index') }}" class="flex items-center gap-2">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search profiles..." class="h-10 w-64 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 text-sm" />
    @if(request('q'))
      <a href="{{ route('admin.payment-profiles.index') }}" class="text-sm text-gray-600">Clear</a>
    @endif
    <a href="{{ route('admin.payment-profiles.create') }}" class="px-3 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">New Profile</a>
  </form>
</x-admin.header>
<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
  <table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
      <tr>
        <th class="px-4 py-2 text-left">User</th>
        <th class="px-4 py-2">Provider</th>
        <th class="px-4 py-2">Account</th>
        <th class="px-4 py-2">Default</th>
        <th class="px-4 py-2"></th>
      </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200/70 dark:divide-gray-700">
      @foreach($profiles as $profile)
      <tr>
        <td class="px-4 py-2">{{ $profile->user->name ?? '-' }}</td>
        <td class="px-4 py-2">{{ $profile->provider }}</td>
        <td class="px-4 py-2">{{ $profile->account_name }} {{ $profile->account_number ? '(' . $profile->account_number . ')' : '' }}</td>
        <td class="px-4 py-2">{!! $profile->is_default ? '<span class="text-green-600">Yes</span>' : '<span class="text-gray-400">No</span>' !!}</td>
        <td class="px-4 py-2 text-right">
          <a href="{{ route('admin.payment-profiles.show', $profile) }}" class="text-slate-600 hover:underline mr-3">View</a>
          <a href="{{ route('admin.payment-profiles.edit', $profile) }}" class="text-blue-600 hover:underline mr-3">Edit</a>
          <form action="{{ route('admin.payment-profiles.destroy', $profile) }}" method="POST" class="inline" onsubmit="return confirm('Delete this profile?')">
            @csrf @method('DELETE')
            <button class="text-red-600 hover:underline">Delete</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $profiles->links() }}</div>
@endsection
