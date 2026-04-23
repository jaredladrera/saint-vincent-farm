<?php // includes/topbar.php
$current = $_GET['page'] ?? 'dashboard';
$titles = [
    'dashboard' => 'Dashboard',
    'products'  => 'Products',
    'orders'  => 'Orders',
    'users'  => 'Users',
    'profile'   => 'Profile',
];
$page_title = $titles[$current] ?? 'Admin';
?>
<header class="topbar">

    <div class="topbar-left">
        <!-- Hamburger -->
        <button class="btn sidebar-toggle me-3" id="sidebarToggle" aria-label="Toggle sidebar">
            <i class="bi bi-list"></i>
        </button>
        <!-- Breadcrumb -->
        <div>
            <p class="topbar-title mb-0"><?= htmlspecialchars($page_title) ?></p>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?page=dashboard">Home</a></li>
                    <?php if ($current !== 'dashboard'): ?>
                        <li class="breadcrumb-item active"><?= htmlspecialchars($page_title) ?></li>
                    <?php endif; ?>
                </ol>
            </nav>
        </div>
    </div>

    <div class="topbar-right">
        <!-- Search -->
        <!-- <div class="topbar-search d-none d-md-flex">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Search…" />
        </div> -->

        <!-- Notifications -->
        <!-- <div class="position-relative">
            <button class="topbar-icon-btn" title="Notifications">
                <i class="bi bi-bell"></i>
                <span class="notif-badge">3</span>
            </button>
        </div> -->

        <!-- Avatar -->
        <a href="<?= BASE_URL ?>/index.php?page=profile" class="topbar-avatar" title="Profile">
            <img src="https://ui-avatars.com/api/?name=Admin+User&background=0B6B1C&color=fff&size=80" alt="Admin" />
        </a>
    </div>

</header>
