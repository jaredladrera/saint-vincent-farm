<?php // includes/sidebar.php
$current = $_GET['page'] ?? 'dashboard';
?>
<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="sidebar">

    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="bi bi-clipboard-data"></i>
        </div>
        <span class="brand-name">Saint Vincent Farm</span>
    </div>

    <!-- Nav -->
    <nav class="sidebar-nav">
        <p class="nav-label">Main</p>
        <ul class="nav flex-column">

            <li class="nav-item">
                <a href="<?= BASE_URL ?>/index.php?page=dashboard"
                   class="nav-link <?= $current === 'dashboard' ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= BASE_URL ?>/index.php?page=products"
                   class="nav-link <?= $current === 'products' ? 'active' : '' ?>">
                    <i class="bi bi-box-seam"></i>
                    <span>Products</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= BASE_URL ?>/index.php?page=orders"
                   class="nav-link <?= $current === 'orders' ? 'active' : '' ?>">
                    <i class="bi bi-bag"></i>
                    <span>Orders</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= BASE_URL ?>/index.php?page=users"
                   class="nav-link <?= $current === 'users' ? 'active' : '' ?>">
                    <i class="bi bi-people-fill"></i>
                    <span>Users</span>
                </a>
            </li>

        </ul>

        <p class="nav-label mt-3">Account</p>
        <ul class="nav flex-column">

            <li class="nav-item">
                <a href="<?= BASE_URL ?>/index.php?page=profile"
                   class="nav-link <?= $current === 'profile' ? 'active' : '' ?>">
                    <i class="bi bi-person-circle"></i>
                    <span>Profile</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="#" class="nav-link text-danger-nav">
                    <i class="bi bi-box-arrow-left"></i>
                    <span>Logout</span>
                </a>
            </li>

        </ul>
    </nav>

    <!-- User card at bottom -->
    <div class="sidebar-user">
        <img src="https://ui-avatars.com/api/?name=Admin+User&background=0B6B1C&color=fff&size=80" alt="Avatar" />
        <div class="sidebar-user-info">
            <span class="user-name">Admin User</span>
            <span class="user-role">Super Admin</span>
        </div>
    </div>

</aside>
