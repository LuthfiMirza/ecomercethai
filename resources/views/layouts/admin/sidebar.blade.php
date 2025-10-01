<aside id="sidebar" class="fixed left-0 top-0 z-40 h-full w-72 -translate-x-full transition-transform duration-200 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 lg:translate-x-0">
  <div class="h-16 flex items-center px-6 border-b border-gray-200 dark:border-gray-800">
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
      <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-primary-600 text-white font-bold">TA</span>
      <span class="text-xl font-semibold">TailAdmin</span>
    </a>
  </div>
  <nav class="p-4 space-y-1">
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 dark:bg-gray-800' : '' }}">
      <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
      <span>{{ __('Dashboard') }}</span>
    </a>
    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->is('admin/users*') ? 'bg-gray-100 dark:bg-gray-800' : '' }}">
      <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
      <span>{{ __('Users') }}</span>
    </a>
    <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->is('admin/products*') ? 'bg-gray-100 dark:bg-gray-800' : '' }}">
      <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2h-4M4 13V6a2 2 0 012-2h4m3 13l-3 3m0 0l-3-3m3 3V10"/></svg>
      <span>{{ __('Products') }}</span>
    </a>
    <a href="{{ route('admin.payments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->is('admin/payments*') ? 'bg-gray-100 dark:bg-gray-800' : '' }}">
      <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v2m14 0h2a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2h12z"/></svg>
      <span>{{ __('Payments') }}</span>
    </a>
    <a href="{{ route('admin.payment-profiles.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->is('admin/payment-profiles*') ? 'bg-gray-100 dark:bg-gray-800' : '' }}">
      <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2zm0 0V4m0 8v8m8-8h-4m-8 0H4"/></svg>
      <span>{{ __('Payment Profiles') }}</span>
    </a>
    <a href="{{ route('admin.banners.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->is('admin/banners*') ? 'bg-gray-100 dark:bg-gray-800' : '' }}">
      <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5h16v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm0 0V3m16 2V3m-6 12l-4 4-4-4"/></svg>
      <span>{{ __('Banners') }}</span>
    </a>
    <a href="{{ route('admin.chat.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 {{ request()->is('admin/chat*') ? 'bg-gray-100 dark:bg-gray-800' : '' }}">
      <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8M8 14h5m-9 2a2 2 0 01-2-2V7a2 2 0 012-2h14a2 2 0 012 2v7a2 2 0 01-2 2H8l-4 4v-4z"/></svg>
      <span>{{ __('Live Chat') }}</span>
    </a>
    <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
      <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7m-9 2v6"/></svg>
      <span>{{ __('View Site') }}</span>
    </a>
  </nav>
</aside>
