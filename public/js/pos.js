'use strict';
(() => {
  const menu = JSON.parse(document.querySelector('#menu-data').textContent);
  const money = cents => new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(cents / 100);
  const cart = new Map();
  let category = 'All items', service = 'Dine in', busy = false, checkoutToken = crypto.randomUUID();
  const cash = document.querySelector('#cash'), checkout = document.querySelector('#checkout'), error = document.querySelector('#checkout-error');
  const total = () => [...cart].reduce((sum, [id, quantity]) => sum + menu.find(p => p.id === id).price * quantity, 0);
  const cashCents = () => Math.round(Number(cash.value || 0) * 100);
  function updatePayment() {
    const value = total(), paid = cashCents();
    document.querySelector('#subtotal').textContent = money(value);
    document.querySelector('#total').textContent = money(value);
    document.querySelector('#change').textContent = money(Math.max(0, paid - value));
    checkout.disabled = busy || !cart.size || !Number.isFinite(paid) || paid < value || !cash.validity.valid;
  }
  function renderCart() {
    const container = document.querySelector('#cart-lines');
    container.replaceChildren();
    for (const [id, quantity] of cart) {
      const product = menu.find(p => p.id === id);
      const row = document.createElement('div'); row.className = 'cart-line';
      const info = document.createElement('div');
      const title = document.createElement('strong'); title.textContent = product.name;
      const price = document.createElement('span'); price.className = 'line-price'; price.textContent = money(product.price) + ' each';
      const controls = document.createElement('div'); controls.className = 'quantity';
      for (const delta of [-1, 1]) {
        const button = document.createElement('button'); button.type = 'button'; button.textContent = delta === -1 ? '−' : '+';
        button.setAttribute('aria-label', `${delta === -1 ? 'Decrease' : 'Increase'} ${product.name} quantity`);
        button.disabled = busy || (delta === 1 && quantity >= 99);
        button.addEventListener('click', () => change(id, delta));
        if (delta === 1) { const count = document.createElement('span'); count.textContent = quantity; controls.append(count); }
        controls.append(button);
      }
      info.append(title, price, controls);
      const right = document.createElement('div'); right.className = 'cart-line-right';
      const sum = document.createElement('b'); sum.textContent = money(product.price * quantity);
      const remove = document.createElement('button'); remove.type = 'button'; remove.className = 'remove-line'; remove.textContent = 'Remove'; remove.disabled = busy;
      remove.setAttribute('aria-label', `Remove ${product.name}`);
      remove.addEventListener('click', () => { cart.delete(id); changed(); });
      right.append(sum, remove); row.append(info, right); container.append(row);
    }
    document.querySelector('#cart-empty').hidden = cart.size > 0;
    document.querySelector('#clear-order').disabled = busy || !cart.size;
    updatePayment();
  }
  function changed() { checkoutToken = crypto.randomUUID(); error.hidden = true; renderCart(); }
  function change(id, delta) {
    if (busy) return;
    const quantity = (cart.get(id) || 0) + delta;
    if (quantity <= 0) cart.delete(id); else cart.set(id, Math.min(99, quantity));
    changed();
  }
  document.querySelectorAll('[data-product]').forEach(button => button.addEventListener('click', () => change(Number(button.dataset.product), 1)));
  document.querySelector('#clear-order').addEventListener('click', () => { if (busy) return; cart.clear(); cash.value = ''; changed(); });
  cash.addEventListener('input', () => { checkoutToken = crypto.randomUUID(); error.hidden = true; updatePayment(); });
  document.querySelectorAll('[data-service]').forEach(button => button.addEventListener('click', () => {
    if (busy) return;
    service = button.dataset.service; checkoutToken = crypto.randomUUID();
    document.querySelectorAll('[data-service]').forEach(b => { b.classList.toggle('selected', b === button); b.setAttribute('aria-pressed', b === button ? 'true' : 'false'); });
  }));
  function filterMenu() {
    const query = document.querySelector('#menu-search').value.trim().toLocaleLowerCase();
    let count = 0;
    document.querySelectorAll('[data-product]').forEach(button => {
      button.hidden = (category !== 'All items' && button.dataset.category !== category) || !button.dataset.name.toLocaleLowerCase().includes(query);
      if (!button.hidden) count++;
    });
    document.querySelector('#menu-empty').hidden = count > 0;
  }
  document.querySelectorAll('[data-category].tab').forEach(button => button.addEventListener('click', () => {
    category = button.dataset.category;
    document.querySelectorAll('.tab').forEach(b => { b.classList.toggle('selected', b === button); b.setAttribute('aria-pressed', b === button ? 'true' : 'false'); });
    filterMenu();
  }));
  document.querySelector('#menu-search').addEventListener('input', filterMenu);
  checkout.addEventListener('click', async () => {
    if (checkout.disabled) return;
    busy = true; error.hidden = true; cash.disabled = true;
    document.querySelectorAll('[data-product], [data-service]').forEach(b => b.disabled = true);
    renderCart(); checkout.textContent = 'Saving order…';
    try {
      const response = await fetch('/orders', {
        method: 'POST', headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ checkout_token: checkoutToken, service, cash: cashCents(), items: [...cart].map(([id, quantity]) => ({ id, quantity })) })
      });
      const data = await response.json();
      if (!response.ok) throw new Error(Object.values(data.errors || {}).flat().join(' ') || data.message || 'Could not save the order. Please try again.');
      window.location.assign(data.receipt);
    } catch (e) {
      error.textContent = e.message || 'Connection lost. Please try again.'; error.hidden = false;
      busy = false; cash.disabled = false;
      document.querySelectorAll('[data-product], [data-service]').forEach(b => b.disabled = false);
      checkout.textContent = 'Complete order →'; renderCart();
    }
  });
  renderCart();
})();
