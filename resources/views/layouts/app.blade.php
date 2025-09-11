<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#4E342E',    // cokelat tua
                        secondary: '#8D6E63',  // cokelat muda
                        accent: '#FF7043',     // oranye
                        cream: '#F5F5DC',      // krem
                    }
                }
            }
        }
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
    
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FFFFFF',    // putih
                        secondary: '#FF7043',  // oranye
                        accent: '#FF7043',     // oranye
                        cream: '#EAD8A4',      // putih
                    }
                }
            }
        }
    </script>
    
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
<body class="bg-primary text-secondary">
    <x-navbar />

    <!-- Add padding to body to prevent content from hiding behind fixed header -->
    <div class="pt-[72px]">
        @yield('content')
    </div>

    <!-- Compare Drawer -->
    <div id="compare-drawer" class="fixed right-4 bottom-4 z-50 hidden w-[380px] max-w-[90vw] p-0 shadow-2xl rounded-2xl overflow-hidden bg-white">
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
        <div class="p-3 flex justify-end gap-2">
            <a href="{{ url('/compare') }}" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-sm">Open Compare Page</a>
        </div>
    </div>

    <!-- Floating Compare Toggle Button -->
    <button id="compare-toggle" class="fixed right-4 bottom-4 z-40 bg-orange-500 hover:bg-orange-600 text-white w-12 h-12 rounded-full shadow-lg flex items-center justify-center">
        <i class="fa-solid fa-code-compare"></i>
    </button>

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
                    <div class=\"flex items-center gap-3 p-3\">
                        <img src=\"${p.image || ''}\" onerror=\"this.style.display='none'\" class=\"w-12 h-12 object-cover rounded-md\"/>
                        <div class=\"flex-1\">
                            <div class=\"text-sm font-semibold text-gray-800\">${p.name || 'Product'}</div>
                            <div class=\"text-xs text-gray-500\">${p.price ? '$'+p.price : ''}</div>
                        </div>
                        <button data-remove=\"${idx}\" class=\"text-red-500 hover:text-red-600 text-sm\">Remove</button>
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

            updateCount();
        })();
    </script>

    <script>
      // Global wishlist/cart interactions (localStorage-backed)
      (function(){
        function qs(id){ return document.getElementById(id); }
        function refresh(){
          const w = JSON.parse(localStorage.getItem('wishlistItems')||'[]');
          const c = parseInt(localStorage.getItem('cartCount')||'0',10);
          if(qs('wishlist-count')){ qs('wishlist-count').textContent = w.length; if(w.length===0) qs('wishlist-count').classList.add('hidden'); else qs('wishlist-count').classList.remove('hidden'); }
          if(qs('cart-count')){ qs('cart-count').textContent = c; if(c===0) qs('cart-count').classList.add('hidden'); else qs('cart-count').classList.remove('hidden'); }
        }
        document.addEventListener('click', function(e){
          const w = e.target.closest('[data-wishlist]');
          if(w){
            const items = JSON.parse(localStorage.getItem('wishlistItems')||'[]');
            const d = w.dataset; const item = { name:d.name, price:d.price, image:d.image };
            if(!items.some(i=>i.name===item.name && i.price===item.price)){ items.push(item); localStorage.setItem('wishlistItems', JSON.stringify(items)); }
            refresh(); return;
          }
          const c = e.target.closest('[data-cart-add]');
          if(c){
            const count = parseInt(localStorage.getItem('cartCount')||'0',10)+1; localStorage.setItem('cartCount', String(count)); refresh(); return;
          }
        });
        refresh();
      })();
    </script>

    <x-footer />
</body>
</html>
