@extends('layouts.admin')

@section('header', 'Edit Product')

@section('content')
<div class="bg-white dark:bg-slate-800 shadow-md rounded-lg p-6">
    <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 mb-6">Edit Product: {{ $product->name }}</h2>

    <form action="{{ localized_route('admin.products.update', ['id' => $product->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Product Name and Category -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Product Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required
                       class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('name') border-red-500 @enderror">
                @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="category_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Category</label>
                <select id="category_id" name="category_id" required
                        class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('category_id') border-red-500 @enderror">
                    <option value="">Select a category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Price and Stock -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="price" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Price</label>
                <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" required step="0.01"
                       class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('price') border-red-500 @enderror">
                @error('price') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="stock" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Stock</label>
                <input type="number" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" required
                       class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('stock') border-red-500 @enderror">
                @error('stock') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Description -->
        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Description</label>
            <textarea id="description" name="description" rows="4"
                      class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-slate-700 dark:border-slate-600 dark:text-white">{{ old('description', $product->description) }}</textarea>
        </div>

        <!-- Color Options -->
        <div class="mb-6">
            <label for="colors" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Available Colors (optional)</label>
            <textarea id="colors" name="colors" rows="2"
                      placeholder="Example: Black, White, Silver"
                      class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('colors') border-red-500 @enderror">{{ old('colors', implode(', ', $product->available_colors ?? [])) }}</textarea>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Separate multiple colors with commas or new lines.</p>
            @error('colors') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Status -->
        <div class="mb-6">
            <label for="status" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Status</label>
            <select id="status" name="status" required
                    class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <!-- Image Upload -->
        <div class="mb-6">
            <label for="image" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Update Primary Image</label>
            <div class="flex items-center space-x-6">
                <div class="shrink-0">
                    <img class="h-20 w-20 object-cover rounded-md" src="{{ $product->image_url ?? 'https://via.placeholder.com/80' }}" alt="Current product image">
                </div>
                <input type="file" id="image" name="image"
                       class="w-full text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-slate-700 dark:file:text-indigo-300 dark:hover:file:bg-slate-600">
            </div>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Leave empty to keep the current cover.</p>
            @error('image') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Existing Gallery -->
        @if($product->images->isNotEmpty())
            <div class="mb-6">
                <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Current Gallery Images</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($product->images as $image)
                        <label class="relative block rounded-xl border border-slate-200 dark:border-slate-700/60 overflow-hidden group">
                            @if($image->is_primary)
                                <span class="absolute left-2 top-2 rounded-full bg-emerald-500/90 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-white shadow">Primary</span>
                            @endif
                            <img src="{{ $image->url ?? 'https://via.placeholder.com/160' }}" alt="Gallery image" class="h-32 w-full object-cover">
                            <div class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-slate-800">
                                <input type="checkbox" name="remove_images[]" value="{{ $image->id }}" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Remove</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Select any images you want to delete, then save the product.</p>
            </div>
        @endif

        <!-- Gallery Upload -->
        <div class="mb-6">
            <label for="gallery_images" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Add Gallery Images</label>
            <input type="file" id="gallery_images" name="gallery_images[]" multiple
                   class="w-full text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-slate-700 dark:file:text-indigo-300 dark:hover:file:bg-slate-600">
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">You can upload multiple files (max 4 MB each).</p>
            @error('gallery_images.*') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-4">
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:bg-slate-600 dark:hover:bg-slate-500">
                Update Product
            </button>
        </div>
    </form>
</div>
@endsection
