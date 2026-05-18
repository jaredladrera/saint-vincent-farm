<?php
require_once '../../../config/pdo_connection.php';

try {
    $db = new Connect();
    $pdo = $db->connection;

    $id = $_POST['id'];

    $stmt = $pdo->prepare("
        SELECT DISTINCT
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
            oi.product_name,
            dd.*
        FROM orders o
        JOIN user_profile u 
            ON u.id = o.user_id
        JOIN order_items oi  
            ON oi.order_id = o.id
        LEFT JOIN delivery_details dd
            ON dd.order_id = o.id
        WHERE o.id = ?
        ORDER BY oi.product_name;
    ");

    $stmt->execute([$id]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (PDOException $e) {
    echo json_encode([]);
}
