

const MM = {
  post(url, data) {
    return fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams(data).toString(),
    }).then(r => r.json());
  },

  get(url) {
    return fetch(url).then(r => r.json());
  },

  showMsg(id, msg, ok = true) {
    const el = document.getElementById(id);
    if (!el) return;
    el.textContent = msg;
    el.style.color = ok ? 'green' : 'red';
  },

  addToCart(productId, qty) {
    MM.post('index.php?page=ajax_add_to_cart', { product_id: productId, qty: qty })
      .then(res => {
        MM.showMsg('msg_' + productId, res.msg || '', !!res.ok);
        if (typeof res.cart_count !== 'undefined') {
          const c = document.getElementById('cartCount');
          if (c) c.textContent = res.cart_count;
        }
      })
      .catch(() => MM.showMsg('msg_' + productId, 'Network error', false));
  },

  removeFromCart(cartId) {
    MM.post('index.php?page=ajax_remove_from_cart', { cart_id: cartId })
      .then(res => {
        MM.showMsg('cartMsg', res.msg || '', !!res.ok);
        if (res.ok) location.reload();
      })
      .catch(() => MM.showMsg('cartMsg', 'Network error', false));
  },

  updateCartQty(cartId, qtyVal) {
    let qty = parseInt(qtyVal || '1', 10);
    if (!qty || qty < 1) qty = 1;
    MM.post('index.php?page=ajax_update_cart_qty', { cart_id: cartId, qty: qty })
      .then(res => {
        MM.showMsg('cartMsg', res.msg || '', !!res.ok);
        if (res.ok) location.reload();
      })
      .catch(() => MM.showMsg('cartMsg', 'Network error', false));
  },

  assignStaff(orderId) {
    const sel = document.getElementById('staff_' + orderId);
    if (!sel) return;
    const staff = sel.value;
    MM.post('index.php?page=ajax_assign_order_staff', { order_id: orderId, staff_phone: staff })
      .then(res => {
        MM.showMsg('orderMsg_' + orderId, res.msg || '', !!res.ok);
        if (res.ok) location.reload();
      })
      .catch(() => MM.showMsg('orderMsg_' + orderId, 'Network error', false));
  },

  updateOrderStatus(orderId) {
    const sel = document.getElementById('status_' + orderId);
    if (!sel) return;
    const st = sel.value;
    MM.post('index.php?page=ajax_update_order_status', { order_id: orderId, status: st })
      .then(res => {
        MM.showMsg('orderMsg_' + orderId, res.msg || '', !!res.ok);
        if (res.ok) location.reload();
      })
      .catch(() => MM.showMsg('orderMsg_' + orderId, 'Network error', false));
  },

  validateLogin() {
    const p = document.getElementById('login_phone').value.trim();
    const pass = document.getElementById('login_pass').value;
    if (p === '' || pass === '') {
      alert('Phone and password required');
      return false;
    }
    return true;
  },

  validateRegister() {
    const name = document.getElementById('reg_name').value.trim();
    const phone = document.getElementById('reg_phone').value.trim();
    const pass = document.getElementById('reg_pass').value;
    const cpass = document.getElementById('reg_cpass').value;
    if (name === '' || phone === '' || pass === '') {
      alert('Name, Phone, Password required');
      return false;
    }
    if (pass !== cpass) {
      alert('Password and confirm password not match');
      return false;
    }
    return true;
  },

  validateForgot() {
    const phone = document.getElementById('fp_phone').value.trim();
    const p1 = document.getElementById('fp_pass').value;
    const p2 = document.getElementById('fp_cpass').value;
    if (phone === '' || p1 === '' || p2 === '') {
      alert('All fields required');
      return false;
    }
    if (p1 !== p2) {
      alert('Password not match');
      return false;
    }
    return true;
  },

  validateProfile() {
    const name = document.getElementById('pr_name').value.trim();
    if (name === '') {
      alert('Name required');
      return false;
    }
    return true;
  },

  validateChangePassword() {
    const oldP = document.getElementById('cp_old').value;
    const n1 = document.getElementById('cp_new').value;
    const n2 = document.getElementById('cp_cnew').value;
    if (oldP === '' || n1 === '' || n2 === '') {
      alert('All fields required');
      return false;
    }
    if (n1 !== n2) {
      alert('New passwords not match');
      return false;
    }
    return true;
  },

  validateCheckout() {
    return true;
  },

  validateProductForm() {
    const model = document.getElementById('p_model').value.trim();
    const brand = document.getElementById('p_brand').value.trim();
    const price = document.getElementById('p_price').value.trim();
    if (model === '' || brand === '' || price === '') {
      alert('Model, Brand, Price required');
      return false;
    }
    return true;
  },

  validateAddStaff() {
    const phone = document.getElementById('st_phone').value.trim();
    const pass = document.getElementById('st_pass').value;
    const cpass = document.getElementById('st_cpass').value;
    if (phone === '' || pass === '') {
      alert('Phone and Password required');
      return false;
    }
    if (pass !== cpass) {
      alert('Password not match');
      return false;
    }
    return true;
  },

  validateSalary() {
    const amt = document.getElementById('sal_amount').value.trim();
    const month = document.getElementById('sal_month').value.trim();
    if (amt === '' || month === '') {
      alert('Amount and Month required');
      return false;
    }
    return true;
  }
};

