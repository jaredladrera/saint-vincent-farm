<?php
// ══ MY ORDERS PAGE ══
$db  = new Connect();
$pdo = $db->connection;

// Handle cancel via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
    $cancel_id = (int) ($_POST['order_id'] ?? 0);
    try {
        $chk = $pdo->prepare("SELECT id, order_status FROM orders WHERE id = ? AND user_id = ?");
        $chk->execute([$cancel_id, $user['id']]);
        $chk_order = $chk->fetch();

        if ($chk_order && $chk_order->order_status === 'pending') {
            $pdo->prepare("UPDATE orders SET order_status = 'cancelled' WHERE id = ? AND user_id = ?")
                ->execute([$cancel_id, $user['id']]);
            $cancel_success = "Order #$cancel_id has been cancelled.";
        } else {
            $cancel_error = "This order cannot be cancelled.";
        }
    } catch (PDOException $e) {
        $cancel_error = "Failed to cancel order. Please try again.";
    }
}

// Fetch all orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user['id']]);
$orders = $stmt->fetchAll();

// Status config — keyed by order_status values
$status_map = [
    'pending'         => ['class' => 'status-pending',   'icon' => 'bi-hourglass-split', 'label' => 'Pending'],
    'processing'      => ['class' => 'status-preparing', 'icon' => 'bi-box-seam',        'label' => 'Processing'],
    'out_for_delivery'=> ['class' => 'status-delivery',  'icon' => 'bi-truck',           'label' => 'Out for Delivery'],
    'delivered'       => ['class' => 'status-delivered', 'icon' => 'bi-bag-check-fill',  'label' => 'Delivered'],
    'cancelled'       => ['class' => 'status-cancelled', 'icon' => 'bi-x-circle-fill',   'label' => 'Cancelled'],
];

// Only 'pending' order_status can be cancelled
$cancellable_statuses = ['pending'];

// Tracking steps
$track_steps = [
    ['key' => 'pending',          'label' => 'Order Placed',    'icon' => 'bi-receipt'],
    ['key' => 'processing',       'label' => 'Processing',      'icon' => 'bi-box-seam'],
    ['key' => 'out_for_delivery', 'label' => 'Out for Delivery','icon' => 'bi-truck'],
    ['key' => 'delivered',        'label' => 'Delivered',       'icon' => 'bi-bag-check'],
];
$step_order = array_column($track_steps, 'key');
?>

<!-- ══ HERO ══ -->
<div class="page-hero">
    <div class="container">
        <div class="section-label">Account</div>
        <h1>My <span class="text-amber">Orders</span></h1>
        <p>Track, view details, and manage all your orders.</p>
    </div>
</div>

<div class="page-content">
    <div class="container">

        <!-- Alerts -->
        <?php if (!empty($cancel_success)): ?>
            <div class="profile-alert profile-alert-success orders-alert">
                <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($cancel_success) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($cancel_error)): ?>
            <div class="profile-alert profile-alert-error orders-alert">
                <i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($cancel_error) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <div class="orders-empty">
                <i class="bi bi-basket orders-empty-icon"></i>
                <h4>No orders yet</h4>
                <p>You haven't placed any orders. Start shopping now!</p>
                <a href="index.php?page=shop" class="btn-submit orders-shop-btn">
                    <i class="bi bi-shop"></i> Browse Shop
                </a>
            </div>

        <?php else: ?>
            <div class="orders-table-wrap">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Payment</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order):
                            $st         = $status_map[$order->order_status] ?? ['class' => 'status-default', 'icon' => 'bi-circle', 'label' => ucwords(str_replace('_', ' ', $order->order_status))];
                            $can_cancel = ($order->order_status === 'pending');
                        ?>
                        <tr>
                            <td class="orders-id">#<?= htmlspecialchars($order->id) ?></td>
                            <td class="orders-date"><?= date('M d, Y', strtotime($order->created_at)) ?></td>
                            <td class="orders-method"><?= htmlspecialchars($order->mode_of_payment ?? 'N/A') ?></td>
                            <td class="orders-amount">₱<?= number_format($order->total_amount ?? 0, 2) ?></td>
                            <td>
                                <span class="profile-status-badge <?= $st['class'] ?>">
                                    <i class="bi <?= $st['icon'] ?>"></i> <?= $st['label'] ?>
                                </span>
                            </td>
                            <td class="orders-actions">
                                <button class="orders-btn-details"
                                        onclick="openOrderDetails(<?= htmlspecialchars(json_encode($order), ENT_QUOTES) ?>)">
                                    <i class="bi bi-eye"></i> Details
                                </button>
                                <?php if ($can_cancel): ?>
                                <form method="POST" class="orders-cancel-form"
                                      onsubmit="return confirmCancel(event, <?= $order->id ?>)">
                                    <input type="hidden" name="cancel_order" value="1">
                                    <input type="hidden" name="order_id" value="<?= $order->id ?>">
                                    <button type="submit" class="orders-btn-cancel">
                                        <i class="bi bi-x-circle"></i> Cancel
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</div>

