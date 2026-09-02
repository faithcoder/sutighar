(function () {
  function initSutigharTheme() {
    const header = document.querySelector('[data-sg-header]');
    const headerLockedCompact = document.body.classList.contains('sg-header-locked-compact');
    let lastY = window.scrollY;

    function updateHeader() {
      if (!header) return;
      const y = window.scrollY;
      if (y <= 40) {
        header.classList.remove('is-compact');
      } else if (Math.abs(y - lastY) > 8) {
        header.classList.toggle('is-compact', y > lastY);
        lastY = y;
      }
    }

    if (headerLockedCompact) {
      if (header) header.classList.add('is-compact');
    } else {
      window.addEventListener('scroll', updateHeader, { passive: true });
      updateHeader();
    }

  const closePops = () => {
    document.querySelectorAll('[data-sg-popover].is-open').forEach((panel) => panel.classList.remove('is-open'));
    document.querySelectorAll('[data-sg-pop-toggle][aria-expanded="true"]').forEach((btn) => btn.setAttribute('aria-expanded', 'false'));
  };

  // ---- Mobile drawer menu ----
  const drawer = document.querySelector('[data-sg-drawer]');
  const drawerToggles = Array.from(document.querySelectorAll('[data-sg-drawer-toggle]'));
  let lastDrawerFocus = null;

  function setDrawerToggleState(isOpen) {
    drawerToggles.forEach((toggle) => {
      toggle.classList.toggle('is-open', isOpen);
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      toggle.setAttribute('aria-label', isOpen ? 'Close menu' : 'Open menu');
    });
  }

  function openDrawer() {
    if (!drawer) return;
    lastDrawerFocus = document.activeElement;
    drawer.classList.add('is-open');
    drawer.setAttribute('aria-hidden', 'false');
    document.documentElement.classList.add('sg-drawer-open');
    setDrawerToggleState(true);
    const firstLink = drawer.querySelector('a[href], button');
    if (firstLink) firstLink.focus();
  }

  function closeDrawer() {
    if (!drawer) return;
    drawer.classList.remove('is-open');
    drawer.setAttribute('aria-hidden', 'true');
    document.documentElement.classList.remove('sg-drawer-open');
    setDrawerToggleState(false);
    if (lastDrawerFocus && lastDrawerFocus.focus) lastDrawerFocus.focus();
  }
  window.sgCloseDrawer = closeDrawer;

  document.addEventListener('click', (event) => {
    if (event.target.closest('[data-sg-drawer-toggle]')) {
      event.preventDefault();
      if (drawer && drawer.classList.contains('is-open')) closeDrawer();
      else openDrawer();
      return;
    }
    if (event.target.closest('[data-sg-drawer-close]')) closeDrawer();
    if (event.target.closest('.sg-drawer__nav a')) closeDrawer();
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeDrawer();
    if (event.key === 'Tab' && drawer && drawer.classList.contains('is-open')) {
      const focusables = Array.from(drawer.querySelectorAll('a[href], button:not([disabled])'));
      if (!focusables.length) return;
      const first = focusables[0];
      const last = focusables[focusables.length - 1];
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    }
  });

  window.addEventListener('resize', () => {
    if (window.innerWidth > 719) closeDrawer();
  });

  // ---- Cart drawer ----
  const cartModal = document.querySelector('[data-sg-cart-modal]');
  let lastCartModalFocus = null;

  function openCartModal() {
    if (!cartModal) return;
    lastCartModalFocus = document.activeElement;
    cartModal.classList.add('is-open');
    cartModal.setAttribute('aria-hidden', 'false');
    document.documentElement.classList.add('sg-drawer-open');
    const closeBtn = cartModal.querySelector('[data-sg-cart-modal-close]');
    if (closeBtn) closeBtn.focus();
  }

  function closeCartModal() {
    if (!cartModal) return;
    cartModal.classList.remove('is-open');
    cartModal.setAttribute('aria-hidden', 'true');
    document.documentElement.classList.remove('sg-drawer-open');
    if (lastCartModalFocus && lastCartModalFocus.focus) lastCartModalFocus.focus();
  }
  window.sgCloseCartModal = closeCartModal;

  function applyCartFragments(fragments) {
    if (!fragments) return;
    if (fragments['[data-sg-cart-count]']) {
      document.querySelectorAll('[data-sg-cart-count]').forEach((el) => {
        const tmp = document.createElement('div');
        tmp.innerHTML = fragments['[data-sg-cart-count]'];
        const next = tmp.firstElementChild;
        if (next) el.replaceWith(next);
      });
    }
    if (fragments['[data-sg-cart-total]']) {
      document.querySelectorAll('[data-sg-cart-total]').forEach((el) => {
        const tmp = document.createElement('div');
        tmp.innerHTML = fragments['[data-sg-cart-total]'];
        const next = tmp.firstElementChild;
        if (next) el.replaceWith(next);
      });
    }
    if (fragments['[data-sg-cart-modal-body]']) {
      const body = document.querySelector('[data-sg-cart-modal-body]');
      if (body) body.innerHTML = fragments['[data-sg-cart-modal-body]'];
    }
    if (fragments['[data-sg-cart-modal-foot]']) {
      const foot = document.querySelector('[data-sg-cart-modal-foot]');
      if (foot) foot.innerHTML = fragments['[data-sg-cart-modal-foot]'];
    }
  }

  async function cartAjax(action, params) {
    if (!window.sutighar || !window.sutighar.cartNonce) return;
    const body = new FormData();
    body.append('action', action);
    body.append('nonce', window.sutighar.cartNonce);
    Object.keys(params).forEach((key) => body.append(key, params[key]));
    try {
      const res = await fetch(window.sutighar.ajaxUrl, { method: 'POST', credentials: 'same-origin', body });
      const json = await res.json();
      if (json && json.fragments) applyCartFragments(json.fragments);
    } catch (error) {
      // silent: keep current state
    }
  }

  document.addEventListener('click', (event) => {
    if (event.target.closest('[data-sg-cart-open]')) {
      event.preventDefault();
      closeDrawer();
      closePops();
      openCartModal();
      return;
    }
    if (event.target.closest('[data-sg-cart-modal-close]')) {
      closeCartModal();
      return;
    }
    const removeBtn = event.target.closest('[data-sg-cart-remove]');
    if (removeBtn) {
      event.preventDefault();
      cartAjax('sg_remove_cart_item', { cart_key: removeBtn.getAttribute('data-sg-cart-remove') });
      return;
    }
    const qtyBtn = event.target.closest('[data-sg-cart-qty]');
    if (qtyBtn && cartModal && cartModal.classList.contains('is-open')) {
      event.preventDefault();
      event.stopImmediatePropagation();
      const key = qtyBtn.getAttribute('data-key');
      const row = qtyBtn.closest('.sg-cart-modal__item');
      const input = row && row.querySelector('input.qty');
      if (!key || !input) return;
      const current = parseInt(input.value, 10) || 1;
      const max = input.max === '' ? Infinity : parseInt(input.max, 10);
      const next = qtyBtn.getAttribute('data-sg-cart-qty') === 'plus' ? Math.min(max, current + 1) : Math.max(1, current - 1);
      if (next === current) return;
      cartAjax('sg_update_cart_item', { cart_key: key, quantity: String(next) });
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeCartModal();
  });

  document.addEventListener('pointerdown', (event) => {
    const toggle = event.target.closest('[data-sg-pop-toggle]');
    const insidePanel = event.target.closest('[data-sg-popover]');
    if (toggle) {
      const key = toggle.getAttribute('data-sg-pop-toggle');
      const panel = document.querySelector('[data-sg-popover="' + key + '"]');
      const willOpen = panel && !panel.classList.contains('is-open');
      closePops();
      if (panel && willOpen) {
        panel.classList.add('is-open');
        toggle.setAttribute('aria-expanded', 'true');
      }
      return;
    }
    if (!insidePanel) closePops();
  }, true);

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      closePops();
      closeSizeChart();
      closeGallery();
    }
    if (event.key === 'ArrowLeft') showGallerySlide(galleryIndex - 1);
    if (event.key === 'ArrowRight') showGallerySlide(galleryIndex + 1);
  });

  document.addEventListener('click', (event) => {
    const qtyBtn = event.target.closest('[data-sg-qty]');
    if (qtyBtn) {
      const wrap = qtyBtn.closest('.quantity');
      const input = wrap && wrap.querySelector('input.qty');
      if (!input) return;
      const step = qtyBtn.getAttribute('data-sg-qty') === 'plus' ? 1 : -1;
      const min = input.min === '' ? 0 : parseFloat(input.min);
      const max = input.max === '' ? Infinity : parseFloat(input.max);
      const current = parseFloat(input.value || '0');
      const next = Math.max(min, Math.min(max, current + step));
      if (next === current) return;
      input.value = next;
      input.dispatchEvent(new Event('change', { bubbles: true }));
    }
  });

  document.addEventListener('click', async (event) => {
    const btn = event.target.closest('[data-sg-card-add-to-cart]');
    if (!btn || !window.sutighar || !window.sutighar.cartNonce || btn.dataset.sgAdding === '1') return;
    event.preventDefault();
    event.stopPropagation();

    const body = new FormData();
    body.append('action', 'sg_add_to_cart');
    body.append('nonce', window.sutighar.cartNonce);
    body.append('product_id', btn.getAttribute('data-product-id'));
    body.append('quantity', '1');

    btn.dataset.sgAdding = '1';
    btn.classList.add('is-adding');
    btn.disabled = true;

    try {
      const res = await fetch(window.sutighar.ajaxUrl, { method: 'POST', credentials: 'same-origin', body });
      const json = await res.json();
      if (json && json.fragments) {
        applyCartFragments(json.fragments);
        btn.classList.add('is-added');
        openCartModal();
        setTimeout(() => btn.classList.remove('is-added'), 1200);
      }
    } catch (error) {
      // Keep the card usable if the network request fails.
    } finally {
      setTimeout(() => {
        btn.disabled = false;
        btn.classList.remove('is-adding');
        delete btn.dataset.sgAdding;
      }, 500);
    }
  });

  document.addEventListener('click', async (event) => {
    const btn = event.target.closest('[data-sg-wishlist-toggle]');
    if (!btn || !window.sutighar) return;
    event.preventDefault();
    const saved = btn.classList.contains('is-saved');
    const body = new FormData();
    body.append('action', saved ? 'sg_wishlist_remove' : 'sg_wishlist_add');
    body.append('nonce', window.sutighar.wishlistNonce);
    body.append('product_id', btn.getAttribute('data-product-id'));
    const res = await fetch(window.sutighar.ajaxUrl, { method: 'POST', credentials: 'same-origin', body });
    const json = await res.json();
    if (!json || !json.success) return;
    btn.classList.toggle('is-saved', !saved);
    btn.setAttribute('aria-pressed', saved ? 'false' : 'true');
    document.querySelectorAll('[data-sg-wishlist-count]').forEach((badge) => {
      badge.textContent = json.data.count;
      badge.setAttribute('data-count', json.data.count);
    });
    if (document.body.classList.contains('page-template-page-wishlist') && saved) {
      const card = btn.closest('.product');
      if (card) card.remove();
    }
  });

  document.addEventListener('click', async (event) => {
    const submitter = event.target.closest('[data-sg-ajax-cart]');
    const form = submitter && submitter.closest('.sg-cart-form');
    if (!form || !submitter || !window.sutighar || submitter.dataset.sgAdding === '1') return;
    event.preventDefault();
    event.stopPropagation();
    event.stopImmediatePropagation();
    const productId = form.querySelector('[name="add-to-cart"], [name="product_id"]');
    const body = new FormData(form);
    if (submitter.name && submitter.value && submitter.name !== 'add-to-cart') {
      body.set(submitter.name, submitter.value);
    }
    body.delete('add-to-cart');
    body.set('action', 'sg_add_to_cart');
    body.set('nonce', window.sutighar.cartNonce);
    if (productId) body.set('product_id', productId.value);
    submitter.dataset.sgAdding = '1';
    submitter.disabled = true;
    submitter.textContent = window.sutighar.i18n.adding;
    try {
      const res = await fetch(window.sutighar.ajaxUrl, { method: 'POST', credentials: 'same-origin', body });
      const json = await res.json();
      if (json && json.fragments) {
        applyCartFragments(json.fragments);
      }
      openCartModal();
      submitter.textContent = window.sutighar.i18n.added + ' ✓';
      setTimeout(() => { submitter.textContent = window.sutighar.i18n.added.replace('Added', 'Add to Cart'); }, 1200);
    } catch (error) {
      submitter.textContent = window.sutighar.i18n.error;
    } finally {
      setTimeout(() => {
        submitter.disabled = false;
        delete submitter.dataset.sgAdding;
      }, 700);
    }
  }, true);

  const density = document.querySelector('[data-sg-density]');
  if (density) {
    const productGrids = Array.from(document.querySelectorAll('ul.products'));
    const mobileGrid = window.matchMedia('(max-width: 719px)');
    let currentLayout = localStorage.getItem('sgProductLayout') || '';
    let currentCols = '4';
    const syncGridVars = () => {
      const isList = currentLayout === 'list';
      document.body.classList.toggle('sg-layout-list', isList);
      productGrids.forEach((grid) => {
        if (!isList && mobileGrid.matches) {
          grid.style.removeProperty('--sg-cols');
        } else {
          grid.style.setProperty('--sg-cols', isList ? '1' : currentCols);
        }
      });
    };
    const applyLayout = (layout, cols) => {
      const isList = layout === 'list';
      currentLayout = isList ? 'list' : 'grid';
      currentCols = cols;
      syncGridVars();
      localStorage.setItem('sgProductLayout', isList ? 'list' : `grid:${cols}`);
      localStorage.removeItem('sgCols');
      density.querySelectorAll('button').forEach((button) => {
        const buttonIsActive = isList
          ? button.dataset.layout === 'list'
          : button.dataset.layout === 'grid' && button.dataset.cols === String(cols);
        button.classList.toggle('is-active', buttonIsActive);
      });
    };
    const saved = localStorage.getItem('sgProductLayout');
    const legacyCols = localStorage.getItem('sgCols');
    if (saved === 'list') {
      applyLayout('list', '4');
    } else if (saved && saved.startsWith('grid:')) {
      applyLayout('grid', saved.split(':')[1] || '4');
    } else if (legacyCols) {
      applyLayout('grid', legacyCols);
    }
    density.addEventListener('click', (event) => {
      const btn = event.target.closest('button[data-layout]');
      if (!btn) return;
      applyLayout(btn.dataset.layout, btn.dataset.cols || '4');
    });
    if (typeof mobileGrid.addEventListener === 'function') {
      mobileGrid.addEventListener('change', syncGridVars);
    } else if (typeof mobileGrid.addListener === 'function') {
      mobileGrid.addListener(syncGridVars);
    }
  }

  const modal = document.querySelector('[data-sg-size-modal]');
  function openSizeChart() {
    if (modal) modal.hidden = false;
  }
  function closeSizeChart() {
    if (modal) modal.hidden = true;
  }
  window.closeSizeChart = closeSizeChart;
  document.addEventListener('click', (event) => {
    if (event.target.closest('[data-sg-size-chart]')) openSizeChart();
    if (event.target.closest('[data-sg-size-close]')) closeSizeChart();
  });

  const galleryRoot = document.querySelector('[data-sg-product-gallery]');
  const galleryModal = document.querySelector('[data-sg-gallery-modal]');
  const galleryImage = galleryModal && galleryModal.querySelector('[data-sg-gallery-image]');
  const galleryCounter = galleryModal && galleryModal.querySelector('[data-sg-gallery-counter]');
  let galleryItems = [];
  let galleryIndex = 0;
  let galleryScale = 1;
  let galleryX = 0;
  let galleryY = 0;
  let galleryDrag = null;
  function normalizeGalleryItems(items) {
    return Array.isArray(items) ? items.filter((item) => item && (item.full || item.display)) : [];
  }
  function galleryItemFromButton(button) {
    if (!button) return null;
    const img = button.querySelector('img');
    return {
      display: button.getAttribute('data-sg-gallery-display') || (img && img.currentSrc) || (img && img.src) || button.getAttribute('data-sg-gallery-full') || '',
      full: button.getAttribute('data-sg-gallery-full') || button.getAttribute('data-sg-gallery-display') || (img && img.currentSrc) || (img && img.src) || '',
      alt: button.getAttribute('data-sg-gallery-alt') || (img && img.alt) || '',
    };
  }
  function readGalleryItems() {
    if (!galleryRoot) return [];
    const json = galleryRoot.querySelector('[data-sg-gallery-json]');
    const raw = (json && json.textContent) || galleryRoot.getAttribute('data-sg-gallery-items') || '[]';
    try {
      return normalizeGalleryItems(JSON.parse(raw));
    } catch (error) {
      return normalizeGalleryItems(Array.from(galleryRoot.querySelectorAll('[data-sg-gallery-open], [data-sg-gallery-select]')).map(galleryItemFromButton));
    }
  }
  galleryItems = readGalleryItems();
  function ensureGalleryItems(opener) {
    if (!galleryItems.length) galleryItems = readGalleryItems();
    const clickedItem = galleryItemFromButton(opener);
    if (clickedItem && clickedItem.full) {
      const exists = galleryItems.some((item) => item.full === clickedItem.full || item.display === clickedItem.display);
      if (!exists) galleryItems.push(clickedItem);
    }
    return clickedItem;
  }
  const clampGalleryScale = (scale) => Math.max(1, Math.min(4, scale));
  function applyGalleryZoom() {
    if (!galleryImage) return;
    galleryImage.style.transform = 'translate3d(' + galleryX + 'px, ' + galleryY + 'px, 0) scale(' + galleryScale + ')';
    galleryImage.classList.toggle('is-zoomed', galleryScale > 1);
  }
  function resetGalleryZoom() {
    galleryScale = 1;
    galleryX = 0;
    galleryY = 0;
    if (galleryImage) {
      galleryImage.style.transformOrigin = '50% 50%';
      galleryImage.classList.remove('is-dragging');
    }
    applyGalleryZoom();
  }
  function setGalleryZoom(nextScale, event) {
    if (!galleryImage) return;
    galleryScale = clampGalleryScale(nextScale);
    if (event) {
      const rect = galleryImage.getBoundingClientRect();
      const x = ((event.clientX - rect.left) / rect.width) * 100;
      const y = ((event.clientY - rect.top) / rect.height) * 100;
      galleryImage.style.transformOrigin = x + '% ' + y + '%';
    }
    if (galleryScale === 1) {
      galleryX = 0;
      galleryY = 0;
    }
    applyGalleryZoom();
  }
  function showGallerySlide(index) {
    if (!galleryModal || galleryModal.hidden || !galleryImage || !galleryItems.length) return;
    galleryIndex = (index + galleryItems.length) % galleryItems.length;
    const item = galleryItems[galleryIndex];
    galleryImage.src = item.full || item.display;
    galleryImage.alt = item.alt || '';
    if (galleryCounter) galleryCounter.textContent = (galleryIndex + 1) + ' / ' + galleryItems.length;
    resetGalleryZoom();
  }
  function openGallery(index, opener) {
    if (!galleryModal || !galleryImage) return;
    const clickedItem = ensureGalleryItems(opener);
    if (!galleryItems.length) return;
    let nextIndex = Number.isFinite(index) ? index : 0;
    if (!galleryItems[nextIndex] && clickedItem) {
      nextIndex = galleryItems.findIndex((item) => item.full === clickedItem.full || item.display === clickedItem.display);
    }
    if (!galleryItems[nextIndex]) nextIndex = 0;
    galleryModal.hidden = false;
    document.documentElement.classList.add('sg-modal-open');
    showGallerySlide(nextIndex);
  }
  function selectGalleryImage(selector) {
    if (!galleryRoot || !selector) return;
    const main = galleryRoot.querySelector('[data-sg-gallery-open]');
    const mainImage = galleryRoot.querySelector('[data-sg-main-gallery-image]');
    const item = galleryItemFromButton(selector);
    const index = Number(selector.getAttribute('data-sg-gallery-index')) || 0;
    if (!main || !mainImage || !item || !item.display) return;

    mainImage.src = item.display;
    mainImage.alt = item.alt || '';
    mainImage.removeAttribute('srcset');
    mainImage.removeAttribute('sizes');
    main.setAttribute('data-sg-gallery-index', String(index));
    main.setAttribute('data-sg-gallery-display', item.display);
    main.setAttribute('data-sg-gallery-full', item.full || item.display);
    main.setAttribute('data-sg-gallery-alt', item.alt || '');
    galleryRoot.querySelectorAll('[data-sg-gallery-select]').forEach((thumb) => {
      thumb.classList.toggle('is-active', thumb === selector);
    });
  }
  function closeGallery() {
    if (!galleryModal || !galleryImage) return;
    galleryModal.hidden = true;
    galleryImage.removeAttribute('src');
    galleryImage.alt = '';
    resetGalleryZoom();
    document.documentElement.classList.remove('sg-modal-open');
  }
  window.closeGallery = closeGallery;
  document.addEventListener('click', (event) => {
    const selector = event.target.closest('[data-sg-gallery-select]');
    if (selector) {
      event.preventDefault();
      selectGalleryImage(selector);
      return;
    }
    const opener = event.target.closest('[data-sg-gallery-open]');
    if (opener) {
      event.preventDefault();
      openGallery(Number(opener.getAttribute('data-sg-gallery-index')), opener);
      return;
    }
    const zoomButton = event.target.closest('[data-sg-gallery-zoom]');
    if (zoomButton) {
      const action = zoomButton.getAttribute('data-sg-gallery-zoom');
      if (action === 'in') setGalleryZoom(galleryScale + .5);
      if (action === 'out') setGalleryZoom(galleryScale - .5);
      if (action === 'reset') resetGalleryZoom();
      return;
    }
    if (event.target.closest('[data-sg-gallery-prev]')) {
      showGallerySlide(galleryIndex - 1);
      return;
    }
    if (event.target.closest('[data-sg-gallery-next]')) {
      showGallerySlide(galleryIndex + 1);
      return;
    }
    if (event.target.closest('[data-sg-gallery-close]')) closeGallery();
  });
  if (galleryImage) {
    galleryImage.addEventListener('dblclick', (event) => {
      setGalleryZoom(galleryScale > 1 ? 1 : 2.5, event);
    });
    galleryImage.addEventListener('pointerdown', (event) => {
      if (galleryScale <= 1) return;
      galleryDrag = {
        pointerId: event.pointerId,
        startX: event.clientX,
        startY: event.clientY,
        imageX: galleryX,
        imageY: galleryY,
      };
      galleryImage.classList.add('is-dragging');
      galleryImage.setPointerCapture(event.pointerId);
    });
    galleryImage.addEventListener('pointermove', (event) => {
      if (!galleryDrag || galleryDrag.pointerId !== event.pointerId) return;
      galleryX = galleryDrag.imageX + event.clientX - galleryDrag.startX;
      galleryY = galleryDrag.imageY + event.clientY - galleryDrag.startY;
      applyGalleryZoom();
    });
    galleryImage.addEventListener('pointerup', () => {
      galleryDrag = null;
      galleryImage.classList.remove('is-dragging');
    });
    galleryImage.addEventListener('pointercancel', () => {
      galleryDrag = null;
      galleryImage.classList.remove('is-dragging');
    });
  }
  if (galleryModal) {
    galleryModal.addEventListener('wheel', (event) => {
      if (galleryModal.hidden) return;
      event.preventDefault();
      setGalleryZoom(galleryScale + (event.deltaY < 0 ? .25 : -.25), event);
    }, { passive: false });
  }

  function syncPaymentCards() {
    document.querySelectorAll('#payment li.wc_payment_method').forEach((item) => {
      const radio = item.querySelector('input[type="radio"][name="payment_method"]');
      item.classList.toggle('is-selected', !!radio && radio.checked);
    });
  }

  function stabilizeCheckoutLayout() {
    const form = document.querySelector('form.checkout');
    const grid = form && form.querySelector('.sg-checkout-grid');
    if (!form || !grid) return;
    Array.from(form.children).forEach((notice) => {
      if (!notice.matches('.woocommerce-NoticeGroup, .woocommerce-NoticeGroup-checkout, .woocommerce-error, .woocommerce-info, .woocommerce-message')) return;
      notice.hidden = true;
      if (notice.nextElementSibling === grid) return;
      form.insertBefore(notice, grid);
    });
  }

  function selectedDistrictSlug() {
    const district = document.querySelector('[name="billing_state"]');
    return district && district.value ? district.value.toLowerCase() : '';
  }

  function syncCheckoutShippingCost() {
    const wrap = document.querySelector('[data-sg-shipping-cost]');
    const value = wrap && wrap.querySelector('[data-sg-shipping-cost-value]');
    if (!wrap || !value) return;
    const district = selectedDistrictSlug();
    if (!district) {
      value.textContent = wrap.getAttribute('data-empty-label') || '';
      wrap.classList.remove('has-value');
      return;
    }
    value.textContent = district === 'dhaka' ? wrap.getAttribute('data-inside-label') : wrap.getAttribute('data-outside-label');
    wrap.classList.add('has-value');
  }

  function syncMobileCheckoutSummary() {
    const summary = document.querySelector('[data-sg-checkout-summary]');
    const payment = document.querySelector('.sg-checkout-page #payment');
    const placeOrder = payment && payment.querySelector('.place-order');
    if (!summary || !payment || !placeOrder) return;

    const existing = payment.querySelector('[data-sg-checkout-summary-mobile]');

    if (window.matchMedia('(max-width: 719px)').matches) {
      const clone = summary.cloneNode(true);
      clone.removeAttribute('data-sg-checkout-summary');
      clone.setAttribute('data-sg-checkout-summary-mobile', '');
      clone.removeAttribute('id');
      clone.querySelectorAll('[id]').forEach((node) => node.removeAttribute('id'));
      if (existing) existing.replaceWith(clone);
      else payment.insertBefore(clone, placeOrder);
      return;
    }

    if (existing) existing.remove();
  }

  document.addEventListener('click', (event) => {
    const summaryToggle = event.target.closest('[data-sg-summary-toggle]');
    if (summaryToggle) {
      const summary = summaryToggle.closest('.sg-cart-summary');
      if (summary) {
        const open = !summary.classList.contains('is-open');
        summary.classList.toggle('is-open', open);
        summaryToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      }
      return;
    }

    const option = event.target.closest('.sg-payment-option');
    if (!option) return;
    const radio = option.querySelector('input[type="radio"][name="payment_method"]');
    if (radio && !radio.checked) {
      radio.checked = true;
      radio.dispatchEvent(new Event('change', { bubbles: true }));
    }
    syncPaymentCards();
  });

  document.addEventListener('change', (event) => {
    if (!event.target.matches('[name="billing_state"]')) return;
    syncCheckoutShippingCost();
    if (window.jQuery) {
      window.jQuery(document.body).trigger('update_checkout');
    }
  });

  document.body.addEventListener('updated_checkout', () => {
    syncPaymentCards();
    stabilizeCheckoutLayout();
    syncCheckoutShippingCost();
    syncMobileCheckoutSummary();
  });
  window.addEventListener('resize', syncMobileCheckoutSummary);
  syncPaymentCards();
  stabilizeCheckoutLayout();
  syncCheckoutShippingCost();
  syncMobileCheckoutSummary();

  document.querySelectorAll('.quantity').forEach((wrap) => {
    if (wrap.querySelector('[data-sg-qty]')) return;
    const minus = document.createElement('button');
    minus.type = 'button';
    minus.className = 'sg-qty-btn';
    minus.dataset.sgQty = 'minus';
    minus.textContent = '−';
    const plus = document.createElement('button');
    plus.type = 'button';
    plus.className = 'sg-qty-btn';
    plus.dataset.sgQty = 'plus';
    plus.textContent = '+';
    wrap.prepend(minus);
    wrap.append(plus);
  });

  document.addEventListener('click', async (event) => {
    const loadMore = event.target.closest('[data-sg-load-more]');
    if (!loadMore) return;
    event.preventDefault();

    const url = loadMore.getAttribute('href');
    const grid = document.querySelector('ul.products');
    if (!url || !grid || loadMore.classList.contains('is-loading')) return;

    const originalLabel = loadMore.textContent;
    loadMore.classList.add('is-loading');
    loadMore.textContent = loadMore.getAttribute('data-loading-label') || 'Loading...';

    try {
      const response = await fetch(url, { credentials: 'same-origin' });
      const html = await response.text();
      const doc = new DOMParser().parseFromString(html, 'text/html');
      doc.querySelectorAll('ul.products > li.product').forEach((card) => {
        grid.appendChild(document.importNode(card, true));
      });

      const next = doc.querySelector('[data-sg-load-more]');
      if (next) {
        loadMore.setAttribute('href', next.getAttribute('href'));
        loadMore.textContent = originalLabel;
        loadMore.classList.remove('is-loading');
      } else {
        const wrap = loadMore.closest('.sg-load-more-wrap');
        if (wrap) wrap.remove();
      }
    } catch (error) {
      window.location.href = url;
    }
  });

  let cartUpdateTimer = null;
  document.addEventListener('change', (event) => {
    const input = event.target.closest('.woocommerce-cart-form input.qty');
    if (!input) return;
    window.clearTimeout(cartUpdateTimer);
    cartUpdateTimer = window.setTimeout(() => {
      const form = input.closest('.woocommerce-cart-form');
      const update = form && form.querySelector('[name="update_cart"]');
      if (update) {
        update.disabled = false;
        update.click();
      }
    }, 350);
  });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSutigharTheme, { once: true });
  } else {
    initSutigharTheme();
  }
})();
