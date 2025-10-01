<div class="space-y-1">
  <label class="block text-sm font-medium">Name</label>
  <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" class="w-full border-gray-300 rounded" required>
  @error('name')<div class="text-sm text-red-600">{{ $message }}</div>@enderror
</div>
<div class="space-y-1">
  <label class="block text-sm font-medium">Description</label>
  <textarea name="description" rows="4" class="w-full border-gray-300 rounded">{{ old('description', $product->description ?? '') }}</textarea>
  @error('description')<div class="text-sm text-red-600">{{ $message }}</div>@enderror
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
  <div class="space-y-1">
    <label class="block text-sm font-medium">Price</label>
    <input type="number" name="price" step="0.01" min="0" value="{{ old('price', $product->price ?? 0) }}" class="w-full border-gray-300 rounded" required>
    @error('price')<div class="text-sm text-red-600">{{ $message }}</div>@enderror
  </div>
  <div class="space-y-1">
    <label class="block text-sm font-medium">Stock</label>
    <input type="number" name="stock" min="0" value="{{ old('stock', $product->stock ?? 0) }}" class="w-full border-gray-300 rounded" required>
    @error('stock')<div class="text-sm text-red-600">{{ $message }}</div>@enderror
  </div>
</div>
<div class="flex items-center gap-2">
  <input type="checkbox" name="is_active" value="1" {{ old('is_active', ($product->is_active ?? true)) ? 'checked' : '' }}>
  <label>Active</label>
</div>

