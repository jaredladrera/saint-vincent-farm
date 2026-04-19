/* ══════════════════════════════════
   ST. VINCENT FARM — MAIN JS
   ══════════════════════════════════ */

// ── CART STATE (in-memory) ──
let cart = []; // [{ id, name, icon, price, unit, qty }]

// ── MOBILE MENU TOGGLE ──
function toggleMobile() {
    const menu = document.getElementById('mobileMenu');
    const icon = document.getElementById('hamburger').querySelector('i');
    menu.classList.toggle('open');
    icon.className = menu.classList.contains('open') ? 'bi bi-x' : 'bi bi-list';
}

// ── CART DRAWER TOGGLE ──
function toggleCartDrawer() {
    const drawer   = document.getElementById('cartDrawer');
    const backdrop = document.getElementById('cartBackdrop');
    const isOpen   = drawer.classList.contains('open');
    drawer.classList.toggle('open', !isOpen);
    backdrop.classList.toggle('open', !isOpen);
}

// ── ADD TO CART (from product card) ──
function addToCart(id, name, icon, price, unit, btnEl) {
    const existing = cart.find(i => i.id === id);
    if (existing) {
        existing.qty++;
    } else {
        cart.push({ id, name, icon, price, unit, qty: 1 });
    }
    updateCartUI();
    animateCartBtn();
    btnFeedback(btnEl);
    showToast(icon + ' ' + name + ' added to cart!');
}

// ── ADD TO CART (from modal) ──
function addToCartFromModal() {
    const id    = window._modalProductId;
    const name  = document.getElementById('modalName').textContent;
    const icon  = document.getElementById('modalIcon').textContent;
    const price = window._modalPrice;
    const unit  = window._modalUnit;
    if (!id) return;

    const existing = cart.find(i => i.id === id);
    if (existing) {
        existing.qty++;
    } else {
        cart.push({ id, name, icon, price, unit, qty: 1 });
    }
    updateCartUI();
    animateCartBtn();
    showToast(icon + ' ' + name + ' added to cart!');
}

// ── CHANGE ITEM QUANTITY ──
function changeQty(id, delta) {
    const item = cart.find(i => i.id === id);
    if (!item) return;
    item.qty += delta;
    if (item.qty <= 0) cart = cart.filter(i => i.id !== id);
    updateCartUI();
}

// ── REMOVE ITEM ──
function removeFromCart(id) {
    cart = cart.filter(i => i.id !== id);
    updateCartUI();
}

// ── UPDATE ALL CART UI ──
function updateCartUI() {
    const total  = cart.reduce((sum, i) => sum + i.qty, 0);
    const badge  = document.getElementById('cartCount');
    const container = document.getElementById('cartItems');
    const empty  = document.getElementById('cartEmpty');
    const footer = document.getElementById('cartFooter');

    // Badge
    badge.textContent = total;
    badge.classList.toggle('visible', total > 0);

    if (cart.length === 0) {
        empty.style.display  = 'flex';
        footer.style.display = 'none';
        container.innerHTML  = '';
        container.appendChild(empty);
        return;
    }

    empty.style.display  = 'none';
    footer.style.display = 'block';

    container.innerHTML = cart.map(function(item) {
        return '<div class="cart-item" id="cart-item-' + item.id + '">'
            + '<div class="cart-item-icon">' + item.icon + '</div>'
            + '<div class="cart-item-info">'
            +   '<h6>' + item.name + '</h6>'
            +   '<span>' + item.price + ' / ' + item.unit + '</span>'
            + '</div>'
            + '<div class="cart-item-qty">'
            +   '<button class="qty-btn" onclick="changeQty(' + item.id + ', -1)">&#8722;</button>'
            +   '<span class="qty-num">' + item.qty + '</span>'
            +   '<button class="qty-btn" onclick="changeQty(' + item.id + ', +1)">+</button>'
            + '</div>'
            + '<button class="cart-item-remove" onclick="removeFromCart(' + item.id + ')" title="Remove">'
            +   '<i class="bi bi-trash3"></i>'
            + '</button>'
            + '</div>';
    }).join('');

    document.getElementById('cartTotalCount').textContent = total + (total === 1 ? ' item' : ' items');
}

// ── ANIMATE BADGE BUMP ──
function animateCartBtn() {
    const badge = document.getElementById('cartCount');
    badge.classList.remove('bump');
    void badge.offsetWidth;
    badge.classList.add('bump');
}

// ── BUTTON FEEDBACK ──
function btnFeedback(btn) {
    if (!btn) return;
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-check-lg"></i> Added!';
    btn.style.background = 'var(--green-mid)';
    setTimeout(function() {
        btn.innerHTML = orig;
        btn.style.background = '';
    }, 1000);
}

// ── PRODUCT MODAL ──
window._modalProductId = null;
window._modalPrice     = '';
window._modalUnit      = '';

function openModal(name, desc, icon, price, unit, tagsStr, productId) {
    window._modalProductId = productId !== undefined ? productId : null;
    window._modalPrice     = price;
    window._modalUnit      = unit;

    document.getElementById('modalIcon').textContent  = icon;
    document.getElementById('modalName').textContent  = name;
    document.getElementById('modalPrice').textContent = price + ' / ' + unit;
    document.getElementById('modalDesc').textContent  = desc;

    var tags = tagsStr.split(',');
    document.getElementById('modalTags').innerHTML =
        tags.map(function(t) { return '<span class="meta-chip">' + t.trim() + '</span>'; }).join('');

    document.getElementById('productModal').classList.add('show');
}

function closeModal() {
    document.getElementById('productModal').classList.remove('show');
}
function closeModalOutside(e) {
    if (e.target.id === 'productModal') closeModal();
}

// ── PRODUCT FILTER ──
function filterProducts(cat, btn) {
    document.querySelectorAll('.product-item').forEach(function(el) {
        el.style.display = (cat === 'all' || el.dataset.cat === cat) ? '' : 'none';
    });
    document.querySelectorAll('.filter-btn').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');
}

// ── TOAST ──
function showToast(msg) {
    var toast = document.getElementById('toast');
    document.getElementById('toastMsg').textContent = msg;
    toast.style.display = 'flex';
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(function() { toast.style.display = 'none'; }, 2800);
}

// ── ORDER FORM ──
function submitOrder(e) {
    e.preventDefault();
    showToast('Order request submitted! We\'ll contact you shortly.');
    e.target.reset();
}

// ── INIT ──
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('orderForm');
    if (form) form.addEventListener('submit', submitOrder);
    updateCartUI();
});

// ── USER DROPDOWN MENU ──
function toggleUserMenu() {
    const btn      = document.querySelector('.user-avatar-btn');
    const dropdown = document.getElementById('userDropdown');
    const backdrop = document.getElementById('userBackdrop');
    if (!dropdown) return;
    const isOpen = dropdown.classList.contains('open');
    dropdown.classList.toggle('open', !isOpen);
    backdrop.classList.toggle('open', !isOpen);
    if (btn) btn.classList.toggle('open', !isOpen);
}

// Close user menu on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const dd = document.getElementById('userDropdown');
        const bd = document.getElementById('userBackdrop');
        const btn = document.querySelector('.user-avatar-btn');
        if (dd) dd.classList.remove('open');
        if (bd) bd.classList.remove('open');
        if (btn) btn.classList.remove('open');
    }
});

// ── TOGGLE PASSWORD VISIBILITY ──
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type  = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type  = 'password';
        icon.className = 'bi bi-eye';
    }
}
