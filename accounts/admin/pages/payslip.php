<?php
require_once '../../config/pdo_connection.php';
$db  = new Connect();
$pdo = $db->connection;

// Get employee id from URL
$employee_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch employee details from user_profile
$empStmt = $pdo->prepare("
    SELECT CONCAT(first_name, ' ', middle_name, ' ', last_name) AS staff_name,
           contact_number,
           email_address,
           address
    FROM user_profile
    WHERE id = ?
");
$empStmt->execute([$employee_id]);
$employee = $empStmt->fetch(PDO::FETCH_ASSOC);

$employee_name = $employee ? htmlspecialchars($employee['staff_name'])     : 'Unknown';
$employee_pos  = $employee ? htmlspecialchars($employee['contact_number']) : '';

// Fetch payslip history from DB using user_id
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
$stmt->execute([$employee_id]);
$payslip_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header d-flex align-items-center gap-3 mb-4">
    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back
    </a>
    <div>
        <h5 class="mb-0">Payslip History</h5>
        <small class="text-muted"><?= $employee_name ?> &mdash; <?= $employee_pos ?></small>
    </div>
    <button class="btn btn-primary btn-sm ms-auto" id="openGeneratePayslip">
        <i class="bi bi-plus-circle"></i> Generate Payslip
    </button>
</div>

<!-- ══════════════════════════════════
     PAYSLIP HISTORY TABLE
══════════════════════════════════ -->
<table class="table table-hover" id="payslipHistoryTable">
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
    <tbody id="payslipHistoryBody">
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
        <tr>
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
            <td class="d-flex gap-1">
                <!-- View -->
                <button class="btn btn-sm btn-outline-primary viewSlipBtn"
                    data-id="<?= $ps['id'] ?>"
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
                    <i class="bi bi-eye"></i>
                </button>
                <!-- Edit -->
                <button class="btn btn-sm btn-outline-warning editSlipBtn"
                    data-id="<?= $ps['id'] ?>"
                    data-periodstart="<?= $ps['period_start'] ?>"
                    data-periodend="<?= $ps['period_end'] ?>"
                    data-daily="<?= $ps['daily_rate'] ?>"
                    data-basic="<?= $ps['basic_pay'] ?>"
                    data-otpay="<?= $ps['ot_pay'] ?>"
                    data-sss="<?= $ps['sss'] ?>"
                    data-philhealth="<?= $ps['philhealth'] ?>"
                    data-pagibig="<?= $ps['pagibig'] ?>"
                    data-late="<?= $ps['late_deduction'] ?>"
                    data-other="<?= $ps['other_deduction'] ?>"
                    data-totaldeduct="<?= $ps['total_deduction'] ?>"
                    data-net="<?= $ps['net_pay'] ?>"
                    data-status="<?= $statusLabel ?>">
                    <i class="bi bi-pencil"></i>
                </button>
                <!-- Delete -->
                <button class="btn btn-sm btn-outline-danger deleteSlipBtn"
                    data-id="<?= $ps['id'] ?>">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>


<!-- ══════════════════════════════════
     GENERATE PAYSLIP MODAL
══════════════════════════════════ -->
<div class="modal fade" id="generatePayslipModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-file-earmark-text"></i> Generate Payslip</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="genUserId" value="<?= $employee_id ?>">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Staff Name</label>
                        <input type="text" class="form-control" value="<?= $employee_name ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Position</label>
                        <input type="text" class="form-control" value="<?= $employee_pos ?>" readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Period Start <span class="text-danger">*</span></label>
                        <input type="date" id="genPeriodStart" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Period End <span class="text-danger">*</span></label>
                        <input type="date" id="genPeriodEnd" class="form-control" required>
                    </div>
                </div>
                <hr>
                <h6 class="text-success mb-3"><i class="bi bi-plus-circle"></i> Earnings</h6>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Daily Rate (₱) <span class="text-danger">*</span></label>
                        <input type="number" id="genDailyRate" class="form-control gc-calc" placeholder="0.00" min="0" step="0.01">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Days Worked <span class="text-danger">*</span></label>
                        <input type="number" id="genDaysWorked" class="form-control gc-calc" placeholder="0" min="0" max="31">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Basic Pay (auto)</label>
                        <input type="text" id="genBasicPay" class="form-control bg-light" readonly placeholder="₱0.00">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">OT Pay (₱)</label>
                        <input type="number" id="genOtPay" class="form-control gc-calc" placeholder="0.00" min="0" step="0.01">
                    </div>
                </div>
                <div class="alert alert-success py-2 mb-3">
                    <strong>Gross Pay: ₱<span id="genGross">0.00</span></strong>
                    <small class="text-muted ms-2">(basic_pay + ot_pay)</small>
                </div>
                <hr>
                <h6 class="text-danger mb-3"><i class="bi bi-dash-circle"></i> Deductions</h6>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">SSS (₱)</label>
                        <input type="number" id="genSSS" class="form-control gc-calc" placeholder="0.00" min="0" step="0.01">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">PhilHealth (₱)</label>
                        <input type="number" id="genPhilHealth" class="form-control gc-calc" placeholder="0.00" min="0" step="0.01">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Pag-IBIG (₱)</label>
                        <input type="number" id="genPagibig" class="form-control gc-calc" placeholder="0.00" min="0" step="0.01">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Late Deduction (₱)</label>
                        <input type="number" id="genLateDeduction" class="form-control gc-calc" placeholder="0.00" min="0" step="0.01">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Other Deduction (₱)</label>
                        <input type="number" id="genOtherDeduction" class="form-control gc-calc" placeholder="0.00" min="0" step="0.01">
                    </div>
                </div>
                <div class="alert alert-danger py-2 mb-3">
                    <strong>Total Deduction: ₱<span id="genTotalDeduction">0.00</span></strong>
                </div>
                <div class="alert alert-primary py-2 mb-3">
                    <h6 class="mb-0">Net Pay: ₱<span id="genNetPay">0.00</span></h6>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Status</label>
                        <select id="genStatus" class="form-select">
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveGeneratedPayslip">
                    <i class="bi bi-save"></i> Save Payslip
                </button>
            </div>
        </div>
    </div>
</div>


<!-- ══════════════════════════════════
     EDIT PAYSLIP MODAL
══════════════════════════════════ -->
<div class="modal fade" id="editSlipModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit Payslip</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editSlipId">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Period Start <span class="text-danger">*</span></label>
                        <input type="date" id="editPeriodStart" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Period End <span class="text-danger">*</span></label>
                        <input type="date" id="editPeriodEnd" class="form-control" required>
                    </div>
                </div>
                <hr>
                <h6 class="text-success mb-3"><i class="bi bi-plus-circle"></i> Earnings</h6>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Daily Rate (₱)</label>
                        <input type="number" id="editDailyRate" class="form-control eu-calc" placeholder="0.00" min="0" step="0.01">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Days Worked</label>
                        <input type="number" id="editDaysWorked" class="form-control eu-calc" placeholder="0" min="0" max="31">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Basic Pay (auto)</label>
                        <input type="text" id="editBasicPay" class="form-control bg-light" readonly placeholder="₱0.00">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">OT Pay (₱)</label>
                        <input type="number" id="editOtPay" class="form-control eu-calc" placeholder="0.00" min="0" step="0.01">
                    </div>
                </div>
                <div class="alert alert-success py-2 mb-3">
                    <strong>Gross Pay: ₱<span id="editGross">0.00</span></strong>
                </div>
                <hr>
                <h6 class="text-danger mb-3"><i class="bi bi-dash-circle"></i> Deductions</h6>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">SSS (₱)</label>
                        <input type="number" id="editSSS" class="form-control eu-calc" placeholder="0.00" min="0" step="0.01">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">PhilHealth (₱)</label>
                        <input type="number" id="editPhilHealth" class="form-control eu-calc" placeholder="0.00" min="0" step="0.01">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Pag-IBIG (₱)</label>
                        <input type="number" id="editPagibig" class="form-control eu-calc" placeholder="0.00" min="0" step="0.01">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Late Deduction (₱)</label>
                        <input type="number" id="editLateDeduction" class="form-control eu-calc" placeholder="0.00" min="0" step="0.01">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Other Deduction (₱)</label>
                        <input type="number" id="editOtherDeduction" class="form-control eu-calc" placeholder="0.00" min="0" step="0.01">
                    </div>
                </div>
                <div class="alert alert-danger py-2 mb-3">
                    <strong>Total Deduction: ₱<span id="editTotalDeduction">0.00</span></strong>
                </div>
                <div class="alert alert-primary py-2 mb-3">
                    <h6 class="mb-0">Net Pay: ₱<span id="editNetPay">0.00</span></h6>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Status</label>
                        <select id="editStatus" class="form-select">
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="saveEditPayslip">
                    <i class="bi bi-save"></i> Update Payslip
                </button>
            </div>
        </div>
    </div>
</div>


<!-- ══════════════════════════════════
     VIEW PAYSLIP DETAIL MODAL
══════════════════════════════════ -->
<div class="modal fade" id="viewSlipModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pay Slip</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>Staff Name:</strong> <?= $employee_name ?><br>
                    <strong>Position:</strong> <?= $employee_pos ?><br>
                    <strong>Pay Period:</strong> <span id="vsPeriod"></span>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-success">Earnings</h6>
                        <table class="table table-sm">
                            <tr><td>Daily Rate</td><td id="vsDaily" class="text-end"></td></tr>
                            <tr><td><strong>Basic Pay</strong></td><td id="vsBasic" class="text-end fw-bold"></td></tr>
                            <tr><td>OT Pay</td><td id="vsOTPay" class="text-end"></td></tr>
                            <tr class="table-success"><td><strong>Gross Pay</strong></td><td id="vsGross" class="text-end fw-bold"></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-danger">Deductions</h6>
                        <table class="table table-sm">
                            <tr><td>SSS</td><td id="vsSSS" class="text-end"></td></tr>
                            <tr><td>PhilHealth</td><td id="vsPhilHealth" class="text-end"></td></tr>
                            <tr><td>Pag-IBIG</td><td id="vsPagIbig" class="text-end"></td></tr>
                            <tr><td>Late / Absent</td><td id="vsLate" class="text-end"></td></tr>
                            <tr><td>Other</td><td id="vsOther" class="text-end"></td></tr>
                            <tr class="table-danger"><td><strong>Total Deductions</strong></td><td id="vsTotalDeduct" class="text-end fw-bold"></td></tr>
                        </table>
                    </div>
                </div>
                <div class="alert alert-primary text-end mb-2">
                    <h5 class="mb-0">Net Pay: ₱<span id="vsNetPay"></span></h5>
                </div>
                <div><strong>Status:</strong> <span id="vsStatus"></span></div>
            </div>
        </div>
    </div>
</div>


<script>
$(function () {

    // ── Open Generate Modal ──────────────────────────────────
    $('#openGeneratePayslip').click(function () {
        $('#generatePayslipModal input[type="number"]').val('');
        $('#generatePayslipModal input[type="date"]').val('');
        $('#genBasicPay').val('');
        $('#genGross').text('0.00');
        $('#genTotalDeduction').text('0.00');
        $('#genNetPay').text('0.00');
        $('#genStatus').val('pending');
        $('#generatePayslipModal').modal('show');
    });

    // ── Helpers ──────────────────────────────────────────────
    function fval(id) { return parseFloat($('#' + id).val()) || 0; }

    // ── Generate Live Calc ───────────────────────────────────
    function recalcGen() {
        let basic      = fval('genDailyRate') * fval('genDaysWorked');
        let gross      = basic + fval('genOtPay');
        let totalDeduct = fval('genSSS') + fval('genPhilHealth') + fval('genPagibig') + fval('genLateDeduction') + fval('genOtherDeduction');
        let net        = gross - totalDeduct;
        $('#genBasicPay').val('₱' + basic.toFixed(2) + '  (₱' + fval('genDailyRate').toFixed(2) + '/day × ' + fval('genDaysWorked') + ' days)');
        $('#genGross').text(gross.toFixed(2));
        $('#genTotalDeduction').text(totalDeduct.toFixed(2));
        $('#genNetPay').text(net.toFixed(2));
    }
    $(document).on('input', '.gc-calc', recalcGen);

    // ── Edit Live Calc ───────────────────────────────────────
    function recalcEdit() {
        let basic       = fval('editDailyRate') * fval('editDaysWorked');
        let gross       = basic + fval('editOtPay');
        let totalDeduct = fval('editSSS') + fval('editPhilHealth') + fval('editPagibig') + fval('editLateDeduction') + fval('editOtherDeduction');
        let net         = gross - totalDeduct;
        $('#editBasicPay').val('₱' + basic.toFixed(2) + '  (₱' + fval('editDailyRate').toFixed(2) + '/day × ' + fval('editDaysWorked') + ' days)');
        $('#editGross').text(gross.toFixed(2));
        $('#editTotalDeduction').text(totalDeduct.toFixed(2));
        $('#editNetPay').text(net.toFixed(2));
    }
    $(document).on('input', '.eu-calc', recalcEdit);

    // ── Save Generate ────────────────────────────────────────
    $('#saveGeneratedPayslip').click(function () {
        let periodStart = $('#genPeriodStart').val();
        let periodEnd   = $('#genPeriodEnd').val();
        if (!periodStart || !periodEnd) { alert('Please select the period start and end dates.'); return; }
        if (!fval('genDailyRate') || !fval('genDaysWorked')) { alert('Please enter daily rate and days worked.'); return; }

        let userId         = $('#genUserId').val();
        let dailyRate      = fval('genDailyRate');
        let basicPay       = dailyRate * fval('genDaysWorked');
        let otPay          = fval('genOtPay');
        let gross          = basicPay + otPay;
        let sss            = fval('genSSS');
        let philhealth     = fval('genPhilHealth');
        let pagibig        = fval('genPagibig');
        let lateDeduction  = fval('genLateDeduction');
        let otherDeduction = fval('genOtherDeduction');
        let totalDeduction = sss + philhealth + pagibig + lateDeduction + otherDeduction;
        let netPay         = gross - totalDeduction;
        let status         = $('#genStatus').val();

        $.post('./../../shared/api.php', {
            key             : 'save_payroll',
            user_id         : userId,
            period_start    : periodStart,
            period_end      : periodEnd,
            daily_rate      : dailyRate,
            ot_pay          : otPay,
            basic_pay       : basicPay,
            sss             : sss,
            philhealth      : philhealth,
            pagibig         : pagibig,
            late_deduction  : lateDeduction,
            other_deduction : otherDeduction,
            total_deduction : totalDeduction,
            net_pay         : netPay,
            status          : status
        }, function (res) {
            if (res.success) {
                $('#generatePayslipModal').modal('hide');
                window.location.href = 'index.php?page=payslip&id=<?= $employee_id ?>';
            } else {
                alert('Error saving payslip: ' + (res.message ?? 'Unknown error'));
            }
        }, 'json').fail(function () { alert('Server error. Please try again.'); });
    });

    // ── Open Edit Modal ──────────────────────────────────────
    $(document).on('click', '.editSlipBtn', function () {
        let btn = $(this);

        let daily      = parseFloat(btn.data('daily'));
        let basic      = parseFloat(btn.data('basic'));
        let otPay      = parseFloat(btn.data('otpay'));
        let sss        = parseFloat(btn.data('sss'));
        let philhealth = parseFloat(btn.data('philhealth'));
        let pagibig    = parseFloat(btn.data('pagibig'));
        let late       = parseFloat(btn.data('late'));
        let other      = parseFloat(btn.data('other'));
        let totalD     = parseFloat(btn.data('totaldeduct'));
        let net        = parseFloat(btn.data('net'));
        let daysWorked = daily > 0 ? Math.round(basic / daily) : 0;

        $('#editSlipId').val(btn.data('id'));
        $('#editPeriodStart').val(btn.data('periodstart'));
        $('#editPeriodEnd').val(btn.data('periodend'));
        $('#editDailyRate').val(daily);
        $('#editDaysWorked').val(daysWorked);
        $('#editBasicPay').val('₱' + basic.toFixed(2) + '  (₱' + daily.toFixed(2) + '/day × ' + daysWorked + ' days)');
        $('#editOtPay').val(otPay);
        $('#editSSS').val(sss);
        $('#editPhilHealth').val(philhealth);
        $('#editPagibig').val(pagibig);
        $('#editLateDeduction').val(late);
        $('#editOtherDeduction').val(other);
        $('#editGross').text((basic + otPay).toFixed(2));
        $('#editTotalDeduction').text(totalD.toFixed(2));
        $('#editNetPay').text(net.toFixed(2));
        $('#editStatus').val(btn.data('status'));

        $('#editSlipModal').modal('show');
    });

    // ── Save Edit ────────────────────────────────────────────
    $('#saveEditPayslip').click(function () {
        let periodStart = $('#editPeriodStart').val();
        let periodEnd   = $('#editPeriodEnd').val();
        if (!periodStart || !periodEnd) { alert('Please select the period dates.'); return; }

        let id             = $('#editSlipId').val();
        let dailyRate      = fval('editDailyRate');
        let basicPay       = dailyRate * fval('editDaysWorked');
        let otPay          = fval('editOtPay');
        let gross          = basicPay + otPay;
        let sss            = fval('editSSS');
        let philhealth     = fval('editPhilHealth');
        let pagibig        = fval('editPagibig');
        let lateDeduction  = fval('editLateDeduction');
        let otherDeduction = fval('editOtherDeduction');
        let totalDeduction = sss + philhealth + pagibig + lateDeduction + otherDeduction;
        let netPay         = gross - totalDeduction;
        let status         = $('#editStatus').val();

        $.post('./../../shared/api.php', {
            key             : 'update_payroll',
            id              : id,
            period_start    : periodStart,
            period_end      : periodEnd,
            daily_rate      : dailyRate,
            basic_pay       : basicPay,
            ot_pay          : otPay,
            sss             : sss,
            philhealth      : philhealth,
            pagibig         : pagibig,
            late_deduction  : lateDeduction,
            other_deduction : otherDeduction,
            total_deduction : totalDeduction,
            net_pay         : netPay,
            status          : status
        }, function (res) {
            if (res.success) {
                $('#editSlipModal').modal('hide');
                window.location.href = 'index.php?page=payslip&id=<?= $employee_id ?>';
            } else {
                alert('Error updating payslip: ' + (res.message ?? 'Unknown error'));
            }
        }, 'json').fail(function () { alert('Server error. Please try again.'); });
    });

    // ── Delete ───────────────────────────────────────────────
    $(document).on('click', '.deleteSlipBtn', function () {
        let id = $(this).data('id');
        if (!confirm('Are you sure you want to delete this payslip? This cannot be undone.')) return;

        $.post('./../../shared/api.php', {
            key : 'delete_payroll',
            id  : id
        }, function (res) {
            if (res.success) {
                window.location.href = 'index.php?page=payslip&id=<?= $employee_id ?>';
            } else {
                alert('Error deleting payslip: ' + (res.message ?? 'Unknown error'));
            }
        }, 'json').fail(function () { alert('Server error. Please try again.'); });
    });

    // ── View Detail ──────────────────────────────────────────
    $(document).on('click', '.viewSlipBtn', function () {
        let btn = $(this);
        $('#vsPeriod').text(btn.data('period'));
        $('#vsDaily').text('₱' + parseFloat(btn.data('daily')).toFixed(2));
        $('#vsBasic').text('₱' + parseFloat(btn.data('basic')).toFixed(2));
        $('#vsOTPay').text('₱' + parseFloat(btn.data('otpay')).toFixed(2));
        $('#vsGross').text('₱' + parseFloat(btn.data('gross')).toFixed(2));
        $('#vsSSS').text('₱' + parseFloat(btn.data('sss')).toFixed(2));
        $('#vsPhilHealth').text('₱' + parseFloat(btn.data('philhealth')).toFixed(2));
        $('#vsPagIbig').text('₱' + parseFloat(btn.data('pagibig')).toFixed(2));
        $('#vsLate').text('₱' + parseFloat(btn.data('late')).toFixed(2));
        $('#vsOther').text('₱' + parseFloat(btn.data('other')).toFixed(2));
        $('#vsTotalDeduct').text('₱' + parseFloat(btn.data('totaldeduct')).toFixed(2));
        $('#vsNetPay').text(parseFloat(btn.data('net')).toFixed(2));
        let status = btn.data('status');
        let badgeClass = status === 'paid' ? 'bg-success' : 'bg-warning';
        $('#vsStatus').html(`<span class="badge ${badgeClass}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`);
        $('#viewSlipModal').modal('show');
    });

});
</script>