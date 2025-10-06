@extends('layouts.admin')

@section('header', 'Content Management')

@section('content')
    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        <div class="flex flex-col gap-7.5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-2xl font-bold text-black dark:text-white">
                    Content Management
                </h3>
                <p class="text-sm font-medium">Manage website content</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.content.banners.index') }}" class="flex items-center gap-2 rounded border border-primary py-2 px-4.5 font-medium text-primary hover:bg-opacity-90">
                    <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 4H12.5V3.5C12.5 2.4 11.6 1.5 10.5 1.5H5.5C4.4 1.5 3.5 2.4 3.5 3.5V4H2C1.4 4 1 4.4 1 5C1 5.6 1.4 6 2 6H3.5V12.5C3.5 13.6 4.4 14.5 5.5 14.5H10.5C11.6 14.5 12.5 13.6 12.5 12.5V6H14C14.6 6 15 5.6 15 5C15 4.4 14.6 4 14 4ZM5.5 3H10.5C10.8 3 11 3.2 11 3.5V4H5V3.5C5 3.2 5.2 3 5.5 3ZM11 12.5C11 12.8 10.8 13 10.5 13H5.5C5.2 13 5 12.8 5 12.5V6H11V12.5Z" fill=""></path>
                    </svg>
                    Banners
                </a>
                <a href="{{ route('admin.content.articles.index') }}" class="flex items-center gap-2 rounded border border-primary py-2 px-4.5 font-medium text-primary hover:bg-opacity-90">
                    <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 4H12.5V3.5C12.5 2.4 11.6 1.5 10.5 1.5H5.5C4.4 1.5 3.5 2.4 3.5 3.5V4H2C1.4 4 1 4.4 1 5C1 5.6 1.4 6 2 6H3.5V12.5C3.5 13.6 4.4 14.5 5.5 14.5H10.5C11.6 14.5 12.5 13.6 12.5 12.5V6H14C14.6 6 15 5.6 15 5C15 4.4 14.6 4 14 4ZM5.5 3H10.5C10.8 3 11 3.2 11 3.5V4H5V3.5C5 3.2 5.2 3 5.5 3ZM11 12.5C11 12.8 10.8 13 10.5 13H5.5C5.2 13 5 12.8 5 12.5V6H11V12.5Z" fill=""></path>
                    </svg>
                    Articles
                </a>
                <a href="{{ route('admin.content.testimonials.index') }}" class="flex items-center gap-2 rounded border border-primary py-2 px-4.5 font-medium text-primary hover:bg-opacity-90">
                    <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 4H12.5V3.5C12.5 2.4 11.6 1.5 10.5 1.5H5.5C4.4 1.5 3.5 2.4 3.5 3.5V4H2C1.4 4 1 4.4 1 5C1 5.6 1.4 6 2 6H3.5V12.5C3.5 13.6 4.4 14.5 5.5 14.5H10.5C11.6 14.5 12.5 13.6 12.5 12.5V6H14C14.6 6 15 5.6 15 5C15 4.4 14.6 4 14 4ZM5.5 3H10.5C10.8 3 11 3.2 11 3.5V4H5V3.5C5 3.2 5.2 3 5.5 3ZM11 12.5C11 12.8 10.8 13 10.5 13H5.5C5.2 13 5 12.8 5 12.5V6H11V12.5Z" fill=""></path>
                    </svg>
                    Testimonials
                </a>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-12 gap-4 md:gap-6 2xl:gap-7.5">
            <!-- Banners Section -->
            <div class="col-span-12 xl:col-span-4">
                <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                    <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
                        <h4 class="text-xl font-semibold text-black dark:text-white">
                            Banners
                        </h4>
                    </div>
                    <div class="p-6.5">
                        <div class="mb-4">
                            <div class="h-40 rounded-md bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                <img src="https://via.placeholder.com/300x150" alt="Banner" class="h-full w-full object-cover rounded-md">
                            </div>
                        </div>
                        <div class="mb-4">
                            <h5 class="font-medium text-black dark:text-white">Summer Sale Banner</h5>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Active</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.content.banners.edit', 1) }}" class="flex items-center gap-1 rounded border border-primary py-1 px-2 text-xs font-medium text-primary hover:bg-opacity-90">
                                <svg class="fill-current" width="12" height="12" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.55005 2.25H4.05005C2.7788 2.25 1.75505 3.29625 1.75505 4.5675L1.75505 13.5675C1.75505 14.8388 2.7788 15.885 4.05005 15.885H13.05C14.3213 15.885 15.3675 14.8388 15.3675 13.5675V9M11.475 1.5H16.5V6.525M3.375 14.625L11.25 6.75L15.75 11.25L7.875 15.75L3.375 15.75V14.625Z" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                                Edit
                            </a>
                            <button class="flex items-center gap-1 rounded border border-meta-1 py-1 px-2 text-xs font-medium text-meta-1 hover:bg-opacity-90">
                                <svg class="fill-current" width="12" height="12" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.7535 2.475H11.581C11.581 1.5 10.806 0.75 9.83104 0.75H8.22354C7.24854 0.75 6.47354 1.5 6.47354 2.475H4.30104C3.32604 2.475 2.55104 3.25 2.55104 4.225V4.825C2.55104 5.425 2.77604 5.95 3.15104 6.325L12.076 15.25C12.451 15.625 12.976 15.85 13.576 15.85H14.176C15.151 15.85 15.926 15.075 15.926 14.1V11.9275V4.225C15.926 3.25 15.151 2.475 14.176 2.475H13.7535ZM6.47354 2.475H9.56104V3C9.56104 3.375 9.86104 3.675 10.236 3.675H11.8435C12.2185 3.675 12.5185 3.375 12.5185 3V2.475H13.7535C14.1285 2.475 14.4285 2.775 14.4285 3.15V14.1C14.4285 14.475 14.1285 14.775 13.7535 14.775H13.576C13.201 14.775 12.901 14.475 12.901 14.1V11.9275C12.901 11.5525 12.601 11.2525 12.226 11.2525H5.81854C5.44354 11.2525 5.14354 11.5525 5.14354 11.9275V14.1C5.14354 14.475 4.84354 14.775 4.46854 14.775H4.30104C3.92604 14.775 3.62604 14.475 3.62604 14.1V4.225C3.62604 3.85 3.92604 3.55 4.30104 3.55H6.47354V2.475ZM11.401 8.17499C11.701 7.87499 11.701 7.34999 11.401 7.04999C11.101 6.74999 10.576 6.74999 10.276 7.04999L7.50104 9.82499C7.35104 9.97499 7.27604 10.125 7.20104 10.275L6.97604 10.8C6.90104 10.95 6.97604 11.175 7.12604 11.25C7.20104 11.325 7.35104 11.4 7.50104 11.325L8.02604 11.1C8.17604 11.025 8.32604 10.95 8.47604 10.8L11.401 8.17499Z" fill=""></path>
                                </svg>
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Articles Section -->
            <div class="col-span-12 xl:col-span-4">
                <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                    <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
                        <h4 class="text-xl font-semibold text-black dark:text-white">
                            Articles
                        </h4>
                    </div>
                    <div class="p-6.5">
                        <div class="mb-4">
                            <h5 class="font-medium text-black dark:text-white">How to Choose the Best Gaming Keyboard</h5>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Published on 2025-09-10</p>
                        </div>
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Learn how to choose the perfect gaming keyboard for your needs...</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.content.articles.edit', 1) }}" class="flex items-center gap-1 rounded border border-primary py-1 px-2 text-xs font-medium text-primary hover:bg-opacity-90">
                                <svg class="fill-current" width="12" height="12" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.55005 2.25H4.05005C2.7788 2.25 1.75505 3.29625 1.75505 4.5675L1.75505 13.5675C1.75505 14.8388 2.7788 15.885 4.05005 15.885H13.05C14.3213 15.885 15.3675 14.8388 15.3675 13.5675V9M11.475 1.5H16.5V6.525M3.375 14.625L11.25 6.75L15.75 11.25L7.875 15.75L3.375 15.75V14.625Z" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                                Edit
                            </a>
                            <button class="flex items-center gap-1 rounded border border-meta-1 py-1 px-2 text-xs font-medium text-meta-1 hover:bg-opacity-90">
                                <svg class="fill-current" width="12" height="12" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.7535 2.475H11.581C11.581 1.5 10.806 0.75 9.83104 0.75H8.22354C7.24854 0.75 6.47354 1.5 6.47354 2.475H4.30104C3.32604 2.475 2.55104 3.25 2.55104 4.225V4.825C2.55104 5.425 2.77604 5.95 3.15104 6.325L12.076 15.25C12.451 15.625 12.976 15.85 13.576 15.85H14.176C15.151 15.85 15.926 15.075 15.926 14.1V11.9275V4.225C15.926 3.25 15.151 2.475 14.176 2.475H13.7535ZM6.47354 2.475H9.56104V3C9.56104 3.375 9.86104 3.675 10.236 3.675H11.8435C12.2185 3.675 12.5185 3.375 12.5185 3V2.475H13.7535C14.1285 2.475 14.4285 2.775 14.4285 3.15V14.1C14.4285 14.475 14.1285 14.775 13.7535 14.775H13.576C13.201 14.775 12.901 14.475 12.901 14.1V11.9275C12.901 11.5525 12.601 11.2525 12.226 11.2525H5.81854C5.44354 11.2525 5.14354 11.5525 5.14354 11.9275V14.1C5.14354 14.475 4.84354 14.775 4.46854 14.775H4.30104C3.92604 14.775 3.62604 14.475 3.62604 14.1V4.225C3.62604 3.85 3.92604 3.55 4.30104 3.55H6.47354V2.475ZM11.401 8.17499C11.701 7.87499 11.701 7.34999 11.401 7.04999C11.101 6.74999 10.576 6.74999 10.276 7.04999L7.50104 9.82499C7.35104 9.97499 7.27604 10.125 7.20104 10.275L6.97604 10.8C6.90104 10.95 6.97604 11.175 7.12604 11.25C7.20104 11.325 7.35104 11.4 7.50104 11.325L8.02604 11.1C8.17604 11.025 8.32604 10.95 8.47604 10.8L11.401 8.17499Z" fill=""></path>
                                </svg>
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Testimonials Section -->
            <div class="col-span-12 xl:col-span-4">
                <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                    <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
                        <h4 class="text-xl font-semibold text-black dark:text-white">
                            Testimonials
                        </h4>
                    </div>
                    <div class="p-6.5">
                        <div class="mb-4 flex items-center">
                            <div class="h-12 w-12 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                <span class="font-medium text-black dark:text-white">JD</span>
                            </div>
                            <div class="ml-3">
                                <h5 class="font-medium text-black dark:text-white">John Doe</h5>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Verified Customer</p>
                            </div>
                        </div>
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">"The gaming keyboard I bought exceeded my expectations. Great quality and fast delivery!"</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.content.testimonials.edit', 1) }}" class="flex items-center gap-1 rounded border border-primary py-1 px-2 text-xs font-medium text-primary hover:bg-opacity-90">
                                <svg class="fill-current" width="12" height="12" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.55005 2.25H4.05005C2.7788 2.25 1.75505 3.29625 1.75505 4.5675L1.75505 13.5675C1.75505 14.8388 2.7788 15.885 4.05005 15.885H13.05C14.3213 15.885 15.3675 14.8388 15.3675 13.5675V9M11.475 1.5H16.5V6.525M3.375 14.625L11.25 6.75L15.75 11.25L7.875 15.75L3.375 15.75V14.625Z" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                                Edit
                            </a>
                            <button class="flex items-center gap-1 rounded border border-meta-1 py-1 px-2 text-xs font-medium text-meta-1 hover:bg-opacity-90">
                                <svg class="fill-current" width="12" height="12" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.7535 2.475H11.581C11.581 1.5 10.806 0.75 9.83104 0.75H8.22354C7.24854 0.75 6.47354 1.5 6.47354 2.475H4.30104C3.32604 2.475 2.55104 3.25 2.55104 4.225V4.825C2.55104 5.425 2.77604 5.95 3.15104 6.325L12.076 15.25C12.451 15.625 12.976 15.85 13.576 15.85H14.176C15.151 15.85 15.926 15.075 15.926 14.1V11.9275V4.225C15.926 3.25 15.151 2.475 14.176 2.475H13.7535ZM6.47354 2.475H9.56104V3C9.56104 3.375 9.86104 3.675 10.236 3.675H11.8435C12.2185 3.675 12.5185 3.375 12.5185 3V2.475H13.7535C14.1285 2.475 14.4285 2.775 14.4285 3.15V14.1C14.4285 14.475 14.1285 14.775 13.7535 14.775H13.576C13.201 14.775 12.901 14.475 12.901 14.1V11.9275C12.901 11.5525 12.601 11.2525 12.226 11.2525H5.81854C5.44354 11.2525 5.14354 11.5525 5.14354 11.9275V14.1C5.14354 14.475 4.84354 14.775 4.46854 14.775H4.30104C3.92604 14.775 3.62604 14.475 3.62604 14.1V4.225C3.62604 3.85 3.92604 3.55 4.30104 3.55H6.47354V2.475ZM11.401 8.17499C11.701 7.87499 11.701 7.34999 11.401 7.04999C11.101 6.74999 10.576 6.74999 10.276 7.04999L7.50104 9.82499C7.35104 9.97499 7.27604 10.125 7.20104 10.275L6.97604 10.8C6.90104 10.95 6.97604 11.175 7.12604 11.25C7.20104 11.325 7.35104 11.4 7.50104 11.325L8.02604 11.1C8.17604 11.025 8.32604 10.95 8.47604 10.8L11.401 8.17499Z" fill=""></path>
                                </svg>
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection