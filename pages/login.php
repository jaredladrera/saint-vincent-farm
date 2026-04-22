<?php
// ══════════════════════════════════
// LOGIN PAGE — pages/login.php
// ══════════════════════════════════

require_once BASE_DIR . '/config/pdo_connection.php';
require_once BASE_DIR . '/shared/helpers.php';


function redirectRole($role, $fallback = 'contact') {
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        redirectByRole($role);
    exit;
}

$redirect = $_GET['redirect'] ?? 'contact';
$error    = '';

// ── Handle login form POST ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            $db  = new Connect();
            $pdo = $db->connection;

            $stmt = $pdo->prepare("
                SELECT * FROM user_profile WHERE email_address = :email LIMIT 1
            ");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(); // returns object (FETCH_OBJ is default)

            if (!$user || $user->password !== $password)  {
                $error = 'Incorrect email or password. Please try again.';
            } else {
                $initials = strtoupper(
                    substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)
                );

                $_SESSION['user'] = [
                    'id'     => $user->id,
                    'name'   => $user->first_name . ' ' . $user->last_name,
                    'email'  => $user->email_address,
                    'avatar' => $initials,
                    'role'   => $user->user_role,
                ];
                // ── Redirect based on role ──
                redirectByRole($user->user_role, $redirect);
            }

        } catch (PDOException $e) {
            $error = 'Login failed. Please try again. (' . $e->getMessage() . ')';
        }
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
