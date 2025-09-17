<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-line-id="{{ env('LINE_ID', 'tokothai') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
      try {
        var saved = localStorage.getItem('theme');
        var isDark = saved ? saved === 'dark' : (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
        if (isDark) { document.documentElement.classList.add('dark'); }
        else { document.documentElement.classList.remove('dark'); }
      } catch (e) { /* no-op */ }
    </script>
    <title>Toko Thailand - Your Tech Partner</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://unpkg.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="resources/css/app.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js for interactive UI -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none !important}</style>
    <script>
        // Single Tailwind CDN config used by the whole site
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        // Light theme accent (orange)
                        accent: { 500: '#FF7043', 600: '#f25d2e' },
                    },
                    container: { center: true, padding: '1rem' },
                    boxShadow: { soft: '0 2px 10px rgba(0,0,0,0.06)', elevated: '0 10px 25px rgba(0,0,0,0.10)' },
                    borderRadius: { xl: '1rem', '2xl': '1.25rem' },
                },
            },
        };
    </script>
    
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
    
    <!-- Removed duplicate Tailwind config to ensure consistent theming -->
    
    <style>
        .pixel-border {
            border: 2px solid #FF7043;
            image-rendering: pixelated;
        }
        .pixel-button {
            @apply font-pixel text-sm py-2 px-4 pixel-border bg-secondary text-white hover:bg-secondary/90 transition-colors;
        }
        .pixel-card {
            @apply bg-primary pixel-border hover:shadow-lg transition-shadow;
        }
        .pixel-heading {
            @apply font-pixel text-2xl md:text-3xl text-secondary;
        }
        .pixel-ad {
            @apply w-full bg-primary pixel-border p-4 text-center;
        }
    </style>
