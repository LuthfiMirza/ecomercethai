@extends('layouts.admin')

@section('header', 'Dashboard')

@section('content')
    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        <!-- Welcome Message -->
        <div class="mb-6">
            <h3 class="text-2xl font-bold text-black dark:text-white">
                Welcome back, Admin 👋
            </h3>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6 xl:grid-cols-4 2xl:gap-7.5">
            <!-- Total Revenue -->
            <div class="rounded-sm border border-stroke bg-white py-6 px-7 shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
                    <svg class="fill-primary dark:fill-white" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.5 17.5H16.25V16.25C16.25 14.1875 14.5625 12.5 12.5 12.5H7.5C5.4375 12.5 3.75 14.1875 3.75 16.25V17.5H2.5V16.25C2.5 13.5 4.75 11.25 7.5 11.25H12.5C15.25 11.25 17.5 13.5 17.5 16.25V17.5ZM10 10C12.0625 10 13.75 8.3125 13.75 6.25C13.75 4.1875 12.0625 2.5 10 2.5C7.9375 2.5 6.25 4.1875 6.25 6.25C6.25 8.3125 7.9375 10 10 10ZM10 8.75C8.625 8.75 7.5 7.625 7.5 6.25C7.5 4.875 8.625 3.75 10 3.75C11.375 3.75 12.5 4.875 12.5 6.25C12.5 7.625 11.375 8.75 10 8.75Z" fill=""></path>
                    </svg>
                </div>
                <div class="mt-4 flex items-end justify-between">
                    <div>
                        <h4 class="text-title-md font-bold text-black dark:text-white">
                            Rp {{ number_format((int) ($totalRevenue ?? 0), 0, ',', '.') }}
                        </h4>
                        <span class="text-sm font-medium">Total Revenue</span>
                    </div>
                    <span class="flex items-center text-sm font-medium text-meta-3">
                        12%
                        <svg class="fill-meta-3" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 5L10 0H0L5 5Z" fill=""></path>
                        </svg>
                    </span>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="rounded-sm border border-stroke bg-white py-6 px-7 shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
                    <svg class="fill-primary dark:fill-white" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.5 8.75H16.25V6.875C16.25 4.125 14.125 1.875 11.5 1.875H8.5C5.875 1.875 3.75 4.125 3.75 6.875V8.75H2.5C1.8125 8.75 1.25 9.3125 1.25 10V15C1.25 15.6875 1.8125 16.25 2.5 16.25H17.5C18.1875 16.25 18.75 15.6875 18.75 15V10C18.75 9.3125 18.1875 8.75 17.5 8.75ZM3.75 6.875C3.75 4.8125 5.4375 3.125 7.5 3.125H12.5C14.5625 3.125 16.25 4.8125 16.25 6.875V8.75H3.75V6.875ZM17.5 15H2.5V10H17.5V15Z" fill=""></path>
                        <path d="M10 12.5C10.6875 12.5 11.25 11.9375 11.25 11.25C11.25 10.5625 10.6875 10 10 10C9.3125 10 8.75 10.5625 8.75 11.25C8.75 11.9375 9.3125 12.5 10 12.5Z" fill=""></path>
                    </svg>
                </div>
                <div class="mt-4 flex items-end justify-between">
                    <div>
                        <h4 class="text-title-md font-bold text-black dark:text-white">
                            {{ $totalOrders ?? 0 }}
                        </h4>
                        <span class="text-sm font-medium">Total Orders</span>
                    </div>
                    <span class="flex items-center text-sm font-medium text-meta-3">
                        3.2%
                        <svg class="fill-meta-3" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 5L10 0H0L5 5Z" fill=""></path>
                        </svg>
                    </span>
                </div>
            </div>

            <!-- Total Customers -->
            <div class="rounded-sm border border-stroke bg-white py-6 px-7 shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
                    <svg class="fill-primary dark:fill-white" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 10C12.0625 10 13.75 8.3125 13.75 6.25C13.75 4.1875 12.0625 2.5 10 2.5C7.9375 2.5 6.25 4.1875 6.25 6.25C6.25 8.3125 7.9375 10 10 10ZM10 8.75C8.625 8.75 7.5 7.625 7.5 6.25C7.5 4.875 8.625 3.75 10 3.75C11.375 3.75 12.5 4.875 12.5 6.25C12.5 7.625 11.375 8.75 10 8.75Z" fill=""></path>
                        <path d="M17.5 17.5H16.25V16.25C16.25 14.1875 14.5625 12.5 12.5 12.5H7.5C5.4375 12.5 3.75 14.1875 3.75 16.25V17.5H2.5V16.25C2.5 13.5 4.75 11.25 7.5 11.25H12.5C15.25 11.25 17.5 13.5 17.5 16.25V17.5Z" fill=""></path>
                    </svg>
                </div>
                <div class="mt-4 flex items-end justify-between">
                    <div>
                        <h4 class="text-title-md font-bold text-black dark:text-white">
                            {{ $totalCustomers ?? 0 }}
                        </h4>
                        <span class="text-sm font-medium">Total Customers</span>
                    </div>
                    <span class="flex items-center text-sm font-medium text-meta-5">
                        2.1%
                        <svg class="fill-meta-5 rotate-180" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 5L10 0H0L5 5Z" fill=""></path>
                        </svg>
                    </span>
                </div>
            </div>

            <!-- Products Sold -->
            <div class="rounded-sm border border-stroke bg-white py-6 px-7 shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
                    <svg class="fill-primary dark:fill-white" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.5 8.75H16.25V6.875C16.25 4.125 14.125 1.875 11.5 1.875H8.5C5.875 1.875 3.75 4.125 3.75 6.875V8.75H2.5C1.8125 8.75 1.25 9.3125 1.25 10V15C1.25 15.6875 1.8125 16.25 2.5 16.25H17.5C18.1875 16.25 18.75 15.6875 18.75 15V10C18.75 9.3125 18.1875 8.75 17.5 8.75ZM3.75 6.875C3.75 4.8125 5.4375 3.125 7.5 3.125H12.5C14.5625 3.125 16.25 4.8125 16.25 6.875V8.75H3.75V6.875ZM17.5 15H2.5V10H17.5V15Z" fill=""></path>
                        <path d="M10 12.5C10.6875 12.5 11.25 11.9375 11.25 11.25C11.25 10.5625 10.6875 10 10 10C9.3125 10 8.75 10.5625 8.75 11.25C8.75 11.9375 9.3125 12.5 10 12.5Z" fill=""></path>
                    </svg>
                </div>
                <div class="mt-4 flex items-end justify-between">
                    <div>
                        <h4 class="text-title-md font-bold text-black dark:text-white">
                            {{ $productsSold ?? 0 }}
                        </h4>
                        <span class="text-sm font-medium">Products Sold</span>
                    </div>
                    <span class="flex items-center text-sm font-medium text-meta-3">
                        5.6%
                        <svg class="fill-meta-3" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 5L10 0H0L5 5Z" fill=""></path>
                        </svg>
                    </span>
                </div>
            </div>
        </div>

        <!-- Charts and Tables -->
        <div class="mt-6 grid grid-cols-12 gap-4 md:gap-6 2xl:gap-7.5">
            <!-- Sales Chart -->
            <div class="col-span-12 xl:col-span-7">
                <div class="rounded-sm border border-stroke bg-white py-6 px-7 shadow-default dark:border-strokedark dark:bg-boxdark">
                    <div class="flex flex-wrap items-start justify-between gap-3 sm:flex-nowrap">
                        <div class="flex w-full flex-wrap gap-3 sm:gap-5">
                            <div class="flex min-w-47.5">
                                <span class="mt-1 mr-2 flex h-4 w-full max-w-4 items-center justify-center rounded-full border border-primary">
                                    <span class="block h-2.5 w-full max-w-2.5 rounded-full bg-primary"></span>
                                </span>
                                <div class="w-full">
                                    <p class="font-semibold text-primary">Total Revenue</p>
                                    <p class="text-sm font-medium">12.04.2025 - 12.05.2025</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex w-full max-w-45 justify-end">
                            <div class="inline-flex items-center rounded-md bg-whiter p-1.5 dark:bg-meta-4">
                                <button class="rounded bg-white py-1 px-3 text-xs font-medium text-black shadow-card hover:bg-white hover:shadow-card dark:bg-boxdark dark:text-white dark:hover:bg-boxdark">
                                    Day
                                </button>
                                <button class="rounded py-1 px-3 text-xs font-medium text-black hover:bg-white hover:shadow-card dark:text-white dark:hover:bg-boxdark">
                                    Week
                                </button>
                                <button class="rounded py-1 px-3 text-xs font-medium text-black hover:bg-white hover:shadow-card dark:text-white dark:hover:bg-boxdark">
                                    Month
                                </button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <canvas id="salesChart" class="mx-auto"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Selling Products -->
            <div class="col-span-12 xl:col-span-5">
                <x-card title="Top Selling Products">
                    <div class="flex flex-col">
                        <div class="grid grid-cols-3 rounded-sm bg-gray-2 dark:bg-meta-4 sm:grid-cols-4">
                            <div class="p-2.5 xl:p-4">
                                <h5 class="text-sm font-medium uppercase xsm:text-xs">Product</h5>
                            </div>
                            <div class="p-2.5 text-center xl:p-4">
                                <h5 class="text-sm font-medium uppercase xsm:text-xs">Sold</h5>
                            </div>
                            <div class="p-2.5 text-center xl:p-4">
                                <h5 class="text-sm font-medium uppercase xsm:text-xs">Price</h5>
                            </div>
                            <div class="hidden p-2.5 text-center sm:block xl:p-4">
                                <h5 class="text-sm font-medium uppercase xsm:text-xs">Status</h5>
                            </div>
                        </div>

                        @forelse($topProducts as $row)
                        <div class="grid grid-cols-3 border-t border-stroke dark:border-strokedark sm:grid-cols-4">
                            <div class="flex items-center p-2.5 xl:p-4">
                                <div class="flex-shrink-0 mr-3">
                                    <img src="{{ $row->product->image_url ?? 'https://via.placeholder.com/40' }}" alt="Product" class="h-10 w-10 rounded-md">
                                </div>
                                <p class="hidden font-medium text-black dark:text-white sm:block">{{ $row->product->name ?? 'Product' }}</p>
                            </div>

                            <div class="flex items-center justify-center p-2.5 xl:p-4">
                                <p class="font-medium text-black dark:text-white">{{ $row->sold }}</p>
                            </div>

                            <div class="flex items-center justify-center p-2.5 xl:p-4">
                                <p class="font-medium text-meta-3">Rp {{ number_format($row->product->price ?? 0,0,',','.') }}</p>
                            </div>

                            <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-4">
                                @php($stock = $row->product->stock ?? 0)
                                <p class="font-medium {{ $stock < 5 ? 'text-meta-5' : 'text-meta-3' }}">{{ $stock < 5 ? 'Low Stock' : 'In Stock' }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="p-4 text-center text-sm text-slate-500 dark:text-slate-400">No data</div>
                        @endforelse
                    </div>
                </x-card>
            </div>

            <!-- Recent Orders Table -->
            <div class="col-span-12 xl:col-span-12">
                <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <h4 class="text-xl font-semibold text-black dark:text-white">
                            Recent Orders
                        </h4>
                        <div>
                            <button class="flex items-center gap-2 rounded bg-primary py-2 px-4.5 font-medium text-white hover:bg-opacity-90">
                                <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15 7H9V1C9 0.4 8.6 0 8 0C7.4 0 7 0.4 7 1V7H1C0.4 7 0 7.4 0 8C0 8.6 0.4 9 1 9H7V15C7 15.6 7.4 16 8 16C8.6 16 9 15.6 9 15V9H15C15.6 9 16 8.6 16 8C16 7.4 15.6 7 15 7Z" fill=""></path>
                                </svg>
                                View All
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col">
                        <div class="grid grid-cols-3 sm:grid-cols-5 rounded-sm bg-gray-2 dark:bg-meta-4">
                            <div class="p-2.5 xl:p-5">
                                <h5 class="text-sm font-medium uppercase xsm:text-xs">Order ID</h5>
                            </div>
                            <div class="p-2.5 text-center xl:p-5">
                                <h5 class="text-sm font-medium uppercase xsm:text-xs">Customer</h5>
                            </div>
                            <div class="p-2.5 text-center xl:p-5">
                                <h5 class="text-sm font-medium uppercase xsm:text-xs">Total</h5>
                            </div>
                            <div class="p-2.5 text-center xl:p-5">
                                <h5 class="text-sm font-medium uppercase xsm:text-xs">Status</h5>
                            </div>
                            <div class="hidden p-2.5 text-center sm:block xl:p-5">
                                <h5 class="text-sm font-medium uppercase xsm:text-xs">Actions</h5>
                            </div>
                        </div>

                        @if(isset($recentOrders) && $recentOrders->count())
                        @foreach($recentOrders as $row)
                        <div class="grid grid-cols-3 sm:grid-cols-5 border-t border-stroke dark:border-strokedark">
                            <div class="flex items-center p-2.5 xl:p-5">
                                <p class="font-medium text-black dark:text-white">#ORD{{ $row->id }}</p>
                            </div>

                            <div class="flex items-center justify-center p-2.5 xl:p-5">
                                <p class="font-medium text-black dark:text-white">{{ $row->user->name ?? 'N/A' }}</p>
                            </div>

                            <div class="flex items-center justify-center p-2.5 xl:p-5">
                                <p class="font-medium text-meta-3">Rp {{ number_format($row->total_amount,0,',','.') }}</p>
                            </div>

                            <div class="flex items-center justify-center p-2.5 xl:p-5">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @switch($row->status)
                                        @case('pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 @break
                                        @case('processing') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 @break
                                        @case('shipped') bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300 @break
                                        @case('completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 @break
                                        @case('cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 @break
                                        @default bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300
                                    @endswitch">
                                    {{ ucfirst($row->status) }}
                                </span>
                            </div>

                            <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                                <a href="{{ route('admin.orders.show', $row) }}" class="font-medium text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">View</a>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <div class="grid grid-cols-3 sm:grid-cols-5 border-t border-stroke dark:border-strokedark">
                            <div class="flex items-center p-2.5 xl:p-5">
                                <p class="font-medium text-black dark:text-white">#ORD001</p>
                            </div>

                            <div class="flex items-center justify-center p-2.5 xl:p-5">
                                <p class="font-medium text-black dark:text-white">John Doe</p>
                            </div>

                            <div class="flex items-center justify-center p-2.5 xl:p-5">
                                <p class="font-medium text-meta-3">Rp 1.500.000</p>
                            </div>

                            <div class="flex items-center justify-center p-2.5 xl:p-5">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    Pending
                                </span>
                            </div>

                            <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                                <a href="#" class="font-medium text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">View</a>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 sm:grid-cols-5 border-t border-stroke dark:border-strokedark">
                            <div class="flex items-center p-2.5 xl:p-5">
                                <p class="font-medium text-black dark:text-white">#ORD002</p>
                            </div>

                            <div class="flex items-center justify-center p-2.5 xl:p-5">
                                <p class="font-medium text-black dark:text-white">Jane Smith</p>
                            </div>

                            <div class="flex items-center justify-center p-2.5 xl:p-5">
                                <p class="font-medium text-meta-3">Rp 800.000</p>
                            </div>

                            <div class="flex items-center justify-center p-2.5 xl:p-5">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Completed
                                </span>
                            </div>

                            <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                                <a href="#" class="font-medium text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">View</a>
                            </div>
                        </div>
                        @endif

                        <div class="grid grid-cols-3 sm:grid-cols-5 border-t border-stroke dark:border-strokedark">
                            <div class="flex items-center p-2.5 xl:p-5">
                                <p class="font-medium text-black dark:text-white">#ORD003</p>
                            </div>

                            <div class="flex items-center justify-center p-2.5 xl:p-5">
                                <p class="font-medium text-black dark:text-white">Robert Johnson</p>
                            </div>

                            <div class="flex items-center justify-center p-2.5 xl:p-5">
                                <p class="font-medium text-meta-3">Rp 2.100.000</p>
                            </div>

                            <div class="flex items-center justify-center p-2.5 xl:p-5">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    Processing
                                </span>
                            </div>

                            <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                                <a href="#" class="font-medium text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">View</a>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 sm:grid-cols-5 border-t border-stroke dark:border-strokedark">
                            <div class="flex items-center p-2.5 xl:p-5">
                                <p class="font-medium text-black dark:text-white">#ORD004</p>
                            </div>

                            <div class="flex items-center justify-center p-2.5 xl:p-5">
                                <p class="font-medium text-black dark:text-white">Emily Davis</p>
                            </div>

                            <div class="flex items-center justify-center p-2.5 xl:p-5">
                                <p class="font-medium text-meta-3">Rp 450.000</p>
                            </div>

                            <div class="flex items-center justify-center p-2.5 xl:p-5">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                    Shipped
                                </span>
                            </div>

                            <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                                <a href="#" class="font-medium text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">View</a>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 sm:grid-cols-5 border-t border-stroke dark:border-strokedark">
                            <div class="flex items-center p-2.5 xl:p-5">
                                <p class="font-medium text-black dark:text-white">#ORD005</p>
                            </div>

                            <div class="flex items-center justify-center p-2.5 xl:p-5">
                                <p class="font-medium text-black dark:text-white">Michael Wilson</p>
                            </div>

                            <div class="flex items-center justify-center p-2.5 xl:p-5">
                                <p class="font-medium text-meta-3">Rp 1.200.000</p>
                            </div>

                            <div class="flex items-center justify-center p-2.5 xl:p-5">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    Cancelled
                                </span>
                            </div>

                            <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                                <a href="#" class="font-medium text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Revenue + Top Customers -->
        <div class="mt-6 grid grid-cols-12 gap-4 md:gap-6 2xl:gap-7.5">
            <div class="col-span-12 xl:col-span-7">
                <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                    <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
                        <h4 class="text-xl font-semibold text-black dark:text-white">Revenue by Category</h4>
                    </div>
                    <div class="p-6.5"><canvas id="categoryChartDash"></canvas></div>
                </div>
            </div>
            <div class="col-span-12 xl:col-span-5">
                <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                    <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
                        <h4 class="text-xl font-semibold text-black dark:text-white">Top Customers</h4>
                    </div>
                    <div class="p-6.5">
                        @forelse(($topCustomers ?? []) as $row)
                        <div class="flex items-center justify-between py-2 border-b last:border-0 border-slate-200 dark:border-slate-700">
                            <div>
                                <p class="font-medium text-black dark:text-white">{{ $row->user->name ?? 'Customer' }}</p>
                                <p class="text-xs text-slate-500">{{ $row->orders }} orders</p>
                            </div>
                            <p class="font-medium text-meta-3">Rp {{ number_format($row->spent,0,',','.') }}</p>
                        </div>
                        @empty
                        <p class="text-sm text-slate-500">No data</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Chart.js for Sales Overview
    document.addEventListener('DOMContentLoaded', function () {
        if (!window.Chart) return;
        var ctx = document.getElementById('salesChart').getContext('2d');
        var salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($labels ?? []),
                datasets: [{
                    label: 'Sales',
                    data: @json($data ?? []),
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    tension: 0.35,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
        // Category Chart on Dashboard
        var cat = document.getElementById('categoryChartDash');
        if (cat) {
            new Chart(cat.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($categoryLabels ?? []),
                    datasets: [{
                        label: 'Revenue',
                        data: @json($categoryData ?? []),
                        backgroundColor: 'rgba(59,130,246,0.7)',
                        borderColor: 'rgba(59,130,246,1)',
                        borderWidth: 1
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });
        }
    });
</script>
@endsection
