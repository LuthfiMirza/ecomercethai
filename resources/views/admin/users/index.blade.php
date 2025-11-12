@extends('layouts.admin')

@section('header', 'Users')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-100">{{ __('admin.nav.users') }}</h2>
        <a href="{{ localized_route('admin.users.create') }}" class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:bg-slate-600 dark:hover:bg-slate-500">
            {{ __('admin.users.quick_admin.create') }}
        </a>
    </div>
</div>

<div class="mb-8 rounded-3xl border border-slate-200 bg-white/80 p-6 shadow-[0_20px_60px_-35px_rgba(15,23,42,0.35)] backdrop-blur dark:border-slate-800 dark:bg-slate-900/40">
    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="text-lg font-semibold text-slate-800 dark:text-slate-100">{{ __('admin.users.quick_admin.title') }}</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('admin.users.quick_admin.description') }}</p>
        </div>
        <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-200">
            {{ __('admin.nav.users') }}
        </span>
    </div>
    <form action="{{ localized_route('admin.users.store') }}" method="POST" class="mt-4 grid gap-4 md:grid-cols-2">
        @csrf
        <input type="hidden" name="is_admin" value="1">
        <div>
            <label for="quickName" class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ __('admin.users.quick_admin.name') }}</label>
            <input id="quickName" name="name" type="text" value="{{ old('name') }}" required autocomplete="name" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-800 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-100">
            @error('name')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="quickEmail" class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ __('admin.users.quick_admin.email') }}</label>
            <input id="quickEmail" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-800 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-100">
            @error('email')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="quickPassword" class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ __('admin.users.quick_admin.password') }}</label>
            <input id="quickPassword" name="password" type="password" required autocomplete="new-password" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-800 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-100">
            @error('password')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="quickPasswordConfirm" class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ __('admin.users.quick_admin.password_confirm') }}</label>
            <input id="quickPasswordConfirm" name="password_confirmation" type="password" required autocomplete="new-password" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-800 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200 dark:border-slate-700 dark:bg-slate-900/50 dark:text-slate-100">
        </div>
        <div class="md:col-span-2">
            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-500 px-4 py-3 text-sm font-semibold uppercase tracking-wide text-white shadow-lg shadow-emerald-500/30 transition hover:bg-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-200 dark:bg-emerald-500 dark:hover:bg-emerald-400">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 5v10m-5-5h10"/>
                </svg>
                {{ __('admin.users.quick_admin.create') }}
            </button>
        </div>
    </form>
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
