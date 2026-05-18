<?php
require_once '../../config/pdo_connection.php';
require_once '../../includes/auth.php';

$db  = new Connect();
$pdo = $db->connection;

// Get logged in staff id from session
$staff_id = currentUser();

// Fetch staff details from user_profile
$empStmt = $pdo->prepare("
    SELECT CONCAT(first_name, ' ', middle_name, ' ', last_name) AS staff_name,
           contact_number,
           email_address,
           address
    FROM user_profile
    WHERE id = ?
");
$empStmt->execute([$staff_id['id']]);
$employee = $empStmt->fetch(PDO::FETCH_ASSOC);

$employee_name = $employee ? htmlspecialchars($employee['staff_name'])     : 'Unknown';
$employee_email = $employee ? htmlspecialchars($employee['email_address']) : '';

// Fetch payslip history from payroll table using user_id = session id
$stmt = $pdo->prepare("
    SELECT id,
           period_start,
           period_end,
           daily_rate,
           basic_pay,
           ot_pay,
           sss,
           philhealth,
           pagibig,
           late_deduction,
           other_deduction,
           total_deduction,
           net_pay,
           status,
           created_at
    FROM payroll
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$staff_id['id']]);
$payslip_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header d-flex align-items-center gap-3 mb-4">
    <div>
        <h5 class="mb-0">My Payslips</h5>
        <small class="text-muted"><?= $employee_name ?> &mdash; <?= $employee_email ?></small>
    </div>
</div>

<!-- SEARCH -->
<input type="text" id="searchMyPayslip" class="form-control mb-3" placeholder="Search by pay period...">

<table class="table table-hover" id="myPayslipTable">
    <thead>
        <tr>
            <th>#</th>
            <th>Pay Period</th>
            <th>Basic Pay</th>
            <th>Gross Pay</th>
            <th>Total Deductions</th>
            <th>Net Pay</th>
            <th>Status</th>
            <th>Generated</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody id="myPayslipBody">
        <?php if (empty($payslip_history)): ?>
        <tr>
            <td colspan="9" class="text-center text-muted">No payslip records found.</td>
        </tr>
        <?php else: ?>
        <?php foreach ($payslip_history as $ps):
            $gross       = $ps['basic_pay'] + $ps['ot_pay'];
            $periodLabel = date('M d, Y', strtotime($ps['period_start'])) . ' – ' . date('M d, Y', strtotime($ps['period_end']));
            $statusLabel = (int)$ps['status'] === 1 ? 'paid' : 'pending';
            $badgeClass  = (int)$ps['status'] === 1 ? 'bg-success' : 'bg-warning';
        ?>
        <tr data-period="<?= strtolower($periodLabel) ?>">
            <td><?= $ps['id'] ?></td>
            <td><?= htmlspecialchars($periodLabel) ?></td>
            <td>₱<?= number_format($ps['basic_pay'], 2) ?></td>
            <td>₱<?= number_format($gross, 2) ?></td>
            <td>₱<?= number_format($ps['total_deduction'], 2) ?></td>
            <td><strong>₱<?= number_format($ps['net_pay'], 2) ?></strong></td>
            <td>
                <span class="badge <?= $badgeClass ?>">
                    <?= ucfirst($statusLabel) ?>
                </span>
            </td>
            <td><?= date('M d, Y', strtotime($ps['created_at'])) ?></td>
            <td>
                <button class="btn btn-sm btn-outline-primary viewMySlipBtn"
                    data-period="<?= htmlspecialchars($periodLabel) ?>"
                    data-daily="<?= $ps['daily_rate'] ?>"
                    data-basic="<?= $ps['basic_pay'] ?>"
                    data-otpay="<?= $ps['ot_pay'] ?>"
                    data-gross="<?= $gross ?>"
                    data-sss="<?= $ps['sss'] ?>"
                    data-philhealth="<?= $ps['philhealth'] ?>"
                    data-pagibig="<?= $ps['pagibig'] ?>"
                    data-late="<?= $ps['late_deduction'] ?>"
                    data-other="<?= $ps['other_deduction'] ?>"
                    data-totaldeduct="<?= $ps['total_deduction'] ?>"
                    data-net="<?= $ps['net_pay'] ?>"
                    data-status="<?= $statusLabel ?>">
                    <i class="bi bi-eye"></i> View
                </button>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<ul class="pagination" id="myPayslipPagination"></ul>


<!-- ══════════════════════════════════
     VIEW PAYSLIP DETAIL MODAL
