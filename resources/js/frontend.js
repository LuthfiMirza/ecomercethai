function initWishlistCart(){
  const WKEY = 'wishlistItems';
  const CKEY = 'cartItems';
  const CCNT = 'cartCount';
  const qs = (sel, root = document) => root.querySelector(sel);
  const read = (key, fallback) => {
    try {
      const raw = localStorage.getItem(key);
      return raw ? JSON.parse(raw) : JSON.parse(JSON.stringify(fallback));
    } catch (error) {
      return Array.isArray(fallback) ? [...fallback] : fallback;
    }
  };
  const write = (key, value) => localStorage.setItem(key, JSON.stringify(value));
  const fmt = (value) => '$' + (Number(value) || 0).toFixed(2);

  const refreshCounters = () => {
    const wl = read(WKEY, []);
    const ci = read(CKEY, []);
    const wishlistCount = document.getElementById('wishlist-count');
    if (wishlistCount) {
      wishlistCount.textContent = wl.length;
      wishlistCount.classList.toggle('hidden', wl.length === 0);
    }

    const cartCount = document.getElementById('cart-count');
    const stored = Number(localStorage.getItem(CCNT) || ci.length || 0);
    if (cartCount) {
      cartCount.textContent = stored;
      cartCount.classList.toggle('hidden', stored === 0);
    }

    const itemsCount = document.getElementById('cart-items-count');
    if (itemsCount) {
      itemsCount.textContent = `${stored} Items`;
      const miniSubtotal = itemsCount.previousElementSibling;
      const sum = ci.reduce((total, item) => total + (Number(item.price) || 0), 0);
      if (miniSubtotal) miniSubtotal.textContent = fmt(sum);
    }
  };

  const openOverlay = (type) => {
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
    if (items.length === 0) {
      wrap.innerHTML = '<div class="p-4 text-center text-sm text-neutral-500 dark:text-neutral-300">Belum ada produk di wishlist.</div>';
      return;
    }
    wrap.innerHTML = items.map((p, idx) => `
      <div class="flex items-center gap-3 p-3">
        <img src="${p.image || ''}" onerror="this.style.display='none'" class="w-12 h-12 object-cover rounded"/>
        <div class="flex-1">
          <div class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">${p.name || 'Product'}</div>
          <div class="text-xs text-neutral-500">${p.price ? fmt(p.price) : ''}</div>
        </div>
        <button data-remove-wishlist="${idx}" class="text-red-600 text-sm hover:underline">Remove</button>
      </div>`).join('');
  };

  const renderCart = () => {
    const wrap = qs('#cart-items');
    if (!wrap) return;
    const items = read(CKEY, []);
    if (items.length === 0) {
      wrap.innerHTML = '<div class="p-4 text-sm text-neutral-600 dark:text-neutral-300">Your cart is empty.</div>';
      const subtotalTarget = qs('#cart-subtotal');
      if (subtotalTarget) subtotalTarget.textContent = fmt(0);
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
            <div class="text-xs text-neutral-500">${fmt(price)}</div>
          </div>
          <button data-remove-cart="${idx}" class="text-red-600 text-sm hover:underline">Remove</button>
        </div>`;
    }).join('');

    const subtotalTarget = qs('#cart-subtotal');
    if (subtotalTarget) subtotalTarget.textContent = fmt(subtotal);
  };

  document.addEventListener('click', (event) => {
    const removeWishlist = event.target.closest('[data-remove-wishlist]');
    if (removeWishlist) {
      const idx = Number(removeWishlist.getAttribute('data-remove-wishlist'));
      const items = read(WKEY, []);
      items.splice(idx, 1);
      write(WKEY, items);
      renderWishlist();
      refreshCounters();
      return;
    }

    const removeCart = event.target.closest('[data-remove-cart]');
    if (removeCart) {
      const idx = Number(removeCart.getAttribute('data-remove-cart'));
      const items = read(CKEY, []);
      items.splice(idx, 1);
      write(CKEY, items);
      localStorage.setItem(CCNT, String(items.length));
      renderCart();
      refreshCounters();
      return;
    }

    const link = event.target.closest('a[href]');
    if (link) {
      const href = link.getAttribute('href') || '';
      const newTab = event.metaKey || event.ctrlKey || link.target === '_blank';
      if (href.endsWith('/wishlist') && !newTab) {
        event.preventDefault();
        renderWishlist();
        openOverlay('wishlist');
        return;
      }
      if (href.endsWith('/cart') && !newTab) {
        event.preventDefault();
        renderCart();
        openOverlay('cart');
        return;
      }
    }

    const closeTarget = event.target.closest('[data-close]');
    if (closeTarget) {
      closeOverlay(closeTarget.getAttribute('data-close') || '');
      return;
    }

    const wishlistBtn = event.target.closest('[data-wishlist]');
    if (wishlistBtn) {
      const d = wishlistBtn.dataset;
      const priceValue = (d.price ?? '').toString().replace(/[^0-9.]/g, '');
      const items = read(WKEY, []);
      if (!items.some((item) => item.name === d.name && Number(item.price) === Number(priceValue))) {
        items.push({ name: d.name, price: Number(priceValue) || 0, image: d.image });
        write(WKEY, items);
      }
      refreshCounters();
      return;
    }

    const cartBtn = event.target.closest('[data-cart-add]');
    if (cartBtn) {
      const d = cartBtn.dataset;
      const priceValue = (d.price ?? '').toString().replace(/[^0-9.]/g, '');
      const items = read(CKEY, []);
      items.push({ name: d.name, price: Number(priceValue) || 0, image: d.image });
      write(CKEY, items);
      localStorage.setItem(CCNT, String(items.length));
      refreshCounters();
      return;
    }

    const genericButton = event.target.closest('button');
    if (!genericButton) return;

    if (/\bAdd to Cart\b/i.test(genericButton.textContent || '')) {
      const card = genericButton.closest('article, .rounded-2xl, .rounded-lg');
      const name = (card && qs('.p-4 h3, h3', card)?.textContent?.trim()) || 'Product';
      const priceText = (card && qs('.p-4 .font-semibold, .font-semibold', card)?.textContent?.replace(/[^0-9.]/g, '')) || '0';
      const img = (card && qs('img', card)?.getAttribute('src')) || '';
      const items = read(CKEY, []);
      items.push({ name, price: Number(priceText) || 0, image: img });
      write(CKEY, items);
      localStorage.setItem(CCNT, String(items.length));
      refreshCounters();
      return;
    }

    if (/\bWishlist\b/i.test(genericButton.textContent || '')) {
      const card = genericButton.closest('article, .rounded-2xl, .rounded-lg');
      const name = (card && qs('.p-4 h3, h3', card)?.textContent?.trim()) || 'Product';
      const priceText = (card && qs('.p-4 .font-semibold, .font-semibold', card)?.textContent?.replace(/[^0-9.]/g, '')) || '0';
      const img = (card && qs('img', card)?.getAttribute('src')) || '';
      const items = read(WKEY, []);
      if (!items.some((item) => item.name === name && Number(item.price) === Number(priceText))) {
        items.push({ name, price: Number(priceText) || 0, image: img });
        write(WKEY, items);
      }
      refreshCounters();
    }
  });

  const wishlistClear = qs('#wishlist-clear');
  if (wishlistClear) {
    wishlistClear.addEventListener('click', () => {
      write(WKEY, []);
      renderWishlist();
      refreshCounters();
    });
  }

  const cartClear = qs('#cart-clear');
  if (cartClear) {
    cartClear.addEventListener('click', () => {
      write(CKEY, []);
      localStorage.setItem(CCNT, '0');
      renderCart();
      refreshCounters();
    });
  }

  refreshCounters();
}

if (typeof window !== 'undefined') {
  window.addEventListener('DOMContentLoaded', () => {
    initWishlistCart();
  });
}

export { initWishlistCart };
