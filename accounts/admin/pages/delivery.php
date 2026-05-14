<?php
// ══════════════════════════════════
// DELIVERY RIDERS — pages/delivery_riders.php
// Route: index.php?page=delivery_riders
// ══════════════════════════════════

require_once '../../config/pdo_connection.php';

$order_list = [];

$db  = new Connect();
$pdo = $db->connection;

// ── CREATE ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
    $name         = trim($_POST['name']         ?? '');
    $description  = trim($_POST['description']  ?? '');
    $vehicle_type = trim($_POST['vehicle_type'] ?? '');

    $stmt = $pdo->prepare("INSERT INTO delivery_details (name, description, vehicle_type) VALUES (?, ?, ?)");
    $stmt->execute([$name, $description, $vehicle_type]);

    header('Location: index.php?page=delivery_details&success=created');
    exit;
}

// ── UPDATE ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update') {
    $id           = (int) $_POST['id'];
    $name         = trim($_POST['name']         ?? '');
    $description  = trim($_POST['description']  ?? '');
    $vehicle_type = trim($_POST['vehicle_type'] ?? '');

    $stmt = $pdo->prepare("UPDATE delivery_details SET name = ?, description = ?, vehicle_type = ? WHERE id = ?");
    $stmt->execute([$name, $description, $vehicle_type, $id]);

    header('Location: index.php?page=delivery&success=updated');
    exit;
}

// ── DELETE ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id   = (int) $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM delivery_details WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: index.php?page=delivery&success=deleted');
    exit;
}

// ── READ all riders ──
$stmt   = $pdo->query("SELECT * FROM delivery_details ORDER BY id DESC");
$riders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$success = $_GET['success'] ?? '';
?>

<!-- ── Toast notification ── -->
<?php if ($success): ?>
<div class="dr-toast" id="drToast">
    <?php if ($success === 'created'): ?>
        <i class="bi bi-check-circle-fill"></i> Rider added successfully.
    <?php elseif ($success === 'updated'): ?>
        <i class="bi bi-check-circle-fill"></i> Rider updated successfully.
    <?php elseif ($success === 'deleted'): ?>
        <i class="bi bi-trash3"></i> Rider deleted.
    <?php endif; ?>
</div>
<script>
    setTimeout(function () {
        var t = document.getElementById('drToast');
        if (t) t.classList.add('dr-toast-hide');
    }, 3000);
</script>
<?php endif; ?>


<!-- ══════════════════════════════════
     DELIVERY RIDERS TABLE
══════════════════════════════════ -->
<div class="dr-page">

    <!-- ── Header row ── -->
    <div class="dr-header">
        <div>
            <h2 class="dr-title"><i class="bi bi-bicycle"></i> Delivery Riders</h2>
            <p class="dr-subtitle">Manage your delivery drivers and their availability.</p>
        </div>
        <button class="dr-btn-add" onclick="drOpenAdd()">
            <i class="bi bi-plus-lg"></i> Add Rider
        </button>
    </div>

    <!-- ── Summary chips ── -->
    <div class="dr-chips">
        <div class="dr-chip">
            <span class="dr-chip-num"><?= count($riders) ?></span>
            <span class="dr-chip-label">Total Riders</span>
        </div>
    </div>

    <!-- ── Table card ── -->
    <div class="dr-card">
        <?php if (empty($riders)): ?>
        <div class="dr-empty">
            <i class="bi bi-bicycle"></i>
            <p>No riders found. Add your first delivery rider.</p>
            <button class="dr-btn-add" onclick="drOpenAdd()">
                <i class="bi bi-plus-lg"></i> Add Rider
            </button>
        </div>
        <?php else: ?>
        <div class="dr-table-wrap">
            <table class="dr-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Vehicle Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($riders as $i => $rider): ?>
                    <tr>
                        <td class="dr-td-num"><?= $rider['id'] ?></td>
                        <td>
                            <div class="dr-rider-name-cell">
                                <div class="dr-avatar">
                                    <?= strtoupper(substr($rider['name'], 0, 1)) ?>
                                </div>
                                <strong><?= htmlspecialchars($rider['name']) ?></strong>
                            </div>
                        </td>
                        <td class="dr-td-muted">
                            <?= htmlspecialchars($rider['description']) ?>
                        </td>
                        <td>
                            <div class="dr-vehicle-cell">
                                <?php
                                $icon = 'bi-bicycle';
                                $v    = strtolower($rider['vehicle_type']);
                                if (str_contains($v, 'motor') || str_contains($v, 'bike'))  $icon = 'bi-scooter';
                                if (str_contains($v, 'car')   || str_contains($v, 'van'))   $icon = 'bi-car-front';
                                if (str_contains($v, 'truck'))                               $icon = 'bi-truck';
                                ?>
                                <i class="bi <?= $icon ?>"></i>
                                <?= htmlspecialchars($rider['vehicle_type']) ?>
                            </div>
                        </td>
                        <td>
                            <div class="dr-actions">
                                <button
                                    class="dr-btn-edit"
                                    title="Edit"
                                    onclick="drOpenEdit(
                                        <?= $rider['id'] ?>,
                                        '<?= addslashes(htmlspecialchars($rider['name'])) ?>',
                                        '<?= addslashes(htmlspecialchars($rider['description'])) ?>',
                                        '<?= addslashes(htmlspecialchars($rider['vehicle_type'])) ?>'
                                    )">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button
                                    class="dr-btn-del"
                                    title="Delete"
                                    onclick="drConfirmDelete(<?= $rider['id'] ?>, '<?= addslashes(htmlspecialchars($rider['name'])) ?>')">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

