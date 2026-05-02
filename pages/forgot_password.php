<?php
// ══════════════════════════════════
// FORGOT PASSWORD COMPONENT
// Usage: require 'pages/forgot_password.php';
// Route: index.php?page=forgot_password
// ══════════════════════════════════

require_once BASE_DIR . '/config/pdo_connection.php';

require_once __DIR__ . './../config/PHPMailer/src/Exception.php';
require_once __DIR__ . './../config/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . './../config/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$step  = $_GET['step'] ?? 'email';
$error = '';

$db  = new Connect();
$pdo = $db->connection;

// ── STEP 1: Submit email → send existing password ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'email') {
    $email = trim($_POST['fp_email'] ?? '');

    if (empty($email)) {
        $error = 'Please enter your email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $stmt = $pdo->prepare("SELECT first_name, password FROM user_profile WHERE email_address = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $error = 'No account found with that email address.';
        } else {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'armsolenterprises@gmail.com';   // ← your email
                $mail->Password   = 'wgly qmub azbw ebjr';      // ← your app password
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;
                $mail->setFrom('armsolenterprises@gmail.com', 'St. Vincent Farm');
                $mail->addAddress($email, $user['name']);
                $mail->isHTML(true);
                $mail->Subject = 'Your Login Details — St. Vincent Farm';
                $mail->Body    = '
                    <div style="font-family:Arial,sans-serif;max-width:480px;margin:0 auto">
                        <div style="background:#1a3a1e;padding:20px 28px;border-radius:12px 12px 0 0">
                            <h2 style="color:#e8a020;margin:0">St. Vincent Farm</h2>
                        </div>
                        <div style="background:#fff;padding:28px;border:1px solid #e4ede4;border-top:none;border-radius:0 0 12px 12px">
                            <p>Hi <strong>' . htmlspecialchars($user['name']) . '</strong>,</p>
                            <p style="color:#555;margin:0 0 20px">Here are your login details as requested:</p>
                            <div style="background:#e8f5e9;border-radius:10px;padding:16px 20px;margin-bottom:20px">
                                <p style="margin:0 0 6px;font-size:0.85rem">Email: <strong>' . htmlspecialchars($email) . '</strong></p>
                                <p style="margin:0;font-size:0.85rem">Password: <strong>' . htmlspecialchars($user['password']) . '</strong></p>
                            </div>
                            <p style="color:#999;font-size:0.82rem">If you did not request this, please contact us immediately.</p>
                        </div>
                    </div>';
                $mail->send();

                header('Location: index.php?page=forgot_password&step=done');
                exit;

            } catch (Exception $e) {
                $error = 'Could not send email. Please try again later.';
            }
        }
    }
}

?>

<!-- ══════════════════════════════════
     FORGOT PASSWORD COMPONENT
══════════════════════════════════ -->
<div class="fp-page">
    <div class="fp-box">

        <!-- ── Body ── -->
        <div class="fp-body">

            <?php if (!empty($error)): ?>
            <div class="fp-alert">
                <i class="bi bi-exclamation-circle"></i>
                <?= $error ?>
            </div>
            <?php endif; ?>


            <?php if ($step === 'email'): ?>
            <!-- ════ STEP 1 — Enter Email ════ -->
            <div class="fp-icon">
                <i class="bi bi-envelope-open"></i>
            </div>
            <h2 class="fp-title">Forgot Password?</h2>
            <p class="fp-subtitle">Enter your account email and we'll send your password to your inbox.</p>

            <form method="POST" action="index.php?page=forgot_password&step=email">
                <div class="fp-field">
                    <label class="fp-label">Email Address</label>
                    <div class="fp-input-wrap">
                        <i class="bi bi-envelope"></i>
                        <input
                            type="email"
                            name="fp_email"
                            class="fp-input"
                            placeholder="juan@email.com"
                            value="<?= htmlspecialchars($_POST['fp_email'] ?? '') ?>"
                            required
                            autofocus
                        />
                    </div>
                </div>
                <button type="submit" class="fp-btn">
                    <i class="bi bi-send"></i> Send My Password
                </button>
            </form>

            <div class="fp-footer-link">
                Remember your password?
                <a href="index.php?page=login">Sign in</a>
            </div>


            <?php elseif ($step === 'done'): ?>
            <!-- ════ DONE ════ -->
            <div class="fp-success-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <h2 class="fp-title">Email Sent!</h2>
            <p class="fp-subtitle">Your password has been sent to your email address. Please check your inbox.</p>
            <a href="index.php?page=login" class="fp-btn">
                <i class="bi bi-box-arrow-in-right"></i> Back to Login
            </a>

            <?php endif; ?>

        </div>
    </div>
</div>