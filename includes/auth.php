<?php
// ══════════════════════════════════
// AUTH HELPER — includes/auth.php
// ══════════════════════════════════
// Start session on every page load
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Check if user is logged in ──
function isLoggedIn(): bool {
    return isset($_SESSION['user']);
}

// ── Get current logged-in user ──
function currentUser(): ?array {
    return $_SESSION['user'] ?? null;
}

// ── Require login — redirect to login page if not authenticated ──
// Pass $redirect = the page to return to after login
function requireLogin(string $redirect = 'contact'): void {
    if (!isLoggedIn()) {
        header('Location: index.php?page=login&redirect=' . urlencode($redirect));
        exit;
    }
}

// ── Log the user out ──
function logout(): void {
    session_unset();
    session_destroy();
}
