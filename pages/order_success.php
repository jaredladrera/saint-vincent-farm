<!-- ══ ORDER SUCCESS PAGE ══ -->
<?php
// $order_id       = $_GET['order_id']   ?? $_SESSION['last_order_id'] ?? '';
$full_name      = currentUser() ?? 'Valued Customer';
$payment_method = $_GET['payment'] ?? $_SESSION['payment_method'] ?? '';
?>

<div class="thankyou-wrap">
    <div class="thankyou-card">

        <!-- Animated checkmark -->
        <div class="thankyou-icon-wrap">
            <div class="thankyou-icon-ring"></div>
            <div class="thankyou-icon">
                <i class="bi bi-check-lg"></i>
            </div>
        </div>

        <div class="thankyou-label">Order Confirmed</div>
        <h1 class="thankyou-title">Thank You, <span class="text-amber"><?= htmlspecialchars(explode(' ', $full_name['name'])[0]) ?>!</span></h1>
        <p class="thankyou-message">
            Your order has been successfully submitted. We'll review your payment and get in touch with you shortly.
        </p>


        <?php if ($payment_method): ?>
        <div class="thankyou-payment-badge">
            <i class="bi bi-credit-card"></i> <?= htmlspecialchars($payment_method) ?>
        </div>
        <?php endif; ?>

        <div class="thankyou-divider"></div>

        <!-- Countdown -->
        <p class="thankyou-redirect-text">
            Redirecting you to home in <span id="countdown" class="thankyou-countdown">5</span> seconds...
        </p>
        <div class="thankyou-progress-bar">
            <div class="thankyou-progress-fill" id="progressFill"></div>
        </div>

        <a href="index.php" class="btn-submit thankyou-home-btn">
            <i class="bi bi-house"></i> Go to Home Now
        </a>

    </div>
</div>

<script>
let seconds = 5;
const countdownEl   = document.getElementById('countdown');
const progressFill  = document.getElementById('progressFill');

// Start progress bar animation
progressFill.style.transition = `width ${seconds}s linear`;
setTimeout(() => { progressFill.style.width = '100%'; }, 50);

const timer = setInterval(() => {
    seconds--;
    countdownEl.textContent = seconds;
    if (seconds <= 0) {
        clearInterval(timer);
        window.location.href = 'index.php';
    }
}, 1000);
</script>