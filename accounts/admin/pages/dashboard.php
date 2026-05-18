<?php // pages/dashboard.php ?>
<?php // dashboard.php
require_once '../../config/pdo_connection.php';

$db  = new Connect();
$pdo = $db->connection;

/* =========================
   DEFAULT VALUES
========================= */
$total_customers = 0;
$total_livestock = 0;
$total_orders    = 0;
$total_sales     = 0;
$monthly_sales   = 0;
$recent_orders   = [];

try {

    /* =========================
       TOTAL CUSTOMERS (KEEP)
    ========================= */
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM user_profile");
    $total_customers = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    /* =========================
       TOTAL LIVESTOCK (KEEP)
    ========================= */
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM livestock WHERE product_type = 'Livestock'");
    $total_livestock = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    /* =========================
       TOTAL ORDERS
    ========================= */
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM orders");
    $total_orders = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    /* =========================
       TOTAL PRODUCTS
    ========================= */
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM livestock WHERE product_type = 'Product'");
    $total_products = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    /* =========================
       TOTAL SALES (ALL TIME)
    ========================= */
    $stmt = $pdo->query("
        SELECT SUM(total_amount) AS total 
        FROM orders 
        WHERE order_status = 'delivered'
    ");
    $total_sales = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

   /* =========================
       MONTHLY SALES (GRAPH DATA - LAST 6 MONTHS)
    ========================= */
    $monthly_labels = [];
    $monthly_data = [];

    $stmt = $pdo->query("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            SUM(total_amount) as total
        FROM orders
        WHERE order_status = 'delivered'
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month ASC
        LIMIT 6
    ");

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
        $monthly_labels[] = date('M Y', strtotime($row['month'] . '-01'));
        $monthly_data[] = (float)$row['total'];
    }
    /* =========================
       RECENT ORDERS (TOP 5 DESC)
    ========================= */
    $stmt = $pdo->query("
        SELECT o.*, 
               CONCAT(u.first_name, ' ', u.last_name) AS customer_name
        FROM orders o
        LEFT JOIN user_profile u ON u.id = o.user_id
        ORDER BY o.id DESC
        LIMIT 5
    ");
    $recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /* =========================
    LIVESTOCK CATEGORY (PIE DATA)
    ========================= */
    $category_labels = [];
    $category_data   = [];

    $stmt = $pdo->query("
        SELECT category, COUNT(*) AS total
        FROM livestock
        GROUP BY category
        ORDER BY total DESC
    ");

    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($categories as $cat) {
        $category_labels[] = $cat['category'];
        $category_data[]   = (int)$cat['total'];
    }

} catch (PDOException $e) {
    // fallback safe values
    $total_customers = 0;
    $total_livestock = 0;
    $total_orders    = 0;
    $total_sales     = 0;
    $monthly_sales   = 0;
    $recent_orders   = [];
}


function getBadgeClass($status) {
    switch ($status) {
        case 'pending':
            return 'bg-warning text-dark'; // yellow
        case 'delivered':
            return 'bg-success'; // green
        case 'out_for_delivery':
            return 'bg-primary'; // blue
        case 'processing':
            return 'bg-info text-dark'; // light blue
        default:
            return 'bg-secondary'; // gray fallback
    }
}
?>
<!-- Stats Row -->
<div class="row g-4 mb-4">

    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary-soft">
                <i class="bi bi-cart-check"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Total Orders</span>
                <span class="stat-value"><?= $total_orders ?></span>
                <!-- <span class="stat-change positive"><i class="bi bi-arrow-up-short"></i>12%</span> -->
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-success-soft">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Revenue</span>
                <span class="stat-value">₱<?= number_format($total_sales, 2) ?></span>
                <!-- <span class="stat-change positive"><i class="bi bi-arrow-up-short"></i>8%</span> -->
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon bg-warning-soft">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Customers</span>
                <span class="stat-value"><?= $total_customers ?></span>
                <!-- <span class="stat-change positive"><i class="bi bi-arrow-up-short"></i>5%</span> -->
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon bg-danger-soft">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Products</span>
                <span class="stat-value"><?= $total_products ?></span>
                <!-- <span class="stat-change negative"><i class="bi bi-arrow-down-short"></i>2%</span> -->
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-2">
        <div class="stat-card">
            <div class="stat-icon bg-warning-soft">
                <i class="bi bi-bucket"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Livestocks</span>
                <span class="stat-value"><?= $total_livestock ?></span>
                <!-- <span class="stat-change negative"><i class="bi bi-arrow-down-short"></i>2%</span> -->
            </div>
        </div>
    </div>

</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">

    <div class="col-12 col-lg-8">
        <div class="card-panel">
            <div class="card-panel-header">
                <span>Sales Overview</span>
                <!-- <select class="form-select form-select-sm w-auto">
                    <option>Last 7 days</option>
                    <option>Last 30 days</option>
                    <option>This year</option>
                </select> -->
            </div>
            <canvas id="salesChart" height="150"></canvas>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card-panel">
            <div class="card-panel-header">
                <span>Category Split</span>
            </div>
            <canvas id="donutChart" height="160"></canvas>
        </div>
    </div>

</div>

<!-- Recent Orders Table -->
<div class="row g-4">

    <div class="col-12">
        <div class="card-panel">
            <div class="card-panel-header">
                <span>Recent Orders</span>
                <a href="<?= BASE_URL ?>/index.php?page=orders" class="btn btn-sm btn-outline-primary-green">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $o): ?>
                        <tr>
                            <td>#<?= $o['id'] ?></td>
                            <td><?= htmlspecialchars($o['customer_name']) ?></td>
                            <td>₱<?= number_format($o['total_amount'], 2) ?></td>
                            <td>
                                <span class="badge  <?= getBadgeClass($o['order_status']) ?>"><?= $o['order_status'] ?></span>
                            </td>
                            <td><?= htmlspecialchars($o['created_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Chart init (inline, specific to this page) -->
<script>
document.addEventListener('DOMContentLoaded', function () {
const labels = <?= json_encode($monthly_labels) ?>;
const data   = <?= json_encode($monthly_data) ?>;
const livestockCategories = <?= json_encode($category_labels) ?>;
const livestockCounts     = <?= json_encode($category_data) ?>;
 
    // Sales Line Chart
    const salesCtx = document.getElementById('salesChart');

    if (salesCtx) {
        new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: labels, // ✅ FROM PHP
                datasets: [{
                    label: 'Sales (₱)',
                    data: data, // ✅ FROM PHP
                    borderColor: '#0B6B1C',
                    backgroundColor: 'rgba(11,107,28,0.08)',
                    tension: 0.45,
                    fill: true,
                    pointBackgroundColor: '#0B6B1C',
                    pointRadius: 5,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f0f0f0' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

   // Donut / Pie Chart (Livestock Categories)
const donutCtx = document.getElementById('donutChart');

if (donutCtx) {
    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: livestockCategories,
            datasets: [{
                data: livestockCounts,
                backgroundColor: [
                    '#0B6B1C',
                    '#3495a8',
                    '#a8e6b3',
                    '#ba2323',
                    '#ecb71b',
                    '#8c1990'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

});
</script>
