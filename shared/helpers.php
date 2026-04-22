<?php 
function redirectByRole($role, $fallback = 'contact') {
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    switch ($role) {
        case 'admin':
            header('Location: ' . $base . '/accounts/admin/index.php');
            break;
        case 'staff':
            header('Location: ' . $base . '/accounts/staff/index.php');
            break;
        default:
            header('Location: ' . $base . '/index.php?page=' . urlencode($fallback));
            break;
    }
    exit;
}
?>