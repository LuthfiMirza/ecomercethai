@extends('layouts.admin')

@section('header', 'Testimonial Management')

@section('content')
    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        <div class="flex flex-col gap-7.5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-2xl font-bold text-black dark:text-white">
                    Testimonial Management
                </h3>
                <p class="text-sm font-medium">Manage customer testimonials</p>
            </div>
            <a href="{{ route('admin.content.testimonials.create') }}" class="flex items-center gap-2 rounded bg-primary py-2 px-4.5 font-medium text-white hover:bg-opacity-90">
                <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 7H9V1C9 0.4 8.6 0 8 0C7.4 0 7 0.4 7 1V7H1C0.4 7 0 7.4 0 8C0 8.6 0.4 9 1 9H7V15C7 15.6 7.4 16 8 16C8.6 16 9 15.6 9 15V9H15C15.6 9 16 8.6 16 8C16 7.4 15.6 7 15 7Z" fill=""></path>
                </svg>
                Add New Testimonial
            </a>
        </div>

        <div class="mt-4 grid grid-cols-12 gap-4 md:mt-6 md:gap-6 2xl:mt-7.5 2xl:gap-7.5">
            <div class="col-span-12 xl:col-span-12">
                <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-6">
                        <div>
                            <h4 class="text-xl font-semibold text-black dark:text-white">
                                Testimonial List
                            </h4>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="relative">
                                <input type="text" placeholder="Search testimonials..." class="w-full rounded border border-stroke bg-gray-50 py-2 pl-10 pr-4 text-black focus:border-primary focus-visible:outline-none dark:border-strokedark dark:bg-meta-4 dark:text-white dark:focus:border-primary">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2">
                                    <svg class="fill-body dark:fill-bodydark" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M9.16667 3.33333C5.94501 3.33333 3.33334 5.945 3.33334 9.16667C3.33334 12.3883 5.94501 15 9.16667 15C12.3883 15 15 12.3883 15 9.16667C15 5.945 12.3883 3.33333 9.16667 3.33333ZM1.66667 9.16667C1.66667 5.02453 5.02451 1.66667 9.16667 1.66667C13.3088 1.66667 16.6667 5.02453 16.6667 9.16667C16.6667 13.3088 13.3088 16.6667 9.16667 16.6667C5.02451 16.6667 1.66667 13.3088 1.66667 9.16667Z" fill=""></path>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M17.7071 16.2929C18.0976 15.9024 18.0976 15.2692 17.7071 14.8787C17.3166 14.4882 16.6834 14.4882 16.2929 14.8787L14.2929 16.8787C13.9024 17.2692 13.9024 17.9024 14.2929 18.2929C14.6834 18.6834 15.3166 18.6834 15.7071 18.2929L17.7071 16.2929Z" fill=""></path>
                                    </svg>
                                </span>
                            </div>
                            <div class="relative">
                                <select class="w-full rounded border border-stroke bg-gray-50 py-2 pl-4 pr-10 text-black focus:border-primary focus-visible:outline-none dark:border-strokedark dark:bg-meta-4 dark:text-white dark:focus:border-primary appearance-none">
                                    <option value="">All Statuses</option>
                                    <option value="published">Published</option>
                                    <option value="pending">Pending</option>
                                </select>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2">
                                    <svg class="fill-body dark:fill-bodydark" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9 14.25C8.6575 14.25 8.3425 14.125 8.1075 13.89L2.1075 7.89C1.6175 7.4 1.6175 6.61 2.1075 6.12C2.5975 5.63 3.3875 5.63 3.8775 6.12L9 11.2425L14.1225 6.12C14.6125 5.63 15.4025 5.63 15.8925 6.12C16.3825 6.61 16.3825 7.4 15.8925 7.89L9.8925 13.89C9.6575 14.125 9.3425 14.25 9 14.25Z" fill=""></path>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col">
                        <div class="grid grid-cols-12 rounded-sm bg-gray-2 dark:bg-meta-4">
                            <div class="col-span-4 p-2.5 xl:p-5">
                                <h5 class="text-sm font-medium uppercase xsm:text-xs">Customer</h5>
                            </div>
                            <div class="col-span-5 p-2.5 xl:p-5">
                                <h5 class="text-sm font-medium uppercase xsm:text-xs">Testimonial</h5>
                            </div>
                            <div class="col-span-1 p-2.5 xl:p-5">
                                <h5 class="text-sm font-medium uppercase xsm:text-xs">Rating</h5>
                            </div>
                            <div class="col-span-2 p-2.5 xl:p-5">
                                <h5 class="text-sm font-medium uppercase xsm:text-xs">Actions</h5>
                            </div>
                        </div>

                        <div class="grid grid-cols-12 border-t border-stroke dark:border-strokedark">
                            <div class="col-span-4 p-2.5 xl:p-5">
                                <div class="flex items-center">
                                    <div class="h-12 w-12 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                        <span class="font-medium text-black dark:text-white">JD</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-medium text-black dark:text-white">John Doe</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">john.doe@example.com</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-5 p-2.5 xl:p-5">
                                <p class="text-sm text-gray-500 dark:text-gray-400">"The gaming keyboard I bought exceeded my expectations. Great quality and fast delivery!"</p>
                            </div>
                            <div class="col-span-1 p-2.5 xl:p-5">
                                <div class="flex items-center">
                                    <svg class="fill-warning" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 12.5L4.5 14.5L5.5 11L3 8.5L6.5 8L8 5L9.5 8L13 8.5L10.5 11L11.5 14.5L8 12.5Z" fill=""></path>
                                    </svg>
                                    <svg class="fill-warning" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 12.5L4.5 14.5L5.5 11L3 8.5L6.5 8L8 5L9.5 8L13 8.5L10.5 11L11.5 14.5L8 12.5Z" fill=""></path>
                                    </svg>
                                    <svg class="fill-warning" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 12.5L4.5 14.5L5.5 11L3 8.5L6.5 8L8 5L9.5 8L13 8.5L10.5 11L11.5 14.5L8 12.5Z" fill=""></path>
                                    </svg>
                                    <svg class="fill-warning" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 12.5L4.5 14.5L5.5 11L3 8.5L6.5 8L8 5L9.5 8L13 8.5L10.5 11L11.5 14.5L8 12.5Z" fill=""></path>
                                    </svg>
                                    <svg class="fill-warning" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 12.5L4.5 14.5L5.5 11L3 8.5L6.5 8L8 5L9.5 8L13 8.5L10.5 11L11.5 14.5L8 12.5Z" fill=""></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="col-span-2 p-2.5 xl:p-5">
                                <div class="flex items-center space-x-3.5">
                                    <a href="{{ route('admin.content.testimonials.edit', 1) }}" class="hover:text-primary">
                                        <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8.55005 2.25H4.05005C2.7788 2.25 1.75505 3.29625 1.75505 4.5675L1.75505 13.5675C1.75505 14.8388 2.7788 15.885 4.05005 15.885H13.05C14.3213 15.885 15.3675 14.8388 15.3675 13.5675V9M11.475 1.5H16.5V6.525M3.375 14.625L11.25 6.75L15.75 11.25L7.875 15.75L3.375 15.75V14.625Z" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                    </a>
                                    <button class="hover:text-primary">
                                        <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M13.7535 2.475H11.581C11.581 1.5 10.806 0.75 9.83104 0.75H8.22354C7.24854 0.75 6.47354 1.5 6.47354 2.475H4.30104C3.32604 2.475 2.55104 3.25 2.55104 4.225V4.825C2.55104 5.425 2.77604 5.95 3.15104 6.325L12.076 15.25C12.451 15.625 12.976 15.85 13.576 15.85H14.176C15.151 15.85 15.926 15.075 15.926 14.1V11.9275V4.225C15.926 3.25 15.151 2.475 14.176 2.475H13.7535ZM6.47354 2.475H9.56104V3C9.56104 3.375 9.86104 3.675 10.236 3.675H11.8435C12.2185 3.675 12.5185 3.375 12.5185 3V2.475H13.7535C14.1285 2.475 14.4285 2.775 14.4285 3.15V14.1C14.4285 14.475 14.1285 14.775 13.7535 14.775H13.576C13.201 14.775 12.901 14.475 12.901 14.1V11.9275C12.901 11.5525 12.601 11.2525 12.226 11.2525H5.81854C5.44354 11.2525 5.14354 11.5525 5.14354 11.9275V14.1C5.14354 14.475 4.84354 14.775 4.46854 14.775H4.30104C3.92604 14.775 3.62604 14.475 3.62604 14.1V4.225C3.62604 3.85 3.92604 3.55 4.30104 3.55H6.47354V2.475ZM11.401 8.17499C11.701 7.87499 11.701 7.34999 11.401 7.04999C11.101 6.74999 10.576 6.74999 10.276 7.04999L7.50104 9.82499C7.35104 9.97499 7.27604 10.125 7.20104 10.275L6.97604 10.8C6.90104 10.95 6.97604 11.175 7.12604 11.25C7.20104 11.325 7.35104 11.4 7.50104 11.325L8.02604 11.1C8.17604 11.025 8.32604 10.95 8.47604 10.8L11.401 8.17499Z" fill=""></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-12 border-t border-stroke dark:border-strokedark">
                            <div class="col-span-4 p-2.5 xl:p-5">
                                <div class="flex items-center">
                                    <div class="h-12 w-12 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                        <span class="font-medium text-black dark:text-white">JS</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-medium text-black dark:text-white">Jane Smith</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">jane.smith@example.com</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-5 p-2.5 xl:p-5">
                                <p class="text-sm text-gray-500 dark:text-gray-400">"Excellent customer service and the mouse I ordered works perfectly. Highly recommended!"</p>
                            </div>
                            <div class="col-span-1 p-2.5 xl:p-5">
                                <div class="flex items-center">
                                    <svg class="fill-warning" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 12.5L4.5 14.5L5.5 11L3 8.5L6.5 8L8 5L9.5 8L13 8.5L10.5 11L11.5 14.5L8 12.5Z" fill=""></path>
                                    </svg>
                                    <svg class="fill-warning" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 12.5L4.5 14.5L5.5 11L3 8.5L6.5 8L8 5L9.5 8L13 8.5L10.5 11L11.5 14.5L8 12.5Z" fill=""></path>
                                    </svg>
                                    <svg class="fill-warning" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 12.5L4.5 14.5L5.5 11L3 8.5L6.5 8L8 5L9.5 8L13 8.5L10.5 11L11.5 14.5L8 12.5Z" fill=""></path>
                                    </svg>
                                    <svg class="fill-warning" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 12.5L4.5 14.5L5.5 11L3 8.5L6.5 8L8 5L9.5 8L13 8.5L10.5 11L11.5 14.5L8 12.5Z" fill=""></path>
                                    </svg>
                                    <svg class="fill-gray-300 dark:fill-gray-600" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 12.5L4.5 14.5L5.5 11L3 8.5L6.5 8L8 5L9.5 8L13 8.5L10.5 11L11.5 14.5L8 12.5Z" fill=""></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="col-span-2 p-2.5 xl:p-5">
                                <div class="flex items-center space-x-3.5">
                                    <a href="{{ route('admin.content.testimonials.edit', 2) }}" class="hover:text-primary">
                                        <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8.55005 2.25H4.05005C2.7788 2.25 1.75505 3.29625 1.75505 4.5675L1.75505 13.5675C1.75505 14.8388 2.7788 15.885 4.05005 15.885H13.05C14.3213 15.885 15.3675 14.8388 15.3675 13.5675V9M11.475 1.5H16.5V6.525M3.375 14.625L11.25 6.75L15.75 11.25L7.875 15.75L3.375 15.75V14.625Z" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                    </a>
                                    <button class="hover:text-primary">
                                        <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M13.7535 2.475H11.581C11.581 1.5 10.806 0.75 9.83104 0.75H8.22354C7.24854 0.75 6.47354 1.5 6.47354 2.475H4.30104C3.32604 2.475 2.55104 3.25 2.55104 4.225V4.825C2.55104 5.425 2.77604 5.95 3.15104 6.325L12.076 15.25C12.451 15.625 12.976 15.85 13.576 15.85H14.176C15.151 15.85 15.926 15.075 15.926 14.1V11.9275V4.225C15.926 3.25 15.151 2.475 14.176 2.475H13.7535ZM6.47354 2.475H9.56104V3C9.56104 3.375 9.86104 3.675 10.236 3.675H11.8435C12.2185 3.675 12.5185 3.375 12.5185 3V2.475H13.7535C14.1285 2.475 14.4285 2.775 14.4285 3.15V14.1C14.4285 14.475 14.1285 14.775 13.7535 14.775H13.576C13.201 14.775 12.901 14.475 12.901 14.1V11.9275C12.901 11.5525 12.601 11.2525 12.226 11.2525H5.81854C5.44354 11.2525 5.14354 11.5525 5.14354 11.9275V14.1C5.14354 14.475 4.84354 14.775 4.46854 14.775H4.30104C3.92604 14.775 3.62604 14.475 3.62604 14.1V4.225C3.62604 3.85 3.92604 3.55 4.30104 3.55H6.47354V2.475ZM11.401 8.17499C11.701 7.87499 11.701 7.34999 11.401 7.04999C11.101 6.74999 10.576 6.74999 10.276 7.04999L7.50104 9.82499C7.35104 9.97499 7.27604 10.125 7.20104 10.275L6.97604 10.8C6.90104 10.95 6.97604 11.175 7.12604 11.25C7.20104 11.325 7.35104 11.4 7.50104 11.325L8.02604 11.1C8.17604 11.025 8.32604 10.95 8.47604 10.8L11.401 8.17499Z" fill=""></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-between border-t border-stroke py-4 px-6 dark:border-strokedark">
                        <p class="text-sm text-black dark:text-white">
                            Showing 1 to 2 of 2 entries
                        </p>
                        <div class="flex gap-2 mt-3 sm:mt-0">
                            <button class="flex items-center justify-center rounded border border-stroke bg-white px-3 py-1 text-sm font-medium text-black hover:bg-primary hover:text-white dark:border-strokedark dark:bg-meta-4 dark:text-white dark:hover:bg-primary">
                                Previous
                            </button>
                            <button class="flex items-center justify-center rounded border border-stroke bg-white px-3 py-1 text-sm font-medium text-black hover:bg-primary hover:text-white dark:border-strokedark dark:bg-meta-4 dark:text-white dark:hover:bg-primary">
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection