<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-line-id="{{ env('LINE_ID', 'tokothai') }}" data-currency="{{ config('app.currency', 'THB') }}" data-locale="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
      try {
        var saved = localStorage.getItem('theme');
        var isDark = saved ? saved === 'dark' : (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
        if (isDark) {
          document.documentElement.classList.add('dark');
          if (document.body) document.body.classList.add('dark');
        }
        else {
          document.documentElement.classList.remove('dark');
          if (document.body) document.body.classList.remove('dark');
        }
      } catch (e) { /* no-op */ }
    </script>
    <title>Toko Thailand - Your Tech Partner</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://unpkg.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>[x-cloak]{display:none !important}</style>
    <style>
        .hero-slider {
            background: linear-gradient(rgba(78, 52, 46, 0.8), rgba(78, 52, 46, 0.8));
        }
        .category-icon:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }
        .product-card {
            transition: all 0.3s ease;
        }
        .product-card:hover { box-shadow: 0 0 15px rgba(255, 112, 67, 0.3); }
        .glass-card { backdrop-filter: blur(10px); background: rgba(255,255,255,0.6); }
        .badge-counter { min-width: 1.25rem; height: 1.25rem; font-size: .75rem; }
    </style>
    
    <!-- Google Fonts - Press Start 2P -->
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    
</head>
<body class="bg-white text-neutral-800 dark:bg-neutral-950 dark:text-neutral-200">
    <x-navbar />

    <!-- Add padding to body to prevent content from hiding behind fixed header -->
    <div class="pt-[72px]">
        @yield('content')
    </div>

    <!-- Wishlist Drawer (bottom sheet) -->
    <div id="wishlist-overlay" class="fixed inset-0 z-50 hidden">
      <div class="absolute inset-0 bg-black/40" data-close="wishlist"></div>
      <aside class="absolute left-1/2 -translate-x-1/2 bottom-0 w-[720px] max-w-[96vw] bg-white dark:bg-neutral-900 rounded-t-2xl shadow-2xl overflow-hidden border border-neutral-200 dark:border-neutral-800">
        <header class="px-4 py-3 flex items-center justify-between border-b border-neutral-200 dark:border-neutral-800">
          <div class="font-semibold">{{ __('common.wishlist_title') }}</div>
          <button class="p-2 rounded hover:bg-neutral-100 dark:hover:bg-neutral-800" data-close="wishlist" aria-label="{{ __('common.close') }}"><i class="fa-solid fa-xmark"></i></button>
        </header>
        <div id="wishlist-items" class="max-h-[50vh] overflow-y-auto divide-y divide-neutral-200 dark:divide-neutral-800"></div>
        <footer class="relative px-4 py-3 flex items-center justify-between border-t border-neutral-200 dark:border-neutral-800">
          <div class="flex items-center gap-3">
            <div class="flex flex-col leading-tight">
              <div class="text-sm text-neutral-600 dark:text-neutral-300">{{ __('common.subtotal') }}</div>
              <div id="wishlist-items-count" class="text-xs text-neutral-500 dark:text-neutral-400" data-template="{{ __('common.items_count', ['count' => '__COUNT__']) }}">{{ __('common.items_count', ['count' => 0]) }}</div>
            </div>
            <div id="wishlist-subtotal" class="font-semibold text-neutral-900 dark:text-neutral-100">{{ format_price(0) }}</div>
          </div>
          <div class="flex items-center gap-2">
            <a href="{{ route('wishlist') }}" class="px-3 py-2 rounded-md border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-sm hover:bg-neutral-50 dark:hover:bg-neutral-800">{{ __('common.view_wishlist') }}</a>
            <a href="{{ route('catalog') }}" class="px-3 py-2 rounded-md bg-accent-500 hover:bg-accent-600 text-white text-sm">{{ __('common.shop_now') }}</a>
          </div>
          <button id="wishlist-clear" class="absolute left-4 text-sm text-red-600 hover:underline">{{ __('common.clear_all') }}</button>
        </footer>
      </aside>
    </div>

    <!-- Cart Drawer (bottom sheet) -->
    <div id="cart-overlay" class="fixed inset-0 z-50 hidden">
      <div class="absolute inset-0 bg-black/40" data-close="cart"></div>
      <aside class="absolute left-1/2 -translate-x-1/2 bottom-0 w-[720px] max-w-[96vw] bg-white dark:bg-neutral-900 rounded-t-2xl shadow-2xl overflow-hidden border border-neutral-200 dark:border-neutral-800">
        <header class="px-4 py-3 flex items-center justify-between border-b border-neutral-200 dark:border-neutral-800">
          <div class="font-semibold">{{ __('common.cart_title') }}</div>
          <button class="p-2 rounded hover:bg-neutral-100 dark:hover:bg-neutral-800" data-close="cart" aria-label="{{ __('common.close') }}"><i class="fa-solid fa-xmark"></i></button>
        </header>
        <div id="cart-items" class="max-h-[50vh] overflow-y-auto divide-y divide-neutral-200 dark:divide-neutral-800"></div>
        <footer class="relative px-4 py-3 flex items-center justify-between border-t border-neutral-200 dark:border-neutral-800">
          <div class="flex items-center gap-3">
            <div class="text-sm text-neutral-600 dark:text-neutral-300">{{ __('common.subtotal') }}</div>
            <div id="cart-subtotal" class="font-semibold text-neutral-900 dark:text-neutral-100">$0.00</div>
          </div>
          <div class="flex items-center gap-2">
            <a href="{{ route('cart') }}" class="px-3 py-2 rounded-md border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-sm hover:bg-neutral-50 dark:hover:bg-neutral-800">{{ __('common.view_cart') }}</a>
            <a href="{{ route('checkout') }}" class="px-3 py-2 rounded-md bg-accent-500 hover:bg-accent-600 text-white text-sm">{{ __('common.checkout') }}</a>
          </div>
          <button id="cart-clear" class="absolute left-4 text-sm text-red-600 hover:underline">{{ __('common.clear_all') }}</button>
        </footer>
      </aside>
    </div>

    <!-- Floating Quick Actions (bottom-right) -->
    <div x-data="{ open:false, chat:false }" class="fixed right-4 bottom-4 z-60" id="quick-actions">
      <!-- Stack of actions -->
      <div x-cloak x-show="open" x-transition.origin.bottom.right class="flex flex-col items-end gap-2 mb-2">
        <!-- Chat via LINE -->
        <a id="qa-line" :href="'https://line.me/ti/p/~'+encodeURIComponent(document.documentElement.getAttribute('data-line-id')||'tokothailand')" target="_blank" rel="noopener" @click="open=false" class="inline-flex items-center gap-2 px-3 py-2 rounded-full shadow-lg bg-green-500 hover:bg-green-600 text-white text-sm">
          <i class="fa-brands fa-line"></i>
          <span>Chat via LINE</span>
        </a>
        <!-- Live Chat popup trigger -->
        <button @click="chat=true; open=false" class="inline-flex items-center gap-2 px-3 py-2 rounded-full shadow-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm">
          <i class="fa-solid fa-comments"></i>
          <span>Live Chat</span>
        </button>
      </div>
      <!-- Main FAB -->
      <button @click="open=!open" class="w-12 h-12 rounded-full shadow-lg bg-accent-500 hover:bg-accent-600 text-white flex items-center justify-center">
        <i x-show="!open" class="fa-solid fa-plus"></i>
        <i x-show="open" class="fa-solid fa-xmark"></i>
      </button>
      
      <!-- Live Chat Popup -->
      <div x-cloak x-show="chat" x-transition.opacity class="fixed inset-0 z-50">
        <div class="absolute inset-0 bg-black/30" @click="chat=false"></div>
        <aside class="absolute right-4 bottom-20 w-[380px] max-w-[92vw] bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl overflow-hidden border border-neutral-200 dark:border-neutral-800">
          <header class="px-4 py-3 bg-indigo-600 text-white flex items-center justify-between">
            <div class="font-semibold">Live Chat</div>
            <button @click="chat=false" class="hover:text-white/90"><i class="fa-solid fa-xmark"></i></button>
          </header>
          <div class="h-64 p-3 overflow-y-auto text-sm" id="livechat-messages">
            <div class="text-neutral-500">Hi! How can we help you today?</div>
          </div>
          <form id="livechat-form" class="flex items-center gap-2 p-3 border-t border-neutral-200 dark:border-neutral-800" @submit.prevent="">
            <input id="livechat-input" type="text" class="flex-1 rounded-md border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-sm" placeholder="Type a message..." />
            <button type="submit" class="px-3 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 text-white text-sm">Send</button>
          </form>
        </aside>
      </div>
    </div>

    <x-footer />
    @stack('scripts')
</body>
</html>
