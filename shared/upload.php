<?php
// ══ PROCESS PAYMENT PROOF - AJAX HANDLER ══

session_start();
require_once './../config/pdo_connection.php';

header('Content-Type: application/json');

// ── Helpers ──────────────────────────────────────────────────────────────────
function jsonSuccess(array $data = []): void {
    echo json_encode(array_merge(['success' => true], $data));
    exit;
}

function jsonError(string $message, int $code = 400): void {
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

// ── Only accept POST ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed.', 405);
}

// ── Collect fields ────────────────────────────────────────────────────────────
$order_id       = trim($_POST['order_id']       ?? '');
$payment_method = trim($_POST['payment_method'] ?? '');

if (empty($order_id)) {
    jsonError('Missing order ID.');
}

// ── File validation ───────────────────────────────────────────────────────────
if (empty($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] === UPLOAD_ERR_NO_FILE) {
    jsonError('No file uploaded.');
}

$file  = $_FILES['payment_proof'];
$error = $file['error'];

if ($error !== UPLOAD_ERR_OK) {
    $upload_errors = [
        UPLOAD_ERR_INI_SIZE   => 'File exceeds server size limit.',
        UPLOAD_ERR_FORM_SIZE  => 'File exceeds form size limit.',
        UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
        UPLOAD_ERR_EXTENSION  => 'Upload blocked by server extension.',
    ];
    jsonError($upload_errors[$error] ?? 'Unknown upload error.');
}

// ── MIME type check (not just extension) ──────────────────────────────────────
$allowed_mime = ['image/jpeg', 'image/png', 'image/webp'];
$finfo        = finfo_open(FILEINFO_MIME_TYPE);
$mime         = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime, $allowed_mime, true)) {
    jsonError('Invalid file type. Only JPG, PNG, and WEBP are allowed.');
}

// ── Size check (5 MB) ─────────────────────────────────────────────────────────
$max_bytes = 5 * 1024 * 1024; // 5 MB
if ($file['size'] > $max_bytes) {
    jsonError('File is too large. Maximum size is 5MB.');
}

// ── Build destination path ────────────────────────────────────────────────────
$upload_dir = __DIR__ . '/images/payment_proofs/';

if (!is_dir($upload_dir)) {
    if (!mkdir($upload_dir, 0755, true)) {
        jsonError('Could not create upload directory.', 500);
    }
}

// Safe filename: order_id + timestamp + random + extension
$ext_map  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
$ext      = $ext_map[$mime];
$filename = 'proof_' . preg_replace('/[^a-zA-Z0-9_\-]/', '', $order_id)
          . '_' . time()
          . '_' . bin2hex(random_bytes(4))
          . '.' . $ext;

$destination = $upload_dir . $filename;

// ── Move file ─────────────────────────────────────────────────────────────────
if (!move_uploaded_file($file['tmp_name'], $destination)) {
    jsonError('Failed to save the uploaded file.', 500);
}

// ── Save to database ──────────────────────────────────────────────────────────
try {
    $db   = new Connect();
    $pdo  = $db->connection;

    $stmt = $pdo->prepare("
        UPDATE orders
        SET payment_proof     = :proof,
            payment_method    = :method,
            payment_status    = 'pending_verification',
            proof_uploaded_at = NOW()
        WHERE order_id = :order_id
    ");

    $stmt->execute([
        ':proof'    => 'images/payment_proofs/' . $filename,
        ':method'   => $payment_method,
        ':order_id' => $order_id,
    ]);

    if ($stmt->rowCount() === 0) {
        // File was uploaded but order wasn't found — clean up and bail
        @unlink($destination);
        jsonError('Order not found. Please contact support.', 404);
    }

} catch (PDOException $e) {
    // File uploaded but DB failed — clean up and return error
    @unlink($destination);
    jsonError('Database error: ' . $e->getMessage(), 500);
}

// ── Return success ────────────────────────────────────────────────────────────
jsonSuccess([
    'message'    => 'Payment proof uploaded successfully.',
    'order_id'   => $order_id,
    'file'       => 'images/payment_proofs/' . $filename,
]);