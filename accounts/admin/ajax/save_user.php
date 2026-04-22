<?php
require_once '../../../config/pdo_connection.php';

header('Content-Type: application/json');

$db  = new Connect();
$pdo = $db->connection;

$action = $_POST['action'] ?? '';

try {

if ($action == 'insert') {

    $stmt = $pdo->prepare("
        INSERT INTO user_profile 
        (first_name, middle_name, last_name, contact_number, email_address, address, user_role, password, date_created)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $_POST['first_name'],
        $_POST['middle_name'],
        $_POST['last_name'],
        $_POST['contact'],
        $_POST['email'],
        $_POST['address'],
        $_POST['role'],
        password_hash($_POST['password'], PASSWORD_DEFAULT)
    ]);

    echo json_encode(['success' => true]);
}

elseif ($action == 'update') {

    $sql = "UPDATE user_profile SET 
        first_name=?,
        middle_name=?,
        last_name=?,
        contact_number=?,
        email_address=?,
        `address`=?,
        user_role=?";

    $params = [
        $_POST['first_name'],
        $_POST['middle_name'],
        $_POST['last_name'],
        $_POST['contact'],
        $_POST['email'],
        $_POST['address'],
        $_POST['role']
    ];

    if (!empty($_POST['password'])) {
        $sql .= ", password=?";
        $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    $sql .= " WHERE id=?";
    $params[] = $_POST['id'];

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['success' => true]);
}

elseif ($action == 'delete') {

    $stmt = $pdo->prepare("DELETE FROM user_profile WHERE id=?");
    $stmt->execute([$_POST['id']]);

    echo json_encode(['success' => true]);
}

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}