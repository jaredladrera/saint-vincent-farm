<?php
require_once '../../config/pdo_connection.php';
$db  = new Connect();
$pdo = $db->connection;

// Fetch all users with user_role = 'staff' from user_profile
$stmt = $pdo->prepare("
    SELECT id,
           CONCAT(first_name, ' ', middle_name, ' ', last_name) AS staff_name,
           contact_number,
           email_address,
           address,
           date_created
    FROM user_profile
    WHERE user_role = 'staff'
    ORDER BY first_name ASC
");
$stmt->execute();
$staff_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header mb-4">
    <h5>Payroll</h5>
</div>

<!-- SEARCH -->
<input type="text" id="searchPayroll" class="form-control mb-3" placeholder="Search staff name...">

<table class="table" id="payrollTable">
    <thead>
        <tr>
            <th>#</th>
            <th>Staff Name</th>
            <th>Contact Number</th>
            <th>Email Address</th>
            <th>Address</th>
            <th>Date Created</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody id="payrollTableBody">
        <?php if (empty($staff_list)): ?>
        <tr>
            <td colspan="7" class="text-center text-muted">No staff found.</td>
        </tr>
        <?php else: ?>
        <?php foreach ($staff_list as $p): ?>
        <tr data-name="<?= strtolower(htmlspecialchars($p['staff_name'])) ?>">
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['staff_name']) ?></td>
            <td><?= htmlspecialchars($p['contact_number'] ?? '—') ?></td>
            <td><?= htmlspecialchars($p['email_address'] ?? '—') ?></td>
            <td><?= htmlspecialchars($p['address'] ?? '—') ?></td>
            <td><?= htmlspecialchars($p['date_created'] ?? '—') ?></td>
            <td>
                <a href="index.php?page=payslip&id=<?= $p['id'] ?>"
                   class="btn btn-sm btn-primary">
                    View
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<ul class="pagination" id="payrollPagination"></ul>

<script>
$(function () {

    let rowsPerPage = 5;
    let currentPage = 1;

    function paginate() {
        let rows = $('#payrollTableBody tr:not(:has(td[colspan]))');
        let visible = rows.filter(':visible');
        let total = visible.length;
        let pages = Math.ceil(total / rowsPerPage);

        rows.hide();
        visible.slice((currentPage - 1) * rowsPerPage, currentPage * rowsPerPage).show();

        let html = '';
        for (let i = 1; i <= pages; i++) {
            html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a href="#" class="page-link payrollPageBtn" data-page="${i}">${i}</a>
                     </li>`;
        }
        $('#payrollPagination').html(html);
    }

    paginate();

    // SEARCH
    $('#searchPayroll').keyup(function () {
        let val = $(this).val().toLowerCase();
        $('#payrollTableBody tr').filter(function () {
            $(this).toggle($(this).data('name') !== undefined && $(this).data('name').includes(val));
        });
        currentPage = 1;
        paginate();
    });

    // PAGINATION CLICK
    $(document).on('click', '.payrollPageBtn', function (e) {
        e.preventDefault();
        currentPage = $(this).data('page');
        paginate();
    });

});
</script>