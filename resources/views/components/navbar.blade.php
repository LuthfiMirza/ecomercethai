@php
    $brandName = config('app.name', 'Lungpaeit');
    $brandLogo = asset('image/logo.jpg');
    $loginUrl = localized_route('login');
    $mobileCategories = $navMegaCategories ?? [];
@endphp

<header x-data="navbar()"
        x-ref="hdr"
        class="sticky top-0 z-[250] bg-white/90 dark:bg-neutral-900/80 backdrop-blur"
        role="banner">
  <!-- Topbar -->
  <div class="hidden md:block bg-[#0b0720] text-neutral-300">
    <div class="container mx-auto px-5 text-xs flex items-center justify-between h-12">
      <!-- Left items with separators, no edges -->
      <nav class="flex items-center divide-x divide-neutral-700">
        <a href="{{ route('catalog') }}" class="px-3 hover:text-white">{{ __('common.product') }}</a>
        <a href="{{ route('contact') }}" class="px-3 hover:text-white">{{ __('common.contact_us') }}</a>
      </nav>
      <!-- Right items: text links | language -->
      <div class="flex items-center gap-4">
        <!-- Removed Wishlist/Login from topbar per request -->
          <x-language-switcher />
      </div>
    </div>
  </div>
  <div class="container mx-auto px-4">
    <div class="flex h-16 items-center gap-3 justify-between">
      <div class="flex items-center gap-2 md:gap-3">
        <button type="button"
                class="md:hidden inline-flex h-10 w-10 items-center justify-center rounded-full border border-neutral-200 bg-white text-neutral-700 shadow-sm hover:border-accent-500 dark:bg-neutral-900 dark:text-neutral-100"
                aria-label="{{ __('common.menu') }}"
                x-ref="mobileTrigger"
                @click="mobileOpen = true">
          <i class="fa-solid fa-bars text-lg"></i>
        </button>
        <!-- Logo -->
        <a href="{{ route('home') }}" class="flex items-center gap-2" aria-label="{{ __('common.go_home') }}">
          <img src="{{ $brandLogo }}" alt="{{ $brandName }}" class="h-10 w-10 rounded-full object-cover shadow-sm" loading="lazy"/>
          <span class="font-semibold text-neutral-800 dark:text-neutral-100">{{ $brandName }}</span>
        </a>
      </div>
      

      <!-- Center Search -->
      <div class="hidden md:block w-full max-w-xl mx-2 md:mx-6 relative" x-data="{open:false}" x-on:click.outside="open=false">
        <form action="{{ route('catalog') }}" method="get" role="search" class="relative" x-on:submit="open=false" autocomplete="off">
          <label for="q" class="sr-only">{{ __('common.search_placeholder') }}</label>
          <input id="q" name="q" type="search" :placeholder="t('search_placeholder')" x-on:focus="open=true" x-on:keydown.escape.window="open=false" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" class="w-full rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 pl-11 pr-24 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-accent-500 text-neutral-800 dark:text-neutral-100"/>
          <div class="absolute inset-y-0 left-3 flex items-center text-neutral-400">
            <i class="fa-solid fa-magnifying-glass"></i>
          </div>
          <button type="submit" class="absolute right-1 top-1 bottom-1 px-4 rounded-full bg-accent-500 hover:bg-accent-600 text-white text-sm">{{ __('common.search') }}</button>
        </form>
        <!-- Suggestions -->
        <!-- Suggestions dropdown: right-aligned, precise under input -->
        <div x-cloak x-show="open" role="listbox" aria-label="{{ __('common.suggestions') }}"
             x-transition.opacity x-transition.scale.origin.top-right
             class="absolute top-full left-0 right-0 mt-2 w-full bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded-2xl shadow-elevated p-2 z-[140]">
          <div class="text-xs text-neutral-500 px-2 py-1">{{ __('common.suggestions') }}</div>
          <ul class="text-sm divide-y divide-neutral-100 dark:divide-neutral-800">
            @foreach(['RTX 4070 Ti','Gaming laptop','NVMe SSD 1TB','4K Monitor'] as $s)
              <li>
                <a class="flex items-center justify-between px-3 py-2 hover:bg-neutral-50 dark:hover:bg-neutral-800 rounded focus:outline-none focus:ring-2 focus:ring-accent-500"
                   href="{{ route('catalog', ['q' => $s]) }}">
                  <span>{{ $s }}</span>
                  <i class="fa-solid fa-arrow-turn-down-left opacity-50"></i>
                </a>
              </li>
            @endforeach
          </ul>
        </div>
      </div>

      <!-- Right icons (mobile only) -->
      <div class="flex items-center gap-3 md:hidden">
        <!-- Wishlist -->
        @if(auth()->check())
        <a href="{{ route('wishlist') }}" data-open-wishlist class="relative inline-flex items-center justify-center w-11 h-11 rounded-full border border-neutral-200 bg-white text-neutral-700 shadow-sm hover:border-accent-500" aria-label="{{ __('common.wishlist') }}">
          <i class="fa-regular fa-heart text-base"></i>
          <span data-wishlist-count data-show-zero="true" class="absolute -top-1 -right-1 inline-flex min-w-[1.15rem] h-[1.15rem] items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-semibold text-white shadow ring-1 ring-white">0</span>
        </a>
        @else
        <a href="{{ $loginUrl }}" class="relative inline-flex items-center justify-center w-11 h-11 rounded-full border border-neutral-200 bg-white text-neutral-700 shadow-sm" aria-label="{{ __('common.wishlist') }}">
          <i class="fa-regular fa-heart text-base"></i>
        </a>
        @endif

        <!-- Cart -->
        <a href="{{ route('cart') }}" data-open-cart class="relative inline-flex items-center justify-center w-11 h-11 rounded-full border border-neutral-200 bg-white text-neutral-700 shadow-sm hover:border-accent-500" aria-label="{{ __('common.cart_title') }}">
          <i class="fa-solid fa-cart-shopping text-neutral-700 dark:text-neutral-200"></i>
          <span id="cart-count" class="absolute -top-1 -right-1 bg-primary-600 text-white rounded-full min-w-[1.1rem] h-[1.1rem] text-[10px] leading-[1.1rem] text-center">0</span>
        </a>

        <!-- Account -->
        @if(auth()->check())
        <a href="{{ route('account') }}" class="hidden sm:inline-flex items-center p-2 rounded-full hover:bg-neutral-100 dark:hover:bg-neutral-800" aria-label="Account menu">
          <i class="fa-solid fa-user-check text-neutral-700 dark:text-neutral-200"></i>
        </a>
        @else
        <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center p-2 rounded-full hover:bg-neutral-100 dark:hover:bg-neutral-800" aria-label="Account menu">
          <i class="fa-regular fa-user text-neutral-700 dark:text-neutral-200"></i>
        </a>
        @endif

        <!-- Mobile search button -->
        <button class="md:hidden p-2 rounded-full hover:bg-neutral-100 dark:hover:bg-neutral-800" x-on:click="openSearch = true" aria-label="{{ __('common.search') }}">
          <i class="fa-solid fa-magnifying-glass"></i>
        </button>

      </div>

      <!-- Right rail (md+): LINE ID + cart summary -->
      @php
        $lineRaw = config('services.line.id', '@jag3901n');
        $lineNormalized = ltrim($lineRaw ?: '@jag3901n', '@');
        $lineDisplay = '@' . $lineNormalized;
      @endphp
      <div class="hidden md:flex items-center gap-6">
        <!-- LINE ID -->
        <button type="button" id="nav-line" data-line-modal-open class="inline-flex items-center gap-3 text-neutral-700 dark:text-neutral-100 bg-transparent hover:text-green-600 dark:hover:text-green-400 focus:outline-none">
          <i class="fa-brands fa-line text-2xl text-green-500"></i>
          <div class="leading-tight">
            <div class="text-xs opacity-90">{{ __('common.line_id') }}</div>
            <div class="text-sm font-medium">{{ $lineDisplay }}</div>
          </div>
        </button>
        <span class="h-8 w-px bg-neutral-200 dark:bg-neutral-700" aria-hidden="true"></span>
        <!-- Cart summary with hover preview (desktop) -->
        <div class="relative" x-data="{open:false, items:[], subtotal:0, load(){ try{ this.items=JSON.parse(localStorage.getItem('cartItems')||'[]'); this.subtotal=this.items.reduce((s,i)=>s+Number(i.price||0),0);}catch(e){ this.items=[]; this.subtotal=0; } }}"
             x-on:mouseenter="open=true; load()" x-on:mouseleave="open=false">
          <a href="{{ route('cart') }}" data-open-cart class="inline-flex items-center gap-3 text-neutral-700 dark:text-neutral-100">
            <i class="fa-solid fa-cart-shopping text-xl"></i>
            <div class="leading-tight">
              <div class="text-sm font-semibold text-red-500">$0.00</div>
              <div id="cart-items-count" class="text-xs opacity-90" data-template="{{ __('common.items_count', ['count' => '__COUNT__']) }}">{{ __('common.items_count', ['count' => 0]) }}</div>
            </div>
          </a>
          <!-- Popover -->
          <div x-cloak x-show="open" x-transition.origin.top.right
               class="absolute right-0 top-full mt-2 w-80 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-2xl shadow-elevated overflow-hidden z-[140]"
               x-on:mouseenter="open=true" x-on:mouseleave="open=false">
            <template x-if="items.length===0">
              <div class="p-3 text-sm text-neutral-600 dark:text-neutral-300">{{ __('common.cart_empty') }}</div>
            </template>
            <template x-if="items.length>0">
              <div>
                <ul class="max-h-60 overflow-auto divide-y divide-neutral-100 dark:divide-neutral-800 p-2">
                  <template x-for="(p,idx) in items" :key="idx">
                    <li class="flex items-center gap-3 p-3">
                      <img :src="p.image" onerror="this.style.display='none'" class="w-12 h-12 object-cover rounded" alt=""/>
                      <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-neutral-900 dark:text-neutral-100 truncate" x-text="p.name"></div>
                        <div class="text-xs text-neutral-500" x-text="'$' + (Number(p.price)||0).toFixed(2)"></div>
                      </div>
                    </li>
                  </template>
                </ul>
                <div class="p-3 flex items-center justify-between">
                  <div class="text-sm text-neutral-600 dark:text-neutral-300">{{ __('common.subtotal') }}</div>
                  <div class="font-semibold text-neutral-900 dark:text-neutral-100" x-text="'$' + subtotal.toFixed(2)"></div>
                </div>
                <div class="p-3 pt-0 grid grid-cols-2 gap-2">
                  <a href="{{ route('cart') }}" class="px-3 py-2 text-sm rounded-md border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-center hover:bg-neutral-50 dark:hover:bg-neutral-800">{{ __('common.view_cart') }}</a>
                  <a href="{{ auth()->check() ? route('checkout') : $loginUrl }}" class="px-3 py-2 text-sm rounded-md {{ auth()->check() ? 'bg-accent-500 hover:bg-accent-600 text-white' : 'bg-neutral-200 text-neutral-600' }} text-center">
                    {{ auth()->check() ? __('common.checkout') : __('common.login') }}
                  </a>
                </div>
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>

    <!-- Menu row -->
    <nav class="hidden md:flex items-center gap-6 h-12 text-sm" aria-label="{{ __('common.menu') }}">
      <!-- Left: categories + main links -->
      <div class="flex items-center gap-6">
        @include('components.mega-menu', ['categories' => $navMegaCategories ?? []])
        <a href="{{ route('faqs') }}" class="text-neutral-700 hover:text-accent-600 dark:text-neutral-200">{{ __('common.faqs') }}</a>
      </div>

      <!-- Right: wishlist + login/register (back to bottom row) -->
      <div class="ml-auto flex items-center gap-4">
        <div class="relative" x-data="{
              open:false,
              items:[],
              load(){
                try {
                  const raw = localStorage.getItem('wishlistItems') || '[]';
                  const parsed = JSON.parse(raw);
                  this.items = Array.isArray(parsed) ? parsed : [];
                } catch (e) {
                  this.items = [];
                }
              },
              init(){
                this.load();
                const update = (e)=>{ if(e?.detail?.items){ this.items = e.detail.items; } else { this.load(); } };
                window.addEventListener('wishlist:update', update);
                window.addEventListener('storage', ()=>this.load());
              }
            }"
             x-on:mouseenter="open=true; load()" x-on:mouseleave="open=false">
        @if(auth()->check())
        <a href="{{ route('wishlist') }}" data-open-wishlist class="inline-flex items-center gap-2 rounded-full border border-neutral-200 bg-white px-4 py-1.5 text-sm font-medium text-neutral-700 shadow-sm transition hover:border-accent-500 dark:text-neutral-200 dark:bg-neutral-900 dark:border-neutral-800">
          <i class="fa-regular fa-heart text-sm"></i>
          <span>{{ __('common.wishlist') }}</span>
          <span data-wishlist-count data-show-zero="true" class="inline-flex min-w-[1.35rem] h-[1.35rem] items-center justify-center rounded-full bg-rose-500 px-1 text-[11px] font-semibold text-white shadow ring-1 ring-white dark:ring-neutral-900">0</span>
        </a>
        @else
        <a href="{{ $loginUrl }}" class="inline-flex items-center gap-2 rounded-full border border-neutral-200 bg-white px-4 py-1.5 text-sm font-medium text-neutral-700 shadow-sm transition dark:text-neutral-200 dark:bg-neutral-900 dark:border-neutral-800">
          <i class="fa-regular fa-heart text-sm"></i>
          <span>{{ __('common.login') }}</span>
        </a>
        @endif
          <div x-cloak x-show="open" x-transition.origin.top.right
               class="absolute right-0 top-full mt-2 w-72 soft-card p-0 overflow-hidden z-[140]"
               x-on:mouseenter="open=true" x-on:mouseleave="open=false">
            <template x-if="items.length===0">
              <div class="p-4 text-center text-sm text-neutral-500 dark:text-neutral-300">{{ __('common.wishlist_empty') }}</div>
            </template>
            <template x-if="items.length>0">
              <div>
                <ul class="max-h-60 overflow-auto divide-y divide-neutral-100 dark:divide-neutral-800">
                  <template x-for="(p,idx) in items" :key="idx">
                    <li class="flex items-center gap-3 p-3">
                      <img :src="p.image" onerror="this.style.display='none'" class="w-12 h-12 object-cover rounded" alt=""/>
                      <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-neutral-900 dark:text-neutral-100 truncate" x-text="p.name || 'Product'"></div>
                        <div class="text-xs text-neutral-500" x-text="p.price ? ('$' + (Number(p.price)||0).toFixed(2)) : ''"></div>
                      </div>
                    </li>
                  </template>
                </ul>
                <div class="p-3 pt-0 grid grid-cols-1 gap-2">
                  <a href="{{ $loginUrl }}" class="btn-accent text-sm text-center">Login</a>
                </div>
              </div>
            </template>
          </div>
        </div>
        @if(auth()->check())
        <div class="relative" x-data="{open:false}" x-on:keydown.escape.window="open=false">
          <button type="button" class="inline-flex items-center gap-2 text-neutral-700 hover:text-accent-600 dark:text-neutral-200" x-on:click="open=!open">
            <i class="fa-regular fa-user"></i>
            <span>{{ \Illuminate\Support\Str::limit(auth()->user()->name, 18) }}</span>
            <i class="fa-solid fa-chevron-down text-xs opacity-80"></i>
          </button>
          <div x-cloak x-show="open" x-transition.origin.top.right
               class="absolute right-0 top-full mt-2 w-48 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl shadow-elevated overflow-hidden z-[140]"
               x-on:mouseenter="open=true" x-on:mouseleave="open=false">
            <a href="{{ route('account') }}" class="block px-4 py-2.5 text-sm text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-800">{{ __('common.dashboard') }}</a>
            @role('admin')
              <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2.5 text-sm text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-800">{{ __('common.admin_panel') }}</a>
            @endrole
            <div class="border-t border-neutral-200 dark:border-neutral-800"></div>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-neutral-800">{{ __('common.logout') }}</button>
            </form>
          </div>
        </div>
        @else
        <div class="flex items-center gap-3">
          <a href="{{ route('login') }}" class="text-neutral-700 hover:text-accent-600 dark:text-neutral-200">{{ __('common.login') }}</a>
          <span class="text-neutral-300" aria-hidden="true">|</span>
          <a href="{{ route('register') }}" class="text-neutral-700 hover:text-accent-600 dark:text-neutral-200">{{ __('common.register') }}</a>
        </div>
        @endif
      </div>
    </nav>
  </div>


  <!-- Mobile slide-over -->
  <template x-teleport="body">
    <div x-cloak
         x-show="mobileOpen"
         x-transition.opacity
         class="fixed inset-0 z-[400]"
         role="dialog"
         aria-modal="true"
         @keydown.escape.prevent.stop="closeMobile()">
      <div class="absolute inset-0 bg-black/60" @click="closeMobile()" aria-hidden="true"></div>
      <aside x-ref="mobilePanel"
             tabindex="-1"
             x-trap.noscroll="mobileOpen"
             class="absolute inset-y-0 left-0 w-full max-w-sm bg-white dark:bg-neutral-900 shadow-2xl p-6 overflow-y-auto focus:outline-none"
             x-transition:enter="transform transition ease-out duration-300"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full">
        <div class="flex items-center justify-between">
          <p class="text-lg font-semibold text-neutral-900 dark:text-neutral-50">{{ __('common.menu') }}</p>
          <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-100" @click="closeMobile()">
            <span class="sr-only">{{ __('common.close') }}</span>
            <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
      <div class="mt-6 space-y-6">
        <form action="{{ route('catalog') }}" method="get" role="search" class="flex items-center gap-2 rounded-2xl border border-neutral-200 bg-neutral-50 px-3 py-2 dark:border-neutral-800 dark:bg-neutral-800">
          <label for="mobile-search" class="sr-only">{{ __('common.search_placeholder') }}</label>
          <i class="fa-solid fa-magnifying-glass text-neutral-400"></i>
          <input id="mobile-search" name="q" type="search" placeholder="{{ __('common.search_placeholder') }}" class="flex-1 bg-transparent text-sm text-neutral-800 outline-none dark:text-neutral-100"/>
          <button type="submit" class="text-sm font-semibold text-accent-500">{{ __('common.search') }}</button>
        </form>
        <nav class="space-y-3 text-sm text-neutral-700 dark:text-neutral-200" aria-label="{{ __('common.menu') }}">
          <a href="{{ route('catalog') }}" class="block rounded-xl border border-neutral-200 px-4 py-2 font-semibold hover:border-accent-500 dark:border-neutral-800">{{ __('common.catalog') }}</a>
          <a href="{{ route('faqs') }}" class="block rounded-xl border border-neutral-200 px-4 py-2 font-semibold hover:border-accent-500 dark:border-neutral-800">{{ __('common.faqs') }}</a>
          <a href="{{ route('contact') }}" class="block rounded-xl border border-neutral-200 px-4 py-2 font-semibold hover:border-accent-500 dark:border-neutral-800">{{ __('common.contact_us') }}</a>
        </nav>
        <div>
          <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">{{ __('common.categories') }}</p>
          <div class="mt-3 grid gap-2">
            @forelse($mobileCategories as $category)
              <a href="{{ $category['url'] ?? '#' }}" class="inline-flex items-center justify-between rounded-2xl border border-neutral-200 bg-white px-4 py-2 text-sm font-medium text-neutral-700 shadow-sm hover:border-accent-500 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-100">
                <span>{{ $category['name'] ?? __('common.mega_view_category') }}</span>
                <i class="fa-solid fa-arrow-right-long text-[10px] opacity-50"></i>
              </a>
            @empty
              <p class="text-sm text-neutral-500 dark:text-neutral-300">{{ __('common.mega_no_categories') }}</p>
            @endforelse
          </div>
        </div>
        <div class="space-y-3">
          <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">{{ __('common.account') }}</p>
          @auth
            <a href="{{ route('account') }}" class="block rounded-xl border border-neutral-200 px-4 py-2 text-sm font-semibold text-neutral-700 hover:border-accent-500 dark:border-neutral-800 dark:text-neutral-100">{{ __('common.dashboard') }}</a>
            @role('admin')
              <a href="{{ route('admin.dashboard') }}" class="block rounded-xl border border-neutral-200 px-4 py-2 text-sm font-semibold text-neutral-700 hover:border-accent-500 dark:border-neutral-800 dark:text-neutral-100">{{ __('common.admin_panel') }}</a>
            @endrole
            <a href="{{ route('wishlist') }}" class="block rounded-xl border border-neutral-200 px-4 py-2 text-sm font-semibold text-neutral-700 hover:border-accent-500 dark:border-neutral-800 dark:text-neutral-100">{{ __('common.wishlist') }}</a>
            <a href="{{ route('cart') }}" class="block rounded-xl border border-neutral-200 px-4 py-2 text-sm font-semibold text-neutral-700 hover:border-accent-500 dark:border-neutral-800 dark:text-neutral-100">{{ __('common.view_cart') }}</a>
          @else
            <a href="{{ $loginUrl }}" class="block rounded-xl border border-neutral-200 px-4 py-2 text-sm font-semibold text-neutral-700 hover:border-accent-500 dark:border-neutral-800 dark:text-neutral-100">{{ __('common.login') }}</a>
            <a href="{{ localized_route('register') }}" class="block rounded-xl border border-neutral-200 px-4 py-2 text-sm font-semibold text-neutral-700 hover:border-accent-500 dark:border-neutral-800 dark:text-neutral-100">{{ __('common.register') }}</a>
          @endauth
        </div>
        <div class="flex items-center justify-between rounded-2xl border border-neutral-200 px-4 py-2 text-sm font-medium text-neutral-700 dark:border-neutral-800 dark:text-neutral-100">
          <span>{{ __('common.line_id') }}</span>
          <button type="button" data-line-modal-open class="inline-flex items-center gap-1 text-accent-500">
            <i class="fa-brands fa-line"></i> LINE
          </button>
        </div>
        <div>
          <x-language-switcher />
        </div>
      </div>
    </aside>
    </div>
  </template>

  <!-- Mobile search modal -->
  <div x-cloak x-show="openSearch" x-transition.opacity class="fixed inset-0 z-[320]" aria-hidden="true">
    <div class="absolute inset-0 bg-black/40" x-on:click="openSearch=false"></div>
    <div class="absolute inset-x-4 top-16 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-elevated p-3">
      <form action="{{ route('catalog') }}" method="get" role="search" class="flex items-center gap-2" autocomplete="off">
        <label for="mq" class="sr-only">{{ __('common.search_placeholder') }}</label>
        <i class="fa-solid fa-magnifying-glass text-neutral-400"></i>
        <input id="mq" name="q" type="search" placeholder="{{ __('common.search_placeholder') }}" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" class="flex-1 bg-transparent outline-none text-sm text-neutral-800 dark:text-neutral-100"/>
        <button type="submit" class="px-3 py-1.5 rounded-md bg-accent-500 hover:bg-accent-600 text-white text-sm">{{ __('common.search') }}</button>
      </form>
    </div>
  </div>

  <script>
    function navbar(){
      return {
        open:false, openSearch:false, mobileOpen:false,
        lang: '{{ app()->getLocale() }}',
        currency: document.documentElement.dataset.currency || 'THB',
        formatter: null,
        dict: {
          en: { search_placeholder: @json(__('common.search_placeholder', [], 'en')) },
          th: { search_placeholder: @json(__('common.search_placeholder', [], 'th')) },
        },
        t(key){ return (this.dict[this.lang]||{})[key] || key; },
        closeMobile(){ this.mobileOpen = false; },
        init(){
          try {
            localStorage.setItem('lang', this.lang);
          } catch (error) {
            // ignore if storage is unavailable
          }
          document.documentElement.setAttribute('lang', this.lang);
          try {
            this.formatter = new Intl.NumberFormat(document.documentElement.getAttribute('data-locale') || document.documentElement.lang || 'en', {
              style: 'currency',
              currency: this.currency
            });
          } catch (error) {
            this.formatter = new Intl.NumberFormat('en', { style: 'currency', currency: 'USD' });
          }
          // hydrate wishlist/cart counters
          const w=document.getElementById('wishlist-count');
          let wishlistRaw = '[]';
          try {
            wishlistRaw = localStorage.getItem('wishlistItems') || '[]';
          } catch (error) {
            wishlistRaw = '[]';
          }
          let wc = 0;
          try {
            wc = (JSON.parse(wishlistRaw) || []).length;
          } catch (error) {
            wc = 0;
          }
          if(w){
            w.textContent=wc;
            if(wc===0) w.classList.add('hidden');
          }
          const cc=document.getElementById('cart-count');
          const ci=document.getElementById('cart-items-count');
          const updateCartInfo = (detail = {}) => {
            let storedCount = 0;
            let storedSubtotal = 0;
            try {
              storedCount = Number(localStorage.getItem('cartCount') || 0);
              storedSubtotal = Number(localStorage.getItem('cartSubtotal') || 0);
            } catch (error) {
              storedCount = 0;
              storedSubtotal = 0;
            }
            const count = Number(detail.count ?? storedCount);
            const subtotal = Number(detail.subtotal ?? storedSubtotal);
            if(cc){ cc.textContent=count; cc.classList.toggle('hidden', count === 0); }
            if(ci){
              const tpl = ci.dataset.template || ':count';
              const countText = String(count);
              ci.textContent = tpl.replace('__COUNT__', countText).replace(':count', countText);
              const miniSubtotal = ci.previousElementSibling;
              if (miniSubtotal && this.formatter) {
                miniSubtotal.textContent = this.formatter.format(subtotal);
              }
            }
          };
          updateCartInfo();
          window.addEventListener('cart:update', (event) => {
            updateCartInfo(event?.detail || {});
          });
          document.addEventListener('i18n:change', (e)=>{
            this.lang = e.detail.lang;
            document.documentElement.setAttribute('lang', this.lang);
          });
          this.$watch('mobileOpen', (value) => {
            try {
              document.body.classList.toggle('overflow-hidden', value);
            } catch (error) {}
            if (value) {
              this.$nextTick(() => {
                this.$refs.mobilePanel?.focus?.();
              });
            } else {
              this.$nextTick(() => {
                this.$refs.mobileTrigger?.focus?.();
              });
            }
          });
        }
      }
    }
  
</script>
</header>
