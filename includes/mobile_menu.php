<?php
require_once __DIR__ . '/auth.php';
$current_page = $_GET['page'] ?? 'home';
$user = currentUser();
?>
<div class="mobile-menu" id="mobileMenu">
    <a href="index.php?page=home"     <?= $current_page=='home'    ? 'style="color:white;font-weight:600"':'' ?>>Home</a>
    <a href="index.php?page=shop"     <?= $current_page=='shop'    ? 'style="color:white;font-weight:600"':'' ?>>Products</a>
    <a href="index.php?page=features" <?= $current_page=='features'? 'style="color:white;font-weight:600"':'' ?>>System Features</a>
    <a href="index.php?page=about"    <?= $current_page=='about'   ? 'style="color:white;font-weight:600"':'' ?>>About</a>
    <a href="index.php?page=contact"  <?= $current_page=='contact' ? 'style="color:white;font-weight:600"':'' ?>>Order Now</a>
    <?php if ($user): ?>
        <a href="index.php?page=logout" style="color:#f87171">
            <i class="bi bi-box-arrow-right"></i> Sign Out (<?= htmlspecialchars(explode(' ', $user['name'])[0]) ?>)
        </a>
    <?php else: ?>
        <a href="index.php?page=login" style="color:var(--amber)">
            <i class="bi bi-person"></i> Sign In
        </a>
    <?php endif; ?>
</div>
