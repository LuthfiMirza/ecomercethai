<header x-data="navbar()"
        x-ref="hdr"
        class="sticky top-0 z-[90] bg-white/90 dark:bg-neutral-900/80 backdrop-blur"
        role="banner">
  <!-- Topbar -->
  <div class="hidden md:block bg-[#0b0720] text-neutral-300">
    <div class="container mx-auto px-5 text-xs flex items-center justify-between h-12">
      <!-- Left items with separators, no edges -->
      <nav class="flex items-center divide-x divide-neutral-700">
        <a href="{{ route('catalog') }}" class="px-3 hover:text-white">Product</a>
        <a href="{{ route('contact') }}" class="px-3 hover:text-white">Contact Us</a>
      </nav>
      <!-- Right items: text links | language | theme -->
      <div class="flex items-center gap-4">
        <!-- Removed Wishlist/Login from topbar per request -->
        
          <x-language-switcher />
          <button x-on:click="toggleTheme" class="p-1.5 rounded hover:bg-white/10" aria-label="Toggle theme">
            <i x-show="isDark" class="fa-solid fa-moon text-neutral-300"></i>
            <i x-show="!isDark" class="fa-solid fa-sun text-yellow-400"></i>
          </button>
        
      </div>
    </div>
  </div>
  <div class="container mx-auto px-4">
    <div class="flex h-16 items-center gap-3 justify-between">
      <!-- Logo -->
      <a href="{{ route('home') }}" class="flex items-center gap-2" aria-label="Go to homepage">
        <img src="{{ asset('favicon.ico') }}" alt="Toko Thailand" class="h-8 w-8 rounded"/>
        <span class="font-semibold text-neutral-800 dark:text-neutral-100">Toko Thailand</span>
      </a>
      

      <!-- Center Search -->
      <div class="hidden md:block w-full max-w-xl mx-2 md:mx-6 relative" x-data="{open:false}" x-on:click.outside="open=false">
        <form action="{{ route('catalog') }}" method="get" role="search" class="relative" x-on:submit="open=false" autocomplete="off">
          <label for="q" class="sr-only">Search products</label>
          <input id="q" name="q" type="search" :placeholder="t('search_placeholder')" x-on:focus="open=true" x-on:keydown.escape.window="open=false" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" class="w-full rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 pl-11 pr-24 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-accent-500 text-neutral-800 dark:text-neutral-100"/>
          <div class="absolute inset-y-0 left-3 flex items-center text-neutral-400">
            <i class="fa-solid fa-magnifying-glass"></i>
          </div>
          <button type="submit" class="absolute right-1 top-1 bottom-1 px-4 rounded-full bg-accent-500 hover:bg-accent-600 text-white text-sm">{{ __('Search') }}</button>
        </form>
        <!-- Suggestions -->
        <!-- Suggestions dropdown: right-aligned, precise under input -->
        <div x-cloak x-show="open" role="listbox" aria-label="Search suggestions"
             x-transition.opacity x-transition.scale.origin.top-right
             class="absolute top-full left-0 right-0 mt-2 w-full bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded-2xl shadow-elevated p-2 z-[140]">
          <div class="text-xs text-neutral-500 px-2 py-1">Suggestions</div>
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
        <!-- Dark mode toggle -->
        <button x-on:click="toggleTheme" class="p-2 rounded-full hover:bg-neutral-100 dark:hover:bg-neutral-800" aria-label="Toggle theme">
          <i x-show="isDark" class="fa-solid fa-moon text-neutral-700 dark:text-neutral-200"></i>
          <i x-show="!isDark" class="fa-solid fa-sun text-yellow-500"></i>
        </button>

        <!-- Wishlist -->
        <a href="{{ route('wishlist') }}" data-open-wishlist class="relative inline-flex items-center p-2 rounded-full hover:bg-neutral-100 dark:hover:bg-neutral-800" aria-label="Wishlist">
          <i class="fa-regular fa-heart text-neutral-700 dark:text-neutral-200"></i>
          <span id="wishlist-count" class="absolute -top-1 -right-1 bg-secondary-500 text-white rounded-full min-w-[1.1rem] h-[1.1rem] text-[10px] leading-[1.1rem] text-center">0</span>
        </a>

        <!-- Cart -->
        <a href="{{ route('cart') }}" data-open-cart class="relative inline-flex items-center p-2 rounded-full hover:bg-neutral-100 dark:hover:bg-neutral-800" aria-label="Open cart">
          <i class="fa-solid fa-cart-shopping text-neutral-700 dark:text-neutral-200"></i>
          <span id="cart-count" class="absolute -top-1 -right-1 bg-primary-600 text-white rounded-full min-w-[1.1rem] h-[1.1rem] text-[10px] leading-[1.1rem] text-center">0</span>
        </a>

        <!-- Account -->
        @auth
        <a href="{{ route('account') }}" class="hidden sm:inline-flex items-center p-2 rounded-full hover:bg-neutral-100 dark:hover:bg-neutral-800" aria-label="Account menu">
          <i class="fa-solid fa-user-check text-neutral-700 dark:text-neutral-200"></i>
        </a>
        @else
        <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center p-2 rounded-full hover:bg-neutral-100 dark:hover:bg-neutral-800" aria-label="Account menu">
          <i class="fa-regular fa-user text-neutral-700 dark:text-neutral-200"></i>
        </a>
        @endauth

        <!-- Mobile search button -->
        <button class="md:hidden p-2 rounded-full hover:bg-neutral-100 dark:hover:bg-neutral-800" x-on:click="openSearch = true" aria-label="Open search">
          <i class="fa-solid fa-magnifying-glass"></i>
        </button>

      </div>

      <!-- Right rail (md+): LINE ID + cart summary -->
      <div class="hidden md:flex items-center gap-6">
        <!-- LINE ID -->
        <a id="nav-line" href="https://line.me/ti/p/~tokothai" target="_blank" rel="noopener" class="inline-flex items-center gap-3 text-neutral-700 dark:text-neutral-100">
          <i class="fa-brands fa-line text-2xl text-green-500"></i>
          <div class="leading-tight">
            <div class="text-xs opacity-90">LINE ID</div>
            <div class="text-sm font-medium">tokothai</div>
          </div>
        </a>
        <span class="h-8 w-px bg-neutral-200 dark:bg-neutral-700" aria-hidden="true"></span>
        <!-- Cart summary with hover preview (desktop) -->
        <div class="relative" x-data="{open:false, items:[], subtotal:0, load(){ try{ this.items=JSON.parse(localStorage.getItem('cartItems')||'[]'); this.subtotal=this.items.reduce((s,i)=>s+Number(i.price||0),0);}catch(e){ this.items=[]; this.subtotal=0; } }}"
             x-on:mouseenter="open=true; load()" x-on:mouseleave="open=false">
          <a href="{{ route('cart') }}" data-open-cart class="inline-flex items-center gap-3 text-neutral-700 dark:text-neutral-100">
            <i class="fa-solid fa-cart-shopping text-xl"></i>
            <div class="leading-tight">
              <div class="text-sm font-semibold text-red-500">$0.00</div>
              <div id="cart-items-count" class="text-xs opacity-90">0 Items</div>
            </div>
          </a>
          <!-- Popover -->
          <div x-cloak x-show="open" x-transition.origin.top.right
               class="absolute right-0 top-full mt-2 w-80 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-2xl shadow-elevated overflow-hidden z-[140]"
               x-on:mouseenter="open=true" x-on:mouseleave="open=false">
            <template x-if="items.length===0">
              <div class="p-3 text-sm text-neutral-600 dark:text-neutral-300">Your cart is empty.</div>
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
                  <div class="text-sm text-neutral-600 dark:text-neutral-300">Subtotal</div>
                  <div class="font-semibold text-neutral-900 dark:text-neutral-100" x-text="'$' + subtotal.toFixed(2)"></div>
                </div>
                <div class="p-3 pt-0 grid grid-cols-2 gap-2">
                  <a href="{{ route('cart') }}" class="px-3 py-2 text-sm rounded-md border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-center hover:bg-neutral-50 dark:hover:bg-neutral-800">View Cart</a>
                  <a href="{{ route('checkout') }}" class="px-3 py-2 text-sm rounded-md bg-accent-500 hover:bg-accent-600 text-white text-center">Checkout</a>
                </div>
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>

    <!-- Menu row -->
    <nav class="hidden md:flex items-center gap-6 h-12 text-sm" aria-label="Primary">
      <!-- Left: categories + main links -->
      <div class="flex items-center gap-6">
        @include('components.mega-menu', ['categories' => $navMegaCategories ?? []])
        <a href="{{ route('faqs') }}" class="text-neutral-700 hover:text-accent-600 dark:text-neutral-200">FAQs</a>
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
          <a href="{{ route('wishlist') }}" data-open-wishlist class="inline-flex items-center gap-2 text-neutral-700 hover:text-accent-600 dark:text-neutral-200">
            <i class="fa-regular fa-heart"></i>
            <span>Wishlist</span>
            <span x-show="items.length>0" x-text="items.length"
                  class="inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-secondary-500 px-1 text-xs font-semibold text-white"></span>
          </a>
          <div x-cloak x-show="open" x-transition.origin.top.right
               class="absolute right-0 top-full mt-2 w-72 soft-card p-0 overflow-hidden z-[140]"
               x-on:mouseenter="open=true" x-on:mouseleave="open=false">
            <template x-if="items.length===0">
              <div class="p-4 text-center text-sm text-neutral-500 dark:text-neutral-300">Belum ada produk di wishlist.</div>
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
                  <a href="{{ route('wishlist') }}" class="btn-accent text-sm text-center">View Wishlist</a>
                </div>
              </div>
            </template>
          </div>
        </div>
        @auth
        <div class="relative" x-data="{open:false}" x-on:keydown.escape.window="open=false">
          <button type="button" class="inline-flex items-center gap-2 text-neutral-700 hover:text-accent-600 dark:text-neutral-200" x-on:click="open=!open">
            <i class="fa-regular fa-user"></i>
            <span>{{ \Illuminate\Support\Str::limit(auth()->user()->name, 18) }}</span>
            <i class="fa-solid fa-chevron-down text-xs opacity-80"></i>
          </button>
          <div x-cloak x-show="open" x-transition.origin.top.right
               class="absolute right-0 top-full mt-2 w-48 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-xl shadow-elevated overflow-hidden z-[140]"
               x-on:mouseenter="open=true" x-on:mouseleave="open=false">
            <a href="{{ route('account') }}" class="block px-4 py-2.5 text-sm text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-800">Dashboard</a>
            @role('admin')
              <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2.5 text-sm text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-800">Admin Panel</a>
            @endrole
            <div class="border-t border-neutral-200 dark:border-neutral-800"></div>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-neutral-800">Keluar</button>
            </form>
          </div>
        </div>
        @else
        <div class="flex items-center gap-3">
          <a href="{{ route('login') }}" class="text-neutral-700 hover:text-accent-600 dark:text-neutral-200">Masuk</a>
          <span class="text-neutral-300" aria-hidden="true">|</span>
          <a href="{{ route('register') }}" class="text-neutral-700 hover:text-accent-600 dark:text-neutral-200">Daftar</a>
        </div>
        @endauth
      </div>
    </nav>
  </div>


  <!-- Mobile slide-over -->
  <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-50" aria-hidden="true">
    <div class="absolute inset-0 bg-black/40" x-on:click="open=false"></div>
    <aside class="absolute right-0 top-0 h-full w-[88vw] max-w-sm bg-white dark:bg-neutral-900 shadow-elevated p-4 overflow-y-auto" role="dialog" aria-label="Mobile menu">
      <div class="flex items-center justify-between mb-4">
        <span class="font-semibold text-neutral-800 dark:text-neutral-100">Menu</span>
        <button x-on:click="open=false" class="p-2 rounded hover:bg-neutral-100 dark:hover:bg-neutral-800" aria-label="Close menu"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="divide-y divide-neutral-200 dark:divide-neutral-800">
        <details class="py-2" :open="true">
          <summary class="cursor-pointer text-neutral-700 dark:text-neutral-200">Kategori</summary>
          <div class="mt-2 grid grid-cols-2 gap-2">
            @foreach (['Komponen','Periferal','Storage','Laptop','Monitor','Aksesoris','Jaringan','Software','Gaming'] as $cat)
              <a href="{{ route('catalog', ['category' => $cat]) }}" class="text-sm text-neutral-700 dark:text-neutral-300 hover:text-accent-600">{{ $cat }}</a>
            @endforeach
          </div>
        </details>
        <div class="py-2 flex flex-col gap-2">
          <a href="{{ route('contact') }}" class="text-neutral-700 dark:text-neutral-200">Contact Us</a>
          <a href="{{ route('faqs') }}" class="text-neutral-700 dark:text-neutral-200">FAQs</a>
          <a href="{{ route('wishlist') }}" class="text-neutral-700 dark:text-neutral-200">Wishlist</a>
          @auth
            @role('admin')
              <a href="{{ route('admin.dashboard') }}" class="text-neutral-700 dark:text-neutral-200">Admin Panel</a>
            @endrole
            <a href="{{ route('account') }}" class="text-neutral-700 dark:text-neutral-200">Akun Saya</a>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="w-full text-left text-neutral-700 dark:text-neutral-200">Keluar</button>
            </form>
          @else
            <a href="{{ route('login') }}" class="text-neutral-700 dark:text-neutral-200">Masuk</a>
            <a href="{{ route('register') }}" class="text-neutral-700 dark:text-neutral-200">Daftar</a>
          @endauth
        </div>
      </div>
    </aside>
  </div>

  <!-- Mobile search modal -->
  <div x-cloak x-show="openSearch" x-transition.opacity class="fixed inset-0 z-50" aria-hidden="true">
    <div class="absolute inset-0 bg-black/40" x-on:click="openSearch=false"></div>
    <div class="absolute inset-x-4 top-16 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-elevated p-3">
      <form action="{{ route('catalog') }}" method="get" role="search" class="flex items-center gap-2" autocomplete="off">
        <label for="mq" class="sr-only">Search products</label>
        <i class="fa-solid fa-magnifying-glass text-neutral-400"></i>
        <input id="mq" name="q" type="search" placeholder="Cari produk..." autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" class="flex-1 bg-transparent outline-none text-sm text-neutral-800 dark:text-neutral-100"/>
        <button type="submit" class="px-3 py-1.5 rounded-md bg-accent-500 hover:bg-accent-600 text-white text-sm">Search</button>
      </form>
    </div>
  </div>

  <script>
    function navbar(){
      return {
        open:false, openSearch:false,
        isDark: document.documentElement.classList.contains('dark'),
        lang: '{{ app()->getLocale() }}',
        dict: {
          en: { search_placeholder: 'Search products…', categories: 'Categories', catalog:'Catalog' },
          th: { search_placeholder: 'ค้นหาสินค้า…', categories: 'หมวดหมู่', catalog:'แคตตาล็อก' },
        },
        t(key){ return (this.dict[this.lang]||{})[key] || key; },
        toggleTheme(){
          this.isDark = !this.isDark;
          document.documentElement.classList.toggle('dark', this.isDark);
          localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
        },
        init(){
          const saved = localStorage.getItem('theme');
          if(saved){ this.isDark = saved === 'dark'; document.documentElement.classList.toggle('dark', this.isDark); }
          localStorage.setItem('lang', this.lang);
          document.documentElement.setAttribute('lang', this.lang);
          // hydrate wishlist/cart counters
          const w=document.getElementById('wishlist-count');
          const wc=(JSON.parse(localStorage.getItem('wishlistItems')||'[]')||[]).length; if(w){ w.textContent=wc; if(wc===0) w.classList.add('hidden'); }
          const cc=document.getElementById('cart-count'); const cn=parseInt(localStorage.getItem('cartCount')||'0',10); if(cc){ cc.textContent=cn; if(cn===0) cc.classList.add('hidden'); }
          const ci=document.getElementById('cart-items-count'); if(ci){ ci.textContent = (cn||0) + ' Items'; }
          document.addEventListener('i18n:change', (e)=>{
            this.lang = e.detail.lang;
            document.documentElement.setAttribute('lang', this.lang);
          });
        }
      }
    }
  
</script>
</header>
