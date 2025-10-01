@extends('layouts.admin')

@section('title', __('Dashboard'))

@section('content')
  <x-admin.header :title="__('Dashboard')" :breadcrumbs="[['label'=>'Admin','href'=>route('admin.dashboard')],['label'=>__('Dashboard')]]">
    <form method="GET" action="#" class="flex items-center gap-2">
      <input type="text" disabled placeholder="Search..." class="h-10 w-64 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 text-sm opacity-50" />
      <a href="#" class="px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-sm">Unduh Laporan</a>
    </form>
  </x-admin.header>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
      <div class="text-sm text-gray-500">Users</div>
      <div class="text-2xl font-semibold">{{ $totalUsers }}</div>
    </div>
    <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
      <div class="text-sm text-gray-500">Products</div>
      <div class="text-2xl font-semibold">{{ $totalProducts }}</div>
    </div>
    <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
      <div class="text-sm text-gray-500">Payments</div>
      <div class="text-2xl font-semibold">{{ $totalPayments }}</div>
    </div>
    <div class="p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
      <div class="text-sm text-gray-500">Active Banners</div>
      <div class="text-2xl font-semibold">{{ $activeBanners }}</div>
    </div>
  </div>

  <div class="mt-6 p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-semibold">Revenue (last 7 days)</h3>
    </div>
    <div id="revenueChart" class="w-full" style="height: 300px;"></div>
  </div>
@endsection

@push('head')
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endpush

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const options = {
        chart: { type: 'line', height: 300, toolbar: { show: false } },
        series: [{ name: 'Revenue', data: @json($revenueData ?? []) }],
        xaxis: { categories: @json($revenueLabels ?? []) },
        stroke: { curve: 'smooth', width: 3 },
        colors: ['#10b981'],
        grid: { borderColor: '#f3f4f6' }
      };
      const el = document.querySelector('#revenueChart');
      if (el) {
        const chart = new ApexCharts(el, options);
        chart.render();
      }
    });
  </script>
@endpush
