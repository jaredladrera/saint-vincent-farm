<?php
require_once __DIR__ . '/auth.php';
$current_page = $_GET['page'] ?? 'home';
$user = currentUser();
?>
<nav class="main-nav">
    <a class="nav-logo" href="index.php?page=home">
        <div class="logo-mark">SV</div>
        <span>St. Vincent Farm</span>
    </a>

    <div class="nav-links" id="desktopNav">
        <a href="index.php?page=home"
           class="<?= $current_page == 'home'     ? 'active-nav' : '' ?>">Home</a>
        <a href="index.php?page=shop"
           class="<?= $current_page == 'shop'     ? 'active-nav' : '' ?>">Products</a>
        <!-- <a href="index.php?page=features"
           class="<?= $current_page == 'features' ? 'active-nav' : '' ?>">System Features</a> -->
        <a href="index.php?page=about"
           class="<?= $current_page == 'about'    ? 'active-nav' : '' ?>">About</a>
        <a href="index.php?page=contact"
           class="<?= $current_page == 'contact'     ? 'active-nav' : '' ?>">Contact Us</a>

        <?php if ($user): ?>
            <!-- Logged-in: show Order Now + user dropdown -->
            <a href="index.php?page=contact"
               class="nav-cta <?= $current_page == 'contact' ? 'active-nav' : '' ?>">
                Order Now
            </a>
            <div class="user-menu-wrap">
                <button class="user-avatar-btn" onclick="toggleUserMenu(event)">
                    <span class="user-avatar"><?= htmlspecialchars($user['avatar']) ?></span>
                    <span class="user-name"><?= htmlspecialchars(explode(' ', $user['name'])[0]) ?></span>
                    <i class="bi bi-chevron-down user-caret"></i>
                </button>
                <div class="user-dropdown" id="userDropdown">
                    <div class="user-dropdown-header">
                        <div class="user-avatar-lg"><?= htmlspecialchars($user['avatar']) ?></div>
                        <div>
                            <strong><?= htmlspecialchars($user['name']) ?></strong>
                            <span><?= htmlspecialchars($user['email']) ?></span>
                        </div>
                    </div>
                    <a href="index.php?page=contact" class="user-dropdown-item">
                        <i class="bi bi-bag"></i> My Orders
                    </a>
                <a href="#"
                    class="user-dropdown-item logout-item"
                    onclick="event.stopPropagation(); window.location.href='index.php?page=logout'; return false;">
                        <i class="bi bi-box-arrow-right"></i> Sign Out
                </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Guest: show Sign In -->
            <a href="index.php?page=login" class="nav-signin">
                <i class="bi bi-person"></i> Sign In
            </a>
        <?php endif; ?>
    </div>

    <!-- Cart Icon -->
    <button class="cart-btn" onclick="toggleCartDrawer()" title="View Cart">
        <i class="bi bi-cart3"></i>
        <span class="cart-count" id="cartCount">0</span>
    </button>

    <button class="hamburger" onclick="toggleMobile()" id="hamburger">
        <i class="bi bi-list"></i>
    </button>
</nav>

<!-- Cart Drawer -->
<div class="cart-backdrop" id="cartBackdrop" onclick="toggleCartDrawer()"></div>
<div class="cart-drawer" id="cartDrawer">
    <div class="cart-drawer-header">
        <h5><i class="bi bi-cart3"></i> Your Cart</h5>
        <button onclick="toggleCartDrawer()"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="cart-drawer-body" id="cartItems">
        <div class="cart-empty" id="cartEmpty">
            <i class="bi bi-cart-x"></i>
            <p>Your cart is empty</p>
        </div>
    </div>
    <div class="cart-drawer-footer" id="cartFooter" style="display:none">
        <div class="cart-total">
            <span>Total Items</span>
            <strong id="cartTotalCount">0</strong>
        </div>
        <?php if ($user): ?>
            <a href="index.php?page=contact" class="cart-checkout-btn" onclick="toggleCartDrawer()">
                <i class="bi bi-bag-check"></i> Proceed to Order
            </a>
        <?php else: ?>
            <a href="index.php?page=login&redirect=contact" class="cart-checkout-btn" onclick="toggleCartDrawer()">
                <i class="bi bi-box-arrow-in-right"></i> Sign In to Order
            </a>
            <p class="cart-login-hint">You need to sign in to place an order.</p>
        <?php endif; ?>
    </div>
</div>

<!-- User dropdown backdrop -->
<div class="user-backdrop" id="userBackdrop" onclick="toggleUserMenu()"></div>
