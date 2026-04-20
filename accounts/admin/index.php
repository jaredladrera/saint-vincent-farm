<?php
// ══════════════════════════════════
// MAIN ROUTER — index.php
// ══════════════════════════════════
define('BASE_DIR', __DIR__);
define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));

$page = $_GET['page'] ?? 'dashboard';

$allowed_pages = ['dashboard', 'products', 'profile'];
if (!in_array($page, $allowed_pages)) $page = 'dashboard';

$titles = [
    'dashboard' => 'Dashboard',
    'products'  => 'Products',
    'profile'   => 'Profile',
];
$page_title = $titles[$page] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($page_title) ?> — Admin Panel</title>
    <?php require BASE_DIR . '/includes/head_assets.php'; ?>
</head>
<body>

    <?php require BASE_DIR . '/includes/sidebar.php'; ?>

    <div class="main-wrapper" id="mainWrapper">

        <?php require BASE_DIR . '/includes/topbar.php'; ?>

        <main class="main-content">
            <?php
                if      ($page === 'dashboard') { require BASE_DIR . '/pages/dashboard.php'; }
                elseif  ($page === 'products')  { require BASE_DIR . '/pages/products.php'; }
                elseif  ($page === 'profile')   { require BASE_DIR . '/pages/profile.php'; }
                else                            { require BASE_DIR . '/pages/dashboard.php'; }
            ?>
        </main>

        <?php require BASE_DIR . '/includes/footer.php'; ?>

    </div>

    <?php require BASE_DIR . '/includes/scripts.php'; ?>
</body>
</html>