══════════════════════════════════ -->
<div class="modal fade" id="viewMySlipModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-file-earmark-text"></i> Pay Slip</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <strong>Staff Name:</strong> <?= $employee_name ?><br>
                    <strong>Email:</strong> <?= $employee_email ?><br>
                    <strong>Pay Period:</strong> <span id="vmsPeriod"></span>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-success">Earnings</h6>
                        <table class="table table-sm">
                            <tr>
                                <td>Daily Rate</td>
                                <td id="vmsDaily" class="text-end"></td>
                            </tr>
                            <tr>
                                <td><strong>Basic Pay</strong></td>
                                <td id="vmsBasic" class="text-end fw-bold"></td>
                            </tr>
                            <tr>
                                <td>OT Pay</td>
                                <td id="vmsOTPay" class="text-end"></td>
                            </tr>
                            <tr class="table-success">
                                <td><strong>Gross Pay</strong></td>
                                <td id="vmsGross" class="text-end fw-bold"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-danger">Deductions</h6>
                        <table class="table table-sm">
                            <tr>
                                <td>SSS</td>
                                <td id="vmsSSS" class="text-end"></td>
                            </tr>
                            <tr>
                                <td>PhilHealth</td>
                                <td id="vmsPhilHealth" class="text-end"></td>
                            </tr>
                            <tr>
                                <td>Pag-IBIG</td>
                                <td id="vmsPagIbig" class="text-end"></td>
                            </tr>
                            <tr>
                                <td>Late / Absent</td>
                                <td id="vmsLate" class="text-end"></td>
                            </tr>
                            <tr>
                                <td>Other</td>
                                <td id="vmsOther" class="text-end"></td>
                            </tr>
                            <tr class="table-danger">
                                <td><strong>Total Deductions</strong></td>
                                <td id="vmsTotalDeduct" class="text-end fw-bold"></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="alert alert-primary text-end mb-2">
                    <h5 class="mb-0">Net Pay: ₱<span id="vmsNetPay"></span></h5>
                </div>

                <div><strong>Status:</strong> <span id="vmsStatus"></span></div>

            </div>

        </div>
    </div>
</div>


<script>
$(function () {

    let rowsPerPage = 5;
    let currentPage = 1;

    // ── Pagination ───────────────────────────────────────────
    function paginate() {
        let rows    = $('#myPayslipBody tr:not(:has(td[colspan]))');
        let visible = rows.filter(':visible');
        let total   = visible.length;
        let pages   = Math.ceil(total / rowsPerPage);

        rows.hide();
        visible.slice((currentPage - 1) * rowsPerPage, currentPage * rowsPerPage).show();

        let html = '';
        for (let i = 1; i <= pages; i++) {
            html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a href="#" class="page-link myPayslipPageBtn" data-page="${i}">${i}</a>
                     </li>`;
        }
        $('#myPayslipPagination').html(html);
    }

    paginate();

    // ── Search ───────────────────────────────────────────────
    $('#searchMyPayslip').keyup(function () {
        let val = $(this).val().toLowerCase();
        $('#myPayslipBody tr').filter(function () {
            $(this).toggle(
                $(this).data('period') !== undefined &&
                $(this).data('period').includes(val)
            );
        });
        currentPage = 1;
        paginate();
    });

    // ── Pagination click ─────────────────────────────────────
    $(document).on('click', '.myPayslipPageBtn', function (e) {
        e.preventDefault();
        currentPage = $(this).data('page');
        paginate();
    });

    // ── View Payslip Detail ──────────────────────────────────
    $(document).on('click', '.viewMySlipBtn', function () {
        let btn = $(this);

        let daily      = parseFloat(btn.data('daily'));
        let basic      = parseFloat(btn.data('basic'));
        let otPay      = parseFloat(btn.data('otpay'));
        let gross      = parseFloat(btn.data('gross'));
        let sss        = parseFloat(btn.data('sss'));
        let philhealth = parseFloat(btn.data('philhealth'));
        let pagibig    = parseFloat(btn.data('pagibig'));
        let late       = parseFloat(btn.data('late'));
        let other      = parseFloat(btn.data('other'));
        let totalD     = parseFloat(btn.data('totaldeduct'));
        let net        = parseFloat(btn.data('net'));
        let status     = btn.data('status');

        $('#vmsPeriod').text(btn.data('period'));
        $('#vmsDaily').text('₱' + daily.toFixed(2));
        $('#vmsBasic').text('₱' + basic.toFixed(2));
        $('#vmsOTPay').text('₱' + otPay.toFixed(2));
        $('#vmsGross').text('₱' + gross.toFixed(2));
        $('#vmsSSS').text('₱' + sss.toFixed(2));
        $('#vmsPhilHealth').text('₱' + philhealth.toFixed(2));
        $('#vmsPagIbig').text('₱' + pagibig.toFixed(2));
        $('#vmsLate').text('₱' + late.toFixed(2));
        $('#vmsOther').text('₱' + other.toFixed(2));
        $('#vmsTotalDeduct').text('₱' + totalD.toFixed(2));
        $('#vmsNetPay').text(net.toFixed(2));

        let badgeClass = status === 'paid' ? 'bg-success' : 'bg-warning';
        $('#vmsStatus').html(`<span class="badge ${badgeClass}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`);

        $('#viewMySlipModal').modal('show');
    });

});
</script>