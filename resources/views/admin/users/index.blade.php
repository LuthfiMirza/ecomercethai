@extends('layouts.admin')
@section('title', 'Users')
@section('content')
<x-admin.header title="Users" :breadcrumbs="[['label'=>'Admin','href'=>route('admin.dashboard')],['label'=>'Users']]">
  <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search users..." class="h-10 w-64 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 text-sm" />
    @if(request('q'))
      <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600">Clear</a>
    @endif
  </form>
</x-admin.header>
<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
  <table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
      <tr>
        <th class="px-4 py-2 text-left">Name</th>
        <th class="px-4 py-2 text-left">Email</th>
        <th class="px-4 py-2 text-left">Roles</th>
        <th class="px-4 py-2"></th>
      </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200/70 dark:divide-gray-700">
      @foreach($users as $user)
      <tr>
        <td class="px-4 py-2">{{ $user->name }}</td>
        <td class="px-4 py-2">{{ $user->email }}</td>
        <td class="px-4 py-2">
          @foreach($user->roles as $role)
            <span class="inline-block text-xs bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded mr-1">{{ $role->name }}</span>
          @endforeach
        </td>
        <td class="px-4 py-2 text-right">
          <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:underline mr-3">Edit</a>
          <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Delete this user?')">
            @csrf @method('DELETE')
            <button class="text-red-600 hover:underline">Delete</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $users->links() }}</div>
@endsection
