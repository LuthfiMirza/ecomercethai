@extends('layouts.admin')

@section('header', 'Edit Testimonial')

@section('content')
    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        <div class="flex flex-col gap-7.5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-2xl font-bold text-black dark:text-white">
                    Edit Testimonial
                </h3>
                <p class="text-sm font-medium">Modify the customer testimonial</p>
            </div>
            <a href="{{ route('admin.content.testimonials.index') }}" class="flex items-center gap-2 rounded border border-primary py-2 px-4.5 font-medium text-primary hover:bg-opacity-90">
                <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.99992 4.99992L12.9999 9.99992L7.99992 14.9999L6.99992 13.9999L10.5858 10.4141H1.99992V8.99992H10.5858L6.99992 5.41406L7.99992 4.99992Z" fill=""></path>
                </svg>
                Back to Testimonials
            </a>
        </div>

        <div class="mt-6 rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
            <form action="#">
                <div class="p-6.5">
                    <div class="mb-4.5">
                        <label class="mb-2.5 block text-black dark:text-white">
                            Customer Name <span class="text-meta-1">*</span>
                        </label>
                        <input type="text" placeholder="Enter customer name" value="John Doe" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                    </div>

                    <div class="mb-4.5">
                        <label class="mb-2.5 block text-black dark:text-white">
                            Customer Email <span class="text-meta-1">*</span>
                        </label>
                        <input type="email" placeholder="Enter customer email" value="john.doe@example.com" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                    </div>

                    <div class="mb-4.5">
                        <label class="mb-2.5 block text-black dark:text-white">
                            Testimonial <span class="text-meta-1">*</span>
                        </label>
                        <textarea rows="5" placeholder="Enter customer testimonial" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">"The gaming keyboard I bought exceeded my expectations. Great quality and fast delivery!"</textarea>
                    </div>

                    <div class="mb-4.5">
                        <label class="mb-2.5 block text-black dark:text-white">
                            Rating <span class="text-meta-1">*</span>
                        </label>
                        <div class="flex items-center">
                            <div class="flex">
                                <svg class="rating-star fill-warning cursor-pointer" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" data-rating="1">
                                    <path d="M12 17.25L5.25 21L7.05 13.95L1.5 9L8.25 8.25L12 1.5L15.75 8.25L22.5 9L16.95 13.95L18.75 21L12 17.25Z" fill=""></path>
                                </svg>
                                <svg class="rating-star fill-warning cursor-pointer" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" data-rating="2">
                                    <path d="M12 17.25L5.25 21L7.05 13.95L1.5 9L8.25 8.25L12 1.5L15.75 8.25L22.5 9L16.95 13.95L18.75 21L12 17.25Z" fill=""></path>
                                </svg>
                                <svg class="rating-star fill-warning cursor-pointer" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" data-rating="3">
                                    <path d="M12 17.25L5.25 21L7.05 13.95L1.5 9L8.25 8.25L12 1.5L15.75 8.25L22.5 9L16.95 13.95L18.75 21L12 17.25Z" fill=""></path>
                                </svg>
                                <svg class="rating-star fill-warning cursor-pointer" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" data-rating="4">
                                    <path d="M12 17.25L5.25 21L7.05 13.95L1.5 9L8.25 8.25L12 1.5L15.75 8.25L22.5 9L16.95 13.95L18.75 21L12 17.25Z" fill=""></path>
                                </svg>
                                <svg class="rating-star fill-gray-300 dark:fill-gray-600 cursor-pointer" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" data-rating="5">
                                    <path d="M12 17.25L5.25 21L7.05 13.95L1.5 9L8.25 8.25L12 1.5L15.75 8.25L22.5 9L16.95 13.95L18.75 21L12 17.25Z" fill=""></path>
                                </svg>
                            </div>
                            <input type="hidden" id="rating" name="rating" value="4">
                            <span class="ml-3 text-sm font-medium text-black dark:text-white" id="rating-value">4 stars</span>
                        </div>
                    </div>

                    <div class="mb-4.5">
                        <label class="mb-2.5 block text-black dark:text-white">
                            Customer Photo
                        </label>
                        <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                            <div class="flex flex-col gap-5.5 sm:flex-row">
                                <div class="flex w-full flex-col">
                                    <div class="h-150 rounded border border-dashed border-primary p-3 dark:border-strokedark">
                                        <div class="flex h-full w-full items-center justify-center">
                                            <img src="https://via.placeholder.com/60" alt="Customer" class="h-full w-full object-cover rounded-md">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4.5">
                        <label class="mb-2.5 block text-black dark:text-white">
                            Status <span class="text-meta-1">*</span>
                        </label>
                        <select class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                            <option value="">Select Status</option>
                            <option value="published" selected>Published</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>

                    <div class="flex justify-end gap-4.5">
                        <button class="flex items-center gap-2 rounded border border-primary py-2 px-6 font-medium text-primary hover:bg-opacity-90">
                            Reset
                        </button>
                        <button class="flex items-center gap-2 rounded bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
                            Update Testimonial
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection