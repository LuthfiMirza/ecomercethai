@extends('layouts.admin')

@section('header', 'Edit Article')

@section('content')
    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        <div class="flex flex-col gap-7.5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-2xl font-bold text-black dark:text-white">
                    Edit Article
                </h3>
                <p class="text-sm font-medium">Modify the blog article</p>
            </div>
            <a href="{{ route('admin.content.articles.index') }}" class="flex items-center gap-2 rounded border border-primary py-2 px-4.5 font-medium text-primary hover:bg-opacity-90">
                <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.99992 4.99992L12.9999 9.99992L7.99992 14.9999L6.99992 13.9999L10.5858 10.4141H1.99992V8.99992H10.5858L6.99992 5.41406L7.99992 4.99992Z" fill=""></path>
                </svg>
                Back to Articles
            </a>
        </div>

        <div class="mt-6 rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
            <form action="#">
                <div class="p-6.5">
                    <div class="mb-4.5">
                        <label class="mb-2.5 block text-black dark:text-white">
                            Title <span class="text-meta-1">*</span>
                        </label>
                        <input type="text" placeholder="Enter article title" value="How to Choose the Best Gaming Keyboard" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">
                    </div>

                    <div class="mb-4.5">
                        <label class="mb-2.5 block text-black dark:text-white">
                            Excerpt
                        </label>
                        <textarea rows="3" placeholder="Enter article excerpt" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">Learn how to choose the perfect gaming keyboard for your needs...</textarea>
                    </div>

                    <div class="mb-4.5">
                        <label class="mb-2.5 block text-black dark:text-white">
                            Content <span class="text-meta-1">*</span>
                        </label>
                        <textarea rows="6" placeholder="Enter article content" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary">When it comes to gaming, having the right keyboard can make a huge difference in your performance and overall experience. Here are some key factors to consider when choosing a gaming keyboard...</textarea>
                    </div>

                    <div class="mb-4.5">
                        <label class="mb-2.5 block text-black dark:text-white">
                            Featured Image
                        </label>
                        <div class="rounded-sm border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark">
                            <div class="flex flex-col gap-5.5 sm:flex-row">
                                <div class="flex w-full flex-col">
                                    <div class="h-150 rounded border border-dashed border-primary p-3 dark:border-strokedark">
                                        <div class="flex h-full w-full items-center justify-center">
                                            <img src="https://via.placeholder.com/300x200" alt="Article" class="h-full w-full object-cover rounded-md">
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
                            <option value="draft">Draft</option>
                        </select>
                    </div>

                    <div class="flex justify-end gap-4.5">
                        <button class="flex items-center gap-2 rounded border border-primary py-2 px-6 font-medium text-primary hover:bg-opacity-90">
                            Reset
                        </button>
                        <button class="flex items-center gap-2 rounded bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
                            Update Article
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection