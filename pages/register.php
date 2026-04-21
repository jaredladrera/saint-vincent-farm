<?php

require "./shared/crud.php";
// ══════════════════════════════════
// REGISTER PAGE — pages/register.php
// ══════════════════════════════════


// Import PDO connection
require_once "./config/pdo_connection.php";
require_once "./shared/helpers.php";

$redirect = $_GET['redirect'] ?? 'contact';
$error    = '';
$success  = false;

// ── Handle registration form POST ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name     = trim($_POST['first_name']     ?? '');
    $middle_name    = trim($_POST['middle_name']    ?? '');
    $last_name      = trim($_POST['last_name']      ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $email_address  = trim($_POST['email_address']  ?? '');
    $purok          = trim($_POST['purok']          ?? '');
    $barangay       = trim($_POST['barangay']       ?? '');
    $citymun        = trim($_POST['citymun']        ?? '');
    $province       = trim($_POST['province']       ?? '');
    $password       = $_POST['password']   ?? '';
    $confirm        = $_POST['confirm_pw'] ?? '';

    // Validation
    if (!$first_name || !$last_name || !$email_address || !$password || !$confirm) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $db  = new Connect();
            $pdo = $db->connection;

            // Check if email already exists
            $checkStmt = $pdo->prepare("SELECT id FROM user_profile WHERE email_address = :email");
            $checkStmt->execute([':email' => $email_address]);

            if ($checkStmt->fetch()) {
                $error = 'An account with that email address already exists.';
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("
                    INSERT INTO user_profile
                        (first_name, middle_name, last_name, contact_number, email_address,
                         purok, barangay, citymun, province, user_role, date_created, password)
                    VALUES
                        (:first_name, :middle_name, :last_name, :contact_number, :email_address,
                         :purok, :barangay, :citymun, :province, 'user', CURDATE(), :password)
                ");

                $stmt->execute([
                    ':first_name'     => $first_name,
                    ':middle_name'    => $middle_name,
                    ':last_name'      => $last_name,
                    ':contact_number' => $contact_number,
                    ':email_address'  => $email_address,
                    ':purok'          => $purok,
                    ':barangay'       => $barangay,
                    ':citymun'        => $citymun,
                    ':province'       => $province,
                    ':password'       => $hashed,
                ]);

                $new_id   = $pdo->lastInsertId();
                $initials = strtoupper(substr($first_name, 0, 1) . substr($last_name, 0, 1));

                $_SESSION['user'] = [
                    'id'     => $new_id,
                    'name'   => $first_name . ' ' . $last_name,
                    'email'  => $email_address,
                    'avatar' => $initials,
                    'role'   => 'user',
                ];


                // Redirect based on role
                redirectByRole($_SESSION['user']['role'], $redirect);
                // header('Location: index.php?page=' . urlencode($redirect) . '&registered=1');
                exit;
            }

        } catch (PDOException $e) {
            $error = 'Registration failed. Please try again. (' . $e->getMessage() . ')';
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

                    <!-- ── Name Fields ── -->
                    <div class="auth-row">
                        <div class="auth-field">
                            <label class="auth-label">First Name <span class="req">*</span></label>
                            <div class="auth-input-wrap">
                                <i class="bi bi-person"></i>
                                <input type="text" name="first_name" class="auth-input"
                                       placeholder="Juan"
                                       value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>"
                                       required autofocus />
                            </div>
                        </div>
                        <div class="auth-field">
                            <label class="auth-label">Middle Name</label>
                            <div class="auth-input-wrap">
                                <i class="bi bi-person"></i>
                                <input type="text" name="middle_name" class="auth-input"
                                       placeholder="Santos"
                                       value="<?= htmlspecialchars($_POST['middle_name'] ?? '') ?>" />
                            </div>
                        </div>
                    </div>

                    <div class="auth-field">
                        <label class="auth-label">Last Name <span class="req">*</span></label>
                        <div class="auth-input-wrap">
                            <i class="bi bi-person-fill"></i>
                            <input type="text" name="last_name" class="auth-input"
                                   placeholder="dela Cruz"
                                   value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>"
                                   required />
                        </div>
                    </div>

                    <!-- ── Contact & Email ── -->
                    <div class="auth-row">
                        <div class="auth-field">
                            <label class="auth-label">Contact Number</label>
                            <div class="auth-input-wrap">
                                <i class="bi bi-telephone"></i>
                                <input type="tel" name="contact_number" class="auth-input"
                                       placeholder="09XX XXX XXXX"
                                       value="<?= htmlspecialchars($_POST['contact_number'] ?? '') ?>" />
                            </div>
                        </div>
                        <div class="auth-field">
                            <label class="auth-label">Email Address <span class="req">*</span></label>
                            <div class="auth-input-wrap">
                                <i class="bi bi-envelope"></i>
                                <input type="email" name="email_address" class="auth-input"
                                       placeholder="juan@email.com"
                                       value="<?= htmlspecialchars($_POST['email_address'] ?? '') ?>"
                                       required />
                            </div>
                        </div>
                    </div>

                    <!-- ── Address Fields ── -->
                    <div class="auth-row">
                        <div class="auth-field">
                            <label class="auth-label">Purok</label>
                            <div class="auth-input-wrap">
                                <i class="bi bi-geo"></i>
                                <input type="text" name="purok" class="auth-input"
                                       placeholder="Purok 1"
                                       value="<?= htmlspecialchars($_POST['purok'] ?? '') ?>" />
                            </div>
                        </div>
                        <div class="auth-field">
                            <label class="auth-label">Barangay</label>
                            <div class="auth-input-wrap">
                                <i class="bi bi-geo"></i>
                                <input type="text" name="barangay" class="auth-input"
                                       placeholder="Barangay"
                                       value="<?= htmlspecialchars($_POST['barangay'] ?? '') ?>" />
                            </div>
                        </div>
                    </div>

                    <div class="auth-row">
                        <div class="auth-field">
                            <label class="auth-label">City / Municipality</label>
                            <div class="auth-input-wrap">
                                <i class="bi bi-building"></i>
                                <input type="text" name="citymun" class="auth-input"
                                       placeholder="City or Municipality"
                                       value="<?= htmlspecialchars($_POST['citymun'] ?? '') ?>" />
                            </div>
                        </div>
                        <div class="auth-field">
                            <label class="auth-label">Province</label>
                            <div class="auth-input-wrap">
                                <i class="bi bi-map"></i>
                                <input type="text" name="province" class="auth-input"
                                       placeholder="Province"
                                       value="<?= htmlspecialchars($_POST['province'] ?? '') ?>" />
                            </div>
                        </div>
                    </div>

                    <!-- ── Password ── -->
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


<!-- <div class="sample">
    <input type="text" class="form-control" id="test">
    <button class="btn btn-primary" onclick="sampleAjax()">Test ajax</button>
</div> -->

<script>

// sampleAjax = () => {
//     test = $('#test').val();

//     alert(`test ${test}`);
// }


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