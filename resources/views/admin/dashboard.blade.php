@extends('layouts.admin')

@section('header', 'Dashboard')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
  <!-- Top row: KPI cards + Gauge -->
  <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
    <!-- Customers -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 md:p-6 dark:border-gray-800 dark:bg-white/[0.03] min-h-[180px]">
      <div class="flex items-start justify-between">
        <div class="flex items-center gap-3 text-slate-500">
          <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 21H3v-1a6 6 0 0112 0v1zm6 0h-6v-1a6 6 0 0112 0v1zM12 11a4 4 0 110-8 4 4 0 010 8zM21 11a4 4 0 10-8 0 4 4 0 008 0z"/></svg>
          </div>
          <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">Customers</p>
            <p class="mt-1 text-3xl font-semibold text-slate-900 dark:text-white">{{ number_format($totalCustomers ?? 0) }}</p>
          </div>
        </div>
        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
          <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M5 10l5-5 5 5H5z"/></svg>
          {{ $customersGrowth >= 0 ? $customersGrowth : 0 }}%
        </span>
      </div>
    </div>

    <!-- Orders -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 md:p-6 dark:border-gray-800 dark:bg-white/[0.03] min-h-[180px]">
      <div class="flex items-start justify-between">
        <div class="flex items-center gap-3 text-slate-500">
          <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-2 7h12m-6-7V6"/></svg>
          </div>
          <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">Orders</p>
            <p class="mt-1 text-3xl font-semibold text-slate-900 dark:text-white">{{ number_format($totalOrders ?? 0) }}</p>
          </div>
        </div>
        <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2 py-0.5 text-xs font-semibold text-rose-700 dark:bg-rose-900/30 dark:text-rose-300">
          <svg class="h-3 w-3 rotate-180" viewBox="0 0 20 20" fill="currentColor"><path d="M5 10l5-5 5 5H5z"/></svg>
          {{ $ordersGrowth }}%
        </span>
      </div>
    </div>

    <!-- Monthly Target Gauge -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 md:p-6 dark:border-gray-800 dark:bg-white/[0.03] min-h-[180px]">
      <div class="flex items-start justify-between">
        <div>
          <h4 class="text-lg font-semibold text-slate-900 dark:text-white">Monthly Target</h4>
          <p class="text-sm text-slate-500 dark:text-slate-400">Target you’ve set for each month</p>
        </div>
        <button class="btn-ghost" type="button">•••</button>
      </div>
      <div class="mt-4">
        <div class="relative h-[100px] overflow-hidden" style="contain: layout size;">
          <canvas id="gaugeMonthlyTarget" class="absolute inset-0 w-full h-full max-h-[100px]"></canvas>
        </div>
      </div>
      <div class="mt-4 text-center hidden">
        <p class="text-xs text-emerald-600 dark:text-emerald-400 inline-flex items-center gap-1"><svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M5 10l5-5 5 5H5z"/></svg> +{{ max(0, round($gaugePercent,2)) }}%</p>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">You earn Rp {{ number_format($todayRevenue,0,',','.') }} today, it’s higher than last month.</p>
      </div>
      <div class="mt-6 grid grid-cols-3 divide-x divide-slate-200 rounded-xl bg-slate-50 p-4 text-center dark:divide-slate-700 dark:bg-slate-800/50 hidden">
        <div>
          <p class="text-xs text-slate-500 dark:text-slate-400">Target</p>
          <p class="text-base font-semibold text-slate-900 dark:text-white">Rp {{ number_format($target,0,',','.') }}</p>
        </div>
        <div>
          <p class="text-xs text-slate-500 dark:text-slate-400">Revenue</p>
          <p class="text-base font-semibold text-slate-900 dark:text-white">Rp {{ number_format($currentMonthRevenue,0,',','.') }}</p>
        </div>
        <div>
          <p class="text-xs text-slate-500 dark:text-slate-400">Today</p>
          <p class="text-base font-semibold text-slate-900 dark:text-white">Rp {{ number_format($todayRevenue,0,',','.') }}</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Middle row: Monthly Sales -->
  <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700/60 dark:bg-slate-900">
      <div class="mb-4 flex items-center justify-between">
        <h4 class="text-lg font-semibold text-slate-900 dark:text-white">Monthly Sales</h4>
        <button type="button" class="btn-ghost">•••</button>
      </div>
      <div class="relative h-[240px] overflow-hidden" style="contain: layout size;">
        <canvas id="barMonthlySales" class="absolute inset-0 w-full h-full max-h-[240px]"></canvas>
      </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700/60 dark:bg-slate-900">
      <div class="mb-2 flex items-center justify-between">
        <div>
          <h4 class="text-lg font-semibold text-slate-900 dark:text-white">Revenue by Category</h4>
          <p class="text-xs text-slate-500 dark:text-slate-400">Performance by product category</p>
        </div>
        <button type="button" class="btn-ghost">•••</button>
      </div>
      <div class="relative h-[220px] overflow-hidden" style="contain: layout size;">
        <canvas id="categoryChartDash" class="absolute inset-0 w-full h-full max-h-[220px]"></canvas>
      </div>
    </div>
  </div>

  <!-- Statistics -->
  <div id="statsCard" class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700/60 dark:bg-slate-900" x-data="{tab:'overview'}">
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h4 class="text-lg font-semibold text-slate-900 dark:text-white">Statistics</h4>
        <p class="text-xs text-slate-500 dark:text-slate-400">Target you’ve set for each month</p>
      </div>
      <div class="flex items-center gap-2">
        <div class="inline-flex rounded-full border border-slate-200 p-1 dark:border-slate-700">
          <button data-tab="overview" @click="tab='overview'" :class="tab==='overview' ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white' : 'text-slate-600 dark:text-slate-300'" class="rounded-full px-3 py-1 text-xs font-medium">Overview</button>
          <button data-tab="sales" @click="tab='sales'" :class="tab==='sales' ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white' : 'text-slate-600 dark:text-slate-300'" class="rounded-full px-3 py-1 text-xs font-medium">Sales</button>
          <button data-tab="revenue" @click="tab='revenue'" :class="tab==='revenue' ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white' : 'text-slate-600 dark:text-slate-300'" class="rounded-full px-3 py-1 text-xs font-medium">Revenue</button>
        </div>
        <div class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs text-slate-600 shadow-sm dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
          <input type="date" id="statsFrom" class="bg-transparent focus:outline-none" value="{{ now()->startOfMonth()->format('Y-m-d') }}" />
          <span>–</span>
          <input type="date" id="statsTo" class="bg-transparent focus:outline-none" value="{{ now()->format('Y-m-d') }}" />
        </div>
      </div>
    </div>
    <div class="relative h-[260px] overflow-hidden" style="contain: layout size;">
      <canvas id="lineStatistics" class="absolute inset-0 w-full h-full max-h-[260px]"></canvas>
    </div>
  </div>

  <!-- Recent Orders -->
  <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700/60 dark:bg-slate-900">
    <div class="mb-4 flex items-center justify-between">
      <h4 class="text-lg font-semibold text-slate-900 dark:text-white">Recent Orders</h4>
      <a href="{{ route('admin.orders.index') }}" class="btn-outline">View All</a>
    </div>
    <div class="overflow-x-auto">
      <table class="table-modern">
        <thead>
          <tr class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
            <th class="py-3 pr-3">Order</th>
            <th class="py-3 pr-3">Customer</th>
            <th class="py-3 pr-3">Status</th>
            <th class="py-3 pr-3 text-right">Total</th>
            <th class="py-3 pr-3">Date</th>
            <th class="py-3 pr-3"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700/60">
          @forelse($recentOrders as $order)
            @php
              $s = strtolower($order->status ?? '');
              $badge = match ($s) {
                'pending' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                'paid', 'completed' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                'shipped' => 'bg-sky-50 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300',
                'cancelled', 'canceled' => 'bg-rose-50 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300',
                default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
              };
            @endphp
            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/50">
              <td class="py-3 pr-3 font-medium text-slate-900 dark:text-white">#{{ $order->id }}</td>
              <td class="py-3 pr-3 text-slate-700 dark:text-slate-300">{{ $order->user->name ?? 'Guest' }}</td>
              <td class="py-3 pr-3">
                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $badge }}">{{ ucfirst($order->status ?? 'Unknown') }}</span>
              </td>
              <td class="py-3 pr-3 text-right text-slate-900 dark:text-white">Rp {{ number_format((float)$order->total_amount,0,',','.') }}</td>
              <td class="py-3 pr-3 text-slate-600 dark:text-slate-400">{{ optional($order->created_at)->format('d M Y, H:i') }}</td>
              <td class="py-3 pr-3 text-right">
                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn-ghost">Details</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="py-6 text-center text-slate-500 dark:text-slate-400">No recent orders</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  // helper: wait for Chart
  function whenChartReady(cb, tries=10){ if (window.Chart) return cb(); if(tries<=0) return; setTimeout(()=>whenChartReady(cb, tries-1), 150); }

  // Monthly sales bar
  (function(){
    const el = document.getElementById('barMonthlySales'); if(!el) return;
    whenChartReady(()=>{
      new Chart(el.getContext('2d'), {
        type: 'bar',
        data: { labels: @json($months ?? []), datasets: [{
          label: 'Sales', data: @json($monthlyRevenue ?? []),
          backgroundColor: 'rgba(59,130,246,0.7)', borderRadius: 8, maxBarThickness: 18
        }]},
        options: { responsive: true, maintainAspectRatio: false, resizeDelay: 200, scales: { y: { beginAtZero: true } }, plugins: { legend: { display:false } } }
      });
    });
  })();

  // Gauge semicircle (doughnut)
  (function(){
    const el = document.getElementById('gaugeMonthlyTarget'); if(!el) return;
    const percent = {{ (float)$gaugePercent }};
    whenChartReady(()=>new Chart(el.getContext('2d'), {
      type: 'doughnut',
      data: { labels:['Progress','Remaining'], datasets:[{ data:[percent, 100-percent],
        backgroundColor:['#6366f1','#e5e7eb'], borderWidth:0 }]
      },
      options: { responsive: true, maintainAspectRatio: false, resizeDelay: 200, circumference: 180, rotation: -90, cutout: '80%', plugins:{ legend:{ display:false }, tooltip:{ enabled:false } } }
    }));
  })();

  // Revenue by category bar
  (function(){ const el=document.getElementById('categoryChartDash'); if(!el) return;
    whenChartReady(()=> new Chart(el.getContext('2d'), { type: 'bar', data: { labels:@json($categoryLabels ?? []), datasets:[{ label:'Revenue', data:@json($categoryData ?? []), backgroundColor:'rgba(59,130,246,0.7)', borderRadius:8 }] }, options:{ responsive:true, maintainAspectRatio:false, resizeDelay: 200, plugins:{ legend:{ display:false } } } })); })();

  // Statistics line with segmented tabs
  (function(){ const el=document.getElementById('lineStatistics'); if(!el) return;
    const labels = @json($months ?? []);
    const revenue = @json($monthlyRevenue ?? []);
    const orders = @json($monthlyOrders ?? []);
    const gradient = (ctx)=>{ const g=ctx.createLinearGradient(0,0,0,220); g.addColorStop(0,'rgba(59,130,246,0.35)'); g.addColorStop(1,'rgba(59,130,246,0.05)'); return g; };
    let chart;
    whenChartReady(()=>{ chart = new Chart(el.getContext('2d'), {
      type:'line', data:{ labels, datasets:[{ label:'Overview', data: revenue, borderColor:'#6366f1', backgroundColor:(c)=>gradient(c.chart.ctx), fill:true, tension:0.35, borderWidth:2 }] },
      options:{ responsive:true, maintainAspectRatio:false, resizeDelay: 200, plugins:{ legend:{ display:false } }, scales:{ y:{ beginAtZero:true } } }
    });});
    // Tab switching via Alpine – listen globally
    document.addEventListener('click', (e)=>{
      const t = e.target.closest('[x-data]'); if(!t) return;
      const tabButton = e.target.closest('button'); if(!tabButton || !t.__x) return;
      const tab = t.__x.$data.tab; // current after click
      const next = t.getAttribute('x-data');
      // switch based on t.__x.$data.tab
      const current = t.__x.$data.tab;
      let series = revenue; let label='Overview';
      if(current==='sales'){ series = orders; label='Sales'; }
      if(current==='revenue'){ series = revenue; label='Revenue'; }
      if(chart){ chart.data.datasets[0].data = series; chart.data.datasets[0].label = label; chart.update('none'); }
    });
  })();

  // Enhance: scoped tabs + date range fetch without interfering existing chart
  (function(){
    const el=document.getElementById('lineStatistics'); if(!el) return;
    const statsCard=document.getElementById('statsCard');
    // Seed globals for reuse
    window.statsData = { revenue: @json($monthlyRevenue ?? []), orders: @json($monthlyOrders ?? []) };
    // Scoped tab switching
    statsCard?.addEventListener('click', (e)=>{
      const btn=e.target.closest('button[data-tab]'); if(!btn) return;
      const chart = window.Chart?.getChart(el);
      if(!chart) return;
      const current = statsCard.__x ? statsCard.__x.$data.tab : btn.getAttribute('data-tab');
      const series = (current==='sales') ? window.statsData.orders : window.statsData.revenue;
      const label = (current==='sales') ? 'Sales' : ((current==='revenue') ? 'Revenue' : 'Overview');
      chart.data.datasets[0].data = series; chart.data.datasets[0].label = label; chart.update('none');
    });
    // Date range native inputs
    const fromEl=document.getElementById('statsFrom');
    const toEl=document.getElementById('statsTo');
    if(fromEl && toEl){
      const onChange=()=>{ const f=fromEl.value, t=toEl.value; if(f&&t) refreshStats(f,t); };
      fromEl.addEventListener('change', onChange);
      toEl.addEventListener('change', onChange);
    }

    async function refreshStats(from,to){ try{ const url=new URL('{{ route('admin.dashboard.metrics') }}', window.location.origin); url.searchParams.set('from',from); url.searchParams.set('to',to); const res=await fetch(url.toString()); const j=await res.json(); const chart = window.Chart?.getChart(el); if(!chart) return; window.statsData.revenue=j.revenue; window.statsData.orders=j.orders; chart.data.labels=j.months; const current = statsCard.__x ? statsCard.__x.$data.tab : 'overview'; const series=(current==='sales')?j.orders:j.revenue; chart.data.datasets[0].data=series; chart.update('none'); }catch(e){} }
  })();
</script>
@endsection
