@extends('layouts.admin')

@section('header', 'Reports & Analytics')

@section('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-2xl font-bold text-black dark:text-white">Reports & Analytics</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Analisis performa bisnis dengan filter fleksibel</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.reports.export.pdf') }}" class="btn-outline">Export PDF</a>
                <a href="{{ route('admin.reports.export.excel') }}" class="btn-primary">Export Excel</a>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" class="mt-4 table-card p-4">
            <div class="grid grid-cols-12 gap-3">
                <div class="col-span-12 lg:col-span-5">
                    <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-300">Periode</label>
                    <div class="inline-flex flex-wrap gap-2">
                        @php $p = $period ?? 'month'; @endphp
                        <label class="cursor-pointer"><input type="radio" name="period" value="week" class="peer hidden" @checked($p==='week')><span class="peer-checked:bg-slate-900 peer-checked:text-white inline-block rounded-full border border-slate-300 px-3 py-1 text-xs text-slate-700 dark:border-slate-600 dark:text-slate-200 dark:peer-checked:bg-slate-200 dark:peer-checked:text-slate-900">Mingguan</span></label>
                        <label class="cursor-pointer"><input type="radio" name="period" value="month" class="peer hidden" @checked($p==='month')><span class="peer-checked:bg-slate-900 peer-checked:text-white inline-block rounded-full border border-slate-300 px-3 py-1 text-xs text-slate-700 dark:border-slate-600 dark:text-slate-200 dark:peer-checked:bg-slate-200 dark:peer-checked:text-slate-900">Bulanan</span></label>
                        <label class="cursor-pointer"><input type="radio" name="period" value="quarter" class="peer hidden" @checked($p==='quarter')><span class="peer-checked:bg-slate-900 peer-checked:text-white inline-block rounded-full border border-slate-300 px-3 py-1 text-xs text-slate-700 dark:border-slate-600 dark:text-slate-200 dark:peer-checked:bg-slate-200 dark:peer-checked:text-slate-900">Kuartal</span></label>
                        <label class="cursor-pointer"><input type="radio" name="period" value="year" class="peer hidden" @checked($p==='year')><span class="peer-checked:bg-slate-900 peer-checked:text-white inline-block rounded-full border border-slate-300 px-3 py-1 text-xs text-slate-700 dark:border-slate-600 dark:text-slate-200 dark:peer-checked:bg-slate-200 dark:peer-checked:text-slate-900">Tahunan</span></label>
                        <label class="cursor-pointer"><input type="radio" name="period" value="custom" class="peer hidden" @checked($p==='custom')><span class="peer-checked:bg-slate-900 peer-checked:text-white inline-block rounded-full border border-slate-300 px-3 py-1 text-xs text-slate-700 dark:border-slate-600 dark:text-slate-200 dark:peer-checked:bg-slate-200 dark:peer-checked:text-slate-900">Custom</span></label>
                    </div>
                </div>
                <div class="col-span-12 sm:col-span-6 lg:col-span-3">
                    <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-300">Custom Range</label>
                    <input id="reportRange" name="range" class="w-full rounded-lg border border-slate-200 bg-white py-2 px-3 text-sm placeholder-slate-400 focus:border-slate-400 focus:outline-none dark:border-slate-700 dark:bg-slate-800" placeholder="Select range" />
                    <input type="hidden" name="from" value="{{ request('from') }}">
                    <input type="hidden" name="to" value="{{ request('to') }}">
                </div>
                <div class="col-span-12 sm:col-span-6 lg:col-span-2">
                    <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-300">Kategori</label>
                    <select name="category_id" class="w-full rounded-lg border border-slate-200 bg-white py-2 pl-3 pr-8 text-sm focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        <option value="">Semua</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" @selected(($selectedCategory ?? null)==$c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-12 sm:col-span-6 lg:col-span-2">
                    <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-300">Produk</label>
                    <select name="product_id" class="w-full rounded-lg border border-slate-200 bg-white py-2 pl-3 pr-8 text-sm focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        <option value="">Semua</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" @selected(($selectedProduct ?? null)==$p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-12 sm:col-span-6 lg:col-span-2">
                    <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-300">Segment</label>
                    <select name="segment" class="w-full rounded-lg border border-slate-200 bg-white py-2 pl-3 pr-8 text-sm focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        @foreach(['all'=>'Semua','new'=>'Pelanggan Baru','returning'=>'Pelanggan Lama'] as $val=>$label)
                            <option value="{{ $val }}" @selected(($segment ?? 'all')===$val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-12 flex justify-end">
                    <button type="submit" class="btn-primary">Terapkan</button>
                </div>
            </div>
        </form>

        <!-- KPI Row -->
        <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3 xl:grid-cols-5">
            <div class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                <p class="text-xs text-slate-500">Revenue ({{ $period }})</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">{{ format_price($totalRevenue ?? 0) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                <p class="text-xs text-slate-500">Orders ({{ $period }})</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">{{ number_format($totalOrders) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                <p class="text-xs text-slate-500">AOV</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">{{ format_price($aov ?? 0) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                <p class="text-xs text-slate-500">CLV (approx)</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">{{ format_price($clv ?? 0) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                <p class="text-xs text-slate-500">MoM Revenue</p>
                @php $up = $momRevenueChange >= 0; @endphp
                <p class="mt-2 text-2xl font-semibold {{ $up ? 'text-emerald-600' : 'text-rose-600' }}">{{ $up ? '+' : '' }}{{ $momRevenueChange }}%</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-12 gap-4 md:gap-6 2xl:gap-7.5">
            <!-- Sales Overview Chart -->
            <div class="col-span-12 xl:col-span-8">
                <div class="table-card p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h4 class="text-xl font-semibold text-black dark:text-white">Sales Overview</h4>
                    </div>
                    <div class="relative h-[300px] overflow-hidden" style="contain: layout size;"><canvas id="salesChart" class="absolute inset-0 w-full h-full max-h-[300px]"></canvas></div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="col-span-12 xl:col-span-4">
                <div class="table-card p-6">
                    <div class="mb-4">
                        <h4 class="text-xl font-semibold text-black dark:text-white">Top Selling Products</h4>
                    </div>
                    <div>
                        @forelse($topProducts as $i=>$row)
                        <div class="mb-3 flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-md bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                    <span class="font-medium text-black dark:text-white">{{ $i+1 }}</span>
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium text-black dark:text-white">{{ $row->product->name ?? 'Product' }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $row->sold }} Sold</p>
                                </div>
                            </div>
                            <p class="font-medium text-meta-3">{{ format_price($row->product->price ?? 0) }}</p>
                        </div>
                        @empty
                        <p class="text-sm text-slate-500">No data</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-12 gap-4 md:gap-6 2xl:gap-7.5">
            <!-- Category Sales Chart -->
            <div class="col-span-12 xl:col-span-6">
                <div class="table-card p-6">
                    <div class="mb-4">
                        <h4 class="text-xl font-semibold text-black dark:text-white">Revenue by Category</h4>
                    </div>
                    <div class="relative h-[260px] overflow-hidden" style="contain: layout size;"><canvas id="categoryChart" class="absolute inset-0 w-full h-full max-h-[260px]"></canvas></div>
                </div>
            </div>
            <!-- Orders Chart -->
            <div class="col-span-12 xl:col-span-6">
                <div class="table-card p-6">
                    <div class="mb-4"><h4 class="text-xl font-semibold text-black dark:text-white">Orders (Monthly)</h4></div>
                    <div class="relative h-[260px] overflow-hidden" style="contain: layout size;"><canvas id="ordersChart" class="absolute inset-0 w-full h-full max-h-[260px]"></canvas></div>
                </div>
            </div>
        </div>

        <!-- Monthly Target Gauge (parity with dashboard) + Mini KPI row -->
        <div class="mt-6 grid grid-cols-12 gap-4 md:gap-6 2xl:gap-7.5">
            <div class="col-span-12 xl:col-span-5">
                <div class="table-card p-6">
                    <div class="mb-4">
                        <h4 class="text-xl font-semibold text-black dark:text-white">Monthly Target</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Target you’ve set for each month</p>
                    </div>
                    <div class="relative h-[140px] overflow-hidden" style="contain: layout size;">
                        <canvas id="gaugeReports" class="absolute inset-0 w-full h-full max-h-[140px]"></canvas>
                    </div>
                    <div class="mt-4 grid grid-cols-3 divide-x divide-slate-200 rounded-xl bg-slate-50 p-4 text-center dark:divide-slate-700 dark:bg-slate-800/50">
                        <div><p class="text-xs text-slate-500">Target</p><p class="text-base font-semibold">{{ format_price($target ?? 0) }}</p></div>
                        <div><p class="text-xs text-slate-500">Revenue</p><p class="text-base font-semibold">{{ format_price($currentMonthRevenue ?? 0) }}</p></div>
                        <div><p class="text-xs text-slate-500">Today</p><p class="text-base font-semibold">{{ format_price($todayRevenue ?? 0) }}</p></div>
                    </div>
                </div>
            </div>
            <div class="col-span-12 xl:col-span-7">
                <div class="table-card p-6">
                    <div class="mb-4 flex items-center gap-4">
                        <div class="rounded-xl border border-slate-200 px-4 py-3 dark:border-slate-700">
                            <p class="text-xs text-slate-500">Revenue</p>
                            <p class="text-lg font-semibold">{{ format_price($totalRevenue ?? 0) }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 px-4 py-3 dark:border-slate-700">
                            <p class="text-xs text-slate-500">Orders</p>
                            <p class="text-lg font-semibold">{{ number_format($totalOrders) }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 px-4 py-3 dark:border-slate-700">
                            <p class="text-xs text-slate-500">AOV</p>
                            <p class="text-lg font-semibold">{{ format_price($aov ?? 0) }}</p>
                        </div>
                    </div>
                    <p class="text-sm text-slate-500">Snapshot of total KPIs</p>
                </div>
            </div>
        </div>

        <!-- Location & Finance -->
        <div class="mt-6 grid grid-cols-12 gap-4 md:gap-6 2xl:gap-7.5">
            <!-- Location Analytics -->
            <div class="col-span-12 xl:col-span-6">
                <div class="table-card p-6">
                    <div class="mb-4">
                        <h4 class="text-xl font-semibold text-black dark:text-white">Location Analytics</h4>
                    </div>
                    <div class="relative h-[260px] overflow-hidden" style="contain: layout size;"><canvas id="locationChart" class="absolute inset-0 w-full h-full max-h-[260px]"></canvas></div>
                </div>
            </div>
            <!-- Revenue vs Cost -->
            <div class="col-span-12 xl:col-span-6">
                <div class="table-card p-6">
                    <div class="mb-4">
                        <h4 class="text-xl font-semibold text-black dark:text-white">Revenue vs Cost</h4>
                    </div>
                    <div>
                        <div class="relative h-[260px] overflow-hidden" style="contain: layout size;"><canvas id="financeChart" class="absolute inset-0 w-full h-full max-h-[260px]"></canvas></div>
                        @if(($finance['cogs_percent'] ?? null) === null)
                            <p class="mt-3 text-xs text-slate-500">Set environment variable <code>COGS_PERCENT</code> untuk menampilkan estimasi Cost & Profit.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Flatpickr date range picker
    (function(){
        var $el = document.getElementById('reportRange');
        if ($el) {
            var init = function(){
                flatpickr($el, {
                    mode: 'range', dateFormat: 'Y-m-d', defaultDate: ['{{ optional($rangeStart)->format('Y-m-d') }}','{{ optional($rangeEnd)->format('Y-m-d') }}'],
                    onChange: function(sel){ if(sel.length===2){ const from=sel[0].toISOString().slice(0,10); const to=sel[1].toISOString().slice(0,10); document.querySelector('input[name=from]').value = from; document.querySelector('input[name=to]').value = to; } }
                });
            };
            if (window.flatpickr) init(); else { var s=document.createElement('script'); s.src='https://cdn.jsdelivr.net/npm/flatpickr'; s.onload=init; document.head.appendChild(s); }
        }
    })();
    // Ensure Chart.js is ready before creating charts
    function whenChartReady(cb, tries = 10) { if (window.Chart) return cb(); if (tries <= 0) return; setTimeout(() => whenChartReady(cb, tries - 1), 120); }

    var salesChart, categoryChart, ordersChart, locationChart, financeChart;
    whenChartReady(function() {
        // Sales Chart (Revenue)
        var salesCtx = document.getElementById('salesChart').getContext('2d');
        salesChart = new Chart(salesCtx, { type: 'line', data: { labels: @json($months ?? []), datasets: [{ label: 'Revenue', data: @json($revenue ?? []), borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.1)', borderWidth: 2, fill: true, tension: 0.35 }] }, options: { responsive: true, maintainAspectRatio: false, resizeDelay: 200 }});

        // Category Chart
        var categoryCtx = document.getElementById('categoryChart').getContext('2d');
        categoryChart = new Chart(categoryCtx, { type: 'bar', data: { labels: @json($categoryLabels ?? []), datasets: [{ label: 'Sales', data: @json($categoryData ?? []), backgroundColor: 'rgba(59,130,246,0.7)', borderColor: 'rgba(59,130,246,1)', borderWidth: 1 }] }, options: { responsive: true, maintainAspectRatio: false, resizeDelay: 200, plugins: { legend: { display: false } } }});

        // Orders Chart
        var ordersCtx = document.getElementById('ordersChart').getContext('2d');
        ordersChart = new Chart(ordersCtx, { type: 'line', data: { labels: @json($months ?? []), datasets: [{ label: 'Orders', data: @json($orders ?? []), borderColor: '#ff7b45', backgroundColor: 'rgba(255,123,69,0.12)', borderWidth: 2, fill: true, tension: 0.35 }] }, options: { responsive: true, maintainAspectRatio: false, resizeDelay: 200 }});

        // Location Chart
        var locEl = document.getElementById('locationChart');
        if (locEl) {
            locationChart = new Chart(locEl.getContext('2d'), { type: 'bar', data: { labels: @json($locationLabels ?? []), datasets: [{ label: 'Orders', data: @json($locationData ?? []), backgroundColor: 'rgba(59,130,246,0.7)' }] }, options: { responsive:true, maintainAspectRatio:false, resizeDelay:200, plugins:{ legend:{ display:false } }, scales:{ y:{ beginAtZero:true } } } });
        }

        // Finance Chart (Revenue vs Cost)
        var finEl = document.getElementById('financeChart');
        if (finEl) {
            var finRevenue = {{ (float)($finance['revenue'] ?? 0) }};
            var finCost = {!! json_encode($finance['cost'] ?? null) !!};
            var datasets = [ { label:'Revenue', data:[finRevenue], backgroundColor:'rgba(34,197,94,0.7)' } ];
            if (finCost !== null) datasets.push({ label:'Cost', data:[finCost], backgroundColor:'rgba(239,68,68,0.7)' });
            financeChart = new Chart(finEl.getContext('2d'), { type:'bar', data:{ labels:['{{ ucfirst($period ?? 'Period') }}'], datasets }, options:{ responsive:true, maintainAspectRatio:false, resizeDelay:200 } });
        }
    });

    // Gauge in Reports (ensure Chart.js is ready)
    (function(){
        const el = document.getElementById('gaugeReports');
        if (!el) return;
        const percent = {{ (float)$gaugePercent }};
        whenChartReady(function(){
            new Chart(el.getContext('2d'), {
                type: 'doughnut',
                data: { labels:['Progress','Remaining'], datasets:[{ data:[percent, 100 - percent], backgroundColor:['#6366f1','#e5e7eb'], borderWidth:0 }] },
                options: { responsive:true, maintainAspectRatio:false, resizeDelay:200, circumference:180, rotation:-90, cutout:'80%', plugins:{ legend:{ display:false }, tooltip:{ enabled:false } } }
            });
        });
    })();

    // Realtime refresh (same range)
    function getCurrentRange(){
        const f = document.querySelector('input[name=from]')?.value || '{{ optional($rangeStart)->format('Y-m-d') }}';
        const t = document.querySelector('input[name=to]')?.value || '{{ optional($rangeEnd)->format('Y-m-d') }}';
        return [f,t];
    }
    async function refreshMetricsLoop(){ try{ const [f,t]=getCurrentRange(); await refreshMetricsRange(f,t);}catch(e){} }
    setInterval(refreshMetricsLoop, 30000);

    async function refreshMetricsRange(from, to) {
        try {
            const url = new URL('{{ route('admin.reports.metrics') }}', window.location.origin);
            url.searchParams.set('from', from); url.searchParams.set('to', to);
            const res = await fetch(url.toString());
            const j = await res.json();
            if (salesChart) { salesChart.data.labels = j.months; salesChart.data.datasets[0].data = j.revenue; salesChart.update('none'); }
            if (categoryChart) { categoryChart.data.labels = j.categoryLabels; categoryChart.data.datasets[0].data = j.categoryData; categoryChart.update('none'); }
            if (ordersChart) { ordersChart.data.labels = j.months; ordersChart.data.datasets[0].data = j.orders; ordersChart.update('none'); }
        } catch(e) {}
    }
</script>
@endsection
