@extends('layouts.app')

@section('content')
<main class="container py-12" x-data="wishlistPage()" x-init="init()" x-on:keydown.escape.window="closeModal()" role="main">
  <section class="soft-card space-y-6 p-6 md:p-8">
    <header class="flex flex-wrap items-start justify-between gap-4">
      <div class="space-y-1">
        <h1 class="text-3xl font-semibold text-neutral-800 dark:text-neutral-100">Wishlist</h1>
        <p class="text-sm text-neutral-500 dark:text-neutral-300">Kelola produk favorit dan lanjutkan belanja saat Anda siap.</p>
      </div>
      <div class="flex items-center gap-2" x-show="items.length" x-cloak>
        <button type="button" class="rounded-md border border-neutral-300 px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800" x-on:click="clearAll()">Kosongkan</button>
        <a href="{{ route('catalog') }}" class="rounded-md bg-accent-500 px-4 py-2 text-sm text-white shadow hover:bg-accent-600">Tambah Produk</a>
      </div>
    </header>

    <div x-cloak x-show="toast" x-transition class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-2 text-sm text-sky-700 dark:border-sky-800 dark:bg-sky-950/40 dark:text-sky-200" x-text="toast"></div>

    <template x-if="items.length === 0">
      <div class="soft-card space-y-4 p-10 text-center">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-800">
          <i class="fa-regular fa-heart text-2xl text-neutral-400"></i>
        </div>
        <h2 class="text-xl font-semibold text-neutral-800 dark:text-neutral-100">Wishlist masih kosong</h2>
        <p class="text-sm text-neutral-500 dark:text-neutral-300">Tambahkan produk dari katalog atau halaman produk untuk melihatnya di sini.</p>
        <div class="flex justify-center gap-3">
          <a href="{{ route('catalog') }}" class="rounded-md bg-accent-500 px-4 py-2 text-sm text-white shadow hover:bg-accent-600">Jelajahi Katalog</a>
          <a href="{{ route('home') }}" class="rounded-md border border-neutral-300 px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800">Kembali ke Beranda</a>
        </div>
      </div>
    </template>

    <div x-show="items.length > 0" x-transition class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px]">
      <section class="space-y-4">
        <template x-for="(item, idx) in items" :key="idx">
          <article class="flex flex-col gap-4 rounded-2xl border border-white/60 bg-white/80 p-4 shadow-inner backdrop-blur-xl dark:border-neutral-700 dark:bg-neutral-900/60 sm:flex-row">
            <div class="w-full shrink-0 sm:w-32">
              <img :src="item.image || placeholder" alt="" class="h-32 w-full rounded-xl bg-neutral-100 object-cover dark:bg-neutral-800"/>
            </div>
            <div class="flex-1 space-y-3">
              <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                <div>
                  <h2 class="text-lg font-semibold text-neutral-800 dark:text-neutral-100" x-text="item.name || 'Produk'"></h2>
                  <div class="text-sm text-neutral-500 dark:text-neutral-400">Disimpan untuk nanti</div>
                </div>
                <button type="button" class="text-sm text-red-600 hover:underline" x-on:click="showRemove(idx)">Hapus</button>
              </div>
              <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="text-base font-semibold text-neutral-900 dark:text-neutral-100" x-text="fmt(item.price)"></div>
                <div class="flex items-center gap-2">
                  <button type="button" class="rounded-md border border-neutral-300 px-3 py-2 text-sm text-neutral-700 hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800" x-on:click="showMove(idx)">Tambah ke Keranjang</button>
                  <a :href="item.href || '#'" class="rounded-md bg-neutral-900 px-3 py-2 text-sm text-white hover:bg-neutral-800 dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-neutral-200">Lihat Detail</a>
                </div>
              </div>
            </div>
          </article>
        </template>
      </section>

      <aside class="space-y-4">
        <div class="soft-card space-y-3 p-5">
          <h2 class="text-lg font-semibold text-neutral-800 dark:text-neutral-100">Ringkasan Wishlist</h2>
          <div class="flex items-center justify-between text-sm text-neutral-600 dark:text-neutral-300">
            <span>Total Item</span>
            <span x-text="items.length + ' produk'"></span>
          </div>
          <div class="flex items-center justify-between text-sm text-neutral-600 dark:text-neutral-300">
            <span>Estimasi Nilai</span>
            <span x-text="fmt(subtotal())"></span>
          </div>
          <div class="border-t border-white/60 pt-3 dark:border-neutral-800">
            <button type="button" class="w-full rounded-md bg-accent-500 px-4 py-2 text-sm font-medium text-white shadow hover:bg-accent-600" x-on:click="moveAllToCart()">Pindahkan Semua ke Keranjang</button>
          </div>
          <a href="{{ route('checkout') }}" class="block w-full rounded-md border border-neutral-300 px-4 py-2 text-center text-sm text-neutral-700 hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800">Lanjut ke Checkout</a>
        </div>
        <div class="soft-card space-y-3 p-5 text-sm text-neutral-500 dark:text-neutral-300">
          <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-200">Tips</h3>
          <ul class="space-y-2">
            <li>Produk di wishlist tidak otomatis dipesan.</li>
            <li>Harga bisa berubah sewaktu-waktu.</li>
            <li>Gunakan tombol keranjang untuk menyelesaikan pembayaran.</li>
          </ul>
        </div>
      </aside>
    </div>
  </section>

  <div x-cloak x-show="modal.open" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40" x-on:click="closeModal()"></div>
    <div class="relative w-[92vw] max-w-md space-y-4 rounded-2xl bg-white p-6 shadow-elevated dark:bg-neutral-900">
      <div class="flex items-start justify-between">
        <div>
          <h2 class="text-lg font-semibold text-neutral-800 dark:text-neutral-100" x-text="modal.mode === 'move' ? 'Tambah ke Keranjang' : 'Hapus Produk'"></h2>
          <p class="text-sm text-neutral-500 dark:text-neutral-400" x-text="modal.mode === 'move' ? 'Produk akan dipindahkan dari wishlist ke keranjang.' : 'Produk akan dihapus dari wishlist Anda.'"></p>
        </div>
        <button type="button" class="text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200" x-on:click="closeModal()"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="flex items-center gap-3 rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
        <img :src="modal.item?.image || placeholder" alt="" class="h-16 w-16 rounded-lg bg-neutral-100 object-cover dark:bg-neutral-800"/>
        <div>
          <div class="text-sm font-semibold text-neutral-800 dark:text-neutral-100" x-text="modal.item?.name || 'Produk'"></div>
          <div class="text-xs text-neutral-500 dark:text-neutral-400" x-text="fmt(modal.item?.price)"></div>
        </div>
      </div>
      <div class="flex justify-end gap-3">
        <button type="button" class="rounded-md border border-neutral-300 px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800" x-on:click="closeModal()">Batal</button>
        <button type="button" class="rounded-md px-4 py-2 text-sm text-white" :class="modal.mode === 'move' ? 'bg-accent-500 hover:bg-accent-600' : 'bg-red-500 hover:bg-red-600'" x-on:click="confirmModal()">Lanjutkan</button>
      </div>
    </div>
  </div>
