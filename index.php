<?php
// ══════════════════════════════════
// MAIN ROUTER — index.php
// ══════════════════════════════════
define('BASE_DIR', __DIR__);
define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));

$page = $_GET['page'] ?? 'home';


// ── Logout must be FIRST before any output ──
if (($_GET['page'] ?? '') === 'logout') {
    session_start();
    session_unset();
    session_destroy();
    header('Location: ' . BASE_URL . '/index.php?page=home');
    exit;
}

require_once BASE_DIR . '/includes/auth.php';

$allowed_pages = ['home', 'shop', 'features', 'about', 'contact', 'login', 'register', 'logout  '];
if (!in_array($page, $allowed_pages)) $page = 'home';

// if ($page === 'contact' && !isLoggedIn()) {
//     header('Location: ' . BASE_URL . '/index.php?page=login&redirect=contact');
//     exit;
// }
if (in_array($page, ['login', 'register']) && isLoggedIn()) {
    $redirect = $_GET['redirect'] ?? 'home';
    header('Location: ' . BASE_URL . '/index.php?page=' . urlencode($redirect));
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
    elseif  ($page == 'features') { 
        echo "feature";    
    require BASE_DIR . '/pages/features.php'; }
    elseif  ($page == 'about')    { require BASE_DIR . '/pages/about.php'; }
    elseif  ($page == 'contact')  { require BASE_DIR . '/pages/contact.php'; }
    elseif  ($page == 'login')    { require BASE_DIR . '/pages/login.php'; }
    elseif  ($page == 'register') { require BASE_DIR . '/pages/register.php'; }
    else                          { require BASE_DIR . '/pages/404.php'; }
?>
</main>

<?php if (!in_array($page, $auth_pages)): ?>
    <?php require BASE_DIR . '/includes/footer.php'; ?>
    <?php require BASE_DIR . '/includes/modal.php'; ?>
<?php endif; ?>

<?php require BASE_DIR . '/includes/scripts.php'; ?>
</body>
</html>
