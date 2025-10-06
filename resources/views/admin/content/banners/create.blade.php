@extends('layouts.admin')

@section('header', 'Add New Banner')

@section('content')
    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        <div class="flex flex-col gap-7.5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-2xl font-bold text-black dark:text-white">
                    Add New Banner
                </h3>
                <p class="text-sm font-medium">Create a new homepage banner</p>
            </div>
            <a href="{{ route('admin.content.banners.index') }}" class="flex items-center gap-2 rounded border border-primary py-2 px-4.5 font-medium text-primary hover:bg-opacity-90">
                <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.99992 4.99992L12.9999 9.99992L7.99992 14.9999L6.99992 13.9999L10.5858 10.4141H1.99992V8.99992H10.5858L6.99992 5.41406L7.99992 4.99992Z" fill=""></path>
                </svg>
                Back to Banners
            </a>
        </div>

        <div class="mt-6 rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
            <form action="#">
                <div class="p-6.5">
                    <div class="mb-4.5">
                        <label class="mb-2.5 block text-black dark:text-white">
                            Title <span class="text-meta-1">*</span>
                        </label>
                        <input type="text" placeholder="Enter banner title" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                    </div>

                    <div class="mb-4.5">
                        <label class="mb-2.5 block text-black dark:text-white">
                            Description
                        </label>
                        <textarea rows="4" placeholder="Enter banner description" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary"></textarea>
                    </div>

                    <div class="mb-4.5">
                        <label class="mb-2.5 block text-black dark:text-white">
                            Banner Image <span class="text-meta-1">*</span>
                        </label>
                        <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                            <div class="flex flex-col gap-5.5 sm:flex-row">
                                <div class="flex w-full flex-col">
                                    <div class="h-150 rounded border border-dashed border-primary p-3 dark:border-strokedark">
                                        <div class="flex h-full w-full items-center justify-center">
                                            <div class="flex flex-col items-center">
                                                <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M18.75 12.75H15.75V15.75C15.75 16.1625 15.4125 16.5 15 16.5C14.5875 16.5 14.25 16.1625 14.25 15.75V12.75H11.25C10.8375 12.75 10.5 12.4125 10.5 12C10.5 11.5875 10.8375 11.25 11.25 11.25H14.25V8.25C14.25 7.8375 14.5875 7.5 15 7.5C15.4125 7.5 15.75 7.8375 15.75 8.25V11.25H18.75C19.1625 11.25 19.5 11.5875 19.5 12C19.5 12.4125 19.1625 12.75 18.75 12.75Z" fill=""></path>
                                                    <path d="M12 22.5C6.2325 22.5 1.5 17.7675 1.5 12C1.5 6.2325 6.2325 1.5 12 1.5C17.7675 1.5 22.5 6.2325 22.5 12C22.5 17.7675 17.7675 22.5 12 22.5ZM12 3C7.0575 3 3 7.0575 3 12C3 16.9425 7.0575 21 12 21C16.9425 21 21 16.9425 21 12C21 7.0575 16.9425 3 12 3Z" fill=""></path>
                                                </svg>
                                                <p class="mt-2 text-sm font-medium">Drag & Drop or Click to upload</p>
                                                <p class="mt-1 text-xs">PNG, JPG, GIF up to 10MB</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4.5">
                        <label class="mb-2.5 block text-black dark:text-white">
                            Link URL (optional)
                        </label>
                        <input type="url" placeholder="Enter link URL" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                    </div>

                    <div class="mb-4.5">
                        <label class="mb-2.5 block text-black dark:text-white">
                            Status <span class="text-meta-1">*</span>
                        </label>
                        <select class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                            <option value="">Select Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="flex justify-end gap-4.5">
                        <button class="flex items-center gap-2 rounded border border-primary py-2 px-6 font-medium text-primary hover:bg-opacity-90">
                            Reset
                        </button>
                        <button class="flex items-center gap-2 rounded bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
                            Save Banner
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection