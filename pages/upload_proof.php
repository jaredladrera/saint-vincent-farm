<?php
// ══ UPLOAD PROOF OF PAYMENT PAGE ══
$order_id       = $_GET['inserted_id'] ?? '';
$full_name      = currentUser() ?? '';
$payment_method = $_GET['payment_mode'] ?? '';

$qr_map = [
    'GCash'         => ['img' => 'assets/images/qr/gcash_qr.jpeg',  'label' => 'GCash',        'name' => 'St. Vincent Farm', 'account' => '09XX XXX XXXX'],
    'Bank Transfer' => ['img' => 'assets/images/qr/bank_qr.png',   'label' => 'Bank Transfer', 'name' => 'St. Vincent Farm', 'account' => 'Acct No: XXXX-XXXX-XXXX'],
];
$qr = $qr_map[$payment_method] ?? null;


function uploadPaymentProof(array $file, string $order_id): array {


    $id = $_POST["order_id"];
    $payment_method = $_POST["payment_method"];

    // ── Validate file ─────────────────────────────────────────────────────────
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error occurred.'];
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'message' => 'File too large. Max 5MB.'];
    }

    $allowed_mime = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime         = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);

    if (!isset($allowed_mime[$mime])) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, WEBP allowed.'];
    }

    // ── Build unique filename ─────────────────────────────────────────────────
    // Format: proof_<order_id>_<Ymd_His>_<random6hex>.ext
    $ext      = $allowed_mime[$mime];
    $filename = sprintf('proof_%s_%s_%s.%s',
        preg_replace('/[^a-zA-Z0-9_-]/', '', $id),
        date('Ymd_His'),
        bin2hex(random_bytes(3)),
        $ext
    );

    // ── Move to folder ────────────────────────────────────────────────────────
    $upload_dir = __DIR__ . './../assets/images/proof/';

    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
        }
        
        if (!move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
            return ['success' => false, 'message' => 'Failed to save file.'];
        } 

        // echo "order id". $id;
        // echo "filename". $filename;

    // ── Save to database ──────────────────────────────────────────────────────
    try {
        $db   = new Connect(); 
        $stmt = $db->connection->prepare("
            UPDATE orders
            SET proof_of_payment = :proof
            WHERE id = :order_id
        ");
        $stmt->execute([
            ':proof'    => 'assets/images/proof/' . $filename,
            ':order_id' => $id,
        ]);


    } catch (PDOException $e) {
        @unlink($upload_dir . $filename); // remove file if DB fails
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
    }

    return ['success' => true, 'file' => $filename];
}

// ── Run ───────────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = uploadPaymentProof(
        $_FILES['payment_proof'] ?? [],
        $order_id
    );


    if ($result['success']) {
        header("Location: index.php?page=order_success&payment=" . urlencode($payment_method));
        exit;
    }


    echo json_encode($result);
    exit;
}

?>

<div class="page-hero">
    <div class="container">
        <div class="section-label">Payment</div>
        <h1>Upload <span class="text-amber">Proof</span></h1>
        <p>Scan the QR below to pay, then upload your screenshot to confirm your order.</p>
    </div>
</div>

