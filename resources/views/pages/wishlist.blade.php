@extends('layouts.app')

@section('content')
<main class="container py-12" x-data="wishlistPage" x-init="init()" x-on:keydown.escape.window="closeModal()" data-wishlist-root role="main">
  <section class="soft-card space-y-6 p-6 md:p-8">
    <header class="flex flex-wrap items-start justify-between gap-4">
      <div class="space-y-1">
        <h1 class="text-3xl font-semibold text-neutral-800 dark:text-neutral-100">{{ __('common.wishlist_title') }}</h1>
        <p class="text-sm text-neutral-500 dark:text-neutral-300">{{ __('wishlist.subtitle') }}</p>
      </div>
      <div class="flex items-center gap-2" x-show="items.length" x-cloak>
        <button type="button" class="rounded-md border border-neutral-300 px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800" x-on:click="clearAll()">{{ __('common.clear_all') }}</button>
        <a href="{{ route('catalog') }}" class="rounded-md bg-accent-500 px-4 py-2 text-sm text-white shadow hover:bg-accent-600">{{ __('wishlist.add_products') }}</a>
      </div>
    </header>

    <div x-cloak x-show="toast" x-transition class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-2 text-sm text-sky-700 dark:border-sky-800 dark:bg-sky-950/40 dark:text-sky-200" x-text="toast"></div>

    <template x-if="items.length === 0">
      <div class="soft-card space-y-4 p-10 text-center">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-800">
          <i class="fa-regular fa-heart text-2xl text-neutral-400"></i>
        </div>
        <h2 class="text-xl font-semibold text-neutral-800 dark:text-neutral-100">{{ __('wishlist.empty_title') }}</h2>
        <p class="text-sm text-neutral-500 dark:text-neutral-300">{{ __('wishlist.empty_message') }}</p>
        <div class="flex justify-center gap-3">
          <a href="{{ route('catalog') }}" class="rounded-md bg-accent-500 px-4 py-2 text-sm text-white shadow hover:bg-accent-600">{{ __('wishlist.explore_catalog') }}</a>
          <a href="{{ route('home') }}" class="rounded-md border border-neutral-300 px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800">{{ __('common.go_home') }}</a>
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
                  <h2 class="text-lg font-semibold text-neutral-800 dark:text-neutral-100" x-text="item.name || translations.productFallback"></h2>
                  <div class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('wishlist.saved_for_later') }}</div>
                </div>
                <button type="button" class="text-sm text-red-600 hover:underline" x-on:click="showRemove(idx)">{{ __('wishlist.remove') }}</button>
              </div>
              <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="text-base font-semibold text-neutral-900 dark:text-neutral-100" x-text="fmt(item.price)"></div>
                <div class="flex items-center gap-2">
                  <button type="button" class="rounded-md border border-neutral-300 px-3 py-2 text-sm text-neutral-700 hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800" x-on:click="showMove(idx)">{{ __('wishlist.move_to_cart') }}</button>
                  <a
                    :href="item.href || '#'"
                    :class="[
                      'rounded-md px-3 py-2 text-sm font-semibold transition',
                      item.href
                        ? 'bg-neutral-900 text-white hover:bg-neutral-800 dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-neutral-200'
                        : 'bg-neutral-200 text-neutral-500 cursor-not-allowed opacity-60 dark:bg-neutral-800 dark:text-neutral-500 pointer-events-none'
                    ]"
                  >
                    {{ __('wishlist.view_details') }}
                  </a>
                </div>
              </div>
            </div>
          </article>
        </template>
      </section>

      <aside class="space-y-4">
        <div class="soft-card space-y-3 p-5">
          <h2 class="text-lg font-semibold text-neutral-800 dark:text-neutral-100">{{ __('wishlist.summary_title') }}</h2>
          <div class="flex items-center justify-between text-sm text-neutral-600 dark:text-neutral-300">
            <span>{{ __('wishlist.total_items') }}</span>
            <span x-text="translations.itemsCount.replace(':count', items.length)"></span>
          </div>
          <div class="flex items-center justify-between text-sm text-neutral-600 dark:text-neutral-300">
            <span>{{ __('wishlist.estimated_value') }}</span>
            <span x-text="fmt(subtotal())"></span>
          </div>
          <div class="border-t border-white/60 pt-3 dark:border-neutral-800">
            <button type="button" class="w-full rounded-md bg-accent-500 px-4 py-2 text-sm font-medium text-white shadow hover:bg-accent-600" x-on:click="moveAllToCart()">{{ __('wishlist.move_all_to_cart') }}</button>
          </div>
          @if(auth()->check())
          <a href="{{ route('checkout') }}" class="block w-full rounded-md border border-neutral-300 px-4 py-2 text-center text-sm text-neutral-700 hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800">{{ __('wishlist.proceed_to_checkout') }}</a>
          @else
          <a href="{{ localized_route('login') }}" class="block w-full rounded-md border border-neutral-300 px-4 py-2 text-center text-sm text-neutral-700 hover:bg-neutral-50">{{ __('wishlist.login_to_checkout') }}</a>
          @endif
        </div>
        <div class="soft-card space-y-3 p-5 text-sm text-neutral-500 dark:text-neutral-300">
          <h3 class="text-sm font-semibold text-neutral-700 dark:text-neutral-200">{{ __('wishlist.tips_title') }}</h3>
          <ul class="space-y-2">
            <li>{{ __('wishlist.tip_not_reserved') }}</li>
            <li>{{ __('wishlist.tip_price_change') }}</li>
            <li>{{ __('wishlist.tip_use_cart') }}</li>
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
          <h2 class="text-lg font-semibold text-neutral-800 dark:text-neutral-100" x-text="modal.mode === 'move' ? translations.modalMoveTitle : translations.modalRemoveTitle"></h2>
          <p class="text-sm text-neutral-500 dark:text-neutral-400" x-text="modal.mode === 'move' ? translations.modalMoveText : translations.modalRemoveText"></p>
        </div>
        <button type="button" class="text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200" x-on:click="closeModal()"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="flex items-center gap-3 rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
        <img :src="modal.item?.image || placeholder" alt="" class="h-16 w-16 rounded-lg bg-neutral-100 object-cover dark:bg-neutral-800"/>
        <div>
          <div class="text-sm font-semibold text-neutral-800 dark:text-neutral-100" x-text="modal.item?.name || translations.productFallback"></div>
          <div class="text-xs text-neutral-500 dark:text-neutral-400" x-text="fmt(modal.item?.price)"></div>
        </div>
      </div>
      <div class="flex justify-end gap-3">
        <button type="button" class="rounded-md border border-neutral-300 px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800" x-on:click="closeModal()">{{ __('wishlist.cancel') }}</button>
        <button type="button" class="rounded-md px-4 py-2 text-sm text-white" :class="modal.mode === 'move' ? 'bg-accent-500 hover:bg-accent-600' : 'bg-red-500 hover:bg-red-600'" x-on:click="confirmModal()">{{ __('wishlist.continue') }}</button>
      </div>
    </div>
  </div>
