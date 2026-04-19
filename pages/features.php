<!-- ══ FEATURES PAGE ══ -->
<?php
$features = [
    [
        'icon'  => 'bi-clipboard2-pulse',
        'color' => 'green',
        'title' => 'Livestock Monitoring',
        'desc'  => 'Track animal health, weight, feeding schedules, and conditions in real-time. Get alerts for animals needing attention.',
    ],
    [
        'icon'  => 'bi-boxes',
        'color' => 'green',
        'title' => 'Inventory Management',
        'desc'  => 'Monitor live stock counts per animal type. View available, reserved, and sold animals with accurate updated data.',
    ],
    [
        'icon'  => 'bi-receipt',
        'color' => 'amber',
        'title' => 'Sales Transactions',
        'desc'  => 'Record and manage all sales with customer info, order details, pricing, and payment status in one place.',
    ],
    [
        'icon'  => 'bi-truck',
        'color' => 'amber',
        'title' => 'Delivery Tracking',
        'desc'  => 'Schedule and track deliveries from farm to customer. Ensure accurate fulfillment and on-time delivery logging.',
    ],
    [
        'icon'  => 'bi-bar-chart-line',
        'color' => 'green',
        'title' => 'Reports & Analytics',
        'desc'  => 'Generate reports on livestock health, sales trends, delivery performance, and inventory for data-driven decisions.',
    ],
    [
        'icon'  => 'bi-shield-lock',
        'color' => 'amber',
        'title' => 'Secure Access Control',
        'desc'  => 'Role-based login ensures farm staff access only what they need. Sensitive data is protected with secure storage.',
    ],
];
?>

<div class="page-hero">
    <div class="container">
        <div class="section-label">The System</div>
        <h1>Powerful <span style="color:var(--amber)">Features</span></h1>
        <p>A web-based livestock monitoring and sales platform built to modernize St. Vincent Farm's operations.</p>
    </div>
</div>

<div class="page-content">
    <div class="container">

        <!-- Feature Cards -->
        <div class="row g-4">
            <?php foreach ($features as $f): ?>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon <?= $f['color'] ?>">
                        <i class="bi <?= $f['icon'] ?>"
                           style="color:<?= $f['color'] == 'green' ? 'var(--green-mid)' : 'var(--amber)' ?>">
                        </i>
                    </div>
                    <h5><?= $f['title'] ?></h5>
                    <p><?= $f['desc'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- CTA Banner -->
        <div class="cta-banner">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 style="color:white;font-family:'Playfair Display',serif;font-weight:700;margin-bottom:0.5rem;">
                        Integrated Module System
                    </h4>
                    <p style="color:rgba(255,255,255,0.6);font-size:0.9rem;margin:0">
                        All modules — monitoring, inventory, sales, delivery, and reporting — work together
                        as one unified platform for St. Vincent Farm.
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="index.php?page=contact" class="btn-primary-hero">
                        Request Demo <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
