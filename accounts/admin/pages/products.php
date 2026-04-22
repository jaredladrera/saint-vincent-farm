<?php // pages/products.php
require_once '../../config/pdo_connection.php';

// ── Fetch all livestock from DB ──
$livestock_list = [];
try {
    $db   = new Connect();
    $pdo  = $db->connection;
    $stmt = $pdo->query("SELECT * FROM livestock ORDER BY date_created DESC");
    $livestock_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $livestock_list = [];
}

// ── Stock status helper ──
function getStockStatus(int $stock): string {
    if ($stock === 0)  return 'Out of Stock';
    if ($stock <= 10)  return 'Low Stock';
    return 'In Stock';
}
?>

<!-- Page Header -->
<div class="page-header mb-4">
    <div>
        <h5 class="page-heading mb-1">Livestocks</h5>
        <p class="text-muted mb-0 small">Manage your livestock inventory</p>
    </div>
    <button class="btn btn-green" id="addLivestockBtn" data-bs-toggle="modal" data-bs-target="#addProductModal">
        <i class="bi bi-plus-lg me-1"></i> Add Livestock
    </button>
</div>

<!-- Filter Bar -->
<div class="card-panel mb-4">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-5">
            <div class="search-bar">
                <i class="bi bi-search"></i>
                <input type="text" id="productSearch" class="form-control" placeholder="Search livestock…" />
            </div>
        </div>
        <div class="col-6 col-md-3">
            <select class="form-select" id="categoryFilter">
                <option value="">All Categories</option>
                <option>Cattle</option>
                <option>Swine</option>
                <option>Sheep</option>
                <option>Goats</option>
                <option>Poultry</option>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <select class="form-select" id="stockFilter">
                <option value="">All Status</option>
                <option>In Stock</option>
                <option>Low Stock</option>
                <option>Out of Stock</option>
            </select>
        </div>
    </div>
</div>