<!-- ══ ORDER DETAILS MODAL ══ -->
<div class="modal-overlay" id="orderDetailsModal">
    <div class="modal-box orders-modal-box">
        <button class="modal-close" onclick="closeOrderDetails()">&times;</button>
        <h4><i class="bi bi-receipt"></i> Order Details</h4>

        <div class="orders-modal-ref">
            <span class="orders-modal-ref-label">Order ID</span>
            <span class="orders-modal-ref-id" id="modal-order-id"></span>
        </div>

        <!-- Tracking Timeline -->
        <div class="orders-track-wrap">
            <div class="orders-track-title"><i class="bi bi-geo-alt-fill"></i> Order Tracking</div>
            <div class="orders-track-steps">
                <?php foreach ($track_steps as $i => $step): ?>
                <div class="orders-track-step" id="track-step-<?= $step['key'] ?>">
                    <div class="orders-track-dot">
                        <i class="bi <?= $step['icon'] ?>"></i>
                    </div>
                    <?php if ($i < count($track_steps) - 1): ?>
                        <div class="orders-track-line"></div>
                    <?php endif; ?>
                    <div class="orders-track-info">
                        <div class="orders-track-label"><?= $step['label'] ?></div>
                        <div class="orders-track-time" id="track-time-<?= $step['key'] ?>"></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <!-- Cancelled step — shown only when cancelled -->
                <div class="orders-track-step" id="track-step-cancelled" style="display:none;">
                    <div class="orders-track-dot orders-track-dot-cancelled">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <div class="orders-track-info">
                        <div class="orders-track-label">Order Cancelled</div>
                        <div class="orders-track-time" id="track-time-cancelled"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Info -->
        <div class="orders-modal-info">
            <div class="orders-modal-row">
                <span class="orders-modal-key"><i class="bi bi-calendar3"></i> Date Placed</span>
                <span class="orders-modal-val" id="modal-date"></span>
            </div>
            <div class="orders-modal-row">
                <span class="orders-modal-key"><i class="bi bi-credit-card"></i> Payment Method</span>
                <span class="orders-modal-val" id="modal-payment"></span>
            </div>
            <div class="orders-modal-row">
                <span class="orders-modal-key"><i class="bi bi-calendar-check"></i> Delivery Date</span>
                <span class="orders-modal-val" id="modal-delivery"></span>
            </div>
            <div class="orders-modal-row">
                <span class="orders-modal-key"><i class="bi bi-chat-left-text"></i> Notes</span>
                <span class="orders-modal-val" id="modal-notes"></span>
            </div>
            <div class="orders-modal-row orders-modal-total-row">
                <span class="orders-modal-key"><i class="bi bi-receipt"></i> Order Total</span>
                <span class="orders-modal-val orders-modal-total" id="modal-total"></span>
            </div>
        </div>

        <!-- Cancel button inside modal — only visible when order_status = 'pending' -->
        <div id="modal-cancel-wrap" style="display:none;">
            <form method="POST" onsubmit="return confirmCancel(event, window._modalOrderId)">
                <input type="hidden" name="cancel_order" value="1">
                <input type="hidden" name="order_id" id="modal-cancel-order-id">
                <button type="submit" class="orders-btn-cancel orders-modal-cancel-btn">
                    <i class="bi bi-x-circle"></i> Cancel This Order
                </button>
            </form>
        </div>

    </div>
</div>

<script>
const stepOrder      = <?= json_encode($step_order) ?>;
const cancellable    = <?= json_encode($cancellable_statuses) ?>;
window._modalOrderId = null;

function openOrderDetails(order) {
    window._modalOrderId = order.id;

    document.getElementById('modal-order-id').textContent = '#' + order.id;
    document.getElementById('modal-date').textContent     = formatDate(order.created_at);
    document.getElementById('modal-payment').textContent  = order.mode_of_payment || 'N/A';
    document.getElementById('modal-delivery').textContent = order.prefered_delivery_date ? formatDate(order.prefered_delivery_date) : 'Not specified';
    document.getElementById('modal-notes').textContent    = order.notes || '—';
    document.getElementById('modal-total').textContent    = '₱' + parseFloat(order.total_amount || 0).toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2});

    // Show cancel button only when order_status === 'pending'
    const cancelWrap = document.getElementById('modal-cancel-wrap');
    cancelWrap.style.display = (order.order_status === 'pending') ? 'block' : 'none';
    document.getElementById('modal-cancel-order-id').value = order.id;

    // Build timeline
    const status      = order.order_status;
    const isCancelled = (status === 'cancelled');
    const activeIdx   = stepOrder.indexOf(status);

    stepOrder.forEach(function(key, i) {
        const step = document.getElementById('track-step-' + key);
        const time = document.getElementById('track-time-' + key);
        if (!step) return;

        step.classList.remove('track-done', 'track-active', 'track-inactive');
        time.textContent = '';

        if (isCancelled) {
            step.classList.add('track-inactive');
        } else if (i < activeIdx) {
            step.classList.add('track-done');
            time.textContent = 'Completed';
        } else if (i === activeIdx) {
            step.classList.add('track-active');
            time.textContent = formatDate(order.updated_at || order.created_at);
        } else {
            step.classList.add('track-inactive');
        }
    });

    // Cancelled step
    const cancelledStep = document.getElementById('track-step-cancelled');
    const cancelledTime = document.getElementById('track-time-cancelled');
    if (isCancelled) {
        cancelledStep.style.display = 'flex';
        cancelledTime.textContent   = formatDate(order.updated_at || order.created_at);
    } else {
        cancelledStep.style.display = 'none';
    }

    document.getElementById('orderDetailsModal').classList.add('show');
}

function closeOrderDetails() {
    document.getElementById('orderDetailsModal').classList.remove('show');
}

function formatDate(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    return d.toLocaleDateString('en-PH', { year:'numeric', month:'short', day:'numeric' });
}

function confirmCancel(e, orderId) {
    if (!confirm('Are you sure you want to cancel Order #' + orderId + '?')) {
        e.preventDefault(); return false;
    }
    return true;
}

document.getElementById('orderDetailsModal').addEventListener('click', function(e) {
    if (e.target === this) closeOrderDetails();
});
</script>