<header x-data="Object.assign(navbar(), { openMega:false, lastFocus:null, headerH:72, updateHeader(){ const r=this.$refs.hdr?.getBoundingClientRect?.(); this.headerH=r?Math.round(r.height):72; } })"
        x-init="updateHeader(); window.addEventListener('resize', ()=>updateHeader())"
        x-ref="hdr"
        class="sticky top-0 z-[90] bg-white/90 dark:bg-neutral-900/80 backdrop-blur"
        role="banner">
  <!-- Topbar -->
  <div class="hidden md:block bg-[#0b0720] text-neutral-300">
    <div class="container mx-auto px-4 text-xs flex items-center justify-between h-9">
      <!-- Left items with separators, no edges -->
      <nav class="flex items-center divide-x divide-neutral-700">
        <a href="#" class="px-3 hover:text-white">About Us</a>
        <a href="{{ url('/product') }}" class="px-3 hover:text-white">Product</a>
        <a href="{{ url('/compare') }}" class="px-3 hover:text-white">Compare</a>
      </nav>
      <!-- Right items: text links | language | theme -->
      <div class="flex items-center gap-4">
        <nav class="flex items-center divide-x divide-neutral-700">
          <a href="#" class="px-3 hover:text-white">Track Your Order</a>
          <a href="#" class="px-3 hover:text-white">Contact Us</a>
          <a href="#" class="px-3 hover:text-white">FAQs</a>
        </nav>
        <!-- Removed Wishlist/Login from topbar per request -->
        <div class="flex items-center gap-3 pl-3">
          <x-language-switcher />
          <button x-on:click="toggleTheme" class="p-1.5 rounded hover:bg-white/10" aria-label="Toggle theme">
            <i x-show="isDark" class="fa-solid fa-moon text-neutral-300"></i>
            <i x-show="!isDark" class="fa-solid fa-sun text-yellow-400"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
  <div class="container mx-auto px-4">
    <div class="flex h-16 items-center gap-3 justify-between">
      <!-- Logo -->
      <a href="{{ url('/') }}" class="flex items-center gap-2" aria-label="Go to homepage">
        <img src="{{ asset('favicon.ico') }}" alt="Toko Thailand" class="h-8 w-8 rounded"/>
        <span class="font-semibold text-neutral-800 dark:text-neutral-100">Toko Thailand</span>
      </a>
      

      <!-- Center Search -->
      <div class="hidden md:block w-full max-w-xl mx-2 md:mx-6 relative" x-data="{open:false}" x-on:click.outside="open=false">
        <form action="{{ url('/search') }}" method="get" role="search" class="relative" x-on:submit="open=false" autocomplete="off">
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
             class="absolute top-full left-0 right-0 mt-2 w-full bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded-2xl shadow-elevated p-2 z-[100]">
          <div class="text-xs text-neutral-500 px-2 py-1">Suggestions</div>
          <ul class="text-sm divide-y divide-neutral-100 dark:divide-neutral-800">
            @foreach(['RTX 4070 Ti','Gaming laptop','NVMe SSD 1TB','4K Monitor'] as $s)
              <li>
                <a class="flex items-center justify-between px-3 py-2 hover:bg-neutral-50 dark:hover:bg-neutral-800 rounded focus:outline-none focus:ring-2 focus:ring-accent-500"
                   href="{{ url('/catalog?q='.urlencode($s)) }}">
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

        <!-- Compare -->
        <a href="{{ url('/compare') }}" class="relative inline-flex items-center p-2 rounded-full hover:bg-neutral-100 dark:hover:bg-neutral-800" aria-label="Open compare">
          <i class="fa-solid fa-code-compare text-neutral-700 dark:text-neutral-200"></i>
          <span id="compare-count" class="absolute -top-1 -right-1 bg-accent-500 text-white rounded-full min-w-[1.1rem] h-[1.1rem] text-[10px] leading-[1.1rem] text-center">0</span>
        </a>

        <!-- Wishlist -->
        <a href="{{ url('/wishlist') }}" class="relative inline-flex items-center p-2 rounded-full hover:bg-neutral-100 dark:hover:bg-neutral-800" aria-label="Wishlist">
          <i class="fa-regular fa-heart text-neutral-700 dark:text-neutral-200"></i>
          <span id="wishlist-count" class="absolute -top-1 -right-1 bg-secondary-500 text-white rounded-full min-w-[1.1rem] h-[1.1rem] text-[10px] leading-[1.1rem] text-center">0</span>
        </a>

        <!-- Cart -->
        <a href="{{ url('/cart') }}" class="relative inline-flex items-center p-2 rounded-full hover:bg-neutral-100 dark:hover:bg-neutral-800" aria-label="Open cart">
          <i class="fa-solid fa-cart-shopping text-neutral-700 dark:text-neutral-200"></i>
          <span id="cart-count" class="absolute -top-1 -right-1 bg-primary-600 text-white rounded-full min-w-[1.1rem] h-[1.1rem] text-[10px] leading-[1.1rem] text-center">0</span>
        </a>

        <!-- Account -->
        <a href="{{ url('/account') }}" class="hidden sm:inline-flex items-center p-2 rounded-full hover:bg-neutral-100 dark:hover:bg-neutral-800" aria-label="Account menu">
          <i class="fa-solid fa-user text-neutral-700 dark:text-neutral-200"></i>
        </a>

        <!-- Mobile search button -->
        <button class="md:hidden p-2 rounded-full hover:bg-neutral-100 dark:hover:bg-neutral-800" x-on:click="openSearch = true" aria-label="Open search">
          <i class="fa-solid fa-magnifying-glass"></i>
        </button>

        <!-- Mobile categories button -->
        <button class="md:hidden p-2 rounded-md border border-neutral-200 dark:border-neutral-700" x-on:click="lastFocus=$event.currentTarget; openMega=true" aria-label="Open categories">
          <i class="fa-solid fa-grid-2"></i>
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
          <a href="{{ url('/cart') }}" class="inline-flex items-center gap-3 text-neutral-700 dark:text-neutral-100">
            <i class="fa-solid fa-cart-shopping text-xl"></i>
            <div class="leading-tight">
              <div class="text-sm font-semibold text-red-500">$0.00</div>
              <div id="cart-items-count" class="text-xs opacity-90">0 Items</div>
            </div>
          </a>
          <!-- Popover -->
          <div x-cloak x-show="open" x-transition.origin.top.right
               class="absolute right-0 top-full mt-2 w-80 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 rounded-2xl shadow-elevated overflow-hidden z-[100]"
               x-on:mouseenter="open=true" x-on:mouseleave="open=false">
            <template x-if="items.length===0">
              <div class="p-3 text-sm text-neutral-600 dark:text-neutral-300">Your cart is empty.</div>
            </template>
            <template x-if="items.length>0">
              <div>
                <ul class="max-h-60 overflow-auto divide-y divide-neutral-100 dark:divide-neutral-800">
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
                  <a href="{{ url('/cart') }}" class="px-3 py-2 text-sm rounded-md border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-center hover:bg-neutral-50 dark:hover:bg-neutral-800">View Cart</a>
                  <a href="{{ url('/checkout') }}" class="px-3 py-2 text-sm rounded-md bg-accent-500 hover:bg-accent-600 text-white text-center">Checkout</a>
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
        <!-- Categories trigger (lower row) -->
        <button
          x-on:click="lastFocus=$event.currentTarget; openMega=true"
          :aria-expanded="openMega.toString()"
          aria-haspopup="dialog"
          aria-controls="megaMenuCategories"
          class="inline-flex items-center gap-2 text-neutral-700 hover:text-accent-600 dark:text-neutral-200 focus:outline-none focus:ring-2 focus:ring-primary-500 rounded">
          <span x-text="t('categories')">Categories</span>
          <i class="fa-solid fa-chevron-down text-xs"></i>
        </button>
        <a href="{{ url('/catalog') }}" class="text-neutral-700 hover:text-accent-600 dark:text-neutral-200" x-text="t('catalog')">Katalog</a>
        <a href="{{ url('/deals') }}" class="text-neutral-700 hover:text-accent-600 dark:text-neutral-200" x-text="t('deals')">Promo</a>
        <a href="{{ url('/support') }}" class="text-neutral-700 hover:text-accent-600 dark:text-neutral-200" x-text="t('support')">Support</a>
      </div>

      <!-- Right: wishlist + login/register (back to bottom row) -->
      <div class="ml-auto flex items-center gap-4">
        <a href="{{ url('/wishlist') }}" class="inline-flex items-center gap-2 text-neutral-700 hover:text-accent-600 dark:text-neutral-200">
          <i class="fa-regular fa-heart"></i>
          <span>Wishlist</span>
        </a>
        <span class="text-neutral-300" aria-hidden="true">|</span>
        <a href="{{ url('/account') }}" class="inline-flex items-center gap-2 text-neutral-700 hover:text-accent-600 dark:text-neutral-200">
          <i class="fa-regular fa-user"></i>
          <span>Login / Register</span>
        </a>
      </div>
    </nav>
  </div>

  <!-- Fullscreen Mega Menu (overlay + panel) -->
  <div x-cloak x-show="openMega" x-transition.opacity class="fixed inset-0 z-[80]" aria-hidden="true">
    <!-- overlay -->
      <div class="absolute inset-0 bg-black/40" x-on:click="openMega=false; lastFocus && lastFocus.focus()"></div>

    <!-- panel just below header -->
    <div id="megaMenuCategories" role="dialog" aria-modal="true" tabindex="-1"
         x-trap.noscroll="openMega"
         x-on:keydown.escape.window="openMega=false; lastFocus && lastFocus.focus()"
         x-ref="panel"
         :style="'top:'+headerH+'px'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-3"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="absolute left-0 right-0 bg-white dark:bg-neutral-900 border-t border-neutral-200 dark:border-neutral-800 shadow-2xl rounded-t-2xl max-h-[80vh] overflow-y-auto">
      <div class="container mx-auto px-4 py-6">
        <div class="flex items-start justify-between">
          <h2 class="text-lg font-semibold">Browse Categories</h2>
          <button class="rounded-xl px-3 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-800 focus:outline-none focus:ring-2 focus:ring-primary-500"
                  x-on:click="openMega=false; lastFocus && lastFocus.focus()" aria-label="Close menu">✕</button>
        </div>

        @php
          $cats = [
            ['name'=>'Computers','links'=>[['PCs','pc'],['Laptops','laptop'],['Components','components'],['Network','network']]],
            ['name'=>'Peripherals','links'=>[['Keyboard','keyboard'],['Mouse','mouse'],['Headset','headset'],['Webcam','webcam']]],
            ['name'=>'Storage','links'=>[['SSD','ssd'],['HDD','hdd'],['NVMe','nvme'],['External','external']]],
            ['name'=>'Displays','links'=>[['Monitor','monitor'],['4K Monitor','4k-monitor'],['Ultrawide','ultrawide'],['Bracket','bracket']]],
            ['name'=>'Power','links'=>[['PSU','psu'],['UPS','ups'],['Stabilizer','stabilizer']]],
            ['name'=>'Cooling','links'=>[['Air Cooler','air-cooler'],['AIO','aio'],['Thermal Paste','paste']]],
            ['name'=>'Gaming','links'=>[['GPU','gpu'],['Console','console'],['Accessory','gaming-acc']]],
            ['name'=>'Software','links'=>[['OS','os'],['Office','office'],['Security','security']]],
          ];
        @endphp

        <!-- Desktop grid -->
        <div class="hidden md:grid mt-6 grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
          @foreach($cats as $cat)
            <section class="rounded-2xl border border-neutral-200 dark:border-neutral-800 p-4 hover:shadow-soft transition">
              <div class="mb-3 flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-neutral-100 dark:bg-neutral-800"></div>
                <h3 class="font-medium">{{ $cat['name'] }}</h3>
              </div>
              <ul class="space-y-1 text-sm text-neutral-600 dark:text-neutral-300">
                @foreach($cat['links'] as $link)
                  @php($label = $link[0])
                  @php($slug  = $link[1])
                  <li><a href="{{ url('/catalog?cat='.$slug) }}" class="hover:underline focus:outline-none focus:ring-2 focus:ring-primary-500 rounded">{{ $label }}</a></li>
                @endforeach
              </ul>
            </section>
          @endforeach
        </div>

        <!-- Mobile accordion -->
        <div class="md:hidden mt-4 divide-y divide-neutral-200 dark:divide-neutral-800">
          @foreach($cats as $idx => $cat)
            <details class="py-2">
              <summary class="cursor-pointer flex items-center justify-between">
                <span class="font-medium">{{ $cat['name'] }}</span>
                <i class="fa-solid fa-chevron-down text-xs"></i>
              </summary>
              <ul class="mt-2 space-y-1 text-sm text-neutral-600 dark:text-neutral-300">
                @foreach($cat['links'] as $link)
                  @php($label = $link[0])
                  @php($slug  = $link[1])
                  <li><a href="{{ url('/catalog?cat='.$slug) }}" class="block px-2 py-1 rounded hover:bg-neutral-50 dark:hover:bg-neutral-800">{{ $label }}</a></li>
                @endforeach
              </ul>
            </details>
          @endforeach
        </div>
      </div>
    </div>
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
              <a href="{{ url('/catalog?category='.urlencode($cat)) }}" class="text-sm text-neutral-700 dark:text-neutral-300 hover:text-accent-600">{{ $cat }}</a>
            @endforeach
          </div>
        </details>
        <div class="py-2 flex flex-col gap-2">
          <a href="{{ url('/catalog') }}" class="text-neutral-700 dark:text-neutral-200">Katalog</a>
          <a href="{{ url('/deals') }}" class="text-neutral-700 dark:text-neutral-200">Promo</a>
          <a href="{{ url('/support') }}" class="text-neutral-700 dark:text-neutral-200">Support</a>
          <a href="{{ url('/wishlist') }}" class="text-neutral-700 dark:text-neutral-200">Wishlist</a>
          <a href="{{ url('/account') }}" class="text-neutral-700 dark:text-neutral-200">Login / Register</a>
        </div>
      </div>
    </aside>
  </div>

  <!-- Mobile search modal -->
  <div x-cloak x-show="openSearch" x-transition.opacity class="fixed inset-0 z-50" aria-hidden="true">
    <div class="absolute inset-0 bg-black/40" x-on:click="openSearch=false"></div>
    <div class="absolute inset-x-4 top-16 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-elevated p-3">
      <form action="{{ url('/search') }}" method="get" role="search" class="flex items-center gap-2" autocomplete="off">
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
        open:false, openSearch:false, mega:false,
        isDark: document.documentElement.classList.contains('dark'),
        lang: localStorage.getItem('lang') || 'en',
        dict: {
          en: { search_placeholder: 'Search products…', categories: 'Categories', catalog:'Catalog', deals:'Deals', support:'Support' },
          th: { search_placeholder: 'ค้นหาสินค้า…', categories: 'หมวดหมู่', catalog:'แคตตาล็อก', deals:'โปรโมชัน', support:'บริการ' },
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
          const savedLang = localStorage.getItem('lang'); if(savedLang){ this.lang = savedLang; }
          // compare badge hydrate (reads from layout script if present)
          const KEY='compareItems';
          const c=document.getElementById('compare-count');
          try{ const n=(JSON.parse(localStorage.getItem(KEY)||'[]')||[]).length; if(c){ c.textContent=n; if(n===0) c.classList.add('hidden'); } }catch(e){}
          // hydrate wishlist/cart counters
          const w=document.getElementById('wishlist-count');
          const wc=(JSON.parse(localStorage.getItem('wishlistItems')||'[]')||[]).length; if(w){ w.textContent=wc; if(wc===0) w.classList.add('hidden'); }
          const cc=document.getElementById('cart-count'); const cn=parseInt(localStorage.getItem('cartCount')||'0',10); if(cc){ cc.textContent=cn; if(cn===0) cc.classList.add('hidden'); }
          const ci=document.getElementById('cart-items-count'); if(ci){ ci.textContent = (cn||0) + ' Items'; }
          document.addEventListener('i18n:change', (e)=>{ this.lang = e.detail.lang; });
        }
      }
    }
  </script>
</header>
