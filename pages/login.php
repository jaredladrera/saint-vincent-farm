<?php
// ══════════════════════════════════
// LOGIN PAGE — pages/login.php
// ══════════════════════════════════

// Where to go after login (default: contact/order page)
$redirect = $_GET['redirect'] ?? 'contact';

// ── Handle login form POST ──
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password'] ?? '';

    // ── Demo credential check ──
    // In production: query DB and use password_verify()
    // Example: SELECT * FROM users WHERE email = ? then password_verify($password, $row['password'])

    // Simulated registered users (replace with real DB query)
    $demo_users = [
        [
            'id'       => 1,
            'name'     => 'Juan dela Cruz',
            'email'    => 'juan@email.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT), // hashed
            'avatar'   => 'JD',
        ],
    ];

    $found = null;
    foreach ($demo_users as $u) {
        if ($u['email'] === $email && password_verify($password, $u['password'])) {
            $found = $u;
            break;
        }
    }

    if (!$email || !$password) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!$found) {
        $error = 'Incorrect email or password. Please try again.';
    } else {
        // ── Set session ──
        $_SESSION['user'] = [
            'id'     => $found['id'],
            'name'   => $found['name'],
            'email'  => $found['email'],
            'avatar' => $found['avatar'],
        ];
        // Redirect to intended page
        header('Location: index.php?page=' . urlencode($redirect));
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
                <h2>Welcome back<br>to the farm.</h2>
                <p>Log in to place your order, track your delivery, and manage your purchases with ease.</p>
                <div class="auth-brand-features">
                    <div class="auth-feat"><i class="bi bi-shield-check"></i> Secure checkout</div>
                    <div class="auth-feat"><i class="bi bi-truck"></i> Delivery tracking</div>
                    <div class="auth-feat"><i class="bi bi-receipt"></i> Order history</div>
                </div>
            </div>
            <div class="auth-brand-art">🐷🐐🐔</div>
        </div>

        <!-- Right: Login Form -->
        <div class="auth-form-panel">
            <div class="auth-form-wrap">

                <div class="auth-form-header">
                    <h3>Sign in</h3>
                    <p>Don't have an account? <a href="index.php?page=register&redirect=<?= urlencode($redirect) ?>">Create one</a></p>
                </div>

                <?php if ($error): ?>
                <div class="auth-alert auth-alert-error">
                    <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <?php if (isset($_GET['registered'])): ?>
                <div class="auth-alert auth-alert-success">
                    <i class="bi bi-check-circle"></i> Account created! Please log in.
                </div>
                <?php endif; ?>

                <form method="POST" action="index.php?page=login&redirect=<?= urlencode($redirect) ?>">

                    <div class="auth-field">
                        <label class="auth-label">Email Address</label>
                        <div class="auth-input-wrap">
                            <i class="bi bi-envelope"></i>
                            <input type="email" name="email" class="auth-input"
                                   placeholder="juan@email.com"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                   required autofocus />
                        </div>
                    </div>

                    <div class="auth-field">
                        <label class="auth-label">
                            Password
                            <a href="#" class="auth-forgot" tabindex="-1">Forgot password?</a>
                        </label>
                        <div class="auth-input-wrap">
                            <i class="bi bi-lock"></i>
                            <input type="password" name="password" id="loginPassword"
                                   class="auth-input" placeholder="Enter your password" required />
                            <button type="button" class="toggle-pw" onclick="togglePassword('loginPassword', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="auth-remember">
                        <label class="auth-check-label">
                            <input type="checkbox" name="remember" />
                            <span>Remember me</span>
                        </label>
                    </div>

                    <button type="submit" class="auth-submit-btn">
                        <i class="bi bi-box-arrow-in-right"></i> Sign In
                    </button>

                </form>

                <div class="auth-demo-hint">
                    <i class="bi bi-info-circle"></i>
                    Demo: <strong>juan@email.com</strong> / <strong>password123</strong>
                </div>

                <div class="auth-divider"><span>or</span></div>

                <a href="index.php?page=register&redirect=<?= urlencode($redirect) ?>" class="auth-alt-btn">
                    <i class="bi bi-person-plus"></i> Create New Account
                </a>

            </div>
        </div>

    </div>
</div>
