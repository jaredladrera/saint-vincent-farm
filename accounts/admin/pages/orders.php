<?php
require_once '../../config/pdo_connection.php';

$order_list = [];

try {
    $db  = new Connect();
    $pdo = $db->connection;

    $stmt = $pdo->query("
        SELECT o.*, 
               CONCAT(u.first_name, ' ', u.last_name) AS customer_name
        FROM orders o
        LEFT JOIN user_profile u ON u.id = o.user_id
        ORDER BY o.created_at DESC
    ");

    $order_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $order_list = [];
}
?>

<div class="page-header mb-4">
    <h5>Orders</h5>
</div>

<!-- SEARCH -->
<input type="text" id="searchOrder" class="form-control mb-3" placeholder="Search customer...">

<table class="table" id="orderTable">
    <thead>
        <tr>
            <th>#</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody id="orderTableBody">
        <?php foreach ($order_list as $o): ?>
        <tr data-name="<?= strtolower($o['customer_name']) ?>">
            <td><?= $o['id'] ?></td>
            <td><?= htmlspecialchars($o['customer_name']) ?></td>
            <td>₱<?= number_format($o['total_amount'], 2) ?></td>
            <td>
                <span class="badge bg-info"><?= $o['order_status'] ?></span>
            </td>
            <td><?= $o['created_at'] ?></td>
            <td>
                <button class="btn btn-sm btn-primary viewOrderBtn"
                    data-id="<?= $o['id'] ?>">
                    View
                </button>

                <button class="btn btn-sm btn-success updateStatus"
                    data-id="<?= $o['id'] ?>"
                    data-status="<?= $o['order_status'] ?>">
                    Update Status
                </button>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<ul class="pagination" id="pagination"></ul>

<div class="modal fade" id="viewModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Order Details</h5>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Livestock</th>
                            <th>Qty</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody id="orderItemsBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="statusModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Update Status</h5>
            </div>
            <div class="modal-body">

                <input type="hidden" id="orderId">

                <select id="orderStatus" class="form-control">
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="shipped">Shipped</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                </select>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-success" id="saveStatus">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewOrderModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Order Summary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <!-- CUSTOMER INFO -->
                <div class="mb-3">
                    <strong>Customer:</strong> <span id="custName"></span><br>
                    <strong>Email:</strong> <span id="custEmail"></span><br>
                    <strong>Contact:</strong> <span id="custContact"></span><br>
                    <strong>Address:</strong> <span id="custAddress"></span>
                </div>

                <!-- ORDER INFO -->
                <div class="mb-3">
                    <strong>Status:</strong> <span id="orderStatusText"></span><br>
                    <strong>Date:</strong> <span id="orderDateText"></span>
                </div>

                <!-- ITEMS -->
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="orderItemsTableBody"></tbody>
                </table>

                <!-- TOTAL -->
                <div class="text-end">
                    <h5>Total: ₱<span id="orderTotal"></span></h5>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
$(function () {

    let rowsPerPage = 5;
    let currentPage = 1;

    function paginate() {
        let rows = $('#orderTableBody tr:visible');
        let total = rows.length;
        let pages = Math.ceil(total / rowsPerPage);

        rows.hide();
        rows.slice((currentPage - 1) * rowsPerPage, currentPage * rowsPerPage).show();

        let html = '';
        for (let i = 1; i <= pages; i++) {
            html += `<li class="page-item ${i===currentPage?'active':''}">
                        <a href="#" class="page-link pageBtn" data-page="${i}">${i}</a>
                    </li>`;
        }

        $('#pagination').html(html);
    }

    paginate();

    function formatStatus(status) {
        switch (status) {
            case 'pending': return '<span class="badge bg-warning">Pending</span>';
            case 'paid': return '<span class="badge bg-primary">Paid</span>';
            case 'shipped': return '<span class="badge bg-info">Shipped</span>';
            case 'delivered': return '<span class="badge bg-success">Delivered</span>';
            case 'cancelled': return '<span class="badge bg-danger">Cancelled</span>';
            default: return status;
        }
    }

    // SEARCH
    $('#searchOrder').keyup(function () {
        let val = $(this).val().toLowerCase();

        $('#orderTableBody tr').filter(function () {
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


    // VIEW ORDER ITEMS
    $(document).on('click', '.viewOrderBtn', function () {
        const id = $(this).data('id');

        $.ajax({
            url: 'ajax/get_order_items.php',
            type: 'POST',
            dataType: 'json',
            data: { id: id },
            success: function (res) {

                if (res.length === 0) {
                    $('#orderItemsTableBody').html(`<tr><td colspan="4" class="text-center">No items</td></tr>`);
                    return;
                }

                let first = res[0];

                // CUSTOMER
                $('#custName').text(first.first_name + ' ' + first.last_name);
                $('#custEmail').text(first.email_address);
                $('#custContact').text(first.contact_number);
                $('#custAddress').text(
                    `${first.address}`
                );

                // ORDER INFO
                $('#orderStatusText').html(formatStatus(first.order_status));
                $('#orderDateText').text(new Date(first.created_at).toLocaleString());

                // ITEMS
                let html = '';
                let total = 0;

                res.forEach(item => {
                    let subtotal = item.quantity * item.price;
                    total += subtotal;

                    html += `
                        <tr>
                            <td>${item.name}</td>
                            <td>${item.quantity}</td>
                            <td>₱${parseFloat(item.price).toFixed(2)}</td>
                            <td>₱${subtotal.toFixed(2)}</td>
                        </tr>
                    `;
                });

                $('#orderItemsTableBody').html(html);
                $('#orderTotal').text(total.toFixed(2));

                $('#viewOrderModal').modal('show');
            }
        });
    });


    // OPEN STATUS MODAL
    $(document).on('click', '.updateStatus', function () {

        let id = $(this).data('id');
        let status = $(this).data('status');

        $('#orderId').val(id);
        $('#orderStatus').val(status);

        $('#statusModal').modal('show');
    });


    // SAVE STATUS (NO RELOAD 🔥)
    $('#saveStatus').click(function () {

        let id = $('#orderId').val();
        let status = $('#orderStatus').val();

        $.post('ajax/update_order_status.php', {
            id: id,
            status: status
        }, function (res) {

            if (res.success) {

                let row = $(`button[data-id="${id}"]`).closest('tr');

                row.find('td:nth-child(4)')
                    .html(`<span class="badge bg-success">${status}</span>`);

                row.find('.updateStatus').data('status', status);

                $('#statusModal').modal('hide');

            }

        }, 'json');
    });

});
</script>