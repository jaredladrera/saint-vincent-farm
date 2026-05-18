<?php
require_once '../../../config/pdo_connection.php';

$db  = new Connect();
$pdo = $db->connection;

$action = $_POST['action'] ?? '';

$uploadDir = '../../../uploads/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

function uploadImage($file, $uploadDir) {
    if ($file['error'] === 0) {
        $filename = time() . '_' . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $uploadDir . $filename);
        return $filename;
    }
    return null;
}

try {

if ($action == 'insert') {

    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $imageName = uploadImage($_FILES['image'], $uploadDir);
    }

    $stmt = $pdo->prepare("
        INSERT INTO livestock 
        (name, category, price, quantity, is_vaccinated, health_score, condition_notes, product_type, sku, image, date_created)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $_POST['name'],
        $_POST['category'],
        $_POST['price'],
        $_POST['stock'],
        $_POST['is_vaccinated'],
        $_POST['health_score'],
        $_POST['notes'],
        $_POST['product_type'],
        $_POST['sku'],
        $imageName
    ]);

    echo json_encode(['success' => true]);
}

elseif ($action == 'update') {

    $sql = "UPDATE livestock SET 
        name=?, category=?, price=?, quantity=?, 
        is_vaccinated=?, health_score=?, condition_notes=?, product_type=?, sku=?";

    $params = [
        $_POST['name'],
        $_POST['category'],
        $_POST['price'],
        $_POST['stock'],
        $_POST['is_vaccinated'],
        $_POST['health_score'],
        $_POST['notes'],
        $_POST['product_type'],
        $_POST['sku'],
    ];

    if (!empty($_FILES['image']['name'])) {
        $imageName = uploadImage($_FILES['image'], $uploadDir);
        $sql .= ", image=?";
        $params[] = $imageName;
    }

    $sql .= " WHERE id=?";
    $params[] = $_POST['id'];

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['success' => true]);
}

elseif ($action == 'delete') {

    $stmt = $pdo->prepare("DELETE FROM livestock WHERE id=?");
    $stmt->execute([$_POST['id']]);

    echo json_encode(['success' => true]);
}

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}