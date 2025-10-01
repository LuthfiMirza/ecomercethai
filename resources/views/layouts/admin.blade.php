<!DOCTYPE html>
<html lang="en" class="h-full">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Admin') }} - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
  </head>
  <body class="h-full bg-gray-50 text-gray-800 dark:bg-gray-900 dark:text-gray-100">
    <div id="app" class="min-h-screen">
      <div class="lg:pl-72">
        @include('layouts.admin.header')
        <main class="p-4 lg:p-6 space-y-6">
          @if (session('status'))
            <div class="mb-4 p-3 rounded-lg bg-green-50 text-green-700 border border-green-200 dark:bg-green-900/20 dark:text-green-300 dark:border-green-900/30">{{ session('status') }}</div>
          @endif
          @yield('content')
        </main>
      </div>
      @include('layouts.admin.sidebar')
      <!-- Mobile overlay -->
      <div id="overlay" class="fixed inset-0 z-30 bg-black/20 backdrop-blur-sm hidden lg:hidden"></div>
    </div>

    <script>
    // Dark mode toggle using localStorage
    (function() {
      const root = document.documentElement;
      const saved = localStorage.getItem('theme');
      if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        root.classList.add('dark');
      }
      document.getElementById('themeToggle')?.addEventListener('click', () => {
        root.classList.toggle('dark');
        localStorage.setItem('theme', root.classList.contains('dark') ? 'dark' : 'light');
      });
    })();

    // Mobile sidebar toggle
    (function(){
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('overlay');
      const openBtn = document.getElementById('openSidebar');
      function open(){ sidebar.classList.remove('-translate-x-full'); overlay.classList.remove('hidden'); }
      function close(){ sidebar.classList.add('-translate-x-full'); overlay.classList.add('hidden'); }
      openBtn?.addEventListener('click', open);
      overlay?.addEventListener('click', close);
    })();

    // Language dropdown
    (function(){
      const btn = document.getElementById('langBtn');
      const menu = document.getElementById('langMenu');
      btn?.addEventListener('click', (e) => {
        e.stopPropagation();
        menu?.classList.toggle('hidden');
      });
      document.addEventListener('click', () => menu?.classList.add('hidden'));
    })();
    </script>
    @stack('scripts')
  </body>
 </html>
