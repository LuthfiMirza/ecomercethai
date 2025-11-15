@php
    $lineRawId = config('services.line.id', '@jag3901n');
    $lineNormalizedId = ltrim($lineRawId ?: '@jag3901n', '@');
    $lineDisplayId = '@' . $lineNormalizedId;
    $lineShareUrl = 'https://line.me/ti/p/@' . $lineNormalizedId;
    $lineQrUrl = asset('image/addline.jpg');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-line-id="{{ $lineNormalizedId }}" data-currency="{{ config('app.currency', 'THB') }}" data-locale="{{ app()->getLocale() }}">
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
        .custom-scroll {
            scrollbar-width: thin;
            scrollbar-color: rgba(99,102,241,.8) transparent;
        }
        .custom-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, rgba(79,70,229,.9), rgba(14,165,233,.7));
            border-radius: 999px;
        }
    </style>
    
    <!-- Google Fonts - Press Start 2P -->
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    
</head>
@php
    $shouldOffsetHeader = ! request()->routeIs('home');
@endphp
<body class="bg-white text-neutral-800">
    <x-navbar />

    @php
        // Keep a small breathing room below the navbar (sticky, not fixed) so pages don't look detached.
        $contentPaddingClass = $shouldOffsetHeader ? 'pt-6 md:pt-8' : 'pt-4 md:pt-0';
    @endphp
    <div class="{{ $contentPaddingClass }}">
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
            <a href="{{ auth()->check() ? localized_route('wishlist') : localized_route('login') }}" class="inline-flex flex-1 min-w-[120px] items-center justify-center rounded-full border px-4 py-2 text-xs font-semibold uppercase tracking-wide shadow {{ auth()->check() ? 'border-neutral-200 bg-white text-neutral-900 hover:bg-neutral-50' : 'border-neutral-200 bg-neutral-200 text-neutral-600' }}">
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
        <button type="button" id="qa-line" data-line-modal-open @click="open=false" class="inline-flex items-center gap-2 px-3 py-2 rounded-full shadow-lg bg-green-500 hover:bg-green-600 text-white text-sm pointer-events-auto">
          <i class="fa-brands fa-line"></i>
          <span>Chat via LINE</span>
        </button>
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
        <aside class="absolute right-4 bottom-20 w-[360px] max-w-[92vw] bg-white dark:bg-neutral-900 rounded-3xl shadow-xl border border-neutral-200/80 dark:border-neutral-800/80 flex flex-col" data-livechat-panel>
          <header class="px-4 py-3 flex items-center justify-between border-b border-neutral-100 dark:border-neutral-800">
            <div>
              <p class="text-xs uppercase tracking-wide text-neutral-400 dark:text-neutral-500">{{ __('Customer Support') }}</p>
              <p class="text-lg font-semibold text-neutral-900 dark:text-neutral-50">{{ __('Live Chat') }}</p>
            </div>
            <button type="button" data-livechat-close @click="chat=false" class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-neutral-200 dark:border-neutral-700 text-neutral-500 hover:text-neutral-700 dark:text-neutral-300">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </header>
          <div class="px-4 py-2 text-[11px] text-neutral-500 dark:text-neutral-400 flex items-center gap-2">
            <span class="inline-flex h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
            {{ __('Typically replies within a few minutes') }}
          </div>
          <div class="flex-1 flex flex-col bg-neutral-50/80 dark:bg-neutral-900">
            <div class="flex-1 px-4">
              <div class="h-64 max-h-64 overflow-y-auto text-sm space-y-4 custom-scroll" id="livechat-messages" data-livechat-log>
                <div class="text-neutral-500 dark:text-neutral-300 bg-white/90 dark:bg-neutral-800/60 border border-dashed border-neutral-200 dark:border-neutral-700 rounded-2xl px-4 py-4 text-center text-xs shadow-sm" data-livechat-empty>
                  {{ __('Hi! How can we help you today?') }}
                </div>
              </div>
            </div>
            <form id="livechat-form" data-livechat-form class="px-4 pb-4 pt-3 space-y-2 border-t border-neutral-100 dark:border-neutral-800 bg-white dark:bg-neutral-900" @submit.prevent="">
              <div class="flex items-center gap-2 rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 px-3 py-2 focus-within:border-indigo-400 focus-within:ring-1 focus-within:ring-indigo-300">
                <input id="livechat-input" data-livechat-input type="text" class="flex-1 bg-transparent border-none text-sm text-neutral-800 dark:text-neutral-100 placeholder-neutral-400 focus:outline-none" placeholder="{{ __('Type a message...') }}" @guest disabled @endguest />
                <button type="submit" data-livechat-send class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium transition disabled:opacity-60" @guest disabled @endguest>
                  <span>{{ __('Send') }}</span>
                  <i class="fa-solid fa-paper-plane text-xs"></i>
                </button>
              </div>
              <p class="text-[11px] text-neutral-400 dark:text-neutral-500 flex items-center gap-1">
                <i class="fa-solid fa-circle-info"></i>
                <span>{{ __('Press Enter to send') }}</span>
              </p>
            </form>
            @guest
              <div class="px-4 pb-4 text-xs text-neutral-500 dark:text-neutral-400">
                {{ __('Please') }}
                <a href="{{ route('login', ['locale' => app()->getLocale()]) }}" class="text-indigo-600 dark:text-indigo-400 font-medium hover:underline">
                  {{ __('sign in') }}
                </a>
                {{ __('to continue the live chat.') }}
              </div>
            @endguest
          </div>
        </aside>
      </div>
  </div>

  <!-- LINE QR Modal -->
  <div class="fixed inset-0 z-[320] hidden items-center justify-center px-4" data-line-modal aria-hidden="true">
    <div class="absolute inset-0 bg-black/50" data-line-modal-close></div>
    <aside class="relative w-full max-w-md rounded-3xl bg-white dark:bg-neutral-900 shadow-2xl border border-neutral-200 dark:border-neutral-800 overflow-hidden">
      <header class="px-6 py-4 flex items-center justify-between bg-gradient-to-r from-emerald-500 to-green-500 text-white">
        <div>
          <p class="text-xs uppercase tracking-wide text-white/70">{{ __('LINE Official') }}</p>
          <p class="text-lg font-semibold">{{ __('Add us on LINE') }}</p>
        </div>
        <button type="button" data-line-modal-close class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/20 text-white hover:bg-white/30">
          <i class="fa-solid fa-xmark text-lg"></i>
        </button>
      </header>
      <div class="px-6 py-5 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <p class="text-xs uppercase tracking-wide text-neutral-400">{{ __('LINE ID') }}</p>
            <p class="text-xl font-semibold text-neutral-900 dark:text-neutral-50">{{ $lineDisplayId }}</p>
          </div>
          <div class="flex items-center gap-2">
            <button type="button" class="inline-flex items-center gap-2 rounded-full border border-neutral-300 px-3 py-1.5 text-sm font-medium text-neutral-700 hover:bg-neutral-50 dark:border-neutral-600 dark:text-neutral-100 dark:hover:bg-neutral-800" data-line-copy="{{ $lineDisplayId }}">
              <i class="fa-solid fa-copy text-xs"></i>
              <span>{{ __('Copy ID') }}</span>
            </button>
            <button type="button" class="inline-flex items-center gap-2 rounded-full bg-green-500 px-3 py-1.5 text-sm font-semibold text-white hover:bg-green-600" data-line-open="{{ $lineShareUrl }}">
              <i class="fa-solid fa-plus"></i>
              <span>{{ __('Open LINE') }}</span>
            </button>
          </div>
        </div>
        <div class="rounded-3xl border border-dashed border-neutral-300 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/60 p-4 text-center">
          <img src="{{ $lineQrUrl }}" alt="LINE QR {{ $lineDisplayId }}" class="mx-auto w-56 h-56 object-contain" loading="lazy">
          <p class="mt-3 text-sm text-neutral-600 dark:text-neutral-300">{{ __('Scan this code with LINE or your phone camera to add us instantly.') }}</p>
        </div>
      </div>
    </aside>
  </div>

    <x-footer />
    <script>
      window.App = Object.assign({}, window.App, {
        locale: @json(app()->getLocale()),
        isAuthenticated: @json(auth()->check()),
        user: @json(auth()->user()?->only(['id','name'])),
        line: {
          id: @json($lineDisplayId),
          normalized: @json($lineNormalizedId),
          url: @json($lineShareUrl),
        },
        chat: {
          fetchUrl: @json(auth()->check() ? localized_route('chat.messages.index') : null),
          postUrl: @json(auth()->check() ? localized_route('chat.messages.store') : null),
          channel: @json(auth()->check() ? 'chat.user.' . auth()->id() : null),
          loginUrl: @json(route('login', ['locale' => app()->getLocale()])),
        }
      });
    </script>
    <script>
      (function () {
        const modal = document.querySelector('[data-line-modal]');
        const openers = document.querySelectorAll('[data-line-modal-open]');
        if (!modal) {
          return;
        }

        const show = () => {
          modal.classList.remove('hidden');
          modal.classList.add('flex');
          modal.setAttribute('aria-hidden', 'false');
          document.body.classList.add('overflow-hidden');
        };

        const hide = () => {
          modal.classList.add('hidden');
          modal.classList.remove('flex');
          modal.setAttribute('aria-hidden', 'true');
          document.body.classList.remove('overflow-hidden');
        };

        openers.forEach((button) => {
          button.addEventListener('click', (event) => {
            event.preventDefault();
            show();
          });
        });

        modal.querySelectorAll('[data-line-modal-close]').forEach((node) => {
          node.addEventListener('click', hide);
        });

        document.addEventListener('keydown', (event) => {
          if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            hide();
          }
        });

        const showCopied = (button, success = true) => {
          const label = button.querySelector('span');
          const original = button.getAttribute('data-original-label') || label?.textContent || '';
          if (!button.getAttribute('data-original-label') && original) {
            button.setAttribute('data-original-label', original);
          }
          if (label) {
            label.textContent = success ? @json(__('Copied!')) : @json(__('Copy failed'));
          }
          button.classList.add('ring-2', success ? 'ring-emerald-300' : 'ring-red-400');
          window.setTimeout(() => {
            if (label) {
              label.textContent = button.getAttribute('data-original-label') || original;
            }
            button.classList.remove('ring-2', 'ring-emerald-300', 'ring-red-400');
          }, 1500);
        };

        modal.querySelectorAll('[data-line-copy]').forEach((button) => {
          button.addEventListener('click', async () => {
            const value = button.getAttribute('data-line-copy');
            if (!value) return;
            try {
              if (navigator.clipboard?.writeText) {
                await navigator.clipboard.writeText(value);
                showCopied(button, true);
                return;
              }
              const textarea = document.createElement('textarea');
              textarea.value = value;
              textarea.style.position = 'fixed';
              textarea.style.opacity = '0';
              document.body.appendChild(textarea);
              textarea.select();
              document.execCommand('copy');
              document.body.removeChild(textarea);
              showCopied(button, true);
            } catch (error) {
              console.error('LINE ID copy failed', error);
              showCopied(button, false);
            }
          });
        });

        modal.querySelectorAll('[data-line-open]').forEach((button) => {
          button.addEventListener('click', () => {
            const url = button.getAttribute('data-line-open');
            if (url) {
              window.open(url, '_blank', 'noopener');
            }
          });
        });
      })();
    </script>
    @stack('scripts')
</body>
</html>
