<?php

requireLogin();
// ══ PROFILE PAGE ══
$db  = new Connect();
$pdo = $db->connection;

$current = currentUser();


$stmt = $pdo->prepare("SELECT * FROM user_profile WHERE id = ?");
$stmt->execute([$current['id']]);
$profile = $stmt->fetch();

$success_msg = '';
$error_msg   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name  = trim($_POST['first_name']  ?? '');
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name   = trim($_POST['last_name']   ?? '');
    $contact     = trim($_POST['contact']     ?? '');
    $address     = trim($_POST['address']     ?? '');
    $email       = trim($_POST['email']       ?? '');
 
    if (empty($first_name) || empty($last_name) || empty($email)) {
        $error_msg = 'First name, last name, and email are required.';
    } else {
        try {
            $upd = $pdo->prepare("
                UPDATE user_profile
                   SET first_name  = :first_name,
                       middle_name = :middle_name,
                       last_name   = :last_name,
                       contact_number     = :contact,
                       address     = :address,
                       email_address       = :email
                 WHERE id          = :id
            ");
            $upd->execute([
                ':first_name'  => $first_name,
                ':middle_name' => $middle_name,
                ':last_name'   => $last_name,
                ':contact'     => $contact,
                ':address'     => $address,
                ':email'       => $email,
                ':id'          => $current['id'],
            ]);
 
            $_SESSION['user']['first_name']  = $first_name;
            $_SESSION['user']['middle_name'] = $middle_name;
            $_SESSION['user']['last_name']   = $last_name;
            $_SESSION['user']['contact_number']     = $contact;
            $_SESSION['user']['address']     = $address;
            $_SESSION['user']['email_address']       = $email;
 
            $stmt2 = $pdo->prepare("SELECT * FROM user_profile WHERE id = ?");
            $stmt2->execute([$user['id']]);
            $profile     = $stmt2->fetch();
            $success_msg = 'Profile updated successfully!';
        } catch (PDOException $e) {
            $error_msg = 'Failed to update profile. Please try again.';
        }
    }
}