window.MM = MM;

// Live grid update (NO dropdown, NO reload)
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('mmSearchForm');
  const q = document.getElementById('mmSearchQ');
  const brand = document.getElementById('mmBrand');
  const sort = document.getElementById('mmSort');

  const gridWrap = document.getElementById('productGridWrap');
  const gridMsg = document.getElementById('productGridMsg');

  if (!form || !q || !gridWrap) return;

  let t = null;

  const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({
    '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
  }[c]));

  const render = (products) => {
    if (!products || products.length === 0) {
      gridWrap.innerHTML = '<p>No products found.</p>';
      if (gridMsg) gridMsg.innerHTML = '';
      return;
    }

    const html = products.map(p => `
      <div class="card">
        <a class="imgwrap" href="index.php?page=product&id=${parseInt(p.id,10)}">
          <img src="view/images/${esc(p.image)}" alt="phone">
        </a>
        <div class="card-body">
          <div class="title">${esc(p.model)}</div>
          <div class="meta">Brand: ${esc(p.brand)}</div>
          <div class="meta">RAM: ${esc(p.ram)} | ROM: ${esc(p.rom)}</div>
          <div class="meta">Stock: ${parseInt(p.qty,10) || 0}</div>
          <div class="price">
            ৳${esc(p.price)}
            ${(parseInt(p.offer_percent,10) > 0) ? `<span class="badge">${parseInt(p.offer_percent,10)}% OFF</span>` : ''}
          </div>
          <div class="actions">
            <button class="btn" onclick="MM.addToCart(${parseInt(p.id,10)}, 1)">Add to Cart</button>
            <a class="btn ghost" href="index.php?page=product&id=${parseInt(p.id,10)}">Details</a>
          </div>
          <div class="small" id="msg_${parseInt(p.id,10)}"></div>
        </div>
      </div>
    `).join('');

    gridWrap.innerHTML = `<div class="grid">${html}</div>`;
    if (gridMsg) gridMsg.innerHTML = '';
  };

  const fetchProducts = () => {
    const params = new URLSearchParams();
    params.set('page', 'ajax_search_products');
    params.set('q', q.value || '');
    if (brand) params.set('brand', brand.value || '');
    if (sort) params.set('sort', sort.value || '');

    fetch(`index.php?${params.toString()}`)
      .then(r => r.json())
      .then(res => {
        if (!res || !res.ok) {
          if (gridMsg) gridMsg.innerHTML = '<div class="alert error">Search error</div>';
          return;
        }
        render(res.products || []);
      })
      .catch(() => {
        if (gridMsg) gridMsg.innerHTML = '<div class="alert error">Network error</div>';
      });
  };

  q.addEventListener('input', () => {
    clearTimeout(t);
    t = setTimeout(fetchProducts, 350);
  });

  if (brand) brand.addEventListener('change', fetchProducts);
  if (sort) sort.addEventListener('change', fetchProducts);

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    fetchProducts();
  });
});
