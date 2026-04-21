<?php 
// ── Role-based redirect helper ──
function redirectByRole($role, $fallback = 'contact') {
    switch ($role) {
        case 'admin':
            header('Location: index.php?page=account/admin');
            break;
        case 'staff':
            header('Location: index.php?page=account/staff'); // adjust as needed
            break;
        default: // 'user', 'customer', etc.
            header('Location: index.php?page=' . urlencode($fallback) . '&registered=1');
            break;
    }
    exit;
}

?>