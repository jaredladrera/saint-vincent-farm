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
    // alert("hey")
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

    // Check if product already exists in the cart
    $.ajax({
        url: './shared/api.php',
        method: 'POST',
        dataType: 'json',
        data: { key: 'getCart', user_id: user_id },
        success: function(items) {
            const existing = items.find(function(i) { return parseInt(i.product_id) === parseInt(id); });

            if (existing) {
                // Product already in cart — update quantity instead
                const cid      = existing.cart_id || existing.id;
                const newQty   = parseInt(existing.cart_quantity) + parseInt(quantity);
                const newAmount = (newQty * parseFloat(price)).toFixed(2);
                $.ajax({
                    url: './shared/api.php',
                    method: 'POST',
                    data: { key: 'updateCartQty', cart_id: cid, quantity: newQty, amount: newAmount },
                    success: function() {
                        updateCartUI();
                        animateCartBtn();
                        btnFeedback(btnEl);
                        showToast('Cart updated!');
                    }
                });
            } else {
                // New product — insert into cart
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
                    success: function() {
                        updateCartUI();
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
        },
        error: function() {
            alert('Failed to check cart. Please try again.');
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
function changeQty(cartId, currentQty, delta, maxStock, price) {
    let newQty = parseInt(currentQty) + delta;
    if (newQty < 1)        newQty = 1;
    if (newQty > maxStock) newQty = maxStock;

    const newAmount = (newQty * parseFloat(price)).toFixed(2);

    $.ajax({
        url: './shared/api.php',
        method: 'POST',
        data: { key: 'updateCartQty', cart_id: cartId, quantity: newQty, amount: newAmount },
        success: function() {
            updateCartUI(); // re-fetch from DB — badge, subtotals & grand total all update
        }
    });
}
// ── REMOVE ITEM ──
function removeFromCart(cartId) {
    // Optimistically hide the item immediately so UI feels instant
    const el = document.getElementById('cart-item-' + cartId);
    if (el) el.remove();

    $.ajax({
        url: './shared/api.php',
        method: 'POST',
        data: { key: 'removeFromCart', cart_id: cartId },
        success: function() {
            updateCartUI(); // re-fetch from DB to sync badge & total
        },
        error: function() {
            updateCartUI(); // restore correct state on failure
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
            // console.log("items fetch", items);
            renderCartUI(items);
        },
        error: function(xhr, status, err) {
            console.error("Status:", status);
            console.error("error:", err);
            console.error("Raw response:", xhr.responseText); // ← this tells you exactly what came back
        }
    });
}

// ── RENDER CART ITEMS INTO DRAWER ──
function renderCartUI(items) {
    const total     = items.reduce((sum, i) => sum + parseInt(i.cart_quantity), 0);
    const grandTotal = items.reduce((sum, i) => sum + parseFloat(i.price) * parseInt(i.cart_quantity), 0);

    // ── Badge ──
    const badge = document.getElementById('cartCount');
    if (badge) {
        badge.textContent = total;
        badge.classList.toggle('visible', total > 0);
    }

    // ── Total count text + hidden input ──
    const totalCountEl = document.getElementById('cartTotalCount');
    if (totalCountEl) {
        totalCountEl.textContent = total + (total === 1 ? ' item' : ' items') + ' — ₱' + grandTotal.toFixed(2);
    }
    $('#hidden-total').val(grandTotal.toFixed(2));

    // ── Footer ──
    const footer = document.getElementById('cartFooter');
    if (footer) footer.style.display = items.length === 0 ? 'none' : 'block';

    // ── Empty state & items ──
    const empty     = document.getElementById('cartEmpty');
    const container = document.getElementById('cartItems');
    if (!container) return;

    if (items.length === 0) {
        if (empty) empty.style.display = 'flex';
        // Clear only cart item divs, leave #cartEmpty intact
        Array.from(container.querySelectorAll('.cart-item')).forEach(function(el) { el.remove(); });
        return;
    }

    if (empty) empty.style.display = 'none';

    // Build new HTML and replace only the cart-item divs
    const html = items.map(function(item) {
        const cid      = item.cart_id || item.id;
        const qty      = parseInt(item.cart_quantity);
        const price    = parseFloat(item.price);
        const stock    = parseInt(item.stock) || 999;
        const subtotal = (price * qty).toFixed(2);
        return '<div class="cart-item" id="cart-item-' + cid + '">'
            + '<div class="cart-item-icon">' + (item.icon || '🛒') + '</div>'
            + '<div class="cart-item-info">'
            +   '<h6>' + item.name + '</h6>'
            +   '<span class="cart-item-price">₱' + price.toFixed(2) + ' / ' + (item.unit || 'pc') + '</span>'
            +   '<div class="cart-item-stepper">'
            +     '<button class="qty-btn" onclick="changeQty(' + cid + ',' + qty + ',-1,' + stock + ',' + price + ')">&#8722;</button>'
            +     '<span class="qty-num">' + qty + '</span>'
            +     '<button class="qty-btn" onclick="changeQty(' + cid + ',' + qty + ',1,' + stock + ',' + price + ')">+</button>'
            +   '</div>'
            +   '<span class="cart-item-subtotal">Subtotal: ₱' + subtotal + '</span>'
            + '</div>'
            + '<button class="cart-item-remove" onclick="removeFromCart(' + cid + ')" title="Remove">'
            +   '<i class="bi bi-trash3"></i>'
            + '</button>'
            + '</div>';
    }).join('');

    // Remove old cart-item divs first, then insert fresh ones before #cartEmpty
    Array.from(container.querySelectorAll('.cart-item')).forEach(function(el) { el.remove(); });
    container.insertAdjacentHTML('afterbegin', html);
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