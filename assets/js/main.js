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


function stepQty(btn, delta, maxStock) {
    const input = btn.closest('.qty-stepper').querySelector('.qty-input');
    let val = parseInt(input.value) + delta;
    if (val < 1)        val = 1;
    if (val > maxStock) val = maxStock;
    input.value = val;
}

// ── ADD TO CART (from product card) ──
function addToCart(id, user_id, price, quantity, btnEl) {
    if (!window.isLoggedIn) {
        window.location.href = 'index.php?page=login&redirect=shop';
        return;
    }

    $.ajax({
        url: './shared/api.php',
        method: 'POST',
        dataType: 'text',
        data: {
            key: 'addToCart',
            product_id: id,
            user_id: user_id,
            quantity: quantity,
            amount: parseInt(price) * parseFloat(quantity)
        },
        success: function(response) {
            updateCartUI();      // ← inside success, runs AFTER insert is done
            animateCartBtn();
            btnFeedback(btnEl);
            showToast('Item added to cart!');
        },
        error: function(xhr) {
            console.error('Add to cart failed:', xhr.responseText);
            alert('Failed to add item to cart.');
        }
    });
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
    showToast( name + ' added to cart!');
}

// ── CHANGE ITEM QUANTITY ──
function changeQty(cartId, currentQty, delta, maxStock) {
    let newQty = parseInt(currentQty) + delta;
    if (newQty < 1)        newQty = 1;
    if (newQty > maxStock) newQty = maxStock;

    $.ajax({
        url: './shared/api.php',
        method: 'POST',
        data: { key: 'updateCartQty', cart_id: cartId, quantity: newQty },
        success: function() {
            updateCartUI(); // re-fetch from DB
        }
    });
}
// ── REMOVE ITEM ──
function removeFromCart(cartId) {
    $.ajax({
        url: './shared/api.php',
        method: 'POST',
        data: { key: 'removeFromCart', cart_id: cartId },
        success: function() {
            updateCartUI(); // re-fetch from DB
        }
    });
}


// ── FETCH CART FROM DB AND REFRESH UI ──
function updateCartUI() {
    if (!window.isLoggedIn || !window.currentUserId) {
        renderCartUI([]);
        return;
    }

    $.ajax({
        url: './shared/api.php',
        method: 'POST',
        dataType: 'json',
        data: { key: 'getCart', user_id: window.currentUserId },
        success: function(items) {

            console.log("items fetch", items);
            renderCartUI(items);
        },
        error: function(xhr, status, err) {
            console.error('Status:', status);
            console.error('Error:', err);
            console.error('Raw response:', xhr.responseText); // ← this tells you exactly what came back
        }
    });
}

// ── RENDER CART ITEMS INTO DRAWER ──
function renderCartUI(items) {
    const total     = items.reduce((sum, i) => sum + parseInt(i.cart_quantity), 0);
    const badge     = document.getElementById('cartCount');
    const container = document.getElementById('cartItems');
    const empty     = document.getElementById('cartEmpty');
    const footer    = document.getElementById('cartFooter');

    badge.textContent = total;
    badge.classList.toggle('visible', total > 0);

    if (items.length === 0) {
        empty.style.display  = 'flex';
        footer.style.display = 'none';
        container.innerHTML  = '';
        container.appendChild(empty);
        return;
    }

    empty.style.display  = 'none';
    footer.style.display = 'block';

    container.innerHTML = items.map(function(item) {
        const subtotal = (parseFloat(item.price) * parseInt(item.cart_quantity)).toFixed(2);
        return '<div class="cart-item" id="cart-item-' + item.id + '">'
            + '<div class="cart-item-icon">' + (item.icon || '🛒') + '</div>'
            + '<div class="cart-item-info">'
            +   '<h6>' + item.name + '</h6>'
            +   '<span>₱' + item.price +'</span> </br>  '
            +   '<span>Quantity : ' + item.cart_quantity +'</span> </br>  '
            +   '<span class="cart-item-subtotal">Subtotal: ₱' + subtotal + '</span>'
            + '</div>'
            + '<div class="cart-item-qty">'
            // +   '<button class="qty-btn" onclick="changeQty(' + item.id + ', ' + item.quantity + ', -1, ' + item.stock + ')">&#8722;</button>'
            // +   '<span class="qty-num">' + item.quantity + '</span>'
            // +   '<button class="qty-btn" onclick="changeQty(' + item.id + ', ' + item.quantity + ', +1, ' + item.stock + ')">+</button>'
            + '</div>'
            + '<button class="cart-item-remove" onclick="removeFromCart(' + item.id + ')" title="Remove">'
            +   '<i class="bi bi-trash3"></i>'
            + '</button>'
            + '</div>';
    }).join('');

    const grandTotal = items.reduce((sum, i) => sum + parseFloat(i.price) * parseInt(i.cart_quantity), 0);
    document.getElementById('cartTotalCount').textContent =
        total + (total === 1 ? ' item' : ' items') + ' — ₱' + grandTotal.toFixed(2);
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

    if (!window.isLoggedIn) {
        window.location.href = 'index.php?page=login&redirect=shop';
        return;
    }

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
    // backdrop.classList.toggle('open', !isOpen);
    // if (btn) btn.classList.toggle('open', !isOpen);
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
