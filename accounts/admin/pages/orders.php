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

    $delivery = $pdo->query("SELECT * FROM delivery_details");

    $delivery_details = $delivery->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $order_list = [];
}

function getBadgeClass($status) {
    switch ($status) {
        case 'pending':
            return 'bg-warning text-dark'; // yellow
        case 'delivered':
            return 'bg-success'; // green
        case 'out_for_delivery':
            return 'bg-primary'; // blue
        case 'processing':
            return 'bg-info text-dark'; // light blue
        default:
            return 'bg-secondary'; // gray fallback
    }
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
                <span class="badge <?= getBadgeClass($o['order_status']) ?>">
                    <?= htmlspecialchars($o['order_status']) ?>
                </span>
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
                <button class="btn btn-sm btn-info text-white delivery_details" onclick="drOpenAdd(<?= $o['id'] ?>)">
                    <i class="bi bi-plus-lg"></i> Delivery Details
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
                    <option value="processing">Processing</option>
                    <option value="out_for_delivery">Out for Delivery</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                </select>

                <br>

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
                <div class="mb-3">
                    <h5>Delivery Details</h5>
                    <strong>Driver:</strong> <span id="driver"></span><br>
                    <strong>Vihecle Type:</strong> <span id="v_type"></span> <br>
                    <strong>Delivery Description:</strong> <span id="dd"></span><br>
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

                <!-- PROOF OF PAYMENT -->
                <div class="mb-3">
                    <strong>Proof of Payment:</strong>
                    <div class="mt-2">
                        <img id="proofOfPaymentImg"
                             src="assets/default-image.jpg"
                             alt="Proof of Payment"
                             style="max-width:100%;max-height:300px;object-fit:contain;border:1px solid #dee2e6;border-radius:6px;"
                             onerror="this.src='assets/default-image.jpg'">
                    </div>
                </div>

                <!-- TOTAL -->
                <div class="text-end">
                    <h5>Delivery Fee: ₱<span id="fee"></span></h5>
                    <h5>Total: ₱<span id="orderTotal"></span></h5>
                </div>

            </div>
        </div>
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

            <input type="hidden" name="action" id="drAction" value="create" />
            <input type="hidden" name="id"     id="drId"     value="" />

            <div class="dr-modal-body">

            <input type="hidden" id="orderId_rider">

                <div class="dr-form-row">
                    <div class="dr-field">
                        <label class="dr-label">Driver's Name <span class="dr-req">*</span></label>
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
                            <select name="vehicle_type" id="v_type_option" class="dr-input" required>
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

                <div class="dr-field">
                    <label class="dr-label">Delivery Fee (₱) <span class="dr-req">*</span></label>
                    <div class="dr-input-wrap">
                        <i class="bi bi-tag"></i>
                        <input
                            type="number"
                            name="cost_per_km"
                            id="delivery_fee"
                            class="dr-input"
                            placeholder="e.g. 150.00"
                            required
                        />
                    </div>
                </div>

            </div>

            <div class="dr-modal-footer">
                <button type="button" class="dr-btn-cancel" onclick="drCloseModal()">
                    Cancel
                </button>
                <button class="dr-btn-save" id="drSaveBtn" onclick="saveRider()">
                    <i class="bi bi-check-lg"></i> Save Rider
                </button>
            </div>

    </div>
</div>

<script>


// ── Open Add modal ──
function drOpenAdd(id) {

    $('#orderId_rider').val(id);

    if(!id){
        alert("Failed to fetch order id");
        return;
    }

    $.ajax({
        url: './../../shared/api.php',
        method: 'POST',
        dataType: 'json',
        data: { 
            key: 'check_rider',
            orderID: id
        },
        success: function(res) {

            if(res){
                $('#drId').val(res.id);
                $('#drName').val(res.name);
                $('#drDescription').val(res.description);
                $('#v_type_option').val(res.vehicle_type);
                $('#delivery_fee').val(res.delivery_fee);

                $('#drModalTitle').html('<i class="bi bi-pencil"></i> Edit Rider');
                $('#drAction').val('update');
                $('#drSaveBtn').html('<i class="bi bi-save"></i> Update Rider');

            } else {
                $('#drId').val('');
                $('#drName').val('');
                $('#drDescription').val('');
                $('#v_type_option').val('');
                $('#delivery_fee').val('');

                $('#drModalTitle').html('<i class="bi bi-person-plus"></i> Add Rider');
                $('#drAction').val('create');
                $('#drSaveBtn').html('<i class="bi bi-plus-lg"></i> Add Rider');
            }

            $('#drModal').addClass('show');
        }
    });
}
// ── Close modal ──
function drCloseModal() {
    document.getElementById('drModal').classList.remove('show');
}
function drCloseOutside(e) {
    if (e.target.id === 'drModal') drCloseModal();
}

function saveRider() {
    let orderId = $('#orderId_rider').val();
    let d_name = $('#drName').val();
    let v_type = $('#v_type_option').val();
    let desc = $('#drDescription').val();
    let fee = parseFloat($('#delivery_fee').val()) || 0;
    let drID = $('#drId').val();

    if(drID) {

        $.ajax({
            url: './../../shared/api.php',
            method: 'POST',
            dataType: 'text',
            data: { 
                key: 'update_rider', 
                drID,
                orderId,
                d_name,
                v_type,
                desc,
                fee
            },
            success: function(res) {
               alert("Success Assigned a Driver");
               drCloseModal();
            }
        });

    } else {

        $.ajax({
            url: './../../shared/api.php',
            method: 'POST',
            dataType: 'text',
            data: { 
                key: 'save_rider', 
                orderId,
                d_name,
                v_type,
                desc,
                fee
            },
            success: function(res) {
               alert("Success Assigned a Driver");
               drCloseModal();
            }
        });
    }


} 


$(function () {

    let rowsPerPage = 10;
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
                console.log("first", first);                    
                // CUSTOMER
                $('#custName').text(first.first_name + ' ' + first.last_name);
                $('#custEmail').text(first.email_address);
                $('#custContact').text(first.contact_number);
                $('#custAddress').text(
                    `${first.address}`
                );
                $('#fee').text(first.delivery_fee || '---');
                $('#dd').text(first.description || '---')
                $('#driver').text(first.name || 'No Driver Yet')
                $('#v_type').text(first.vehicle_type || '---')

                // PROOF OF PAYMENT IMAGE
                let proofImg = (first.proof_of_payment && first.proof_of_payment.trim() !== '')
                    ? first.proof_of_payment
                    : 'assets/default-image.jpg';
                $('#proofOfPaymentImg').attr('src', proofImg);

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
                            <td>${item.product_name}</td>
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
        // let drVehicle = $('#drVehicle').val();
        // let delivery_fee = $('#delivery_fee').val();

        if(delivery_fee <= 0) {
             alert("Need A Valid Input");
             return;                      
        }

        // alert(`fees ${delivery_fee}`)
        alert(`id ${id}`)

        $.post('ajax/update_order_status.php', {
            id: id,
            status: status
            // delivery_fee: delivery_fee
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