$orders_stmt = $pdo->prepare("
    SELECT * FROM orders
     WHERE user_id = ?
     ORDER BY created_at DESC
     LIMIT 5
");
$orders_stmt->execute([$current['id']]);
$recent_orders = $orders_stmt->fetchAll();
?>

<!-- ══ HERO ══ -->
<div class="page-hero">
    <div class="container">
        <div class="section-label">Account</div>
        <h1>My <span class="text-amber">Profile</span></h1>
        <p>Manage your personal information and view your recent orders.</p>
    </div>
</div>

<!-- ══ CONTENT ══ -->
<div class="page-content">
    <div class="container">
        <div class="row g-4">

            <!-- LEFT: Edit Form -->
            <div class="col-lg-7">
                <div class="contact-form-wrap">

                    <h5 class="profile-section-title">
                        <i class="bi bi-person-fill"></i> Personal Information
                    </h5>

                    <!-- Avatar Block -->
                    <div class="profile-avatar-block">
                        <div class="profile-avatar-initial">
                            <?= strtoupper(substr($profile->name ?? 'U', 0, 1)) ?>
                        </div>
                        <div>
                            <div class="profile-avatar-name"><?= htmlspecialchars($profile->name ?? '') ?></div>
                            <div class="profile-avatar-email"><?= htmlspecialchars($profile->email ?? '') ?></div>
                            <div class="profile-avatar-badge">
                                <i class="bi bi-patch-check-fill"></i> Verified Member
                            </div>
                        </div>
                    </div>

                    <!-- Alerts -->
                    <?php if ($success_msg): ?>
                        <div class="profile-alert profile-alert-success">
                            <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success_msg) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($error_msg): ?>
                        <div class="profile-alert profile-alert-error">
                            <i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($error_msg) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Form -->
 <!-- Form -->
                    <form method="POST">
                        <input type="hidden" name="update_profile" value="1">
                        <div class="row g-3">
 
                            <div class="col-sm-6">
                                <label class="form-label-sm">First Name <span class="profile-required">*</span></label>
                                <input type="text" name="first_name" class="form-control-custom"
                                       value="<?= htmlspecialchars($profile->first_name ?? '') ?>"
                                       placeholder="First name" required />
                            </div>
 
                            <div class="col-sm-6">
                                <label class="form-label-sm">Middle Name</label>
                                <input type="text" name="middle_name" class="form-control-custom"
                                       value="<?= htmlspecialchars($profile->middle_name ?? '') ?>"
                                       placeholder="Middle name (optional)" />
                            </div>
 
                            <div class="col-sm-6">
                                <label class="form-label-sm">Last Name <span class="profile-required">*</span></label>
                                <input type="text" name="last_name" class="form-control-custom"
                                       value="<?= htmlspecialchars($profile->last_name ?? '') ?>"
                                       placeholder="Last name" required />
                            </div>
 
                            <div class="col-sm-6">
                                <label class="form-label-sm">Email Address <span class="profile-required">*</span></label>
                                <input type="email" name="email" class="form-control-custom"
                                       value="<?= htmlspecialchars($profile->email_address ?? '') ?>"
                                       placeholder="your@email.com" required />
                            </div>
 
                            <div class="col-sm-6">
                                <label class="form-label-sm">Contact Number</label>
                                <input type="text" name="contact" class="form-control-custom"
                                       value="<?= htmlspecialchars($profile->contact_number ?? '') ?>"
                                       placeholder="e.g. 09XX XXX XXXX" />
                            </div>
 
                            <div class="col-sm-6">
                                <label class="form-label-sm">Delivery Address</label>
                                <input type="text" name="address" class="form-control-custom"
                                       value="<?= htmlspecialchars($profile->address ?? '') ?>"
                                       placeholder="Barangay, City, Province" />
                            </div>
 
                        </div>
 
                        <button type="submit" class="btn-submit profile-save-btn">
                            <i class="bi bi-save"></i> Save Changes
                        </button>
                    </form>
                </div>

                <!-- Recent Orders -->
                <div class="contact-form-wrap profile-orders-wrap">
                    <h5 class="profile-section-title">
                        <i class="bi bi-bag-check"></i> Recent Orders
                    </h5>

                    <?php if (empty($recent_orders)): ?>
                        <div class="profile-empty-orders">
                            <i class="bi bi-basket profile-empty-icon"></i>
                            <p>No orders yet. Start shopping!</p>
                            <a href="index.php?page=shop" class="btn-submit profile-shop-btn">
                                <i class="bi bi-shop"></i> Browse Shop
                            </a>
                        </div>
                    <?php else: ?>
                        <?php
                        $status_map = [
                            'pending_verification' => 'status-pending',
                            'confirmed'            => 'status-confirmed',
                            'delivered'            => 'status-delivered',
                            'cancelled'            => 'status-cancelled',
                        ];
                        $status_icons = [
                            'pending_verification' => 'bi-hourglass-split',
                            'confirmed'            => 'bi-check-circle-fill',
                            'delivered'            => 'bi-truck',
                            'cancelled'            => 'bi-x-circle-fill',
                        ];
                        foreach ($recent_orders as $order):
                            $st_class = $status_map[$order->order_status]   ?? 'status-default';
                            $st_icon  = $status_icons[$order->order_status] ?? 'bi-circle';
                        ?>
                        <div class="profile-order-row">
                            <div>
                                <div class="profile-order-id">Order #<?= htmlspecialchars($order->id) ?></div>
                                <div class="profile-order-meta">
                                    <?= date('M d, Y', strtotime($order->created_at)) ?>
                                    &nbsp;·&nbsp; <?= htmlspecialchars($order->payment_method ?? 'N/A') ?>
                                </div>
                            </div>
                            <div class="profile-order-right">
                                <span class="profile-order-amount">
                                    ₱<?= number_format($order->total_amount ?? 0, 2) ?>
                                </span>
                                <span class="profile-status-badge <?= $st_class ?>">
                                    <i class="bi <?= $st_icon ?>"></i>
                                    <?= ucwords(str_replace('_', ' ', $order->payment_status ?? 'pending')) ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <a href="index.php?page=orders" class="profile-view-all">
                            View all orders <i class="bi bi-arrow-right"></i>
                        </a>
                    <?php endif; ?>

                </div>
            </div>

            <!-- RIGHT: Summary Card -->
            <div class="col-lg-5">
                <div class="contact-info-card">
                    <h4>Account Summary</h4>
                    <p>Your current details and account status at a glance.</p>

                    <div class="contact-row">
                        <div class="contact-icon"><i class="bi bi-person-fill"></i></div>
                        <div>
                            <h6>Full Name</h6>
                            <p><?= htmlspecialchars($profile->first_name. ' '.$profile->last_name ?? 'Not set') ?></p>
                        </div>
                    </div>

                    <div class="contact-row">
                        <div class="contact-icon"><i class="bi bi-telephone-fill"></i></div>
                        <div>
                            <h6>Contact Number</h6>
                            <p><?= htmlspecialchars($profile->contact_number ?? 'Not set') ?></p>
                        </div>
                    </div>

                    <div class="contact-row">
                        <div class="contact-icon"><i class="bi bi-envelope-fill"></i></div>
                        <div>
                            <h6>Email Address</h6>
                            <p><?= htmlspecialchars($profile->email_address ?? 'Not set') ?></p>
                        </div>
                    </div>

                    <div class="contact-row">
                        <div class="contact-icon"><i class="bi bi-geo-alt-fill"></i></div>
                        <div>
                            <h6>Delivery Address</h6>
                            <p><?= htmlspecialchars($profile->address ?? 'Not set') ?></p>
                        </div>
                    </div>

                    <div class="contact-note">
                        <p>
                            <i class="bi bi-info-circle"></i>
                            Keep your contact number and address updated for smooth order delivery.
                        </p>
                    </div>

                    <a href="#" class="profile-card-link" onclick="document.getElementById('changePassModal').classList.add('show');return false;">
                        <i class="bi bi-lock-fill profile-card-link-icon"></i>
                        Change Password
                        <i class="bi bi-chevron-right profile-card-link-caret"></i>
                    </a>

                    <a href="index.php?page=my_order" class="profile-card-link">
                        <i class="bi bi-bag-fill profile-card-link-icon"></i>
                        My Orders
                        <i class="bi bi-chevron-right profile-card-link-caret"></i>
                    </a>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- ══ CHANGE PASSWORD MODAL ══ -->