<div class="page-content">
    <div class="container">
        <div class="row g-4 justify-content-center">

            <div class="col-lg-7">
                <div class="contact-form-wrap">

                    <h5 class="proof-section-title">
                        <i class="bi bi-upload"></i> Payment Proof Upload
                    </h5>

                    <?php if ($order_id): ?>
                        <p class="proof-order-ref">
                            Order Reference: <strong class="ref-id">#<?= htmlspecialchars($order_id) ?></strong>
                            <?php if ($payment_method): ?>
                                &nbsp;·&nbsp; <span class="ref-method"><?= htmlspecialchars($payment_method) ?></span>
                            <?php endif; ?>
                        </p>
                    <?php else: ?>
                        <p class="proof-order-ref">Please attach your payment screenshot below.</p>
                    <?php endif; ?>

                    <!-- QR Code Block -->
                    <?php if ($qr): ?>
                    <div class="qr-block">
                        <div class="qr-label">
                            <i class="bi bi-qr-code"></i> Scan to Pay via <?= htmlspecialchars($qr['label']) ?>
                        </div>
                        <img src="<?= htmlspecialchars($qr['img']) ?>"
                             alt="<?= htmlspecialchars($qr['label']) ?> QR Code"
                             class="qr-image" />
                        <div class="qr-name"><?= htmlspecialchars($qr['name']) ?></div>
                        <div class="qr-account"><?= htmlspecialchars($qr['account']) ?></div>
                    </div>
                    <?php elseif ($payment_method): ?>
                    <div class="proof-no-qr-notice">
                        <i class="bi bi-info-circle-fill proof-no-qr-icon"></i>
                        <span>
                            You selected <strong><?= htmlspecialchars($payment_method) ?></strong>.
                            No QR needed — just upload a photo of your order confirmation or note below.
                        </span>
                    </div>
                    <?php endif; ?>

                    <form id="proofForm" action="index.php?page=upload_proof" method="POST" enctype="multipart/form-data">
    
                        <?php if ($order_id): ?>
                            <input type="hidden" name="order_id" value="<?= htmlspecialchars($order_id) ?>">
                        <?php endif; ?>
                        <?php if ($payment_method): ?>
                            <input type="hidden" name="payment_method" value="<?= htmlspecialchars($payment_method) ?>">
                        <?php endif; ?>

                        <!-- Drop Zone -->
                        <div id="dropZone" class="proof-dropzone" onclick="document.getElementById('proofFile').click()">
                            <div id="dropContent">
                                <div class="proof-drop-icon">🧾</div>
                                <div class="proof-drop-title">Click or drag & drop your screenshot here</div>
                                <div class="proof-drop-hint">JPG, PNG, WEBP — max 5MB</div>
                            </div>
                            <div id="previewWrap" class="proof-preview-wrap">
                                <img id="previewImg" src="" alt="Preview" class="proof-preview-img" />
                                <div id="previewName" class="proof-preview-name"></div>
                                <button type="button" onclick="clearFile(event)" class="proof-remove-btn">
                                    Remove image
                                </button>
                            </div>
                        </div>

                        <input type="file" id="proofFile" name="payment_proof"
                               accept="image/jpeg,image/png,image/webp"
                               class="proof-file-input" required />


                        <div id="uploadError" class="proof-upload-error"></div>

                        <button type="submit" class="btn-submit" id="submitBtn">
                            <i class="bi bi-cloud-upload"></i> Submit Payment Proof
                        </button>

                    </form>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="col-lg-5">
                <div class="contact-info-card">
                    <h4>Payment Tips</h4>
                    <p>Make sure your screenshot clearly shows the following details.</p>

                    <div class="contact-row">
                        <div class="contact-icon"><i class="bi bi-check-circle-fill"></i></div>
                        <div>
                            <h6>Transaction Amount</h6>
                            <p>The full amount paid must be visible in the screenshot.</p>
                        </div>
                    </div>

                    <div class="contact-row">
                        <div class="contact-icon"><i class="bi bi-check-circle-fill"></i></div>
                        <div>
                            <h6>Reference Number</h6>
                            <p>Include the GCash or bank reference number for verification.</p>
                        </div>
                    </div>

                    <div class="contact-row">
                        <div class="contact-icon"><i class="bi bi-check-circle-fill"></i></div>
                        <div>
                            <h6>Date & Time</h6>
                            <p>The transaction date and time should be clearly visible.</p>
                        </div>
                    </div>

                    <div class="contact-row">
                        <div class="contact-icon"><i class="bi bi-check-circle-fill"></i></div>
                        <div>
                            <h6>Recipient Name</h6>
                            <p>Confirm the payment was sent to St. Vincent Farm.</p>
                        </div>
                    </div>

                    <div class="contact-note">
                        <p>Orders are confirmed within <strong>1–2 hours</strong> after payment verification. You'll receive a confirmation via SMS or email.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
const dropZone  = document.getElementById('dropZone');
const fileInput = document.getElementById('proofFile');
const preview   = document.getElementById('previewWrap');
const previewImg= document.getElementById('previewImg');
const previewNm = document.getElementById('previewName');
const dropCont  = document.getElementById('dropContent');
const errorBox  = document.getElementById('uploadError');
const MAX_SIZE  = 5 * 1024 * 1024;

function showPreview(file) {
    if (!file.type.startsWith('image/')) { showError('Please upload a valid image file (JPG, PNG, WEBP).'); return; }
    if (file.size > MAX_SIZE)            { showError('File is too large. Maximum size is 5MB.'); return; }
    hideError();
    const reader = new FileReader();
    reader.onload = e => {
        previewImg.src         = e.target.result;
        previewNm.textContent  = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
        dropCont.style.display = 'none';
        preview.style.display  = 'block';
    };
    reader.readAsDataURL(file);
}

function clearFile(e) {
    e.stopPropagation();
    fileInput.value        = '';
    previewImg.src         = '';
    preview.style.display  = 'none';
    dropCont.style.display = 'block';
    hideError();
}

function showError(msg) { errorBox.textContent = msg; errorBox.style.display = 'block'; }
function hideError()    { errorBox.style.display = 'none'; }

fileInput.addEventListener('change', () => { if (fileInput.files[0]) showPreview(fileInput.files[0]); });

dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('dragover'); });
dropZone.addEventListener('dragleave', ()  => { dropZone.classList.remove('dragover'); });
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    const file = e.dataTransfer.files[0];
    if (file) { const dt = new DataTransfer(); dt.items.add(file); fileInput.files = dt.files; showPreview(file); }
});

// ── Submit: validate then let the form POST normally ─────────────────────────
document.getElementById('proofForm').addEventListener('submit', function (e) {
    if (!fileInput.files[0]) {
        e.preventDefault();
        showError('Please upload your payment screenshot before submitting.');
    }
});
</script>