<!-- Products Table -->
<div class="card-panel">
    <div class="table-responsive">
        <table class="table admin-table" id="productsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Livestock Name</th>
                    <th>Category</th>
                    <th>Price per kilo</th>
                    <th>Stock</th>
                    <th>Vaccinated</th>
                    <th>Health Score</th>
                    <th>Condition Notes</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="livestockTableBody">
                <?php if (empty($livestock_list)): ?>
                <tr id="emptyRow">
                    <td colspan="10" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                        No livestock records found.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($livestock_list as $p):
                    $status     = getStockStatus((int)$p['quantity']);
                    $statusSlug = strtolower(str_replace(' ', '-', $status));
                ?>
                <tr  data-name="<?= strtolower(htmlspecialchars($p['name'])) ?>"
                     data-category="<?= htmlspecialchars($p['category']) ?>"
                     data-status="<?= $status ?>">
                    <td class="fw-semibold text-green small"><?= $p['id'] ?></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><span class="category-tag"><?= htmlspecialchars($p['category']) ?></span></td>
                    <td class="fw-semibold">₱<?= number_format($p['price'], 2) ?></td>
                    <td><?= $p['quantity'] ?></td>
                    <td 
                        data-vaccinated="<?= $p['is_vaccinated'] ?>">
                        <?= $p['is_vaccinated'] ? 'Yes' : 'No' ?>
                    </td>
                    <td><?= htmlspecialchars($p['health_score']) ?></td>
                    <td><?= htmlspecialchars($p['condition_notes']) ?></td>
                    <td><span class="status-badge status-<?= $statusSlug ?>"><?= $status ?></span></td>
                    <td>
                        <div class="action-btns">
                            <button class="action-btn edit editLivestockBtn"
                                title="Edit"
                                data-id="<?= $p['id'] ?>"
                                data-name="<?= htmlspecialchars($p['name']) ?>"
                                data-category="<?= htmlspecialchars($p['category']) ?>"
                                data-price="<?= $p['price'] ?>"
                                data-stock="<?= $p['quantity'] ?>"
                                data-is_vaccinated="<?= htmlspecialchars($p['is_vaccinated']) ?>"
                                data-health="<?= htmlspecialchars($p['health_score']) ?>"
                                data-notes="<?= htmlspecialchars($p['condition_notes']) ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="action-btn delete deleteLivestockBtn"
                                title="Delete"
                                data-id="<?= $p['id'] ?>"
                                data-name="<?= htmlspecialchars($p['name']) ?>">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
        <small class="text-muted">Showing <span id="rowCount"><?= count($livestock_list) ?></span> livestock</small>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
            </ul>
        </nav>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade modal-lg" id="addProductModal" tabindex="-1" aria-labelledby="addProductLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content admin-modal">
            <div class="modal-header">
                <h6 class="modal-title fw-bold" id="addProductLabel">
                    <i class="bi bi-plus-circle me-2 text-green" id="modalIcon"></i>
                    <span id="modalTitle">Add New Livestock</span>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="livestockId" value="" />
                <div class="mb-3">
                    <label class="form-label" for="productName">Livestock Name</label>
                    <input type="text" id="productName" class="form-control" placeholder="e.g. Chicken, Pig" />
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label" for="productCategory">Category</label>
                        <select id="productCategory" class="form-select">
                            <option>Cattle</option>
                            <option>Swine</option>
                            <option>Sheep</option>
                            <option>Goats</option>
                            <option>Poultry</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="productPrice">Price (₱) per kilo</label>
                        <input type="number" id="productPrice" class="form-control" placeholder="0.00" />
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label" for="productStock">Stock Quantity</label>
                        <input type="number" id="productStock" class="form-control" placeholder="0" />
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="productVaccinated">Vaccinated</label>
                        <select id="productVaccinated" class="form-select">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="productHealthScore">Health Score</label>
                    <input type="text" id="productHealthScore" class="form-control" placeholder="e.g. 100 out of 100 is healthy" />
                </div>
                <div class="mb-3">
                    <label class="form-label" for="productNotes">Conditional Notes</label>
                    <textarea id="productNotes" class="form-control" rows="3" placeholder="Short livestock description…"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-green btn-sm" id="saveProductBtn">
                    <i class="bi bi-save" id="saveBtnIcon"></i>
                    <span id="saveBtnText">Save Livestock</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(function () {

    // ══════════════════════════════════
    // HELPERS
    // ══════════════════════════════════
    function getStockStatus(stock) {
        stock = parseInt(stock);
        if (stock === 0)  return { label: 'Out of Stock', slug: 'out-of-stock' };
        if (stock <= 10)  return { label: 'Low Stock',    slug: 'low-stock' };
        return                   { label: 'In Stock',     slug: 'in-stock' };
    }

    function resetModal() {
        $('#livestockId').val('');
        $('#modalIcon').attr('class', 'bi bi-plus-circle me-2 text-green');
        $('#modalTitle').text('Add New Livestock');
        $('#saveBtnIcon').attr('class', 'bi bi-save');
        $('#saveBtnText').text('Save Livestock');
        $('#productName').val('');
        $('#productPrice').val('');
        $('#productStock').val('');
        $('#productHealthScore').val('');
        $('#productNotes').val('');
        $('#productCategory').prop('selectedIndex', 0);
        $('#productVaccinated').prop('selectedIndex', 0);
        // Clear validation states
        $('.form-control, .form-select').removeClass('is-invalid');
    }

    function validate() {
        let valid = true;
        const name  = $('#productName').val().trim();
        const price = $('#productPrice').val().trim();
        const stock = $('#productStock').val().trim();
        const health = $('#productHealthScore').val().trim();

        if (!name) {
            $('#productName').addClass('is-invalid').focus();
            valid = false;
        } else {
            $('#productName').removeClass('is-invalid');
        }
        if (!price || isNaN(price) || parseFloat(price) < 0) {
            $('#productPrice').addClass('is-invalid');
            if (valid) { $('#productPrice').focus(); valid = false; }
        } else {
            $('#productPrice').removeClass('is-invalid');
        }
        if (!stock || isNaN(stock) || parseInt(stock) < 0) {
            $('#productStock').addClass('is-invalid');
            if (valid) { $('#productStock').focus(); valid = false; }
        } else {
            $('#productStock').removeClass('is-invalid');
        }
        if (!health) {
            $('#productHealthScore').addClass('is-invalid');
            if (valid) { $('#productHealthScore').focus(); valid = false; }
        } else {
            $('#productHealthScore').removeClass('is-invalid');
        }
        return valid;
    }

    // ══════════════════════════════════
    // OPEN MODAL — ADD MODE
    // ══════════════════════════════════
    $('#addLivestockBtn').on('click', function () {
        resetModal();
    });

    // ══════════════════════════════════
    // OPEN MODAL — EDIT MODE
    // ══════════════════════════════════
    $(document).on('click', '.editLivestockBtn', function () {
        const d = $(this).data();
        resetModal();

        $('#livestockId').val(d.id);
        $('#modalIcon').attr('class', 'bi bi-pencil-square me-2 text-warning');
        $('#modalTitle').text('Edit Livestock');
        $('#saveBtnIcon').attr('class', 'bi bi-pencil');
        $('#saveBtnText').text('Update Livestock');

        $('#productName').val(d.name);
        $('#productCategory').val(d.category);
        $('#productPrice').val(d.price);
        $('#productStock').val(d.stock);
        $('#productVaccinated').val(d.is_vaccinated);
        $('#productHealthScore').val(d.health);
        $('#productNotes').val(d.notes);

        $('#addProductModal').modal('show');
    });

    // ══════════════════════════════════
    // SAVE — INSERT or UPDATE
    // ══════════════════════════════════
    $('#saveProductBtn').on('click', function () {
        if (!validate()) return;

        const $btn    = $(this);
        const id      = $('#livestockId').val();
        const isEdit  = id !== '';
        const action  = isEdit ? 'update' : 'insert';

        // ✅ Capture all values HERE before anything clears them
        const captured = {
            name      : $('#productName').val().trim(),
            category  : $('#productCategory').val(),
            price     : $('#productPrice').val().trim(),
            stock     : $('#productStock').val().trim(),
            is_vaccinated: $('#productVaccinated').val(),
            health    : $('#productHealthScore').val().trim(),
            notes     : $('#productNotes').val().trim(),
        };
       
        $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Saving...');

        $.ajax({
            url: 'ajax/save_livestock.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action      : action,
                id          : id,
                name        : captured.name,
                category    : captured.category,
                price       : captured.price,
                stock       : captured.stock,
                is_vaccinated  : captured.is_vaccinated,
                health_score: captured.health,
                notes       : captured.notes,
            },
            success: function (res) {
                 
                if (res.success) {
                    $('#addProductModal').modal('hide');
                    resetModal(); // ✅ Safe to reset now — we already have captured values

                    if (isEdit) {
                        const status  = getStockStatus(captured.stock);
                        const $row    = $('.editLivestockBtn[data-id="' + id + '"]').closest('tr');

                        // ✅ Update row using captured values
                        $row.attr('data-name',     captured.name.toLowerCase());
                        $row.attr('data-category', captured.category);
                        $row.attr('data-status',   status.label);

                        $row.find('td:nth-child(2)').text(captured.name);
                        $row.find('td:nth-child(3) .category-tag').text(captured.category);
                        $row.find('td:nth-child(4)').text('₱' + parseFloat(captured.price).toFixed(2));
                        $row.find('td:nth-child(5)').text(captured.stock);
                        const vaccinatedText = captured.is_vaccinated == 1 ? 'Yes' : 'No';
                        $row.find('td:nth-child(6)').text(vaccinatedText);
                        $row.find('td:nth-child(7)').text(captured.health);
                        $row.find('td:nth-child(8)').text(captured.notes);
                        $row.find('td:nth-child(9) .status-badge')
                            .attr('class', 'status-badge status-' + status.slug)
                            .text(status.label);

                        // ✅ Refresh data attributes on edit button
                        const $editBtn = $row.find('.editLivestockBtn');
                        $editBtn.attr('data-name',      captured.name);
                        $editBtn.attr('data-category',  captured.category);
                        $editBtn.attr('data-price',     captured.price);
                        $editBtn.attr('data-stock',     captured.stock);
                        $editBtn.attr('data-is_vaccinated',captured.is_vaccinated);
                        $editBtn.attr('data-health',    captured.health);
                        $editBtn.attr('data-notes',     captured.notes);

                        showToast('Livestock updated successfully!', 'success');

                    } else {
                        // New insert — reload to get real DB id
                        showToast('Livestock added successfully!', 'success');
                        setTimeout(() => location.reload(), 1000);
                    }

                } else {
                    showToast('Error: ' + res.message, 'danger');
                }
            },
            error: function (xhr) {
                showToast('Server error. Please try again.', 'danger');
                console.error(xhr.responseText);
            },
            complete: function () {
                $btn.prop('disabled', false).html(
                    '<i class="bi bi-' + (isEdit ? 'pencil' : 'save') + '"></i> ' +
                    (isEdit ? 'Update Livestock' : 'Save Livestock')
                );
            }
        });
    });

    // ══════════════════════════════════
    // DELETE
    // ══════════════════════════════════
    $(document).on('click', '.deleteLivestockBtn', function () {
        const id   = $(this).data('id');
        const name = $(this).data('name');
        const $row = $(this).closest('tr');

        if (!confirm('Are you sure you want to delete "' + name + '"?')) return;

        $.ajax({
            url: 'ajax/save_livestock.php',
            type: 'POST',
            dataType: 'json',
            data: { action: 'delete', id: id },
            success: function (res) {
                if (res.success) {
                    $row.fadeOut(300, function () {
                        $(this).remove();
                        const count = $('#livestockTableBody tr:visible').length;
                        $('#rowCount').text(count);
                        if (count === 0) {
                            $('#livestockTableBody').html(`
                                <tr id="emptyRow">
                                    <td colspan="10" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                        No livestock records found.
                                    </td>
                                </tr>
                            `);
                        }
                    });
                    showToast('"' + name + '" deleted successfully.', 'success');
                } else {
                    showToast('Error: ' + res.message, 'danger');
                }
            },
            error: function () {
                showToast('Server error. Please try again.', 'danger');
            }
        });
    });

    // ══════════════════════════════════
    // LIVE SEARCH + FILTER
    // ══════════════════════════════════
    function applyFilters() {
    const q   = $('#productSearch').val().toLowerCase().trim();
    const cat = $('#categoryFilter').val();
    const st  = $('#stockFilter').val();
    let visible = 0;

    $('#livestockTableBody tr').each(function () {
        // ✅ Use .attr() instead of .data() for reliable HTML attribute reading
        const name     = $(this).attr('data-name')     || '';
        const category = $(this).attr('data-category') || '';
        const status   = $(this).attr('data-status')   || '';

        // Skip the empty state row
        if ($(this).attr('id') === 'emptyRow') return;

        const matchQ   = !q   || name.includes(q);
        const matchCat = !cat || category === cat;
        const matchSt  = !st  || status === st;
        const show     = matchQ && matchCat && matchSt;

        $(this).toggle(show);
        if (show) visible++;
        });

        $('#rowCount').text(visible);
    }

    $('#productSearch').on('input',   applyFilters);
    $('#categoryFilter').on('change', applyFilters);
    $('#stockFilter').on('change',    applyFilters);

    // ══════════════════════════════════
    // TOAST HELPER
    // ══════════════════════════════════
    function showToast(message, type = 'success') {
        const id    = 'toast_' + Date.now();
        const color = type === 'success' ? '#2d6a35' : '#dc3545';
        const icon  = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle';

        const $toast = $(`
            <div id="${id}" style="
                position: fixed; bottom: 24px; right: 24px; z-index: 9999;
                background: ${color}; color: #fff; padding: 12px 20px;
                border-radius: 8px; font-size: 14px; display: flex;
                align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                animation: fadeInUp 0.3s ease;">
                <i class="bi ${icon}"></i> ${message}
            </div>
        `);

        $('body').append($toast);
        setTimeout(() => $toast.fadeOut(400, () => $toast.remove()), 3000);
    }


    const rowsPerPage = 5;
    let currentPage = 1;

    function paginateTable() {
        const rows = $('#livestockTableBody tr').not('#emptyRow');
        const totalRows = rows.length;
        const totalPages = Math.ceil(totalRows / rowsPerPage);

        rows.hide();

        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        rows.slice(start, end).show();

        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        const $pagination = $('.pagination');
        $pagination.empty();

        // Previous
        $pagination.append(`
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="prev">&laquo;</a>
            </li>
        `);

        // Pages
        for (let i = 1; i <= totalPages; i++) {
            $pagination.append(`
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `);
        }

        // Next
        $pagination.append(`
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="next">&raquo;</a>
            </li>
        `);
    }

    // Handle clicks
    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();

        const page = $(this).data('page');

        const totalRows = $('#livestockTableBody tr').not('#emptyRow').length;
        const totalPages = Math.ceil(totalRows / rowsPerPage);

        if (page === 'prev' && currentPage > 1) currentPage--;
        else if (page === 'next' && currentPage < totalPages) currentPage++;
        else if (!isNaN(page)) currentPage = parseInt(page);

        paginateTable();
    });

    // Init
    paginateTable();

});
</script>