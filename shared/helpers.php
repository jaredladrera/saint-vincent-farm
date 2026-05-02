<?php 
function redirectByRole($role, $fallback = 'contact') {

    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    $role = strtolower($role); // normalize

    switch ($role) {

        case 'administrator':
            header('Location: ' . $base . '/accounts/admin/index.php?page=dashboard');
            break;

        case 'staff':
            header('Location: ' . $base . '/accounts/admin/index.php?page=products');
            break;

        default:
            header('Location: ' . $base . '/index.php?page=' . urlencode($fallback));
            break;
    }

    exit;
}
?>