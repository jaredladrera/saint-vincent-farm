<?php
require_once '../../../config/pdo_connection.php';

try {
    $db = new Connect();
    $pdo = $db->connection;

    $id = $_POST['id'];

    $stmt = $pdo->prepare("
        SELECT 
            o.id,
            o.order_status,
            o.total_amount,
            o.created_at,

            u.first_name,
            u.last_name,
            u.email_address,
            u.contact_number,
            u.address,

            oi.quantity,
            oi.price,

            p.name

        FROM orders o
        JOIN user_profile u ON u.id = o.user_id
        JOIN order_items oi ON oi.order_id = o.id
        JOIN livestock p ON p.id = oi.product_id

        WHERE o.id = ?
    ");

    $stmt->execute([$id]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (PDOException $e) {
    echo json_encode([]);
}
