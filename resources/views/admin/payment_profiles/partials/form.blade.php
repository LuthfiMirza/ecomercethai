<div class="space-y-1">
  <label class="block text-sm font-medium">User</label>
  <select name="user_id" class="w-full border-gray-300 rounded">
    @foreach($users as $u)
      <option value="{{ $u->id }}" @selected(old('user_id', $profile->user_id ?? '') == $u->id)>{{ $u->name }} ({{ $u->email }})</option>
    @endforeach
  </select>
  @error('user_id')<div class="text-sm text-red-600">{{ $message }}</div>@enderror
</div>
<div class="space-y-1">
  <label class="block text-sm font-medium">Provider</label>
  <input type="text" name="provider" value="{{ old('provider', $profile->provider ?? '') }}" class="w-full border-gray-300 rounded" required>
  @error('provider')<div class="text-sm text-red-600">{{ $message }}</div>@enderror
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
  <div class="space-y-1">
    <label class="block text-sm font-medium">Account Name</label>
    <input type="text" name="account_name" value="{{ old('account_name', $profile->account_name ?? '') }}" class="w-full border-gray-300 rounded">
  </div>
  <div class="space-y-1">
    <label class="block text-sm font-medium">Account Number</label>
    <input type="text" name="account_number" value="{{ old('account_number', $profile->account_number ?? '') }}" class="w-full border-gray-300 rounded">
  </div>
</div>
<div class="flex items-center gap-2">
  <input type="checkbox" name="is_default" value="1" {{ old('is_default', ($profile->is_default ?? false)) ? 'checked' : '' }}>
  <label>Default</label>
</div>

