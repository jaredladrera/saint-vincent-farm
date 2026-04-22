<?php
require_once '../../config/pdo_connection.php';

$user_list = [];
try {
    $db  = new Connect();
    $pdo = $db->connection;

    $stmt = $pdo->query("SELECT * FROM user_profile ORDER BY date_created DESC");
    $user_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $user_list = [];
}
?>

<div class="page-header mb-4">
    <div>
        <h5 class="page-heading mb-1">Users</h5>
        <p class="text-muted small mb-0">Manage system users</p>
    </div>
    <button class="btn btn-green" data-bs-toggle="modal" data-bs-target="#userModal" id="addUserBtn">
        <i class="bi bi-plus-lg me-1"></i> Add User
    </button>
</div>

<!-- SEARCH -->
<div class="card-panel mb-3">
    <input type="text" id="searchUser" class="form-control" placeholder="Search user...">
</div>

<!-- TABLE -->
<div class="card-panel">
    <div class="table-responsive">
        <table class="table admin-table" id="userTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Contact Number</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
                <?php foreach ($user_list as $u): ?>
                <tr data-name="<?= strtolower($u['first_name'].' '.$u['last_name']) ?>">
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?></td>
                    <td><?= htmlspecialchars($u['contact_number']) ?></td>
                    <td><?= htmlspecialchars($u['email_address']) ?></td>
                    <td><?= htmlspecialchars($u['address']) ?></td>
                    <td><?= htmlspecialchars($u['user_role']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning editUser"
                            data-id="<?= $u['id'] ?>"
                            data-first="<?= htmlspecialchars($u['first_name']) ?>"
                            data-middle="<?= htmlspecialchars($u['middle_name']) ?>"
                            data-last="<?= htmlspecialchars($u['last_name']) ?>"
                            data-contact="<?= htmlspecialchars($u['contact_number']) ?>"
                            data-email="<?= htmlspecialchars($u['email_address']) ?>"
                            data-address="<?= htmlspecialchars($u['address']) ?>"
                            data-role="<?= htmlspecialchars($u['user_role']) ?>">
                            Edit
                        </button>

                        <button class="btn btn-sm btn-danger deleteUser"
                            data-id="<?= $u['id'] ?>"
                            data-name="<?= htmlspecialchars($u['first_name']) ?>">
                            Delete
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    <div class="mt-3">
        <ul class="pagination" id="pagination"></ul>
    </div>
</div>

<!-- MODAL -->
<div class="modal modal-lg fade" id="userModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modalTitle">Add User</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="userId">

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" class="form-control" placeholder="Enter first name">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="middle_name">Middle Name</label>
                        <input type="text" id="middle_name" class="form-control" placeholder="Enter middle name">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" class="form-control" placeholder="Enter last name">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="contact">Contact Number</label>
                        <input type="text" id="contact" class="form-control" placeholder="Enter contact number">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" class="form-control" placeholder="Enter email">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="address">Address</label>
                        <input type="text" id="address" class="form-control" placeholder="Enter address">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="role">Role</label>
                        <select name="role" id="role" class="form-control">
                            <option value="User">User</option>
                            <option value="Staff">Staff</option>
                            <option value="Administrator">Administrator</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="password">Password</label>
                        <input type="password" id="password" class="form-control" placeholder="Enter password">
                    </div>
                </div>
            </div>


            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-success" id="saveUser">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
$(function () {

// SEARCH
$('#searchUser').on('keyup', function () {
    let val = $(this).val().toLowerCase();

    $('#userTableBody tr').filter(function () {
        $(this).toggle($(this).data('name').includes(val));
    });

    currentPage = 1;
    paginate();
});

// PAGINATION CLICK
$(document).on('click', '.pageBtn', function (e) {
    e.preventDefault();
    currentPage = $(this).data('page');
    paginate();
});

// RESET MODAL
function reset() {
    $('#userId').val('');
    $('#modalTitle').text('Add User');
    $('#userModal input').val('');
}

// ADD
$('#addUserBtn').click(reset);

// EDIT
$(document).on('click', '.editUser', function () {
    let d = $(this).data();

    $('#userId').val(d.id);
    $('#first_name').val(d.first);
    $('#middle_name').val(d.middle);
    $('#last_name').val(d.last);
    $('#contact').val(d.contact);
    $('#email').val(d.email);
    $('#role').val(d.role);
    $('#address').val(d.address);

    $('#modalTitle').text('Edit User');
    $('#userModal').modal('show');
});

// SAVE
$('#saveUser').click(function () {

    let id = $('#userId').val();
    let action = id ? 'update' : 'insert';

    let data = {
        action: action,
        id: id,
        first_name: $('#first_name').val(),
        middle_name: $('#middle_name').val(),
        last_name: $('#last_name').val(),
        contact: $('#contact').val(),
        email: $('#email').val(),
        role: $('#role').val(),
        address: $('#address').val(),
        password: $('#password').val()
    };

    $.post('ajax/save_user.php', data, function (res) {

        if (res.success) {

            $('#userModal').modal('hide');

            if (action === 'insert') {
                showToast('User added successfully!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('User updated successfully!', 'success');
                setTimeout(() => location.reload(), 1000);
            }

        } else {
            alert(res.message);
        }

    }, 'json');
});

// DELETE
$(document).on('click', '.deleteUser', function () {

    if (!confirm('Delete this user?')) return;

    let id = $(this).data('id');

    $.post('ajax/save_user.php', { action: 'delete', id: id }, function (res) {

        if (res.success) {
            showToast('User deleted successfully!', 'success');
            setTimeout(() => location.reload(), 1000);
        }

    }, 'json');
});

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


    const rowsPerPage = 10;
    let currentPage = 1;

    function paginateTable() {
        const rows = $('#userTableBody tr').not('#emptyRow');
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

        // Pages
        for (let i = 1; i <= totalPages; i++) {
            $pagination.append(`
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `);
        }

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