<?php
ob_start(); // ← ADD THIS AS THE VERY FIRST LINE
// ══════════════════════════════════
// MAIN ROUTER — index.php
// ══════════════════════════════════
define('BASE_DIR', __DIR__);
define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));

require_once BASE_DIR . '/includes/auth.php';

$page = $_GET['page'] ?? 'home';

// ── Logout must be FIRST before any output ──
if ($page === 'logout') {
    session_start();
    session_unset();
    session_destroy();
    header('Location: ' . BASE_URL . '/index.php?page=home');
    exit;
}

$allowed_pages = [
    'home', 'shop', 'features', 'about', 'contact', 'profile', 'my_order', 'forgot_password',
    'login', 'register', 'order_request_form', 'upload_proof', 'order_success',
    'account/admin', 'account/staff',
];
if (!in_array($page, $allowed_pages)) $page = 'home';

// ── Public pages (no login required) ──
$public_pages = ['home', 'shop', 'features', 'about', 'contact', 'login', 'register', 'forgot_password'];

// ── Redirect logged-in users away from login/register ──
if (in_array($page, ['login', 'register']) && isLoggedIn()) {
    $redirect = $_GET['redirect'] ?? 'home';
    header('Location: ' . BASE_URL . '/index.php?page=' . urlencode($redirect));
    exit;
}

// ── Protect all non-public pages — BEFORE any HTML output ──
if (!in_array($page, $public_pages) && !isLoggedIn()) {
    header('Location: ' . BASE_URL . '/index.php?page=login&redirect=' . urlencode($page));
    exit;
}


$titles = [
    'home'     => 'Home',
    'shop'     => 'Products',
    'features' => 'System Features',
    'about'    => 'About Us',
    'contact'  => 'Place Order',
    'login'    => 'Sign In',
    'register' => 'Create Account',
];
$page_title = $titles[$page] ?? 'Page';
$auth_pages = ['login', 'register'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?= $page_title ?> — St. Vincent Farm</title>
<?php require BASE_DIR . '/includes/head_assets.php'; ?>
</head>
<body>

<?php if (!in_array($page, $auth_pages)): ?>
    <?php require BASE_DIR . '/includes/navbar.php'; ?>
    <?php require BASE_DIR . '/includes/mobile_menu.php'; ?>
<?php endif; ?>

<main>
<?php
    if      ($page == 'home')     { require BASE_DIR . '/pages/home.php'; }
    elseif  ($page == 'shop')     { require BASE_DIR . '/pages/shop.php'; }
    elseif  ($page == 'features') { require BASE_DIR . '/pages/features.php'; }
    elseif  ($page == 'about')    { require BASE_DIR . '/pages/about.php'; }
    elseif  ($page == 'contact')  { require BASE_DIR . '/pages/contact.php'; }
    elseif  ($page == 'login')    { require BASE_DIR . '/pages/login.php'; }
    elseif  ($page == 'register') { require BASE_DIR . '/pages/register.php'; }
    elseif  ($page == 'forgot_password') { require BASE_DIR . '/pages/forgot_password.php'; }
    elseif  ($page == 'upload_proof') { require BASE_DIR . '/pages/upload_proof.php'; }
    elseif  ($page == 'order_success') { require BASE_DIR . '/pages/order_success.php'; }
    elseif  ($page == 'my_order') { require BASE_DIR . '/pages/my_order.php'; }
    elseif  ($page == 'order_request_form') { require BASE_DIR . '/pages/order_request_form.php'; }
    elseif  ($page == 'profile') { require BASE_DIR . '/pages/profile.php'; }
    else                          { require BASE_DIR . '/pages/404.php'; }
?>
</main>

<?php if (!in_array($page, $auth_pages)): ?>
    <?php require BASE_DIR . '/includes/footer.php'; ?>
    <?php require BASE_DIR . '/includes/modal.php'; ?>
<?php endif; ?>

<?php require BASE_DIR . '/includes/scripts.php'; ?>

<?php ob_end_flush(); ?> 
</body>
</html>