</div>


<!-- ══════════════════════════════════
     ADD / EDIT MODAL
══════════════════════════════════ -->
<div class="dr-modal-overlay" id="drModal" onclick="drCloseOutside(event)">
    <div class="dr-modal">

        <div class="dr-modal-header">
            <h5 id="drModalTitle"><i class="bi bi-person-plus"></i> Add Rider</h5>
            <button class="dr-modal-close" onclick="drCloseModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form method="POST" action="index.php?page=delivery_riders" id="drForm">
            <input type="hidden" name="action" id="drAction" value="create" />
            <input type="hidden" name="id"     id="drId"     value="" />

            <div class="dr-modal-body">

                <div class="dr-form-row">
                    <div class="dr-field">
                        <label class="dr-label">Full Name <span class="dr-req">*</span></label>
                        <div class="dr-input-wrap">
                            <i class="bi bi-person"></i>
                            <input
                                type="text"
                                name="name"
                                id="drName"
                                class="dr-input"
                                placeholder="e.g. Juan dela Cruz"
                                required
                            />
                        </div>
                    </div>

                    <div class="dr-field">
                        <label class="dr-label">Vehicle Type <span class="dr-req">*</span></label>
                        <div class="dr-input-wrap">
                            <i class="bi bi-bicycle"></i>
                            <select name="vehicle_type" id="drVehicle" class="dr-input" required>
                                <option value="">Select vehicle...</option>
                                <option value="Bicycle">Bicycle</option>
                                <option value="Motorcycle">Motorcycle</option>
                                <option value="Car">Car</option>
                                <option value="Van">Van</option>
                                <option value="Truck">Truck</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="dr-field">
                    <label class="dr-label">Description</label>
                    <div class="dr-input-wrap">
                        <i class="bi bi-card-text"></i>
                        <input
                            type="text"
                            name="description"
                            id="drDescription"
                            class="dr-input"
                            placeholder="e.g. Covers Batangas City area"
                        />
                    </div>
                </div>

            </div>

            <div class="dr-modal-footer">
                <button type="button" class="dr-btn-cancel" onclick="drCloseModal()">
                    Cancel
                </button>
                <button type="submit" class="dr-btn-save" id="drSaveBtn">
                    <i class="bi bi-check-lg"></i> Save Rider
                </button>
            </div>
        </form>

    </div>
</div>


<!-- ══════════════════════════════════
     DELETE CONFIRM MODAL
══════════════════════════════════ -->
<div class="dr-modal-overlay" id="drDeleteModal" onclick="drCloseDeleteOutside(event)">
    <div class="dr-modal dr-modal-sm">
        <div class="dr-modal-header dr-modal-header-danger">
            <h5><i class="bi bi-exclamation-triangle"></i> Confirm Delete</h5>
            <button class="dr-modal-close" onclick="drCloseDelete()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="dr-modal-body">
            <p class="dr-delete-msg">
                Are you sure you want to delete rider <strong id="drDeleteName"></strong>?
                This action cannot be undone.
            </p>
        </div>
        <form method="POST" action="index.php?page=delivery_riders">
            <input type="hidden" name="action" value="delete" />
            <input type="hidden" name="id" id="drDeleteId" value="" />
            <div class="dr-modal-footer">
                <button type="button" class="dr-btn-cancel" onclick="drCloseDelete()">Cancel</button>
                <button type="submit" class="dr-btn-delete">
                    <i class="bi bi-trash3"></i> Yes, Delete
                </button>
            </div>
        </form>
    </div>
</div>


<script>
// ── Open Add modal ──
function drOpenAdd() {
    document.getElementById('drModalTitle').innerHTML = '<i class="bi bi-person-plus"></i> Add Rider';
    document.getElementById('drAction').value      = 'create';
    document.getElementById('drId').value          = '';
    document.getElementById('drName').value        = '';
    document.getElementById('drDescription').value = '';
    document.getElementById('drVehicle').value     = '';
    document.getElementById('drSaveBtn').innerHTML = '<i class="bi bi-plus-lg"></i> Add Rider';
    document.getElementById('drModal').classList.add('show');
}

// ── Open Edit modal ──
function drOpenEdit(id, name, description, vehicle) {
    document.getElementById('drModalTitle').innerHTML = '<i class="bi bi-pencil-square"></i> Edit Rider';
    document.getElementById('drAction').value      = 'update';
    document.getElementById('drId').value          = id;
    document.getElementById('drName').value        = name;
    document.getElementById('drDescription').value = description;
    document.getElementById('drVehicle').value     = vehicle;
    document.getElementById('drSaveBtn').innerHTML = '<i class="bi bi-check-lg"></i> Save Changes';
    document.getElementById('drModal').classList.add('show');
}

// ── Close modal ──
function drCloseModal() {
    document.getElementById('drModal').classList.remove('show');
}
function drCloseOutside(e) {
    if (e.target.id === 'drModal') drCloseModal();
}

// ── Delete confirm ──
function drConfirmDelete(id, name) {
    document.getElementById('drDeleteId').value          = id;
    document.getElementById('drDeleteName').textContent  = name;
    document.getElementById('drDeleteModal').classList.add('show');
}
function drCloseDelete() {
    document.getElementById('drDeleteModal').classList.remove('show');
}
function drCloseDeleteOutside(e) {
    if (e.target.id === 'drDeleteModal') drCloseDelete();
}

// ── Close modals on Escape ──
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') { drCloseModal(); drCloseDelete(); }
});
</script>