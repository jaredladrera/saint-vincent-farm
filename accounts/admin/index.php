<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../index.php");
    exit(); // Always call exit after header redirect
}

$user = $_SESSION['user'];
?>
<?php
// ══════════════════════════════════
// MAIN ROUTER — index.php
// ══════════════════════════════════
define('BASE_DIR', __DIR__);
define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));

$page = $_GET['page'] ?? 'dashboard';

$allowed_pages = ['dashboard', 'products', 'orders', 'users', 'profile', 'delivery', 'payroll', 'payslip', 'mypayslip'];
if (!in_array($page, $allowed_pages)) $page = 'dashboard';

$titles = [
    'dashboard' => 'Dashboard',
    'products'  => 'Products',
    'orders'  => 'Orders',
    'users'  => 'Users',
    'profile'   => 'Profile',
    'delivery'   => 'Delivery',
    'payroll'   => 'Payroll',
    'payslip'   => 'Payslip',
    'mypayslip'   => 'Payslip',
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
                elseif  ($page === 'orders')  { require BASE_DIR . '/pages/orders.php'; }
                elseif  ($page === 'users')  { require BASE_DIR . '/pages/users.php'; }
                elseif  ($page === 'profile')   { require BASE_DIR . '/pages/profile.php'; }
                elseif  ($page === 'payroll')   { require BASE_DIR . '/pages/payroll.php'; }
                elseif  ($page === 'payslip')   { require BASE_DIR . '/pages/payslip.php'; }
                elseif  ($page === 'mypayslip')   { require BASE_DIR . '/pages/mypayslip.php'; }
                elseif  ($page === 'delivery')   { require BASE_DIR . '/pages/delivery.php'; }
                else                            { require BASE_DIR . '/pages/dashboard.php'; }
            ?>
        </main>

        <?php require BASE_DIR . '/includes/footer.php'; ?>

    </div>

    <?php require BASE_DIR . '/includes/scripts.php'; ?>
</body>
</html>