</main>
@endsection

@push('scripts')
<script>
  function wishlistPage() {
    const WKEY = 'wishlistItems';
    const CKEY = 'cartItems';
    const CCNT = 'cartCount';
    const placeholder = 'https://placehold.co/120x120?text=No+Image';

    const read = (key, fallback = []) => {
      try {
        const raw = localStorage.getItem(key);
        if (!raw) return Array.isArray(fallback) ? [...fallback] : fallback;
        const parsed = JSON.parse(raw);
        return Array.isArray(parsed) ? parsed : fallback;
      } catch (e) {
        return Array.isArray(fallback) ? [...fallback] : fallback;
      }
    };

    const write = (key, value) => localStorage.setItem(key, JSON.stringify(value));

    const format = (value) => '$' + (Number(value) || 0).toFixed(2);

    const updateWishlistOverlay = (items) => {
      const wrap = document.getElementById('wishlist-items');
      if (!wrap) return;
      if (!items.length) {
        wrap.innerHTML = '<div class="p-4 text-center text-sm text-neutral-500 dark:text-neutral-300">Belum ada produk di wishlist.</div>';
        return;
      }
      wrap.innerHTML = items.map((p, idx) => `
        <div class="flex items-center gap-3 p-3">
          <img src="${p.image || ''}" onerror="this.style.display='none'" class="w-12 h-12 object-cover rounded"/>
          <div class="flex-1">
            <div class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">${p.name || 'Product'}</div>
            <div class="text-xs text-neutral-500">${p.price ? format(p.price) : ''}</div>
          </div>
          <button data-remove-wishlist="${idx}" class="text-red-600 text-sm hover:underline">Remove</button>
        </div>`).join('');
    };

    const updateCartOverlay = (items) => {
      const wrap = document.getElementById('cart-items');
      const subtotalEl = document.getElementById('cart-subtotal');
      if (!wrap) return;
      if (!items.length) {
        wrap.innerHTML = '<div class="p-4 text-sm text-neutral-600 dark:text-neutral-300">Your cart is empty.</div>';
        if (subtotalEl) subtotalEl.textContent = format(0);
        return;
      }
      let subtotal = 0;
      wrap.innerHTML = items.map((p, idx) => {
        const price = Number(p.price) || 0;
        subtotal += price;
        return `
          <div class="flex items-center gap-3 p-3">
            <img src="${p.image || ''}" onerror="this.style.display='none'" class="w-12 h-12 object-cover rounded"/>
            <div class="flex-1">
              <div class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">${p.name || 'Product'}</div>
              <div class="text-xs text-neutral-500">${format(price)}</div>
            </div>
            <button data-remove-cart="${idx}" class="text-red-600 text-sm hover:underline">Remove</button>
          </div>`;
      }).join('');
      if (subtotalEl) subtotalEl.textContent = format(subtotal);
    };

    const syncCounters = () => {
      const wishlist = read(WKEY, []);
      const cart = read(CKEY, []);
      const wishlistCount = document.getElementById('wishlist-count');
      if (wishlistCount) {
        wishlistCount.textContent = wishlist.length;
        wishlistCount.classList.toggle('hidden', wishlist.length === 0);
      }
      const cartCount = document.getElementById('cart-count');
      const stored = Number(localStorage.getItem(CCNT) || cart.length || 0);
      if (cartCount) {
        cartCount.textContent = stored;
        cartCount.classList.toggle('hidden', stored === 0);
      }
      const itemsCount = document.getElementById('cart-items-count');
      if (itemsCount) {
        itemsCount.textContent = stored + ' Items';
        const miniSubtotal = itemsCount.previousElementSibling;
        const sum = cart.reduce((total, item) => total + (Number(item.price) || 0), 0);
        if (miniSubtotal) {
          miniSubtotal.textContent = format(sum);
        }
      }
    };

    return {
      items: [],
      modal: { open: false, mode: null, index: null, item: null },
      toast: '',
      toastTimer: null,
      placeholder,
      init() {
        this.items = read(WKEY, []);
        updateWishlistOverlay(this.items);
        updateCartOverlay(read(CKEY, []));
        syncCounters();
        window.addEventListener('storage', (event) => {
          if (event.key === WKEY || event.key === CKEY || event.key === CCNT) {
            this.items = read(WKEY, []);
            updateWishlistOverlay(this.items);
            updateCartOverlay(read(CKEY, []));
            syncCounters();
          }
        });
      },
      fmt(value) {
        return format(value);
      },
      subtotal() {
        return this.items.reduce((total, item) => total + (Number(item.price) || 0), 0);
      },
      showMove(index) {
        const item = this.items[index];
        if (!item) return;
        this.modal = { open: true, mode: 'move', index, item };
      },
      showRemove(index) {
        const item = this.items[index];
        if (!item) return;
        this.modal = { open: true, mode: 'remove', index, item };
      },
      closeModal() {
        this.modal.open = false;
        this.modal.mode = null;
        this.modal.index = null;
        this.modal.item = null;
      },
      confirmModal() {
        const mode = this.modal.mode;
        const index = this.modal.index;
        if (mode === 'move') {
          this.moveToCart(index);
        } else if (mode === 'remove') {
          this.removeItem(index);
        }
        this.closeModal();
      },
      removeItem(index) {
        if (index < 0 || index >= this.items.length) return;
        const removed = this.items.splice(index, 1)[0];
        write(WKEY, this.items);
        updateWishlistOverlay(this.items);
        syncCounters();
        this.showToast((removed?.name || 'Produk') + ' dihapus dari wishlist.');
      },
      moveToCart(index) {
        if (index < 0 || index >= this.items.length) return;
        const cart = read(CKEY, []);
        const moved = this.items.splice(index, 1)[0];
        if (moved) {
          cart.push(moved);
          write(CKEY, cart);
          localStorage.setItem(CCNT, String(cart.length));
        }
        write(WKEY, this.items);
        updateWishlistOverlay(this.items);
        updateCartOverlay(cart);
        syncCounters();
        this.showToast((moved?.name || 'Produk') + ' dipindahkan ke keranjang.');
      },
      moveAllToCart() {
        if (!this.items.length) return;
        const cart = read(CKEY, []);
        const movedCount = this.items.length;
        cart.push(...this.items);
        this.items = [];
        write(WKEY, []);
        write(CKEY, cart);
        localStorage.setItem(CCNT, String(cart.length));
        updateWishlistOverlay(this.items);
        updateCartOverlay(cart);
        syncCounters();
        this.showToast(movedCount + ' produk dipindahkan ke keranjang.');
      },
      clearAll() {
        if (!this.items.length) return;
        this.items = [];
        write(WKEY, []);
        updateWishlistOverlay(this.items);
        syncCounters();
        this.showToast('Wishlist dikosongkan.');
      },
      showToast(message) {
        this.toast = message;
        clearTimeout(this.toastTimer);
        this.toastTimer = setTimeout(() => {
          this.toast = '';
        }, 2800);
      }
    };
  }
</script>
@endpush
