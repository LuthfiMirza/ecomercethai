@extends('layouts.admin')

@section('header', 'Customer Detail')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="flex flex-col gap-7.5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-2xl font-bold text-black dark:text-white">{{ $user->name }}</h3>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-300">Joined {{ $user->created_at->format('d M Y') }}</p>
        </div>
        <div class="flex gap-2">
            @if($user->is_banned)
                <form action="{{ localized_route('admin.users.activate', ['id' => $user->id]) }}" method="POST">
                    @csrf
                    <button class="px-4 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700">Activate</button>
                </form>
            @else
                <form action="{{ localized_route('admin.users.ban', ['id' => $user->id]) }}" method="POST">
                    @csrf
                    <button class="px-4 py-2 rounded bg-rose-600 text-white hover:bg-rose-700">Ban</button>
                </form>
            @endif
            <a href="{{ localized_route('admin.users.edit', ['id' => $user->id]) }}" class="px-4 py-2 rounded bg-slate-700 text-white hover:bg-slate-600">Edit</a>
            <a href="{{ localized_route('admin.users.index') }}" class="px-4 py-2 rounded bg-slate-200 text-slate-800 hover:bg-slate-300 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600">Back</a>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-12 gap-4 md:gap-6 2xl:gap-7.5">
        <div class="col-span-12 xl:col-span-4 space-y-6">
            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex items-center gap-4">
                    <img class="h-14 w-14 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" alt="{{ $user->name }}">
                    <div>
                        <p class="font-semibold text-black dark:text-white">{{ $user->name }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-300">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-sm"><span class="font-medium">Role:</span> {{ $user->is_admin ? 'Admin' : 'Customer' }}</p>
                    <p class="text-sm"><span class="font-medium">Status:</span> {{ $user->is_banned ? 'Banned' : 'Active' }}</p>
                </div>
            </div>

            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                <h4 class="text-lg font-semibold text-black dark:text-white mb-4">Recent Orders</h4>
                @forelse($recentOrders as $o)
                    <div class="flex items-center justify-between py-2 border-b last:border-0 border-slate-200 dark:border-slate-700">
                        <div>
                            <p class="text-sm font-medium">#ORD{{ $o->id }}</p>
                            <p class="text-xs text-slate-500">{{ $o->created_at->format('d M Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm">{{ format_price($o->total_amount ?? 0) }}</p>
                                    @php $cls = match($o->status){
                                        'pending' => 'badge-warn',
                                        'processing' => 'badge-info',
                                        'shipped' => 'badge-info',
                                        'completed' => 'badge-success',
                                        'cancelled' => 'badge-danger',
                                        default => 'badge-neutral',
                                    }; @endphp
                                    <span class="badge {{ $cls }}">{{ ucfirst($o->status) }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No recent orders.</p>
                @endforelse
            </div>

            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                <h4 class="text-lg font-semibold text-black dark:text-white mb-4">Status Activity</h4>
                <div class="space-y-3 max-h-64 overflow-auto">
                    @forelse($statusLogs as $log)
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm"><span class="font-medium capitalize">{{ $log->action }}</span> by {{ $log->admin->name ?? 'System' }}</p>
                                <p class="text-xs text-slate-500">{{ $log->created_at->format('d M Y H:i') }} — {{ $log->from_state }} → {{ $log->to_state }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No status changes yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-span-12 xl:col-span-8">
            <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-xl font-semibold text-black dark:text-white">Order History</h4>
                    <a class="text-sm text-indigo-600 hover:text-indigo-700" href="{{ localized_route('admin.orders.index', ['q' => $user->email]) }}">View all</a>
                </div>
                <div class="overflow-x-auto table-card">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase">Order</th>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase">Total</th>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium uppercase">Date</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            <tr>
                                <td class="px-4 py-2">#ORD{{ $order->id }}</td>
                                <td class="px-4 py-2">{{ format_price($order->total_amount ?? 0) }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @switch($order->status)
                                            @case('pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 @break
                                            @case('processing') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 @break
                                            @case('shipped') bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300 @break
                                            @case('completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 @break
                                            @case('cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 @break
                                            @default bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300
                                        @endswitch">{{ ucfirst($order->status) }}</span>
                                </td>
                                <td class="px-4 py-2">{{ $order->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-2 text-right">
                                    <a href="{{ localized_route('admin.orders.show', ['id' => $order->id]) }}" class="px-3 py-1.5 bg-slate-700 text-white rounded-md hover:bg-slate-600 text-xs">View</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">No orders found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $orders->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