</head>
<body class="bg-white text-neutral-800 dark:bg-neutral-950 dark:text-neutral-200">
    <x-navbar />

    <!-- Add padding to body to prevent content from hiding behind fixed header -->
    <div class="pt-[72px]">
        @yield('content')
    </div>

    <!-- Compare Drawer -->
    <div id="compare-drawer" class="fixed right-4 bottom-4 z-50 hidden w-[380px] max-w-[90vw] p-0 shadow-2xl rounded-2xl overflow-hidden bg-white dark:bg-neutral-900 dark:text-neutral-100">
        <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-orange-500 to-amber-500 text-white">
            <div class="font-semibold">Compare Products</div>
            <div class="space-x-2">
                <button id="compare-clear" class="text-white/90 hover:text-white text-sm">Clear</button>
                <button id="compare-close" class="text-white/90 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>
        <div id="compare-items" class="max-h-72 overflow-auto divide-y"></div>
        <!-- Footer: Live Chat + Actions -->
        <div class="p-3 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 text-sm text-neutral-700 dark:text-neutral-200">
                <i class="fa-brands fa-line text-2xl text-green-500" aria-hidden="true"></i>
                <div>
                    <div class="font-medium">Live Chat</div>
                    <div class="text-xs opacity-80">LINE ID: <span id="line-id">tokothai</span></div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button id="copy-line-id" class="px-3 py-1.5 rounded-md border border-neutral-200 dark:border-neutral-700 text-sm hover:bg-neutral-50 dark:hover:bg-neutral-800">Copy ID</button>
                <a id="line-link" href="#" target="_blank" rel="noopener" class="px-3 py-1.5 rounded-md bg-green-500 hover:bg-green-600 text-white text-sm">Chat via LINE</a>
                <a href="{{ url('/compare') }}" class="px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white rounded-md text-sm">Open Compare Page</a>
            </div>
        </div>
    </div>

    <!-- Wishlist Drawer (bottom sheet) -->
    <div id="wishlist-overlay" class="fixed inset-0 z-50 hidden">
      <div class="absolute inset-0 bg-black/40" data-close="wishlist"></div>
      <aside class="absolute left-1/2 -translate-x-1/2 bottom-0 w-[720px] max-w-[96vw] bg-white dark:bg-neutral-900 rounded-t-2xl shadow-2xl overflow-hidden border border-neutral-200 dark:border-neutral-800">
        <header class="px-4 py-3 flex items-center justify-between border-b border-neutral-200 dark:border-neutral-800">
          <div class="font-semibold">Wishlist</div>
          <button class="p-2 rounded hover:bg-neutral-100 dark:hover:bg-neutral-800" data-close="wishlist" aria-label="Close wishlist"><i class="fa-solid fa-xmark"></i></button>
        </header>
        <div id="wishlist-items" class="max-h-[50vh] overflow-y-auto divide-y divide-neutral-200 dark:divide-neutral-800"></div>
        <footer class="px-4 py-3 flex items-center justify-between border-t border-neutral-200 dark:border-neutral-800">
          <button id="wishlist-clear" class="text-sm text-red-600 hover:underline">Clear All</button>
          <a href="{{ url('/wishlist') }}" class="px-3 py-2 rounded-md bg-accent-500 hover:bg-accent-600 text-white text-sm">Go to Wishlist</a>
        </footer>
      </aside>
    </div>

    <!-- Cart Drawer (bottom sheet) -->
    <div id="cart-overlay" class="fixed inset-0 z-50 hidden">
      <div class="absolute inset-0 bg-black/40" data-close="cart"></div>
      <aside class="absolute left-1/2 -translate-x-1/2 bottom-0 w-[720px] max-w-[96vw] bg-white dark:bg-neutral-900 rounded-t-2xl shadow-2xl overflow-hidden border border-neutral-200 dark:border-neutral-800">
        <header class="px-4 py-3 flex items-center justify-between border-b border-neutral-200 dark:border-neutral-800">
          <div class="font-semibold">Cart</div>
          <button class="p-2 rounded hover:bg-neutral-100 dark:hover:bg-neutral-800" data-close="cart" aria-label="Close cart"><i class="fa-solid fa-xmark"></i></button>
        </header>
        <div id="cart-items" class="max-h-[50vh] overflow-y-auto divide-y divide-neutral-200 dark:divide-neutral-800"></div>
        <footer class="relative px-4 py-3 flex items-center justify-between border-t border-neutral-200 dark:border-neutral-800">
          <div class="flex items-center gap-3">
            <div class="text-sm text-neutral-600 dark:text-neutral-300">Subtotal</div>
            <div id="cart-subtotal" class="font-semibold text-neutral-900 dark:text-neutral-100">$0.00</div>
          </div>
          <div class="flex items-center gap-2">
            <a href="{{ url('/cart') }}" class="px-3 py-2 rounded-md border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-sm hover:bg-neutral-50 dark:hover:bg-neutral-800">View Cart</a>
            <a href="{{ url('/checkout') }}" class="px-3 py-2 rounded-md bg-accent-500 hover:bg-accent-600 text-white text-sm">Checkout</a>
          </div>
          <button id="cart-clear" class="absolute left-4 text-sm text-red-600 hover:underline">Clear All</button>
        </footer>
      </aside>
    </div>

    <!-- Floating Quick Actions (bottom-right) -->
    <div x-data="{ open:false, chat:false }" class="fixed right-4 bottom-4 z-60" id="quick-actions">
      <!-- Stack of actions -->
      <div x-cloak x-show="open" x-transition.origin.bottom.right class="flex flex-col items-end gap-2 mb-2">
        <!-- Compare opener; uses existing compare script by reusing id -->
        <button id="compare-toggle" @click="open=false" class="inline-flex items-center gap-2 px-3 py-2 rounded-full shadow-lg bg-orange-500 hover:bg-orange-600 text-white text-sm">
          <i class="fa-solid fa-code-compare"></i>
          <span>Compare</span>
        </button>
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

    <script>
        (function(){
            const KEY = 'compareItems';
            const getItems = () => { try { return JSON.parse(localStorage.getItem(KEY) || '[]'); } catch (e) { return []; } };
            const setItems = (items) => localStorage.setItem(KEY, JSON.stringify(items));

            function updateCount(){
                const c = document.getElementById('compare-count');
                if(!c) return;
                const n = getItems().length;
                c.textContent = n;
                c.classList.toggle('hidden', n === 0);
            }

            function renderDrawer(){
                const wrap = document.getElementById('compare-items');
                if(!wrap) return;
                const items = getItems();
                if(items.length === 0){
                    wrap.innerHTML = '<div class="p-4 text-sm text-gray-600">No items added. Use "Add to Compare" on products.</div>';
                    return;
                }
                wrap.innerHTML = items.map((p,idx)=>`
                    <div class="flex items-center gap-3 p-3">
                        <img src="${p.image || ''}" onerror="this.style.display='none'" class="w-12 h-12 object-cover rounded-md"/>
                        <div class="flex-1">
                            <div class="text-sm font-semibold text-gray-800">${p.name || 'Product'}</div>
                            <div class="text-xs text-gray-500">${p.price ? '$'+p.price : ''}</div>
                        </div>
                        <button data-remove="${idx}" class="text-red-500 hover:text-red-600 text-sm">Remove</button>
                    </div>`).join('');
            }

            const drawer = document.getElementById('compare-drawer');
            const toggleBtn = document.getElementById('compare-toggle');
            const closeBtn = document.getElementById('compare-close');
            const clearBtn = document.getElementById('compare-clear');
            function openDrawer(){ drawer && drawer.classList.remove('hidden'); }
            function closeDrawer(){ drawer && drawer.classList.add('hidden'); }
            toggleBtn && toggleBtn.addEventListener('click', ()=>{ renderDrawer(); openDrawer(); });
            closeBtn && closeBtn.addEventListener('click', closeDrawer);
            clearBtn && clearBtn.addEventListener('click', ()=>{ setItems([]); renderDrawer(); updateCount(); });
            drawer && drawer.addEventListener('click', (e)=>{
                const btn = e.target.closest('[data-remove]');
                if(btn){
                    const idx = parseInt(btn.getAttribute('data-remove'));
                    const arr = getItems();
                    arr.splice(idx,1); setItems(arr); renderDrawer(); updateCount();
                }
            });

            document.addEventListener('click', function(e){
                const btn = e.target.closest('[data-compare]');
                if(!btn) return;
                const dataset = btn.dataset;
                const item = { name: dataset.name, price: dataset.price, image: dataset.image, rating: dataset.rating || '', stock: dataset.stock || '', brand: dataset.brand || '', variation: dataset.variation || '' };
                const arr = getItems();
                if(!arr.some(p => (p.name||'')=== (item.name||'') && (p.price||'') === (item.price||''))){
                    arr.push(item); setItems(arr); updateCount();
                }
                renderDrawer(); openDrawer();
            });

            // hydrate LINE ID in drawer
            const LINE_ID = document.documentElement.getAttribute('data-line-id') || 'tokothailand';
            const lineSpan = document.getElementById('line-id');
            const lineLink = document.getElementById('line-link');
            const copyBtn = document.getElementById('copy-line-id');
            if(lineSpan) lineSpan.textContent = LINE_ID;
            if(lineLink) lineLink.href = `https://line.me/ti/p/~${encodeURIComponent(LINE_ID)}`;
            if(copyBtn){
              copyBtn.addEventListener('click', async ()=>{
                try{ await navigator.clipboard.writeText(LINE_ID); copyBtn.textContent='Copied'; setTimeout(()=>copyBtn.textContent='Copy ID',1200); }catch(e){ /* ignore */ }
              });
            }

            updateCount();
        })();
    </script>

    <script>
      // Global wishlist/cart interactions + bottom sheet drawers
      (function(){
        const WKEY='wishlistItems';
        const CKEY='cartItems';
        const CCNT='cartCount';
        const qs = (sel,root=document)=>root.querySelector(sel);
        const qsa = (sel,root=document)=>Array.from(root.querySelectorAll(sel));
        const read = (k,def)=>{ try{ return JSON.parse(localStorage.getItem(k) ?? JSON.stringify(def)); }catch(e){ return def; } };
        const write = (k,v)=>localStorage.setItem(k, JSON.stringify(v));
        const fmt = (n)=>'$'+(Number(n)||0).toFixed(2);

        function refreshCounters(){
          const wl = read(WKEY, []);
          const ci = read(CKEY, []);
          const wEl = qs('#wishlist-count');
          const cEl = qs('#cart-count');
          if(wEl){ wEl.textContent = wl.length; wEl.classList.toggle('hidden', wl.length===0); }
          if(cEl){
            const cnt = Number(localStorage.getItem(CCNT) || ci.length || 0);
            cEl.textContent = cnt; cEl.classList.toggle('hidden', cnt===0);
          }
          const itemsCount = qs('#cart-items-count');
          if(itemsCount){
            const cnt = Number(localStorage.getItem(CCNT) || ci.length || 0);
            itemsCount.textContent = cnt+' Items';
            // Update mini subtotal shown above items count in navbar
            const subtotal = ci.reduce((s,p)=> s + (Number(p.price)||0), 0);
            const miniSubtotalEl = itemsCount.previousElementSibling;
            if(miniSubtotalEl){ miniSubtotalEl.textContent = fmt(subtotal); }
          }
        }

        function openOverlay(type){ const o = qs('#'+type+'-overlay'); o && o.classList.remove('hidden'); }
        function closeOverlay(type){ const o = qs('#'+type+'-overlay'); o && o.classList.add('hidden'); }

        function renderWishlist(){
          const wrap = qs('#wishlist-items'); if(!wrap) return;
          const items = read(WKEY, []);
          if(items.length===0){ wrap.innerHTML = '<div class="p-4 text-sm text-neutral-600 dark:text-neutral-300">Wishlist is empty.</div>'; return; }
          wrap.innerHTML = items.map((p,idx)=>`
            <div class="flex items-center gap-3 p-3">
              <img src="${p.image||''}" onerror="this.style.display='none'" class="w-12 h-12 object-cover rounded"/>
              <div class="flex-1">
                <div class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">${p.name||'Product'}</div>
                <div class="text-xs text-neutral-500">${p.price?fmt(p.price):''}</div>
              </div>
              <button data-remove-wishlist="${idx}" class="text-red-600 text-sm hover:underline">Remove</button>
            </div>`).join('');
        }

        function renderCart(){
          const wrap = qs('#cart-items'); if(!wrap) return;
          const items = read(CKEY, []);
          if(items.length===0){ wrap.innerHTML = '<div class="p-4 text-sm text-neutral-600 dark:text-neutral-300">Your cart is empty.</div>'; const st=qs('#cart-subtotal'); st && (st.textContent = fmt(0)); return; }
          let subtotal = 0;
          wrap.innerHTML = items.map((p,idx)=>{ subtotal += Number(p.price)||0; return `
            <div class="flex items-center gap-3 p-3">
              <img src="${p.image||''}" onerror="this.style.display='none'" class="w-12 h-12 object-cover rounded"/>
              <div class="flex-1">
                <div class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">${p.name||'Product'}</div>
                <div class="text-xs text-neutral-500">${fmt(p.price||0)}</div>
              </div>
              <button data-remove-cart="${idx}" class="text-red-600 text-sm hover:underline">Remove</button>
            </div>`; }).join('');
          const st=qs('#cart-subtotal'); st && (st.textContent = fmt(subtotal));
        }

        // Delegated remove buttons
        document.addEventListener('click', function(e){
          const rw = e.target.closest('[data-remove-wishlist]');
          if(rw){ const idx=Number(rw.getAttribute('data-remove-wishlist')); const arr=read(WKEY,[]); arr.splice(idx,1); write(WKEY,arr); renderWishlist(); refreshCounters(); return; }
          const rc = e.target.closest('[data-remove-cart]');
          if(rc){ const idx=Number(rc.getAttribute('data-remove-cart')); const arr=read(CKEY,[]); arr.splice(idx,1); write(CKEY,arr); localStorage.setItem(CCNT,String(arr.length)); renderCart(); refreshCounters(); return; }
        });

        // Open overlays from nav links (prevent navigation unless new tab)
        document.addEventListener('click', function(e){
          const a = e.target.closest('a[href]');
          if(!a) return;
          const href = a.getAttribute('href')||'';
          const newTab = e.metaKey || e.ctrlKey || a.target === '_blank';
          if(href.endsWith('/wishlist')){ if(!newTab){ e.preventDefault(); renderWishlist(); openOverlay('wishlist'); } }
          if(href.endsWith('/cart')){ if(!newTab){ e.preventDefault(); renderCart(); openOverlay('cart'); } }
        });

        // Close overlays
        document.addEventListener('click', function(e){
          const cl = e.target.closest('[data-close]');
          if(cl){ closeOverlay(cl.getAttribute('data-close')); }
        });
        qs('#wishlist-clear')?.addEventListener('click', ()=>{ write(WKEY, []); renderWishlist(); refreshCounters(); });
        qs('#cart-clear')?.addEventListener('click', ()=>{ write(CKEY, []); localStorage.setItem(CCNT,'0'); renderCart(); refreshCounters(); });

        // Add handlers (supports buttons with/without data-attrs)
        function addWishlist(item){ const arr=read(WKEY,[]); if(!arr.some(i=>i.name===item.name && i.price===item.price)){ arr.push(item); write(WKEY,arr); } refreshCounters(); }
        function addCart(item){ const arr=read(CKEY,[]); arr.push(item); write(CKEY,arr); localStorage.setItem(CCNT, String(arr.length)); refreshCounters(); }

        document.addEventListener('click', function(e){
          const w = e.target.closest('[data-wishlist]');
          if(w){ const d=w.dataset; const wp=(d.price??'').toString().replace(/[^0-9.]/g,''); addWishlist({ name:d.name, price:Number(wp)||0, image:d.image }); return; }
          const c = e.target.closest('[data-cart-add]');
          if(c){ const d=c.dataset; const dp=(d.price??'').toString().replace(/[^0-9.]/g,''); addCart({ name:d.name, price:Number(dp)||0, image:d.image }); return; }
          const btn = e.target.closest('button');
          if(!btn) return;
          if(/\bAdd to Cart\b/i.test(btn.textContent||'')){
            const card = btn.closest('article, .rounded-2xl, .rounded-lg');
            const name = (card && qs('.p-4 h3, h3', card)?.textContent?.trim()) || 'Product';
            const priceText = (card && qs('.p-4 .font-semibold, .font-semibold', card)?.textContent?.replace(/[^0-9.]/g,'')) || '0';
            const img = (card && qs('img', card)?.getAttribute('src')) || '';
            addCart({ name, price:Number(priceText)||0, image:img });
            return;
          }
          if(/\bWishlist\b/i.test(btn.textContent||'')){
            const card = btn.closest('article, .rounded-2xl, .rounded-lg');
            const name = (card && qs('.p-4 h3, h3', card)?.textContent?.trim()) || 'Product';
            const priceText = (card && qs('.p-4 .font-semibold, .font-semibold', card)?.textContent?.replace(/[^0-9.]/g,'')) || '0';
            const img = (card && qs('img', card)?.getAttribute('src')) || '';
            addWishlist({ name, price:Number(priceText)||0, image:img });
            return;
          }
        });

        // Initial
        refreshCounters();
      })();
    </script>

    <x-footer />
</body>
</html>
