<?php
require_once '../../../config/pdo_connection.php';

header('Content-Type: application/json');

$db  = new Connect();
$pdo = $db->connection;

$stmt = $pdo->prepare("
    UPDATE orders 
    SET order_status=?, updated_at=NOW() 
    WHERE id=?
");

$stmt->execute([
    $_POST['status'],
    $_POST['id']
]);

echo json_encode(['success' => true]);