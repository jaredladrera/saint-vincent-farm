<?php
// ══════════════════════════════════
// REGISTER PAGE — pages/register.php
// ══════════════════════════════════

$redirect = $_GET['redirect'] ?? 'contact';
$error    = '';
$success  = false;

// ── Handle registration form POST ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email']     ?? '');
    $phone     = trim($_POST['phone']     ?? '');
    $password  = $_POST['password']  ?? '';
    $confirm   = $_POST['confirm_pw'] ?? '';

    // Validation
    if (!$full_name || !$email || !$password || !$confirm) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // ── In production: INSERT into users table ──
        // $hashed = password_hash($password, PASSWORD_DEFAULT);
        // $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
        // $stmt->execute([$full_name, $email, $phone, $hashed]);

        // For demo: auto-login after register
        $initials = '';
        $parts = explode(' ', $full_name);
        foreach ($parts as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
        }
        $initials = substr($initials, 0, 2);

        $_SESSION['user'] = [
            'id'     => rand(100, 999), // demo ID (use real DB id in production)
            'name'   => $full_name,
            'email'  => $email,
            'avatar' => $initials,
        ];

        // Redirect to intended page
        header('Location: index.php?page=' . urlencode($redirect) . '&registered=1');
        exit;
    }
}
?>

<div class="auth-page">
    <div class="auth-split">

        <!-- Left: Branding Panel -->
        <div class="auth-brand">
            <div class="auth-brand-inner">
                <a href="index.php?page=home" class="auth-logo">
                    <div class="logo-mark">SV</div>
                    <span>St. Vincent Farm</span>
                </a>
                <h2>Join St. Vincent<br>Farm today.</h2>
                <p>Create a free account to order livestock, track deliveries, and manage your purchases all in one place.</p>
                <div class="auth-brand-features">
                    <div class="auth-feat"><i class="bi bi-person-check"></i> Free account</div>
                    <div class="auth-feat"><i class="bi bi-bag-heart"></i> Easy ordering</div>
                    <div class="auth-feat"><i class="bi bi-bell"></i> Order updates</div>
                </div>
            </div>
            <div class="auth-brand-art">🌿🐷🐐</div>
        </div>

        <!-- Right: Register Form -->
        <div class="auth-form-panel">
            <div class="auth-form-wrap">

                <div class="auth-form-header">
                    <h3>Create Account</h3>
                    <p>Already have an account? <a href="index.php?page=login&redirect=<?= urlencode($redirect) ?>">Sign in</a></p>
                </div>

                <?php if ($error): ?>
                <div class="auth-alert auth-alert-error">
                    <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="index.php?page=register&redirect=<?= urlencode($redirect) ?>">

                    <div class="auth-field">
                        <label class="auth-label">Full Name <span class="req">*</span></label>
                        <div class="auth-input-wrap">
                            <i class="bi bi-person"></i>
                            <input type="text" name="full_name" class="auth-input"
                                   placeholder="Juan dela Cruz"
                                   value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"
                                   required autofocus />
                        </div>
                    </div>

                    <div class="auth-field">
                        <label class="auth-label">Email Address <span class="req">*</span></label>
                        <div class="auth-input-wrap">
                            <i class="bi bi-envelope"></i>
                            <input type="email" name="email" class="auth-input"
                                   placeholder="juan@email.com"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                   required />
                        </div>
                    </div>

                    <div class="auth-field">
                        <label class="auth-label">Phone Number</label>
                        <div class="auth-input-wrap">
                            <i class="bi bi-telephone"></i>
                            <input type="tel" name="phone" class="auth-input"
                                   placeholder="09XX XXX XXXX"
                                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" />
                        </div>
                    </div>

                    <div class="auth-row">
                        <div class="auth-field">
                            <label class="auth-label">Password <span class="req">*</span></label>
                            <div class="auth-input-wrap">
                                <i class="bi bi-lock"></i>
                                <input type="password" name="password" id="regPassword"
                                       class="auth-input" placeholder="Min. 6 characters" required />
                                <button type="button" class="toggle-pw" onclick="togglePassword('regPassword', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="auth-field">
                            <label class="auth-label">Confirm Password <span class="req">*</span></label>
                            <div class="auth-input-wrap">
                                <i class="bi bi-lock-fill"></i>
                                <input type="password" name="confirm_pw" id="regConfirm"
                                       class="auth-input" placeholder="Repeat password" required />
                                <button type="button" class="toggle-pw" onclick="togglePassword('regConfirm', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Password strength indicator -->
                    <div class="pw-strength-wrap" id="pwStrengthWrap" style="display:none">
                        <div class="pw-strength-bar">
                            <div class="pw-strength-fill" id="pwStrengthFill"></div>
                        </div>
                        <span class="pw-strength-label" id="pwStrengthLabel"></span>
                    </div>

                    <div class="auth-terms">
                        <label class="auth-check-label">
                            <input type="checkbox" name="agree_terms" required />
                            <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></span>
                        </label>
                    </div>

                    <button type="submit" class="auth-submit-btn">
                        <i class="bi bi-person-plus"></i> Create Account
                    </button>

                </form>

                <div class="auth-divider"><span>or</span></div>

                <a href="index.php?page=login&redirect=<?= urlencode($redirect) ?>" class="auth-alt-btn">
                    <i class="bi bi-box-arrow-in-right"></i> Sign In Instead
                </a>

            </div>
        </div>

    </div>
</div>

<script>
// Password strength meter
document.getElementById('regPassword').addEventListener('input', function() {
    const val  = this.value;
    const wrap = document.getElementById('pwStrengthWrap');
    const fill = document.getElementById('pwStrengthFill');
    const lbl  = document.getElementById('pwStrengthLabel');
    if (!val) { wrap.style.display = 'none'; return; }
    wrap.style.display = 'flex';
    let score = 0;
    if (val.length >= 6)  score++;
    if (val.length >= 10) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const levels = [
        { w: '20%',  color: '#e24b4a', label: 'Very weak' },
        { w: '40%',  color: '#e8a020', label: 'Weak' },
        { w: '60%',  color: '#f5c430', label: 'Fair' },
        { w: '80%',  color: '#4caf50', label: 'Strong' },
        { w: '100%', color: '#2d6a35', label: 'Very strong' },
    ];
    const lvl = levels[Math.min(score - 1, 4)] || levels[0];
    fill.style.width      = lvl.w;
    fill.style.background = lvl.color;
    lbl.textContent       = lvl.label;
    lbl.style.color       = lvl.color;
});
</script>
