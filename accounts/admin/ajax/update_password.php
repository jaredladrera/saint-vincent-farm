<?php
session_start();
require_once '../../../config/pdo_connection.php';

$db  = new Connect();
$pdo = $db->connection;

header('Content-Type: application/json');

// AUTH CHECK
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user']['id'];

// INPUTS
$current = $_POST['current_password'] ?? '';
$new     = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

// VALIDATION
if (!$current || !$new || !$confirm) {
    echo json_encode(['success' => false, 'message' => 'All fields required']);
    exit;
}

if ($new !== $confirm) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
    exit;
}

try {

    // ✅ GET CURRENT PASSWORD (PDO WAY)
    $stmt = $pdo->prepare("SELECT password FROM user_profile WHERE id = ?");
    $stmt->execute([$user_id]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    // ✅ VERIFY PASSWORD (supports old plaintext fallback)
    if (
        !password_verify($current, $user['password']) &&
        $current !== $user['password']
    ) {
        echo json_encode(['success' => false, 'message' => 'Wrong current password']);
        exit;
    }

    // ✅ HASH NEW PASSWORD
    $newHash = password_hash($new, PASSWORD_DEFAULT);

    // ✅ UPDATE PASSWORD (PDO WAY)
    $stmt = $pdo->prepare("UPDATE user_profile SET password = ? WHERE id = ?");
    $success = $stmt->execute([$newHash, $user_id]);

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Password updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed']);
    }

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}