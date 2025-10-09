@extends('layouts.admin')

@section('header', 'Users')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100">User Management</h2>
        <a href="{{ localized_route('admin.users.create') }}" class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:bg-slate-600 dark:hover:bg-slate-500">
            Add New User
        </a>
    </div>
</div>

<div class="table-card overflow-hidden">
    <!-- Filters -->
    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" placeholder="Search by name or email..." class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-slate-700 dark:border-slate-600 dark:text-white dark:placeholder-slate-400">
            <select class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                <option value="">All Roles</option>
                <option value="admin">Admin</option>
                <option value="customer">Customer</option>
            </select>
            <select class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="banned">Banned</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="-my-2 overflow-x-auto">
        <div class="py-2 align-middle inline-block min-w-full">
            <div class="shadow overflow-hidden border-b border-slate-200 dark:border-slate-700">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">Role</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">Joined</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" alt="">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-slate-900 dark:text-slate-200">
                                                <a href="{{ localized_route('admin.users.show', ['id' => $user->id]) }}" class="hover:underline">{{ $user->name }}</a>
                                                @if($user->is_banned)
                                                    <span class="ml-2 px-2 py-0.5 text-[10px] rounded-full bg-rose-100 text-rose-700">Banned</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="badge {{ $user->is_admin ? 'badge-info' : 'badge-neutral' }}">{{ $user->is_admin ? 'Admin' : 'Customer' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">{{ $user->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ localized_route('admin.users.show', ['id' => $user->id]) }}" class="px-3 py-1.5 bg-slate-600 text-white rounded-md hover:bg-slate-700 text-xs">View</a>
                                    <a href="{{ localized_route('admin.users.edit', ['id' => $user->id]) }}" class="px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-xs">Edit</a>
                                    <x-confirm-delete action="{{ localized_route('admin.users.destroy', ['id' => $user->id]) }}">Delete</x-confirm-delete>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-center text-slate-500 dark:text-slate-400">
                                    No users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="p-6 border-t border-slate-200 dark:border-slate-700">
        {{ $users->links() }}
    </div>
</div>
@endsection
