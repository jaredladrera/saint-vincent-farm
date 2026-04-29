<?php
session_start();
require_once '../../../config/pdo_connection.php';

header('Content-Type: application/json');

$db  = new Connect();
$pdo = $db->connection;

// AUTH CHECK
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user']['id'];

// GET DATA
$first_name = trim($_POST['first_name'] ?? '');
$last_name  = trim($_POST['last_name'] ?? '');
$email      = trim($_POST['email'] ?? '');
$contact    = trim($_POST['contact_number'] ?? '');
$address    = trim($_POST['address'] ?? '');

// VALIDATION
if (!$first_name || !$email) {
    echo json_encode(['success' => false, 'message' => 'Required fields missing']);
    exit;
}


try {

    // ✅ PDO UPDATE
    $stmt = $pdo->prepare("
        UPDATE user_profile 
        SET first_name = ?, 
            last_name = ?, 
            email_address = ?, 
            contact_number = ?, 
            address = ?
        WHERE id = ?
    ");

    $success = $stmt->execute([
        $first_name,
        $last_name,
        $email,
        $contact,
        $address,
        $user_id
    ]);

    if ($success) {

        // ✅ UPDATE SESSION
        $_SESSION['user']['first_name'] = $first_name;
        $_SESSION['user']['last_name']  = $last_name;
        $_SESSION['user']['name']       = $first_name . ' ' . $last_name;
        $_SESSION['user']['email']      = $email;
        $_SESSION['user']['contact_number'] = $contact;
        $_SESSION['user']['address'] = $address;

        echo json_encode(['success' => true, 'message' => 'Profile updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed']);
    }

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}