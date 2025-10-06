<header class="sticky top-0 z-50 h-16 bg-white/90 backdrop-blur border-b border-gray-200 dark:bg-gray-900/80 dark:border-gray-800 flex items-center justify-between px-4 lg:px-6">
  <div class="flex items-center gap-2">
    <button id="openSidebar" class="lg:hidden inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300">
      <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
    <div class="font-semibold text-lg">@yield('title')</div>
  </div>
  <div class="flex items-center gap-3">
    <div class="flex-1 hidden md:flex items-center">
      <input type="text" placeholder="{{ __('Search...') }}" class="w-[520px] h-10 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm px-4" />
    </div>
    <div class="hidden md:block">
      <x-language-switcher />
    </div>
    <x-admin.theme-panel />
    <button id="themeToggle" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300">
      <svg id="sun" class="h-5 w-5 block dark:hidden" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M3 12h2m14 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/><circle cx="12" cy="12" r="4"/></svg>
      <svg id="moon" class="h-5 w-5 hidden dark:block" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
    </button>
    <div class="flex items-center gap-3">
      <span class="text-sm text-gray-600 dark:text-gray-300">{{ auth()->user()->name ?? '' }}</span>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="text-sm text-red-600 hover:underline">{{ __('Logout') }}</button>
      </form>
    </div>
  </div>
</header>
