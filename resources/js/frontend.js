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
      return new Intl.NumberFormat('en', { style: 'currency', currency: 'THB' });
    }
  })();
  const fmt = (value) => numberFormatter.format(Number(value) || 0);

  const messageCatalog = {
    en: {
      wishlistEmpty: 'No products in wishlist yet.',
      remove: 'Remove',
      productFallback: 'Product',
      cartEmpty: 'Your cart is empty.',
      qty: 'Qty: :qty',
      colorLabel: 'Color: :color',
      wishlistAdded: 'Already in wishlist',
    },
    th: {
      wishlistEmpty: 'ยังไม่มีสินค้าในรายการโปรด',
      remove: 'ลบ',
      productFallback: 'สินค้า',
      cartEmpty: 'ตะกร้าของคุณยังว่างอยู่',
      qty: 'จำนวน: :qty',
      colorLabel: 'สี: :color',
      wishlistAdded: 'อยู่ในรายการโปรดแล้ว',
    },
    id: {
      wishlistEmpty: 'Belum ada produk di wishlist.',
      remove: 'Hapus',
      productFallback: 'Produk',
      cartEmpty: 'Keranjang Anda masih kosong.',
      qty: 'Qty: :qty',
      colorLabel: 'Warna: :color',
      wishlistAdded: 'Sudah ada di wishlist',
    },
  };

  const translate = (key, vars = {}) => {
    const dict = messageCatalog[locale] || messageCatalog.en;
    let template = dict[key] ?? messageCatalog.en[key] ?? key;
    Object.entries(vars).forEach(([token, value]) => {
      const pattern = new RegExp(`:${token}\\b`, 'g');
      template = template.replace(pattern, value);
    });
    return template;
  };

  const parsePrice = (raw) => {
    if (typeof raw === 'number' && Number.isFinite(raw)) {
      return raw;
    }
    const cleaned = String(raw ?? '').replace(/[^0-9.,-]/g, '').replace(/,/g, '');
    const parsed = Number(cleaned);
    return Number.isFinite(parsed) ? parsed : 0;
  };

  const normalizeValue = (value) => (value ?? '').toString().trim().toLowerCase();

  const isMatchingWishlistItem = (item = {}, target = {}) => {
    const itemId = item.product_id ?? item.id ?? null;
    const targetId = target.product_id ?? target.id ?? null;
    if (itemId && targetId) {
      return String(itemId) === String(targetId);
    }
    return normalizeValue(item.name) === normalizeValue(target.name);
  };

  const toggleWishlistButtonState = (button, active) => {
    if (!button) return;
    button.classList.toggle('wishlist-active', !!active);
    button.dataset.wishlistActive = active ? 'true' : 'false';
    const defaultLabel = button.dataset.wishlistLabelDefault || button.getAttribute('aria-label') || '';
    if (!button.dataset.wishlistLabelDefault && defaultLabel) {
      button.dataset.wishlistLabelDefault = defaultLabel;
    }
    button.setAttribute('aria-pressed', active ? 'true' : 'false');
    if (active) {
      const addedLabel = translate('wishlistAdded');
      if (addedLabel) {
        button.setAttribute('aria-label', addedLabel);
      }
    } else if (button.dataset.wishlistLabelDefault) {
      button.setAttribute('aria-label', button.dataset.wishlistLabelDefault);
    }
  };

  const refreshWishlistButtons = () => {
    const items = read(WKEY, []);
    document.querySelectorAll('[data-wishlist]').forEach((button) => {
      const target = {
        product_id: button.dataset.productId || null,
        name: button.dataset.name || '',
      };
      const matched = items.some((item) => isMatchingWishlistItem(item, target));
      toggleWishlistButtonState(button, matched);
    });
  };

  const cartPopup = qs('[data-cart-popup]');
  const cartPopupImage = cartPopup?.querySelector('[data-cart-popup-image]');
  const cartPopupPlaceholder = cartPopup?.querySelector('[data-cart-popup-placeholder]');
  const cartPopupName = cartPopup?.querySelector('[data-cart-popup-name]');
  const cartPopupMeta = cartPopup?.querySelector('[data-cart-popup-meta]');
  let cartPopupTimer = null;
  const wishlistPopup = qs('[data-wishlist-popup]');
  const wishlistPopupImage = wishlistPopup?.querySelector('[data-wishlist-popup-image]');
  const wishlistPopupPlaceholder = wishlistPopup?.querySelector('[data-wishlist-popup-placeholder]');
  const wishlistPopupName = wishlistPopup?.querySelector('[data-wishlist-popup-name]');
  const wishlistPopupMeta = wishlistPopup?.querySelector('[data-wishlist-popup-meta]');
  let wishlistPopupTimer = null;

  const clearTimer = (timerRef) => {
    if (timerRef) {
      clearTimeout(timerRef);
      return null;
    }
    return timerRef;
  };

  const hideCartPopup = () => {
    if (!cartPopup) return;
    cartPopup.classList.add('opacity-0', 'translate-y-4', 'pointer-events-none');
    cartPopup.classList.remove('opacity-100', 'translate-y-0');
    cartPopup.setAttribute('aria-hidden', 'true');
    cartPopupTimer = clearTimer(cartPopupTimer);
  };

  const startCartPopupTimer = (delay = 5000) => {
    if (!cartPopup) return;
    cartPopupTimer = clearTimer(cartPopupTimer);
    cartPopupTimer = window.setTimeout(() => hideCartPopup(), delay);
  };

  const showCartPopup = ({ name, image, price, quantity } = {}) => {
    if (!cartPopup) return;
    if (cartPopupImage) {
      if (image) {
        cartPopupImage.src = image;
        cartPopupImage.classList.remove('hidden');
        cartPopupPlaceholder?.classList.add('hidden');
      } else {
        cartPopupImage.src = '';
        cartPopupImage.classList.add('hidden');
        cartPopupPlaceholder?.classList.remove('hidden');
      }
    }
    if (cartPopupName) {
      cartPopupName.textContent = name || translate('productFallback');
    }
    if (cartPopupMeta) {
      const qty = Number(quantity) || 1;
      const unit = Number(price) || 0;
      const unitText = fmt(unit);
      const totalText = fmt(unit * qty);
      cartPopupMeta.textContent = qty > 1 ? `${qty} × ${unitText} • ${totalText}` : unitText;
    }
    cartPopup.classList.remove('opacity-0', 'translate-y-4', 'pointer-events-none');
    cartPopup.classList.add('opacity-100', 'translate-y-0');
    cartPopup.setAttribute('aria-hidden', 'false');
    startCartPopupTimer();
  };

  cartPopup?.querySelectorAll('[data-cart-popup-close]').forEach((button) => {
    button.addEventListener('click', (event) => {
      event.preventDefault();
      hideCartPopup();
    });
  });
  cartPopup?.addEventListener('mouseenter', () => {
    cartPopupTimer = clearTimer(cartPopupTimer);
  });
  cartPopup?.addEventListener('mouseleave', () => {
    startCartPopupTimer(5000);
  });

  const hideWishlistPopup = () => {
    if (!wishlistPopup) return;
    wishlistPopup.classList.add('opacity-0', 'translate-y-4', 'pointer-events-none');
    wishlistPopup.classList.remove('opacity-100', 'translate-y-0');
    wishlistPopup.setAttribute('aria-hidden', 'true');
    wishlistPopupTimer = clearTimer(wishlistPopupTimer);
  };

  const showWishlistPopup = ({ name, image, price } = {}) => {
    if (!wishlistPopup) return;
    if (wishlistPopupImage) {
      if (image) {
        wishlistPopupImage.src = image;
        wishlistPopupImage.classList.remove('hidden');
        wishlistPopupPlaceholder?.classList.add('hidden');
      } else {
        wishlistPopupImage.src = '';
        wishlistPopupImage.classList.add('hidden');
        wishlistPopupPlaceholder?.classList.remove('hidden');
      }
    }
    if (wishlistPopupName) {
      wishlistPopupName.textContent = name || translate('productFallback');
    }
    if (wishlistPopupMeta) {
      wishlistPopupMeta.textContent = fmt(Number(price) || 0);
    }
    wishlistPopupTimer = clearTimer(wishlistPopupTimer);
    wishlistPopup.classList.remove('opacity-0', 'translate-y-4', 'pointer-events-none');
    wishlistPopup.classList.add('opacity-100', 'translate-y-0');
    wishlistPopup.setAttribute('aria-hidden', 'false');
  };

  wishlistPopup?.querySelectorAll('[data-wishlist-popup-close]').forEach((button) => {
    button.addEventListener('click', (event) => {
      event.preventDefault();
      hideWishlistPopup();
    });
  });

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
      color: item.color || null,
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
    document.querySelectorAll('[data-wishlist-count]').forEach((badge) => {
      const template = badge.dataset.template || '__COUNT__';
      const showZero = badge.dataset.showZero === 'true';
      const countText = String(wishlistItems.length || 0);
      badge.textContent = template.replace('__COUNT__', countText).replace(':count', countText);
      badge.classList.toggle('hidden', !showZero && Number(countText) === 0);
    });

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
      wrap.innerHTML = `<div class="p-4 text-center text-sm text-neutral-500 dark:text-neutral-300">${translate('wishlistEmpty')}</div>`;
    } else {
      wrap.innerHTML = items.map((p, idx) => {
        const price = Number(p.price) || 0;
        subtotal += price;
        return `
      <div class="flex items-center gap-3 p-3">
        <img src="${p.image || ''}" onerror="this.style.display='none'" class="w-12 h-12 object-cover rounded" alt=""/>
        <div class="flex-1">
          <div class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">${p.name || translate('productFallback')}</div>
          <div class="text-xs text-neutral-500">${p.price ? fmt(p.price) : ''}</div>
        </div>
        <button data-remove-wishlist="${idx}" class="text-red-600 text-sm hover:underline">${translate('remove')}</button>
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
    refreshWishlistButtons();
  };

  const renderCart = () => {
    const wrap = qs('#cart-items');
    if (!wrap) return;

    if (!cartState.items.length) {
      wrap.innerHTML = `<div class="p-4 text-sm text-neutral-600 dark:text-neutral-300">${translate('cartEmpty')}</div>`;
      const subtotalTarget = qs('#cart-subtotal');
      if (subtotalTarget) subtotalTarget.textContent = fmt(0);
      return;
    }

    wrap.innerHTML = cartState.items.map((item, index) => `
      <div class="flex items-center gap-3 p-3" data-cart-row="${item.id ?? `local-${index}`}">
        <img src="${item.image || ''}" onerror="this.style.display='none'" class="w-12 h-12 object-cover rounded" alt=""/>
        <div class="flex-1 min-w-0">
          <div class="text-sm font-semibold text-neutral-900 dark:text-neutral-100 truncate">${item.name || translate('productFallback')}</div>
          ${item.color ? `<div class="text-xs text-neutral-500 dark:text-neutral-400">${translate('colorLabel', { color: item.color })}</div>` : ''}
          <div class="text-xs text-neutral-500">${translate('qty', { qty: item.quantity })}</div>
          <div class="text-xs text-neutral-500">${fmt(item.price)}</div>
        </div>
        <button data-remove-cart="${item.id ?? ''}" data-remove-cart-index="${index}" class="text-red-600 text-sm hover:underline">${translate('remove')}</button>
      </div>`).join('');

    const subtotalTarget = qs('#cart-subtotal');
    if (subtotalTarget) subtotalTarget.textContent = fmt(cartState.subtotal);
  };

  const persistWishlist = (items) => {
    write(WKEY, items);
    window.dispatchEvent(new CustomEvent('wishlist:update', { detail: { items } }));
    renderWishlist();
    refreshCounters();
    refreshWishlistButtons();
  };

  const toggleWishlistItem = (data, sourceButton) => {
    const normalized = {
      name: data.name,
      price: data.price,
      image: data.image,
      product_id: data.product_id ?? null,
      href: data.href || '',
    };
    const items = read(WKEY, []);
    const index = items.findIndex((item) => isMatchingWishlistItem(item, normalized));

    if (index >= 0) {
      items.splice(index, 1);
      persistWishlist(items);
      if (sourceButton) toggleWishlistButtonState(sourceButton, false);
      hideWishlistPopup();
      return { added: false };
    }

    items.push(normalized);
    persistWishlist(items);
    if (sourceButton) toggleWishlistButtonState(sourceButton, true);
    showWishlistPopup(normalized);
    return { added: true };
  };

  const postCartAdd = async (productId, quantity = 1, color = '') => {
    if (!productId) {
      console.warn('Missing product id for cart add');
      return false;
    }

    try {
      const body = new URLSearchParams({
        product_id: String(productId),
        quantity: String(quantity || 1),
      });
      if (color) {
        body.append('color', color);
      }

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
        return false;
      }

      await fetchCartSummary(true);
      return true;
    } catch (error) {
      console.error('Unable to add to cart', error);
      return false;
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
      const priceValue = parsePrice(d.price);
      toggleWishlistItem({
        name: d.name || translate('productFallback'),
        price: priceValue,
        image: d.image,
        product_id: d.productId || null,
        href: d.url || wishlistBtn.getAttribute('data-url') || '',
      }, wishlistBtn);
      return;
    }

    const cartBtn = event.target.closest('[data-cart-add]');
    if (cartBtn) {
      event.preventDefault();
      const d = cartBtn.dataset;
      const productId = d.productId || cartBtn.getAttribute('data-product-id');
      const quantity = Number(d.quantity || 1) || 1;
      const priceValue = parsePrice(d.price);
      const payload = {
        name: d.name || translate('productFallback'),
        image: d.image || '',
        price: priceValue,
        quantity,
        color: d.color || '',
      };

      let added = true;
      if (productId) {
        added = await postCartAdd(productId, quantity);
      } else {
        // Fallback to legacy local storage behaviour if no product id is present (demo content)
        cartState.items.push({
          id: null,
          product_id: null,
          name: payload.name,
          quantity,
          price: priceValue,
          subtotal: priceValue * quantity,
          image: payload.image,
          color: payload.color || null,
        });
        cartState.count += quantity;
        cartState.subtotal += priceValue * quantity;
        updateCartState(cartState);
      }

      if (added !== false) {
        showCartPopup(payload);
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
      persistWishlist(items);
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
      persistWishlist([]);
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
      refreshWishlistButtons();
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