<div class="modal-overlay" id="changePassModal">
    <div class="modal-box">
        <button class="modal-close" onclick="closePassModal()">&times;</button>
        <h4><i class="bi bi-lock-fill"></i> Change Password</h4>
        <p>Enter your current password, then choose a new one.</p>
 
        <div id="passAlert" class="profile-alert" style="display:none;"></div>
 
        <div style="margin-top:1.25rem;">
            <div class="chpass-field">
                <label class="form-label-sm">Current Password</label>
                <div class="chpass-input-wrap">
                    <input type="password" id="oldPassword" class="form-control-custom" placeholder="Your current password" />
                    <button type="button" class="chpass-toggle" onclick="togglePass('oldPassword', this)">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
 
            <div class="chpass-field">
                <label class="form-label-sm">New Password</label>
                <div class="chpass-input-wrap">
                    <input type="password" id="newPassword" class="form-control-custom" placeholder="At least 8 characters" />
                    <button type="button" class="chpass-toggle" onclick="togglePass('newPassword', this)">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
 
            <div class="chpass-field">
                <label class="form-label-sm">Confirm New Password</label>
                <div class="chpass-input-wrap">
                    <input type="password" id="confirmPassword" class="form-control-custom" placeholder="Repeat new password" />
                    <button type="button" class="chpass-toggle" onclick="togglePass('confirmPassword', this)">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
        </div>
 
        <button class="btn-submit" id="changePassBtn" onclick="submitChangePassword(<?= $current['id']; ?>)">
            <i class="bi bi-save"></i> Update Password
        </button>
    </div>
</div>


<script>
function closePassModal() {
    document.getElementById('changePassModal').classList.remove('show');
    document.getElementById('oldPassword').value     = '';
    document.getElementById('newPassword').value     = '';
    document.getElementById('confirmPassword').value = '';
    hidePassAlert();
}
 
function togglePass(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type      = 'text';
        icon.className  = 'bi bi-eye-slash';
    } else {
        input.type      = 'password';
        icon.className  = 'bi bi-eye';
    }
}
 
function showPassAlert(msg, type) {
    const el = document.getElementById('passAlert');
    el.className   = 'profile-alert ' + (type === 'success' ? 'profile-alert-success' : 'profile-alert-error');
    el.innerHTML   = '<i class="bi bi-' + (type === 'success' ? 'check-circle-fill' : 'exclamation-circle-fill') + '"></i> ' + msg;
    el.style.display = 'flex';
}
 
function hidePassAlert() {
    document.getElementById('passAlert').style.display = 'none';
}
 
function submitChangePassword(user_id) {
    const oldPass  = document.getElementById('oldPassword').value.trim();
    const newPass  = document.getElementById('newPassword').value.trim();
    const confPass = document.getElementById('confirmPassword').value.trim();

 
    if (!oldPass || !newPass || !confPass) {
        showPassAlert('Please fill in all fields.', 'error'); return;
    }
    if (newPass.length < 8) {
        showPassAlert('New password must be at least 8 characters.', 'error'); return;
    }
    if (newPass !== confPass) {
        showPassAlert('New passwords do not match.', 'error'); return;
    }
 
    const btn = document.getElementById('changePassBtn');
    btn.disabled  = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Updating...';
 
    $.ajax({
        url:      './shared/api.php',
        method:   'POST',
        dataType: 'json',
        data: { old_password: oldPass, new_password: newPass, login_id: user_id, key: "changePassword" },
        success: function(res) {


            console.log("response", res)

            if (res.success) {
                showPassAlert(res.message || 'Password updated successfully!', 'success');
                setTimeout(closePassModal, 1800);
            } else {
                showPassAlert(res.message || 'Something went wrong.', 'error');
            }
            btn.disabled  = false;
            btn.innerHTML = '<i class="bi bi-save"></i> Update Password';
        },
        error: function() {
            showPassAlert('Request failed. Please try again.', 'error');
            btn.disabled  = false;
            btn.innerHTML = '<i class="bi bi-save"></i> Update Password';
        }
    });
}
 
// Close modal when clicking backdrop
document.getElementById('changePassModal').addEventListener('click', function(e) {
    if (e.target === this) closePassModal();
});
</script>