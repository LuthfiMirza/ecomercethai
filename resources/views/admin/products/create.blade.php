@extends('layouts.admin')

@section('header', 'Add New Product')

@section('content')
<div class="bg-white dark:bg-slate-800 shadow-md rounded-lg p-6">
    <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-6">Create New Product</h2>

    <form action="{{ localized_route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Product Name and Category -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Product Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('name') border-red-500 @enderror">
                @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="category_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Category</label>
                <select id="category_id" name="category_id" required
                        class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('category_id') border-red-500 @enderror">
                    <option value="">Select a category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Price and Stock -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="price" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Price</label>
                <input type="number" id="price" name="price" value="{{ old('price') }}" required step="0.01"
                       class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('price') border-red-500 @enderror">
                @error('price') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="stock" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Stock</label>
                <input type="number" id="stock" name="stock" value="{{ old('stock') }}" required
                       class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('stock') border-red-500 @enderror">
                @error('stock') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Description -->
        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Description</label>
            <textarea id="description" name="description" rows="4"
                      class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-slate-700 dark:border-slate-600 dark:text-white">{{ old('description') }}</textarea>
        </div>

        <!-- Color Options -->
        <div class="mb-6">
            <label for="colors" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Available Colors (optional)</label>
            <textarea id="colors" name="colors" rows="2"
                      placeholder="Example: Black, White, Silver"
                      class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('colors') border-red-500 @enderror">{{ old('colors') }}</textarea>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Separate multiple colors with commas or new lines.</p>
            @error('colors') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Status -->
        <div class="mb-6">
            <label for="status" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Status</label>
            <select id="status" name="status" required
                    class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <!-- Image Upload -->
        <div class="mb-6">
            <label for="image" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Primary Product Image</label>
            <input type="file" id="image" name="image"
                   class="w-full text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-slate-700 dark:file:text-indigo-300 dark:hover:file:bg-slate-600">
            @error('image') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Used as the cover image across listings.</p>
        </div>

        <!-- Gallery Upload -->
        <div class="mb-6">
            <label for="gallery_images" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Gallery Images</label>
            <input type="file" id="gallery_images" name="gallery_images[]" multiple
                   class="w-full text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-slate-700 dark:file:text-indigo-300 dark:hover:file:bg-slate-600">
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">You can select multiple files at once (max 4 MB each).</p>
            @error('gallery_images.*') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        @php
            $oldColorGalleries = old('color_galleries', []);
        @endphp

        <!-- Color Galleries -->
        <div class="mb-8" data-color-gallery-root data-color-gallery-index="{{ count($oldColorGalleries) }}">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Color Galleries</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Add separate images for each color variant.</p>
                </div>
                <button type="button" class="inline-flex items-center gap-2 rounded-full border border-slate-300 px-4 py-1.5 text-xs font-semibold text-slate-700 hover:border-slate-400 dark:border-slate-600 dark:text-slate-200" data-color-gallery-add>
                    <i class="fa-solid fa-plus"></i>
                    {{ __('Add Color') }}
                </button>
            </div>
            <div class="mt-4 space-y-4" data-color-gallery-list>
                @foreach($oldColorGalleries as $index => $gallery)
                    <div class="rounded-2xl border border-slate-200 p-4 dark:border-slate-700" data-color-gallery-item>
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex-1">
                                <label class="text-xs font-semibold text-slate-600 dark:text-slate-200">Color name / key</label>
                                <input type="text" name="color_galleries[{{ $index }}][color_key]" value="{{ $gallery['color_key'] ?? '' }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-white" placeholder="e.g. Merah" data-color-input>
                            </div>
                            <button type="button" class="text-xs font-semibold text-rose-600 hover:text-rose-500" data-color-gallery-remove>&times; {{ __('Remove') }}</button>
                        </div>
                        <div class="mt-3">
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-200">Upload images</label>
                            <input type="file" name="color_galleries[{{ $index }}][images][]" multiple class="mt-1 w-full text-slate-500 file:mr-4 file:rounded-full file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-slate-700 dark:file:text-indigo-300">
                            <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">These images will show when the color is selected.</p>
                            @error('color_galleries.' . $index . '.images.*')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endforeach
            </div>
            <template data-color-gallery-template>
                <div class="rounded-2xl border border-slate-200 p-4 dark:border-slate-700" data-color-gallery-item>
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-1">
                            <label class="text-xs font-semibold text-slate-600 dark:text-slate-200">Color name / key</label>
                            <input type="text" name="color_galleries[__INDEX__][color_key]" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-white" placeholder="e.g. Navy" data-color-input>
                        </div>
                        <button type="button" class="text-xs font-semibold text-rose-600 hover:text-rose-500" data-color-gallery-remove>&times; {{ __('Remove') }}</button>
                    </div>
                    <div class="mt-3">
                        <label class="text-xs font-semibold text-slate-600 dark:text-slate-200">Upload images</label>
                        <input type="file" name="color_galleries[__INDEX__][images][]" multiple class="mt-1 w-full text-slate-500 file:mr-4 file:rounded-full file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-slate-700 dark:file:text-indigo-300">
                        <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">Select one or more files for this color.</p>
                    </div>
                </div>
            </template>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-4">
            <a href="{{ localized_route('admin.products.index') }}" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:bg-slate-600 dark:hover:bg-slate-500">
                Save Product
            </button>
        </div>
    </form>
</div>
@endsection

@include('admin.products.partials.color-gallery-scripts')
