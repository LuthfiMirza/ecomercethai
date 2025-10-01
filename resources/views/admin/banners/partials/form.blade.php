<div class="space-y-1">
  <label class="block text-sm font-medium">Title</label>
  <input type="text" name="title" value="{{ old('title', $banner->title ?? '') }}" class="w-full border-gray-300 rounded" required>
  @error('title')<div class="text-sm text-red-600">{{ $message }}</div>@enderror
</div>
<div class="space-y-1">
  <label class="block text-sm font-medium">Image Path/URL</label>
  <input type="text" name="image_path" value="{{ old('image_path', $banner->image_path ?? '') }}" class="w-full border-gray-300 rounded" required>
  @error('image_path')<div class="text-sm text-red-600">{{ $message }}</div>@enderror
</div>
<div class="space-y-1">
  <label class="block text-sm font-medium">Link URL</label>
  <input type="url" name="link_url" value="{{ old('link_url', $banner->link_url ?? '') }}" class="w-full border-gray-300 rounded">
  @error('link_url')<div class="text-sm text-red-600">{{ $message }}</div>@enderror
</div>
<div class="space-y-1">
  <label class="block text-sm font-medium">Placement</label>
  <select name="placement" class="w-full border-gray-300 rounded">
    @foreach($placements as $p)
      <option value="{{ $p }}" @selected(old('placement', $banner->placement ?? '') == $p)>{{ $p }}</option>
    @endforeach
  </select>
  @error('placement')<div class="text-sm text-red-600">{{ $message }}</div>@enderror
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
  <div class="space-y-1">
    <label class="block text-sm font-medium">Starts At</label>
    <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($banner->starts_at ?? null)->format('Y-m-d\TH:i')) }}" class="w-full border-gray-300 rounded">
  </div>
  <div class="space-y-1">
    <label class="block text-sm font-medium">Ends At</label>
    <input type="datetime-local" name="ends_at" value="{{ old('ends_at', optional($banner->ends_at ?? null)->format('Y-m-d\TH:i')) }}" class="w-full border-gray-300 rounded">
  </div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
  <div class="flex items-center gap-2">
    <input type="checkbox" name="is_active" value="1" {{ old('is_active', ($banner->is_active ?? true)) ? 'checked' : '' }}>
    <label>Active</label>
  </div>
  <div class="space-y-1">
    <label class="block text-sm font-medium">Priority</label>
    <input type="number" name="priority" value="{{ old('priority', $banner->priority ?? 0) }}" class="w-full border-gray-300 rounded">
  </div>
</div>

