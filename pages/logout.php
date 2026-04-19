<?php
// ══════════════════════════════════
// LOGOUT — pages/logout.php
// ══════════════════════════════════
require_once 'includes/auth.php';
logout();
header('Location: index.php?page=home&logged_out=1');
exit;
