<!-- ══ ORDER REQUEST / PAYMENT PAGE ══ -->
<?php
$payment_options = [
    'Cash on Delivery',
    'GCash',
    'Bank Transfer',
    'Cash on Pickup',
];


// Cart total from session
$cart_items   = $_SESSION['cart']         ?? [];
$cart_total   = 0;
$order_total = $_GET['grandtotal'] ?? 0;
foreach ($cart_items as $item) {
    $cart_total += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
}

$db = new Connect();
$pdo = $db->connection;


// getting grandtotal

$stmt = $pdo->prepare("
    SELECT 
        SUM(amount) AS grand_total,
         GROUP_CONCAT(DISTINCT product_id) AS product_ids
    FROM cart
    WHERE user_id = ? AND cart = 1
");

$stmt->execute([$user['id']]);

$result = $stmt->fetch();
$grand_total = $result->grand_total ?? 0;
$product_ids = $result->product_ids ?? [];

// get all my cart
$getMyCart = $pdo->prepare("
    SELECT 
        c.id AS cart_id,
        c.user_id AS cart_user_id,
        c.product_id AS cart_product_id,
        c.quantity AS cart_quantity,
        c.amount AS cart_amount,
        c.created_at AS cart_created_at,
        c.cart AS cart_status,

        l.id AS livestock_id,
        l.category AS livestock_category,
        l.name AS livestock_name,
        l.quantity AS livestock_stock,
        l.price AS livestock_price,
        l.is_vaccinated AS livestock_is_vaccinated,
        l.condition_notes AS livestock_condition,
        l.health_score AS livestock_health_score,
        l.date_created AS livestock_created_date

    FROM cart AS c
    INNER JOIN livestock AS l 
        ON c.product_id = l.id
    WHERE c.user_id = ? AND c.cart = 1;
");

$getMyCart->execute([$user['id']]);

$myCart = $getMyCart->fetchAll(PDO::FETCH_ASSOC);
$jsonCart = json_encode($myCart);

?>

<div class="page-hero">
    <div class="container">
        <div class="section-label">Checkout</div>
        <h1>Order <span style="color:var(--amber)">Summary</span></h1>
        <p>Review your details and confirm your order below.</p>
    </div>
</div>

<div class="page-content">
    <div class="container">
        <div class="row g-4">

            <!-- Order Form -->
            <div class="col-lg-7">
                <div class="contact-form-wrap">
                    <h5 style="font-weight:700;color:var(--green-deep);margin-bottom:1.5rem;font-size:1.05rem">
                        Order & Payment Details
                    </h5>

                    <!-- Customer Info (from session, read-only) -->
                    <div class="session-info-block" style="background:var(--green-pale,#f3faf3);border:1px solid #c8e6c9;border-radius:10px;padding:1.1rem 1.3rem;margin-bottom:1.5rem;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                            <span style="font-weight:700;color:var(--green-deep);font-size:0.95rem;">
                                <i class="bi bi-person-check-fill"></i> Customer Details
                            </span>
                            <a href="index.php?page=profile" style="font-size:0.8rem;color:var(--amber);text-decoration:none;">
                                <i class="bi bi-pencil-square"></i> Edit Profile
                            </a>
                        </div>
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <div style="font-size:0.78rem;color:#777;margin-bottom:2px;">Full Name</div>
                                <div style="font-weight:600;color:#222;"><?= htmlspecialchars($user["name"]) ?: '<span style="color:#bbb;font-style:italic;">Not set</span>'; ?></div>
                            </div>
                            <div class="col-sm-6">
                                <div style="font-size:0.78rem;color:#777;margin-bottom:2px;">Contact Number</div>
                                <div style="font-weight:600;color:#222;"><?= htmlspecialchars($user["contact"]) ?: '<span style="color:#bbb;font-style:italic;">Not set</span>'; ?></div>
                            </div>
                            <div class="col-sm-6" style="margin-top:0.5rem;">
                                <div style="font-size:0.78rem;color:#777;margin-bottom:2px;">Email Address</div>
                                <div style="font-weight:600;color:#222;"><?= htmlspecialchars($user["email"]) ?: '<span style="color:#bbb;font-style:italic;">Not set</span>'; ?></div>
                            </div>
                            <div class="col-sm-6" style="margin-top:0.5rem;">
                                <div style="font-size:0.78rem;color:#777;margin-bottom:2px;">Delivery Address</div>
                                <div style="font-weight:600;color:#222;"><?= htmlspecialchars($user["address"]) ?: '<span style="color:#bbb;font-style:italic;">Not set</span>'; ?></div>
                            </div>
                        </div>

                        <!-- Hidden fields to carry session values into form submission -->
                        <input type="hidden" name="full_name" value="<?= htmlspecialchars($user["name"]) ?>">
                        <input type="hidden" name="contact"   value="<?= htmlspecialchars($user["contact"]) ?>">
                        <input type="hidden" name="email"     value="<?= htmlspecialchars($user["email"]) ?>">
                        <input type="hidden" name="address"   value="<?= htmlspecialchars($user["address"]) ?>">
                    </div>

                    <div id="orderForm">
                        <div class="row g-3">
<!-- 
                            <div class="col-sm-6">
                                <label class="form-label-sm">Quantity / Weight</label>
                                <input type="text" name="quantity" class="form-control-custom"
                                       placeholder="e.g. 2 heads / 5 kg" />
                            </div> -->

                            <div class="col-12">
                                <label class="form-label-sm">Preferred Delivery Date</label>
                                <input type="date" name="delivery_date" class="form-control-custom" id="prefered_delivery_date"/>
                            </div>

                            <div class="col-12">
                                <label class="form-label-sm">Payment Method</label>
                                <select name="payment" class="form-control-custom" id="payment_method" required>
                                    <?php foreach ($payment_options as $pay): ?>
                                        <option value="<?= $pay ?>"><?= $pay ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label-sm">Additional Notes</label>
                                <textarea name="notes" class="form-control-custom" rows="3" id="note"
                                          placeholder="Special requests, slaughter preference, etc."></textarea>
                            </div>

                        </div>

                        <!-- Order Total -->
                        <div style="display:flex;align-items:center;justify-content:space-between;
                                    background:var(--green-pale,#f3faf3);border:1px solid #c8e6c9;
                                    border-radius:10px;padding:0.9rem 1.2rem;margin-top:1.2rem;">
                            <span style="font-size:1rem;font-weight:600;color:var(--green-deep);">
                                <i class="bi bi-receipt"></i> Order Total
                            </span>
                            <span style="font-size:1.25rem;font-weight:800;color:var(--amber);">
                                ₱<?= number_format($grand_total, 2) ?>
                            </span>
                        </div>
                        <input type="hidden" name="total_amount" value="<?= $cart_total ?>">
                        <?php if($grand_total > 0): ?>
                            <button type="submit" class="btn-submit" onclick="confirm_order(<?= $user['id']; ?>, <?= $grand_total; ?>, '<?= $product_ids; ?>' )">
                                <i class="bi bi-bag-check"></i> Confirm Order
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-5">
                <div class="contact-info-card">
                    <h4>Contact Information</h4>
                    <p>We're available to assist you with inquiries, orders, and delivery arrangements.</p>

                    <div class="contact-row">
                        <div class="contact-icon"><i class="bi bi-geo-alt-fill"></i></div>
                        <div>
                            <h6>Farm Address</h6>
                            <p>St. Vincent Farm, Batangas, Calabarzon, Philippines</p>
                        </div>
                    </div>

                    <div class="contact-row">
                        <div class="contact-icon"><i class="bi bi-telephone-fill"></i></div>
                        <div>
                            <h6>Phone / Viber</h6>
                            <p>+63 9XX XXX XXXX</p>
                        </div>
                    </div>

                    <div class="contact-row">
                        <div class="contact-icon"><i class="bi bi-envelope-fill"></i></div>
                        <div>
                            <h6>Email</h6>
                            <p>orders@stvincentfarm.ph</p>
                        </div>
                    </div>

                    <div class="contact-row">
                        <div class="contact-icon"><i class="bi bi-clock-fill"></i></div>
                        <div>
                            <h6>Operating Hours</h6>
                            <p>Monday – Saturday<br>6:00 AM – 5:00 PM</p>
                        </div>
                    </div>

                    <div class="contact-note">
                        <p>Delivery available within Batangas area. Advance orders recommended especially for events.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    //console.log(cartData);
    
//confirm order 
// function confirm_order(user_id, grandTotal, product_ids) {
//     const cartData = <?= $jsonCart ?>;
//     const payment_method = $("#payment_method").val();
//     const prefered_delivery_date = $("#prefered_delivery_date").val();
//     const notes = $("#note").val();
//     // const prefered_delivery_date = $("#prefered_delivery_date").val();

//     // alert([...product_ids])
//     const idArray = product_ids.split(',').map(Number);

//     if(!prefered_delivery_date){
//         alert("Need prefered date to fill up");
//         return;
//     }

//     $.ajax({
//         url: './shared/api.php',
//         method: 'POST',
//         dataType: 'text',
//         data: {
//             key: 'checkout',
//             user_id,
//             order_status: 'pending',
//             total_amount: grandTotal,
//             mode_of_payment: payment_method,
//             notes: notes ? notes : 'no notes',
//             prefered_delivery_date,
//             order_ids: product_ids
//         },
//         success: function(response) {

//             // console.log("response", response);
//             // header
//             if(response) {
//                 if(payment_method === 'GCash' || payment_method === 'Bank Transfer') {
//                     // window.location.href = `page.php?upload=${user_id}&status=${status}`;
//                     window.location.href = `index.php?page=upload_proof&total=${grandTotal}&payment_mode=${payment_method}&inserted_id=${response}`;
//                 } else {

//                     $.ajax({
//                         url: './shared/api.php',
//                         method: 'POST',
//                         dataType: 'json',
//                         data: {
//                             key: 'insertCartBulk',
//                             cart_data: JSON.stringify(cartData) // 🔥 important
//                         },
//                         success: function(res) {
//                             console.log(res);
//                         },
//                         error: function(err) {
//                             console.error(err.responseText);
//                         }
//                     });

//                     // update cart
//                     $.ajax({
//                         url: './shared/api.php',
//                         method: 'POST',
//                         dataType: 'text',
//                         data: {
//                             key: 'updateCart',
//                             user_id,
//                             order_ids: product_ids
//                         },
//                         success: (res) => {
//                                 window.location.href =
//                                     `index.php?page=order_success&payment=${payment_method}`;
                            
//                         },
//                         error: (er) => {
//                             console.error('Checkout failed:', er.responseText);
//                         }
//                     });
//                     // alert("hello");
//                     //update the cart / remove to the cart

//                 }
//             } else {
//                 alert("Issue on checking out order");
//             }
//         },
//         error: function(xhr) {
//             console.error('Checkout failed:', xhr.responseText);
//         }
//     })

// }

const cartDataTest = <?= $jsonCart ?>;
console.log(cartDataTest)
function confirm_order(user_id, grandTotal, product_ids) {
    const cartData = <?= $jsonCart ?>;
    const payment_method = $("#payment_method").val();
    const prefered_delivery_date = $("#prefered_delivery_date").val();
    const notes = $("#note").val();

    if (!prefered_delivery_date) {
        alert("Need preferred delivery date");
        return;
    }

    // STEP 1: checkout
    $.ajax({
        url: './shared/api.php',
        method: 'POST',
        dataType: 'text',
        data: {
            key: 'checkout',
            user_id: user_id,
            order_status: 'pending',
            total_amount: grandTotal,
            mode_of_payment: payment_method,
            notes: notes ? notes : 'no notes',
            prefered_delivery_date: prefered_delivery_date,
            order_ids: product_ids
        },
        success: function(order_id) {

            if (!order_id) {
                alert("Issue on checking out order");
                return;
            }

            // If online payment → redirect immediately
            if (payment_method === 'GCash' || payment_method === 'Bank Transfer') {
                window.location.href =
                    `index.php?page=upload_proof&total=${grandTotal}&payment_mode=${payment_method}&inserted_id=${order_id}`;
                return;
            }

            // STEP 2: insertCartBulk
            $.ajax({
                url: './shared/api.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    key: 'insertCartBulk',
                    order_id: order_id,
                    cart_data: JSON.stringify(cartData)
                },
                success: function(res) {
                    console.log("response order_items", res);
                    // STEP 3: updateCart
                    $.ajax({
                        url: './shared/api.php',
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            key: 'updateCart',
                            user_id: user_id,
                            order_ids: product_ids
                        },
                        success: function(res2) {

                            // FINAL: redirect only when ALL succeed
                            window.location.href =
                               `index.php?page=order_success&payment=${payment_method}`;
                        },
                        error: function(err) {
                            console.error('updateCart failed:', err.responseText);
                        }
                    });

                },
                error: function(err) {
                    console.error('insertCartBulk failed:', err.responseText);
                }
            });

        },
        error: function(xhr) {
            console.error('Checkout failed:', xhr.responseText);
        }
    });
}


</script>