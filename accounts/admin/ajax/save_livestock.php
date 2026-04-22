<?php
// ══════════════════════════════════
// AJAX — ajax/save_livestock.php
// ══════════════════════════════════
require_once '../../../config/pdo_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$action       = trim($_POST['action']       ?? 'insert');
$id           = intval($_POST['id']         ?? 0);
$name         = trim($_POST['name']         ?? '');
$category     = trim($_POST['category']     ?? '');
$price        = trim($_POST['price']        ?? '');
$stock        = trim($_POST['stock']        ?? '');
$is_vaccinated   = trim($_POST['is_vaccinated']   ?? 'No');
$health_score = trim($_POST['health_score'] ?? '');
$notes        = trim($_POST['notes']        ?? '');

// ── Server-side Validation (skip for fetch) ──
if (in_array($action, ['insert', 'update'])) {
    if (!$name) {
        echo json_encode(['success' => false, 'message' => 'Livestock name is required.']);
        exit;
    }
    if (!is_numeric($price) || $price < 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid price.']);
        exit;
    }
    if (!is_numeric($stock) || $stock < 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid stock quantity.']);
        exit;
    }
    if ($action === 'update' && $id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid livestock ID for update.']);
        exit;
    }
}

try {
    $db  = new Connect();
    $pdo = $db->connection;

    // ── INSERT ──
    if ($action === 'insert') {
        $stmt = $pdo->prepare("
            INSERT INTO livestock 
                (name, category, price, quantity, is_vaccinated, health_score, condition_notes, date_created)
            VALUES 
                (:name, :category, :price, :quantity, :is_vaccinated, :health_score, :condition_notes, CURDATE())
        ");
        $stmt->execute([
            ':name'         => $name,
            ':category'     => $category,
            ':price'        => $price,
            ':quantity'        => $stock,
            ':is_vaccinated'   => $is_vaccinated,
            ':health_score' => $health_score,
            ':condition_notes'        => $notes,
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Livestock added successfully.',
            'id'      => $pdo->lastInsertId(),
        ]);

    // ── UPDATE ──
    } elseif ($action === 'update') {
        $stmt = $pdo->prepare("
            UPDATE livestock SET
                name         = :name,
                category     = :category,
                price        = :price,
                quantity        = :quantity,
                is_vaccinated   = :is_vaccinated,
                health_score = :health_score,
                condition_notes        = :condition_notes
            WHERE id = :id
        ");
        $stmt->execute([
            ':name'         => $name,
            ':category'     => $category,
            ':price'        => $price,
            ':quantity'        => $stock,
            ':is_vaccinated'   => $is_vaccinated,
            ':health_score' => $health_score,
            ':condition_notes'        => $notes,
            ':id'           => $id,
        ]);
        echo json_encode([
            'success' => true,
            'message' => 'Livestock updated successfully.',
            'id'      => $id,
        ]);

    // ── DELETE ──
    } elseif ($action === 'delete') {
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM livestock WHERE id = :id");
        $stmt->execute([':id' => $id]);
        echo json_encode(['success' => true, 'message' => 'Livestock deleted successfully.']);

    } else {
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    }

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
    ]);
}