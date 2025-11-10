<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-line-id="{{ env('LINE_ID', 'tokothai') }}" data-currency="{{ config('app.currency', 'THB') }}" data-locale="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Lungpaeit') }} - Your Tech Partner</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo.jpg') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://unpkg.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @viteReactRefresh
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
        [data-wishlist].wishlist-active {
            background-color: #f43f5e !important;
            border-color: #f43f5e !important;
            color: #fff !important;
        }
        [data-wishlist].wishlist-active i {
            color: inherit !important;
        }
    </style>
    
    <!-- Google Fonts - Press Start 2P -->
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    
</head>
<body class="bg-white text-neutral-800">
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
        <footer class="px-4 py-4 space-y-3 border-t border-neutral-200 dark:border-neutral-800">
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
              <div class="flex flex-col leading-tight">
                <div class="text-sm text-neutral-600 dark:text-neutral-300">{{ __('common.subtotal') }}</div>
                <div id="wishlist-items-count" class="text-xs text-neutral-500 dark:text-neutral-400" data-template="{{ __('common.items_count', ['count' => '__COUNT__']) }}">{{ __('common.items_count', ['count' => 0]) }}</div>
              </div>
              <div id="wishlist-subtotal" class="font-semibold text-neutral-900 dark:text-neutral-100">{{ format_price(0) }}</div>
            </div>
            <button id="wishlist-clear" class="inline-flex items-center gap-1 rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs font-semibold text-red-600 transition hover:bg-red-100 dark:border-red-500/40 dark:bg-red-500/10 dark:text-red-300">
              <i class="fa-solid fa-broom text-[10px]"></i>
              <span>{{ __('common.clear_all') }}</span>
            </button>
          </div>
          <div class="flex flex-wrap items-center justify-end gap-2">
            @if(auth()->check())
            <a href="{{ localized_route('wishlist') }}" data-overlay-bypass class="inline-flex items-center gap-2 rounded-full border border-neutral-300 bg-white px-4 py-2 text-sm font-medium text-neutral-700 transition hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-800">
              {{ __('common.view_wishlist') }}
            </a>
            @else
            <a href="{{ localized_route('login') }}" class="inline-flex items-center gap-2 rounded-full border border-neutral-300 bg-white px-4 py-2 text-sm font-medium text-neutral-700 transition hover:bg-neutral-50">
              {{ __('common.login') }}
            </a>
            @endif
            <a href="{{ localized_route('catalog') }}" class="inline-flex items-center gap-2 rounded-full bg-accent-500 px-4 py-2 text-sm font-semibold text-white shadow transition hover:bg-accent-600">
              {{ __('common.shop_now') }}
            </a>
          </div>
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
        <footer class="px-4 py-4 space-y-3 border-t border-neutral-200 dark:border-neutral-800">
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
              <div class="flex flex-col leading-tight">
                <div class="text-sm text-neutral-600 dark:text-neutral-300">{{ __('common.subtotal') }}</div>
                <div class="text-xs text-neutral-500 dark:text-neutral-400">
                  <span id="cart-items-count-overlay" data-template="{{ __('common.items_count', ['count' => '__COUNT__']) }}">{{ __('common.items_count', ['count' => 0]) }}</span>
                </div>
              </div>
              <div id="cart-subtotal" class="font-semibold text-neutral-900 dark:text-neutral-100">$0.00</div>
            </div>
            <button id="cart-clear" class="inline-flex items-center gap-1 rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs font-semibold text-red-600 transition hover:bg-red-100 dark:border-red-500/40 dark:bg-red-500/10 dark:text-red-300">
              <i class="fa-solid fa-broom text-[10px]"></i>
              <span>{{ __('common.clear_all') }}</span>
            </button>
          </div>
          <div class="flex flex-wrap items-center justify-end gap-2">
            <a href="{{ localized_route('cart') }}" data-overlay-bypass class="inline-flex items-center gap-2 rounded-full border border-neutral-300 bg-white px-4 py-2 text-sm font-medium text-neutral-700 transition hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-800">
              {{ __('common.view_cart') }}
            </a>
            <a href="{{ auth()->check() ? localized_route('checkout') : localized_route('login') }}" class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold shadow transition {{ auth()->check() ? 'bg-accent-500 text-white hover:bg-accent-600' : 'bg-neutral-200 text-neutral-600' }}">
              {{ auth()->check() ? __('common.checkout') : __('common.login') }}
            </a>
          </div>
        </footer>
      </aside>
    </div>

    <!-- Add-to-cart popup -->
    <div data-cart-popup aria-hidden="true" class="fixed bottom-6 right-6 z-[85] w-full max-w-sm rounded-2xl border border-orange-100 bg-white shadow-[0_25px_70px_-30px_rgba(16,24,40,0.4)] p-4 flex gap-4 opacity-0 translate-y-4 pointer-events-none transition-all duration-300">
      <div class="relative flex gap-4 w-full">
        <div class="relative h-16 w-16 flex-shrink-0 rounded-xl bg-neutral-100 overflow-hidden">
          <img data-cart-popup-image src="" alt="" class="h-full w-full object-cover hidden" />
          <div data-cart-popup-placeholder class="absolute inset-0 flex items-center justify-center text-neutral-400">
            <i class="fa-solid fa-bag-shopping"></i>
          </div>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-xs font-semibold uppercase tracking-wide text-orange-500">{{ __('common.cart_popup_title') }}</p>
          <p data-cart-popup-name class="text-sm font-semibold text-neutral-900 truncate">Product</p>
          <p data-cart-popup-meta class="text-xs text-neutral-500 mt-0.5">1 × ฿0.00</p>
          <div class="mt-3 flex flex-wrap gap-2">
            <a href="{{ localized_route('cart') }}" class="inline-flex flex-1 min-w-[120px] items-center justify-center rounded-full bg-accent-500 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white shadow hover:bg-accent-600">
              {{ __('common.cart_popup_view_cart') }}
            </a>
            <a href="{{ auth()->check() ? localized_route('checkout') : localized_route('login') }}" class="inline-flex flex-1 min-w-[120px] items-center justify-center rounded-full border border-neutral-200 px-4 py-2 text-xs font-semibold uppercase tracking-wide {{ auth()->check() ? 'text-neutral-700 hover:bg-neutral-50' : 'text-neutral-400' }}">
              {{ auth()->check() ? __('common.cart_popup_checkout') : __('common.login') }}
            </a>
            <button type="button" data-cart-popup-close class="text-xs font-semibold text-neutral-500 hover:text-neutral-700">
              {{ __('common.cart_popup_continue') }}
            </button>
          </div>
        </div>
        <button type="button" data-cart-popup-close class="absolute -top-2 -right-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-neutral-100 text-neutral-500 hover:bg-neutral-200">
          <i class="fa-solid fa-xmark text-xs"></i>
        </button>
      </div>
    </div>

    <!-- Add-to-wishlist popup -->
    <div data-wishlist-popup aria-hidden="true" class="fixed bottom-6 right-6 z-[84] w-full max-w-sm rounded-2xl border border-rose-100 bg-white shadow-[0_25px_70px_-30px_rgba(16,24,40,0.35)] p-4 flex gap-4 opacity-0 translate-y-4 pointer-events-none transition-all duration-300">
      <div class="relative flex gap-4 w-full">
        <div class="relative h-16 w-16 flex-shrink-0 rounded-xl bg-rose-50 overflow-hidden">
          <img data-wishlist-popup-image src="" alt="" class="h-full w-full object-cover hidden" />
          <div data-wishlist-popup-placeholder class="absolute inset-0 flex items-center justify-center text-rose-400">
            <i class="fa-solid fa-heart"></i>
          </div>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-xs font-semibold uppercase tracking-wide text-rose-500">{{ __('common.wishlist_popup_title') }}</p>
          <p data-wishlist-popup-name class="text-sm font-semibold text-neutral-900 truncate">Product</p>
          <p data-wishlist-popup-meta class="text-xs text-neutral-500 mt-0.5">฿0.00</p>
          <div class="mt-3 flex flex-wrap gap-2">
            <a href="{{ auth()->check() ? localized_route('wishlist') : localized_route('login') }}" class="inline-flex flex-1 min-w-[120px] items-center justify-center rounded-full px-4 py-2 text-xs font-semibold uppercase tracking-wide shadow {{ auth()->check() ? 'bg-secondary-500 text-white hover:bg-secondary-500/90' : 'bg-neutral-200 text-neutral-600' }}">
              {{ auth()->check() ? __('common.wishlist_popup_view') : __('common.login') }}
            </a>
            <button type="button" data-wishlist-popup-close class="text-xs font-semibold text-neutral-500 hover:text-neutral-700">
              {{ __('common.wishlist_popup_continue') }}
            </button>
          </div>
        </div>
        <button type="button" data-wishlist-popup-close class="absolute -top-2 -right-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-neutral-100 text-neutral-500 hover:bg-neutral-200">
          <i class="fa-solid fa-xmark text-xs"></i>
        </button>
      </div>
    </div>

    <!-- Floating Quick Actions (bottom-right) -->
    <div x-data="{ open:false, chat:false }" class="fixed right-4 bottom-4 z-60 pointer-events-none" id="quick-actions">
      <!-- Stack of actions -->
      <div x-cloak x-show="open" x-transition.origin.bottom.right class="flex flex-col items-end gap-2 mb-2 pointer-events-auto">
        <!-- Chat via LINE -->
        <a id="qa-line" :href="'https://line.me/ti/p/~'+encodeURIComponent(document.documentElement.getAttribute('data-line-id')||'tokothailand')" target="_blank" rel="noopener" @click="open=false" class="inline-flex items-center gap-2 px-3 py-2 rounded-full shadow-lg bg-green-500 hover:bg-green-600 text-white text-sm pointer-events-auto">
          <i class="fa-brands fa-line"></i>
          <span>Chat via LINE</span>
        </a>
        <!-- Live Chat popup trigger -->
        <button type="button" data-livechat-open @click="chat=true; open=false" class="inline-flex items-center gap-2 px-3 py-2 rounded-full shadow-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm pointer-events-auto">
          <i class="fa-solid fa-comments"></i>
          <span>Live Chat</span>
        </button>
      </div>
      <!-- Main FAB -->
      <button @click="open=!open" class="w-12 h-12 rounded-full shadow-lg bg-accent-500 hover:bg-accent-600 text-white flex items-center justify-center pointer-events-auto">
        <i x-show="!open" class="fa-solid fa-plus"></i>
        <i x-show="open" class="fa-solid fa-xmark"></i>
      </button>
      
      <!-- Live Chat Popup -->
      <div x-cloak x-show="chat" x-transition.opacity class="fixed inset-0 z-50 pointer-events-auto">
        <div class="absolute inset-0 bg-black/30" @click="chat=false"></div>
        <aside class="absolute right-4 bottom-20 w-[380px] max-w-[92vw] bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl overflow-hidden border border-neutral-200 dark:border-neutral-800" data-livechat-panel>
          <header class="px-4 py-3 bg-indigo-600 text-white flex items-center justify-between">
            <div class="font-semibold">Live Chat</div>
            <button type="button" data-livechat-close @click="chat=false" class="hover:text-white/90"><i class="fa-solid fa-xmark"></i></button>
          </header>
          <div class="h-64 p-3 overflow-y-auto text-sm" id="livechat-messages" data-livechat-log>
            <div class="text-neutral-500" data-livechat-empty>Hi! How can we help you today?</div>
          </div>
          <form id="livechat-form" data-livechat-form class="flex items-center gap-2 p-3 border-t border-neutral-200 dark:border-neutral-800" @submit.prevent="">
            <input id="livechat-input" data-livechat-input type="text" class="flex-1 rounded-md border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-3 py-2 text-sm" placeholder="Type a message..." @guest disabled @endguest />
            <button type="submit" data-livechat-send class="px-3 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 text-white text-sm" @guest disabled @endguest>Send</button>
          </form>
          @guest
            <div class="px-3 pb-3 text-xs text-neutral-500">
              Please <a href="{{ route('login', ['locale' => app()->getLocale()]) }}" class="text-indigo-600 hover:underline">sign in</a> to continue the live chat.
            </div>
          @endguest
        </aside>
      </div>
    </div>

    <x-footer />
    <script>
      window.App = Object.assign({}, window.App, {
        locale: @json(app()->getLocale()),
        isAuthenticated: @json(auth()->check()),
        user: @json(auth()->user()?->only(['id','name'])),
        chat: {
          fetchUrl: @json(auth()->check() ? route('chat.messages.index', ['locale' => app()->getLocale()]) : null),
          postUrl: @json(auth()->check() ? route('chat.messages.store', ['locale' => app()->getLocale()]) : null),
          channel: @json(auth()->check() ? 'chat.user.' . auth()->id() : null),
          loginUrl: @json(route('login', ['locale' => app()->getLocale()])),
        }
      });
    </script>
    @stack('scripts')
</body>
</html>
