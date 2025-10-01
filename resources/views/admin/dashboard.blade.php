@extends('layouts.admin')

@section('title', __('Dashboard'))

@section('content')
  <div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-2 text-sm text-gray-500">
      <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700">{{ __('Dashboard') }}</span>
      <span class="text-gray-400">›</span>
      <span class="text-gray-400">Ringkasan</span>
    </div>
    <div class="flex items-center gap-2">
      <button class="h-9 px-3 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50">Unduh Laporan</button>
      <button class="h-9 px-3 rounded-xl bg-gray-900 text-white hover:bg-gray-800 flex items-center gap-2">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h18v4H3zM3 13h18v8H3zM7 13v8M17 13v8"/></svg>
        Lihat Analitik
      </button>
    </div>
  </div>
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
        series: [{ name: 'Revenue', data: @json($revenueData) }],
        xaxis: { categories: @json($revenueLabels) },
        stroke: { curve: 'smooth', width: 3 },
        colors: ['#10b981'],
        grid: { borderColor: '#f3f4f6' }
      };
      const chart = new ApexCharts(document.querySelector('#revenueChart'), options);
      chart.render();
    });
  </script>
@endpush
