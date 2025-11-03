function initWishlistCart(){
  const WKEY = 'wishlistItems';
  const CART_KEY = 'cartItems';
  const CART_COUNT_KEY = 'cartCount';
  const CART_SUBTOTAL_KEY = 'cartSubtotal';

  const qs = (sel, root = document) => root.querySelector(sel);
  const read = (key, fallback) => {
    try {
      const raw = localStorage.getItem(key);
      if (!raw) return Array.isArray(fallback) ? [...fallback] : fallback;
      const parsed = JSON.parse(raw);
      if (Array.isArray(fallback) && !Array.isArray(parsed)) {
        return [...fallback];
      }
      return parsed ?? fallback;
    } catch (error) {
      return Array.isArray(fallback) ? [...fallback] : fallback;
    }
  };
  const write = (key, value) => {
    try {
      localStorage.setItem(key, JSON.stringify(value));
    } catch (error) {
      console.warn('Local storage write failed', error);
    }
  };

  const localeAttr = document.documentElement.dataset.locale || document.documentElement.lang || 'en';
  const locale = localeAttr.split('-')[0];
  const basePath = locale ? `/${locale}` : '';
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
  const currency = document.documentElement.dataset.currency || 'THB';
  const numberFormatter = (() => {
    try {
      return new Intl.NumberFormat(localeAttr || 'en', { style: 'currency', currency });
    } catch (error) {
      return new Intl.NumberFormat('en', { style: 'currency', currency: 'USD' });
    }
  })();
  const fmt = (value) => numberFormatter.format(Number(value) || 0);

  let cartState = {
    items: Array.isArray(read(CART_KEY, [])) ? read(CART_KEY, []) : [],
    subtotal: Number(localStorage.getItem(CART_SUBTOTAL_KEY) || 0),
    count: Number(localStorage.getItem(CART_COUNT_KEY) || 0),
  };
  let summaryPromise = null;

  const persistCartState = () => {
    write(CART_KEY, cartState.items);
    localStorage.setItem(CART_COUNT_KEY, String(cartState.count));
    localStorage.setItem(CART_SUBTOTAL_KEY, String(cartState.subtotal));
  };

  const dispatchCartUpdate = () => {
    window.dispatchEvent(new CustomEvent('cart:update', {
      detail: {
        count: cartState.count,
        subtotal: cartState.subtotal,
        items: cartState.items,
      },
    }));
  };

  const updateCartState = (payload = {}) => {
    const items = Array.isArray(payload.items) ? payload.items.map((item) => ({
      id: item.id,
      product_id: item.product_id,
      name: item.name,
      quantity: Number(item.quantity) || 0,
      price: Number(item.price) || 0,
      subtotal: Number(item.subtotal ?? ((Number(item.price) || 0) * (Number(item.quantity) || 0))),
      image: item.image || '',
    })) : [];

    const subtotal = Number(payload.subtotal ?? items.reduce((total, item) => total + (item.subtotal || 0), 0));
    const count = Number(payload.count ?? items.reduce((total, item) => total + (item.quantity || 0), 0));

    cartState = { items, subtotal, count };
    persistCartState();
    renderCart();
    refreshCounters();
    dispatchCartUpdate();
  };

  const fetchCartSummary = async (silent = false) => {
    if (summaryPromise) return summaryPromise;

    summaryPromise = (async () => {
      try {
        const response = await fetch(`${basePath}/cart/summary`, {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
          credentials: 'same-origin',
        });

        if (!response.ok) {
          if (!silent) console.error('Failed to fetch cart summary', response.status);
          return;
        }

        const data = await response.json();
        if (data?.success) {
          updateCartState(data);
        }
      } catch (error) {
        if (!silent) console.error('Unable to load cart summary', error);
      } finally {
        summaryPromise = null;
      }
    })();

    return summaryPromise;
  };

  const refreshCounters = () => {
    const wishlistItems = read(WKEY, []);
    const wishlistCount = document.getElementById('wishlist-count');
    if (wishlistCount) {
      wishlistCount.textContent = wishlistItems.length;
      wishlistCount.classList.toggle('hidden', wishlistItems.length === 0);
    }

    const wishlistSummaryCount = document.getElementById('wishlist-items-count');
    if (wishlistSummaryCount) {
      const template = wishlistSummaryCount.dataset.template || ':count';
      const countText = String(wishlistItems.length || 0);
      wishlistSummaryCount.textContent = template.replace('__COUNT__', countText).replace(':count', countText);
    }

    const wishlistSubtotalEl = document.getElementById('wishlist-subtotal');
    if (wishlistSubtotalEl) {
      const wishlistTotal = wishlistItems.reduce((total, item) => total + (Number(item?.price) || 0), 0);
      wishlistSubtotalEl.textContent = fmt(wishlistTotal);
    }

    const cartCountEl = document.getElementById('cart-count');
    if (cartCountEl) {
      cartCountEl.textContent = cartState.count;
      cartCountEl.classList.toggle('hidden', cartState.count === 0);
    }

    const itemsCountEl = document.getElementById('cart-items-count');
    if (itemsCountEl) {
      const template = itemsCountEl.dataset.template || ':count';
      const countText = String(cartState.count);
      itemsCountEl.textContent = template.replace('__COUNT__', countText).replace(':count', countText);
      const miniSubtotal = itemsCountEl.previousElementSibling;
      if (miniSubtotal) miniSubtotal.textContent = fmt(cartState.subtotal);
    }

    const overlayCountEl = document.getElementById('cart-items-count-overlay');
    if (overlayCountEl) {
      const template = overlayCountEl.dataset.template || ':count';
      const countText = String(cartState.count);
      overlayCountEl.textContent = template.replace('__COUNT__', countText).replace(':count', countText);
    }
  };

  const openOverlay = async (type) => {
    if (type === 'cart') {
      await fetchCartSummary(true);
    }
    const overlay = qs(`#${type}-overlay`);
    if (overlay) overlay.classList.remove('hidden');
  };

  const closeOverlay = (type) => {
    const overlay = qs(`#${type}-overlay`);
    if (overlay) overlay.classList.add('hidden');
  };

  const renderWishlist = () => {
    const wrap = qs('#wishlist-items');
    if (!wrap) return;
    const items = read(WKEY, []);
    let subtotal = 0;

    if (items.length === 0) {
      wrap.innerHTML = '<div class="p-4 text-center text-sm text-neutral-500 dark:text-neutral-300">Belum ada produk di wishlist.</div>';
    } else {
      wrap.innerHTML = items.map((p, idx) => {
        const price = Number(p.price) || 0;
        subtotal += price;
        return `
      <div class="flex items-center gap-3 p-3">
        <img src="${p.image || ''}" onerror="this.style.display='none'" class="w-12 h-12 object-cover rounded" alt=""/>
        <div class="flex-1">
          <div class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">${p.name || 'Product'}</div>
          <div class="text-xs text-neutral-500">${p.price ? fmt(p.price) : ''}</div>
        </div>
        <button data-remove-wishlist="${idx}" class="text-red-600 text-sm hover:underline">Remove</button>
      </div>`;
      }).join('');
    }

    const wishlistSubtotal = qs('#wishlist-subtotal');
    if (wishlistSubtotal) wishlistSubtotal.textContent = fmt(subtotal);

    const wishlistCountDisplay = qs('#wishlist-items-count');
    if (wishlistCountDisplay) {
      const template = wishlistCountDisplay.dataset.template || ':count';
      const countText = String(items.length || 0);
      wishlistCountDisplay.textContent = template.replace('__COUNT__', countText).replace(':count', countText);
    }
  };

  const renderCart = () => {
    const wrap = qs('#cart-items');
    if (!wrap) return;

    if (!cartState.items.length) {
      wrap.innerHTML = '<div class="p-4 text-sm text-neutral-600 dark:text-neutral-300">Your cart is empty.</div>';
      const subtotalTarget = qs('#cart-subtotal');
      if (subtotalTarget) subtotalTarget.textContent = fmt(0);
      return;
    }

    wrap.innerHTML = cartState.items.map((item, index) => `
      <div class="flex items-center gap-3 p-3" data-cart-row="${item.id ?? `local-${index}`}">
        <img src="${item.image || ''}" onerror="this.style.display='none'" class="w-12 h-12 object-cover rounded" alt=""/>
        <div class="flex-1 min-w-0">
          <div class="text-sm font-semibold text-neutral-900 dark:text-neutral-100 truncate">${item.name || 'Product'}</div>
          <div class="text-xs text-neutral-500">Qty: ${item.quantity}</div>
          <div class="text-xs text-neutral-500">${fmt(item.price)}</div>
        </div>
        <button data-remove-cart="${item.id ?? ''}" data-remove-cart-index="${index}" class="text-red-600 text-sm hover:underline">Remove</button>
      </div>`).join('');

    const subtotalTarget = qs('#cart-subtotal');
    if (subtotalTarget) subtotalTarget.textContent = fmt(cartState.subtotal);
  };

  const addToWishlist = (data) => {
    const items = read(WKEY, []);
    if (!items.some((item) => item.name === data.name)) {
      items.push(data);
      write(WKEY, items);
      window.dispatchEvent(new CustomEvent('wishlist:update', { detail: { items } }));
    }
    renderWishlist();
    refreshCounters();
  };

  const postCartAdd = async (productId, quantity = 1) => {
    if (!productId) {
      console.warn('Missing product id for cart add');
      return;
    }

    try {
      const body = new URLSearchParams({
        product_id: String(productId),
        quantity: String(quantity || 1),
      });

      const response = await fetch(`${basePath}/cart/add`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: body.toString(),
      });

      if (!response.ok) {
        console.error('Failed to add cart item', response.status);
        return;
      }

      await fetchCartSummary(true);
    } catch (error) {
      console.error('Unable to add to cart', error);
    }
  };

  const removeCartItem = async (cartItemId) => {
    if (!cartItemId) return;
    try {
      const response = await fetch(`${basePath}/cart/${cartItemId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
      });

      if (!response.ok) {
        console.error('Failed to remove cart item', response.status);
        return;
      }

      await fetchCartSummary(true);
    } catch (error) {
      console.error('Unable to remove cart item', error);
    }
  };

  const clearCart = async () => {
    try {
      const response = await fetch(`${basePath}/cart`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
      });

      if (!response.ok) {
        console.error('Failed to clear cart', response.status);
        return;
      }

      await fetchCartSummary(true);
    } catch (error) {
      console.error('Unable to clear cart', error);
    }
  };

  document.addEventListener('click', async (event) => {
    const closeTarget = event.target.closest('[data-close]');
    if (closeTarget) {
      event.preventDefault();
      closeOverlay(closeTarget.getAttribute('data-close') || '');
      return;
    }

    const openWishlistLink = event.target.closest('[data-open-wishlist]');
    if (openWishlistLink) {
      event.preventDefault();
      renderWishlist();
      openOverlay('wishlist');
      return;
    }

    const openCartLink = event.target.closest('[data-open-cart]');
    if (openCartLink) {
      event.preventDefault();
      await openOverlay('cart');
      return;
    }

    const wishlistBtn = event.target.closest('[data-wishlist]');
    if (wishlistBtn) {
      const d = wishlistBtn.dataset;
      const priceValue = (d.price ?? '').toString().replace(/[^0-9.]/g, '');
      addToWishlist({ name: d.name || 'Product', price: Number(priceValue) || 0, image: d.image });
      return;
    }

    const cartBtn = event.target.closest('[data-cart-add]');
    if (cartBtn) {
      event.preventDefault();
      const d = cartBtn.dataset;
      const productId = d.productId || cartBtn.getAttribute('data-product-id');
      const quantity = Number(d.quantity || 1) || 1;

      if (productId) {
        await postCartAdd(productId, quantity);
      } else {
        // Fallback to legacy local storage behaviour if no product id is present (demo content)
        const priceValue = (d.price ?? '').toString().replace(/[^0-9.]/g, '');
        cartState.items.push({
          id: null,
          product_id: null,
          name: d.name || 'Product',
          quantity,
          price: Number(priceValue) || 0,
          subtotal: (Number(priceValue) || 0) * quantity,
          image: d.image || '',
        });
        cartState.count += quantity;
        cartState.subtotal += (Number(priceValue) || 0) * quantity;
        updateCartState(cartState);
      }

      return;
    }

    const removeCartBtn = event.target.closest('[data-remove-cart]');
    if (removeCartBtn) {
      event.preventDefault();
      const cartItemId = removeCartBtn.getAttribute('data-remove-cart');
      if (cartItemId) {
        await removeCartItem(cartItemId);
      } else {
        const localIndexAttr = removeCartBtn.getAttribute('data-remove-cart-index');
        if (localIndexAttr !== null) {
          const idx = Number(localIndexAttr);
          if (!Number.isNaN(idx)) {
            const items = [...cartState.items];
            items.splice(idx, 1);
            updateCartState({ items });
          }
        }
      }
      return;
    }

    const removeWishlist = event.target.closest('[data-remove-wishlist]');
    if (removeWishlist) {
      event.preventDefault();
      const idx = Number(removeWishlist.getAttribute('data-remove-wishlist'));
      const items = read(WKEY, []);
      items.splice(idx, 1);
      write(WKEY, items);
      window.dispatchEvent(new CustomEvent('wishlist:update', { detail: { items } }));
      renderWishlist();
      refreshCounters();
      return;
    }

    const link = event.target.closest('a[href]');
    if (link) {
      const href = link.getAttribute('href') || '';
      const newTab = event.metaKey || event.ctrlKey || link.target === '_blank';
      const bypassOverlay = link.hasAttribute('data-overlay-bypass');
      if (!newTab && !bypassOverlay && /\/wishlist$/.test(href)) {
        event.preventDefault();
        renderWishlist();
        openOverlay('wishlist');
        return;
      }
      if (!newTab && !bypassOverlay && /\/cart$/.test(href)) {
        event.preventDefault();
        await openOverlay('cart');
        return;
      }
    }
  });

  const wishlistClear = qs('#wishlist-clear');
  if (wishlistClear) {
    wishlistClear.addEventListener('click', (event) => {
      event.preventDefault();
      write(WKEY, []);
      window.dispatchEvent(new CustomEvent('wishlist:update', { detail: { items: [] } }));
      renderWishlist();
      refreshCounters();
    });
  }

  const cartClear = qs('#cart-clear');
  if (cartClear) {
    cartClear.addEventListener('click', async (event) => {
      event.preventDefault();
      await clearCart();
    });
  }

  window.addEventListener('storage', (event) => {
    if (event.key === WKEY) {
      renderWishlist();
      refreshCounters();
    }
  });

  renderWishlist();
  renderCart();
  refreshCounters();
  fetchCartSummary(true);
}

if (typeof window !== 'undefined') {
  window.addEventListener('DOMContentLoaded', () => {
    initWishlistCart();
  });
}

export { initWishlistCart };
