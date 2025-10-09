@extends('layouts.admin')

@section('header', 'Promos')

@section('content')
    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        <x-table
          title="Promo List"
          subtitle="Manage promotional campaigns and discounts"
          add-url="{{ localized_route('admin.promos.create') }}"
          add-label="Add New Promo"
          :pagination="$coupons"
          :search="true"
          search-placeholder="Search promos..."
          :search-value="$q ?? ''"
          action="{{ localized_route('admin.promos.index') }}"
        >
          <x-slot:filters>
            <div>
              <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-300">Status</label>
              <select name="status" class="w-full rounded-lg border border-slate-200 bg-white py-2 pl-3 pr-8 text-sm focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                <option value="">All Statuses</option>
                <option value="active" @selected(($status ?? '')==='active')>Active</option>
                <option value="inactive" @selected(($status ?? '')==='inactive')>Inactive</option>
              </select>
            </div>
          </x-slot:filters>

          <x-slot:head>
            <tr>
              <th>Promo Code</th>
              <th>Type</th>
              <th>Value</th>
              <th>Valid</th>
              <th>Used</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </x-slot:head>

          <x-slot:body>
            @forelse($coupons as $c)
              <tr>
                <td class="font-medium text-slate-900 dark:text-slate-200">{{ $c->code }}</td>
                <td>{{ ucfirst($c->discount_type) }}</td>
                <td>{{ $c->discount_type==='percent' ? $c->discount_value.'%' : 'Rp '.number_format($c->discount_value,0,',','.') }}</td>
                <td>{{ optional($c->starts_at)->format('d M Y') }} - {{ optional($c->ends_at)->format('d M Y') }}</td>
                <td>{{ $c->used_count }}/{{ $c->usage_limit ?? '∞' }}</td>
                <td><span class="badge {{ $c->status==='active' ? 'badge-success' : 'badge-neutral' }}">{{ ucfirst($c->status) }}</span></td>
                <td class="cell-actions">
          <a href="{{ localized_route('admin.promos.show', ['id' => $c->id]) }}" class="btn-ghost text-xs">View</a>
          <a href="{{ localized_route('admin.promos.edit', ['id' => $c->id]) }}" class="btn-outline text-xs">Edit</a>
          <x-confirm-delete action="{{ localized_route('admin.promos.destroy', ['id' => $c->id]) }}">Delete</x-confirm-delete>
                </td>
              </tr>
            @empty
              <tr><td colspan="7" class="py-6 text-center text-slate-500">No promos found.</td></tr>
            @endforelse
          </x-slot:body>
        </x-table>
    </div>
@endsection
