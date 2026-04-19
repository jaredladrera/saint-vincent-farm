<!-- ══ ABOUT PAGE ══ -->
<?php
$objectives = [
    [
        'num'   => '1',
        'title' => 'Automate Livestock Monitoring',
        'desc'  => 'Replace paper-based animal health logs with a digital system for real-time tracking of livestock conditions and health records.',
    ],
    [
        'num'   => '2',
        'title' => 'Streamline Sales & Transactions',
        'desc'  => 'Record sales accurately with customer details, order summaries, and payment tracking all in one centralized platform.',
    ],
    [
        'num'   => '3',
        'title' => 'Improve Delivery Management',
        'desc'  => 'Track delivery schedules and fulfillment status to minimize delays and improve customer satisfaction on every order.',
    ],
    [
        'num'   => '4',
        'title' => 'Enable Data-Driven Decisions',
        'desc'  => 'Generate reports and analytics to support informed farm management and planning for sustainable long-term growth.',
    ],
];
?>

<div class="page-hero">
    <div class="container">
        <div class="section-label">Our Story</div>
        <h1>About <span style="color:var(--amber)">St. Vincent Farm</span></h1>
        <p>A growing livestock farm committed to quality, sustainability, and modern farm management.</p>
    </div>
</div>

<div class="page-content">
    <div class="container">

        <!-- Mission / Vision / Location -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="about-block">
                    <div class="about-icon">
                        <i class="bi bi-geo-alt-fill" style="color:var(--green-mid)"></i>
                    </div>
                    <h5>Our Location</h5>
                    <p>Situated in Batangas, Philippines — a region known for its rich agricultural land. Our farm provides an ideal environment for healthy livestock growth.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="about-block">
                    <div class="about-icon">
                        <i class="bi bi-heart-pulse-fill" style="color:var(--earth)"></i>
                    </div>
                    <h5>Our Mission</h5>
                    <p>To raise healthy livestock using responsible farming practices, and to deliver quality animals to customers with a seamless, digitally-managed sales experience.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="about-block">
                    <div class="about-icon">
                        <i class="bi bi-eye-fill" style="color:var(--amber)"></i>
                    </div>
                    <h5>Our Vision</h5>
                    <p>To become a leading livestock farm in Batangas — embracing technology to modernize farm operations while maintaining quality and community trust.</p>
                </div>
            </div>
        </div>

        <!-- Background + Objectives -->
        <div class="row g-5 align-items-start">

            <!-- Background -->
            <div class="col-md-6">
                <div class="section-label">Background</div>
                <h3 class="section-title mb-3">Why We Built This <em>System</em></h3>
                <p style="font-size:0.9rem;color:#555;line-height:1.8;margin-bottom:1rem">
                    St. Vincent Farm previously managed all records manually — through logbooks and paper records.
                    As operations grew, tracking livestock inventory, monitoring animal conditions, recording
                    transactions, and scheduling deliveries became increasingly difficult.
                </p>
                <p style="font-size:0.9rem;color:#555;line-height:1.8">
                    The web-based livestock monitoring and sales system was developed to replace these manual
                    methods with a centralized, efficient platform — reducing errors, improving data accuracy,
                    and supporting sustainable farm management. The system covers livestock monitoring,
                    inventory, sales, delivery tracking, and report generation.
                </p>
            </div>

            <!-- Objectives Timeline -->
            <div class="col-md-6">
                <div class="section-label">Objectives</div>
                <h3 class="section-title mb-3">Study <em>Goals</em></h3>

                <?php foreach ($objectives as $obj): ?>
                <div class="timeline-item">
                    <div class="tl-dot"><?= $obj['num'] ?></div>
                    <div class="tl-content">
                        <h6><?= $obj['title'] ?></h6>
                        <p><?= $obj['desc'] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>
</div>