</main>
@endsection

@push('scripts')
<script>
  (() => {
    const component = () => {
      const WKEY = 'wishlistItems';
      const CKEY = 'cartItems';
      const CCNT = 'cartCount';
      const CSUB = 'cartSubtotal';
      const placeholder = 'https://placehold.co/120x120?text=No+Image';
      const translations = {
        productFallback: @json(__('common.product')),
        wishlistEmpty: @json(__('common.wishlist_empty')),
        cartEmpty: @json(__('common.cart_empty')),
        remove: @json(__('wishlist.remove')),
        itemsCount: @json(__('common.items_count', ['count' => ':count'])),
        toastRemoved: @json(__('wishlist.toast_removed')),
        toastMoved: @json(__('wishlist.toast_moved')),
        toastMovedAll: @json(__('wishlist.toast_moved_all')),
        toastCleared: @json(__('wishlist.toast_cleared')),
        toastMoveFailed: @json(__('wishlist.toast_move_failed')),
        toastMovePartial: @json(__('wishlist.toast_move_partial')),
        modalMoveTitle: @json(__('wishlist.modal_move_title')),
        modalRemoveTitle: @json(__('wishlist.modal_remove_title')),
        modalMoveText: @json(__('wishlist.modal_move_text')),
        modalRemoveText: @json(__('wishlist.modal_remove_text')),
      };

      const endpoints = {
        cartAdd: @json(localized_route('cart.add')),
        cartSummary: @json(localized_route('cart.summary')),
      };

      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

      const requestHeaders = {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      };

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
          wrap.innerHTML = `<div class="p-4 text-center text-sm text-neutral-500 dark:text-neutral-300">${translations.wishlistEmpty}</div>`;
          return;
        }
        wrap.innerHTML = items.map((p, idx) => `
          <div class="flex items-center gap-3 p-3">
            <img src="${p.image || ''}" onerror="this.style.display='none'" class="w-12 h-12 object-cover rounded"/>
            <div class="flex-1">
              <div class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">${p.name || translations.productFallback}</div>
              <div class="text-xs text-neutral-500">${p.price ? format(p.price) : ''}</div>
            </div>
            <button data-remove-wishlist="${idx}" class="text-red-600 text-sm hover:underline">${translations.remove}</button>
          </div>`).join('');
      };

      const updateCartOverlay = (items) => {
        const wrap = document.getElementById('cart-items');
        const subtotalEl = document.getElementById('cart-subtotal');
        if (!wrap) return;
        if (!items.length) {
          wrap.innerHTML = `<div class="p-4 text-sm text-neutral-600 dark:text-neutral-300">${translations.cartEmpty}</div>`;
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
              <div class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">${p.name || translations.productFallback}</div>
                <div class="text-xs text-neutral-500">${format(price)}</div>
              </div>
              <button data-remove-cart="${idx}" class="text-red-600 text-sm hover:underline">${translations.remove}</button>
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
          itemsCount.textContent = translations.itemsCount.replace(':count', stored);
          const miniSubtotal = itemsCount.previousElementSibling;
          const sum = cart.reduce((total, item) => total + (Number(item.price) || 0), 0);
          if (miniSubtotal) {
            miniSubtotal.textContent = format(sum);
          }
        }
      };

      const syncCartStateWithServer = async () => {
        try {
          const response = await fetch(endpoints.cartSummary, {
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
          });

          if (!response.ok) {
            return false;
          }

          const data = await response.json();
          if (!data?.success) {
            return false;
          }

          const items = Array.isArray(data.items) ? data.items : [];
          write(CKEY, items);
          localStorage.setItem(CCNT, String(data.count ?? 0));
          localStorage.setItem(CSUB, String(data.subtotal ?? 0));
          updateCartOverlay(items);
          syncCounters();
          window.dispatchEvent(new CustomEvent('cart:update', {
            detail: {
              count: data.count ?? 0,
              subtotal: data.subtotal ?? 0,
              items,
            },
          }));

          return true;
        } catch (error) {
          return false;
        }
      };

      const addCartItem = async (item) => {
        if (!item?.product_id) {
          return false;
        }

        try {
          const body = new URLSearchParams({
            product_id: String(item.product_id),
            quantity: '1',
          });

          if (item?.color) {
            body.append('color', item.color);
          }

          const response = await fetch(endpoints.cartAdd, {
            method: 'POST',
            headers: requestHeaders,
            credentials: 'same-origin',
            body: body.toString(),
          });

          if (!response.ok) {
            return false;
          }

          const payload = await response.json().catch(() => ({}));

          return payload?.success !== false;
        } catch (error) {
          return false;
        }
      };

      return {
        translations,
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
        async confirmModal() {
          const mode = this.modal.mode;
          const index = this.modal.index;
          if (mode === 'move') {
            await this.moveToCart(index);
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
          const name = removed?.name || translations.productFallback;
          this.showToast(translations.toastRemoved.replace(':name', name));
        },
        async moveToCart(index) {
          if (index < 0 || index >= this.items.length) return;
          const moved = this.items[index];
          if (!moved) return;
          const name = moved?.name || translations.productFallback;

          const success = await addCartItem(moved);
          if (!success) {
            this.showToast(translations.toastMoveFailed.replace(':name', name));
            return;
          }

          this.items.splice(index, 1);
          write(WKEY, this.items);
          updateWishlistOverlay(this.items);
          syncCounters();
          await syncCartStateWithServer();
          this.showToast(translations.toastMoved.replace(':name', name));
        },
        async moveAllToCart() {
          if (!this.items.length) return;

          const pending = [...this.items];
          let movedCount = 0;
          const remaining = [];

          for (const item of pending) {
            const success = await addCartItem(item);
            if (success) {
              movedCount += 1;
            } else {
              remaining.push(item);
            }
          }

          this.items = remaining;
          write(WKEY, this.items);
          updateWishlistOverlay(this.items);
          syncCounters();

          if (movedCount > 0) {
            await syncCartStateWithServer();
          }

          if (movedCount > 0 && remaining.length === 0) {
            this.showToast(translations.toastMovedAll.replace(':count', movedCount));
            return;
          }

          if (remaining.length > 0) {
            const message = translations.toastMovePartial
              .replace(':moved', movedCount)
              .replace(':failed', remaining.length);
            this.showToast(message);
          }
        },
        clearAll() {
          if (!this.items.length) return;
          this.items = [];
          write(WKEY, []);
          updateWishlistOverlay(this.items);
          syncCounters();
          this.showToast(translations.toastCleared);
        },
        showToast(message) {
          this.toast = message;
          clearTimeout(this.toastTimer);
          this.toastTimer = setTimeout(() => {
            this.toast = '';
          }, 2800);
        }
      };
    };

    const register = () => {
      if (!window.Alpine) {
        return;
      }

      window.Alpine.data('wishlistPage', component);

      document.querySelectorAll('[data-wishlist-root]').forEach((el) => {
        if (el.__x) {
          return;
        }
        window.Alpine.initTree(el);
      });
    };

    if (window.Alpine) {
      register();
    } else {
      document.addEventListener('alpine:init', register, { once: true });
    }
  })();
</script>
@endpush
