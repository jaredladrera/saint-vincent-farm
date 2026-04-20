<?php // pages/dashboard.php ?>

<!-- Stats Row -->
<div class="row g-4 mb-4">

    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary-soft">
                <i class="bi bi-cart-check"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Total Orders</span>
                <span class="stat-value">1,284</span>
                <span class="stat-change positive"><i class="bi bi-arrow-up-short"></i>12%</span>
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
                <span class="stat-value">₱84,320</span>
                <span class="stat-change positive"><i class="bi bi-arrow-up-short"></i>8%</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning-soft">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Customers</span>
                <span class="stat-value">340</span>
                <span class="stat-change positive"><i class="bi bi-arrow-up-short"></i>5%</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon bg-danger-soft">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Products</span>
                <span class="stat-value">56</span>
                <span class="stat-change negative"><i class="bi bi-arrow-down-short"></i>2%</span>
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
                <select class="form-select form-select-sm w-auto">
                    <option>Last 7 days</option>
                    <option>Last 30 days</option>
                    <option>This year</option>
                </select>
            </div>
            <canvas id="salesChart" height="110"></canvas>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card-panel">
            <div class="card-panel-header">
                <span>Category Split</span>
            </div>
            <canvas id="donutChart" height="180"></canvas>
            <ul class="donut-legend mt-3">
                <li><span class="dot" style="background:#0B6B1C"></span> Vegetables</li>
                <li><span class="dot" style="background:#34a84a"></span> Fruits</li>
                <li><span class="dot" style="background:#a8e6b3"></span> Others</li>
            </ul>
        </div>
    </div>

</div>

<!-- Recent Orders Table -->
<div class="row g-4">

    <div class="col-12">
        <div class="card-panel">
            <div class="card-panel-header">
                <span>Recent Orders</span>
                <a href="<?= BASE_URL ?>/index.php?page=products" class="btn btn-sm btn-outline-primary-green">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $orders = [
                            ['#1001','Maria Santos','Ampalaya Bundle','₱240','Completed','Apr 18, 2025'],
                            ['#1002','Jose Reyes','Sitaw Pack','₱180','Pending','Apr 18, 2025'],
                            ['#1003','Ana Cruz','Kangkong Bunch','₱90','Processing','Apr 17, 2025'],
                            ['#1004','Pedro Lim','Kamote (5kg)','₱350','Completed','Apr 17, 2025'],
                            ['#1005','Rosa Garcia','Mixed Greens','₱520','Cancelled','Apr 16, 2025'],
                        ];
                        foreach ($orders as $o):
                        ?>
                        <tr>
                            <td class="fw-semibold text-green"><?= $o[0] ?></td>
                            <td><?= $o[1] ?></td>
                            <td><?= $o[2] ?></td>
                            <td><?= $o[3] ?></td>
                            <td><span class="status-badge status-<?= strtolower($o[4]) ?>"><?= $o[4] ?></span></td>
                            <td class="text-muted"><?= $o[5] ?></td>
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

    // Sales Line Chart
    const salesCtx = document.getElementById('salesChart');
    if (salesCtx) {
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
                datasets: [{
                    label: 'Sales (₱)',
                    data: [4200, 5800, 3900, 7200, 6100, 8400, 9100],
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
                    y: { beginAtZero: true, grid: { color: '#f0f0f0' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Donut Chart
    const donutCtx = document.getElementById('donutChart');
    if (donutCtx) {
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: ['Vegetables', 'Fruits', 'Others'],
                datasets: [{
                    data: [52, 30, 18],
                    backgroundColor: ['#0B6B1C', '#34a84a', '#a8e6b3'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                cutout: '72%',
                plugins: { legend: { display: false } }
            }
        });
    }
});
</script>
