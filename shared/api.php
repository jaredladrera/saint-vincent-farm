<?php 
include "crud.php";
// include "./../config/pdo_connection.php";
$obj = new CrudOperation;

$conn = new Connect();
$pdo = $conn->connection; // ← your PDO instance


if(isset($_POST['key'])):
    $key = $_POST['key'];
    $message = "Add to the cart";

    if($key == 'addToCart'): 
        $data = array(
              "product_id" => $_POST["product_id"],
              "user_id" => $_POST["user_id"],
              "quantity" => $_POST["quantity"],
              "amount" => $_POST["amount"],
              "created_at" => date("Y-m-d"),
              "cart" => true
        );

        $obj->insertAny("cart", $data, $message);

    endif;

    if ($key == 'getCart'): 
        $user_id = $_POST['user_id'];
        $stmt = $pdo->prepare("
            SELECT 
                c.id, 
                c.quantity AS cart_quantity, 
                c.amount, 
                c.product_id,
                p.name, 
                p.price, 
                p.quantity AS stock_quantity
            FROM cart c
            JOIN livestock p ON c.product_id = p.id
            WHERE c.user_id = ? AND c.cart = 1
        ");
        $stmt->execute([$user_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    endif;

    if ($key === 'updateCartQty'):
        $cart_id  = $_POST['cart_id'];
        $quantity = $_POST['quantity'];
        $amount   = $_POST['amount'];
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ?, amount = ? WHERE id = ?");
        $stmt->execute([$quantity, $amount, $cart_id]);
        echo 'ok';
    endif;

    if ($key === 'removeFromCart'): 
        $cart_id = $_POST['cart_id'];
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ?");
        $stmt->execute([$cart_id]);
        echo 'ok';
    endif;


    if($key === 'checkout'):
        $data = array(
              "user_id" => $_POST["user_id"],
              "order_status" => $_POST["order_status"],
              "total_amount" => $_POST["total_amount"],
              "mode_of_payment" => $_POST["mode_of_payment"],
              "notes" => $_POST["notes"],
              "prefered_delivery_date" => $_POST["prefered_delivery_date"],
              "created_at" => date("Y-m-d"),
              "order_ids" => $_POST["order_ids"]
        );

       $inserted_id = $obj->insertGetId("orders", $data);

       echo $inserted_id;
        
    endif;


  if($key === 'updateCart'):
        $ids = array_map('intval', explode(',', $_POST['order_ids']));
        $user_id = $_POST['user_id'];

        // safety check
        if (empty($ids) || !is_array($ids)) {
            exit('Invalid IDs');
        }

        // create ?,?,?
        // $placeholders = implode(',', array_fill(0, count($ids), '?'));

        // echo "placeholder". $placeholders;

        //add user_id condition
        $sql = "UPDATE cart SET cart = 0 WHERE user_id = ?";

        $stmt = $pdo->prepare($sql);

        // merge ids + user_id
        // $params = array_merge([$user_id]);

        $stmt->execute([$user_id]);

        if ($stmt->rowCount() > 0) {
            echo 'ok';
        } else {
            echo 'No rows updated (check IDs or user_id)';
        }

    endif;

    if($key === "changePassword"): 

        $old_password = $_POST['old_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $user_id = $_POST['login_id'];
        
        if (empty($old_password) || empty($new_password)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']); exit;
        }
        if (strlen($new_password) < 8) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters.']); exit;
        }
        
        try {
            $db   = new Connect();
            $stmt = $db->connection->prepare("SELECT password FROM user_profile WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
        
        if (!$user || $old_password !== $user->password) {
            echo json_encode([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ]);
            exit;
        }
                
            // $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $upd    = $db->connection->prepare("UPDATE user_profile SET password = ? WHERE id = ?");
            $upd->execute([$new_password, $user_id]);
        
            echo json_encode(['success' => true, 'message' => 'Password updated successfully!']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
        }


    endif;


    if ($key === 'insertCartBulk'):

        $order_id = $_POST['order_id'] ?? null;
        $cart_data = $_POST['cart_data'] ?? '[]';

        if (!$order_id) {
            echo json_encode([
                'status' => false,
                'message' => 'Missing order_id'
            ]);
            exit;
        }

        // Decode JSON
        $cartItems = json_decode($cart_data, true);

        if (!is_array($cartItems) || empty($cartItems)) {
            echo json_encode([
                'status' => false,
                'message' => 'Invalid cart data'
            ]);
            exit;
        }

        try {
            $pdo->beginTransaction();

            // Prepare insert
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_name, quantity, price)
                VALUES (?, ?, ?, ?)
            ");

            $deductStock = $pdo->prepare("
                UPDATE livestock SET quantity = quantity - ? WHERE id = ? AND quantity >= ?
            ");

            foreach ($cartItems as $item) {

                // ⚠️ Adjust keys depending on your cart structure
                $product_name = $item['livestock_name'];
                $quantity     = $item['cart_quantity'];
                $price        = $item['livestock_price'];
                $product_id   = $item['livestock_id'];

                $stmt->execute([
                    $order_id,
                    $product_name,
                    $quantity,
                    $price
                ]);

                // Deduct stock — only if enough stock exists (quantity >= ordered qty)
                $deductStock->execute([$quantity, $product_id, $quantity]);
            }

            $pdo->commit();

            echo json_encode([
                'status' => true,
                'message' => 'Bulk insert successful'
            ]);

        } catch (Exception $e) {
            $pdo->rollBack();

            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }

    endif;

    if($key === "save_rider"):

        $data = array(
              "order_id" => $_POST["orderId"],
              "description" => $_POST["desc"],
              "delivery_fee" => $_POST["fee"],
              "name" => $_POST["d_name"],
              "vehicle_type" => $_POST["v_type"]
        );

        $obj->insertAny("delivery_details", $data, "Save Success");

    endif;

    if($key === "check_rider"):

        $order_id = $_POST['orderID'] ?? null;

        if(!$order_id){
            echo json_encode([
                'status' => false,
                'message' => 'Order ID is required'
            ]);
            exit;
        }

        $stmt = $pdo->prepare("
            SELECT 
                id,
                name,
                description,
                vehicle_type,
                order_id,
                delivery_fee
            FROM delivery_details
            WHERE order_id = ?
        ");

        $stmt->execute([$order_id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode($result);

    endif;

    if($key === "update_rider"):

        $stmt = $pdo->prepare("
            UPDATE delivery_details 
            SET 
                name = ?, 
                description = ?, 
                vehicle_type = ?, 
                delivery_fee = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $_POST['d_name'],
            $_POST['desc'],
            $_POST['v_type'],
            $_POST['fee'],
            $_POST['drID']
        ]);

        echo "updated";

    endif;

    if($key === "save_payroll"):

        $stmt = $pdo->prepare("
            INSERT INTO payroll (
                user_id,
                period_start,
                period_end,
                daily_rate,
                ot_pay,
                sss,
                pagibig,
                philhealth,
                late_deduction,
                other_deduction,
                net_pay,
                status,
                total_deduction,
                basic_pay,
                created_at
            ) VALUES (
                :user_id,
                :period_start,
                :period_end,
                :daily_rate,
                :ot_pay,
                :sss,
                :pagibig,
                :philhealth,
                :late_deduction,
                :other_deduction,
                :net_pay,
                :status,
                :total_deduction,
                :basic_pay,
                CURDATE()
            )
        ");

        $stmt->execute([
            ':user_id'         => (int)$_POST['user_id'],
            ':period_start'    => $_POST['period_start'],
            ':period_end'      => $_POST['period_end'],
            ':daily_rate'      => (float)($_POST['daily_rate'] ?? 0),
            ':ot_pay'          => (float)($_POST['ot_pay'] ?? 0),
            ':sss'             => (float)($_POST['sss'] ?? 0),
            ':pagibig'         => (float)($_POST['pagibig'] ?? 0),
            ':philhealth'      => (float)($_POST['philhealth'] ?? 0),
            ':late_deduction'  => (float)($_POST['late_deduction'] ?? 0),
            ':other_deduction' => (float)($_POST['other_deduction'] ?? 0),
            ':net_pay'         => (float)($_POST['net_pay'] ?? 0),
            ':status'          => $_POST['status'] === 'paid' ? 1 : 0,
            ':total_deduction' => (float)($_POST['total_deduction'] ?? 0),
            ':basic_pay'       => (float)($_POST['basic_pay'] ?? 0),
        ]);

        $newId = $pdo->lastInsertId();

        echo json_encode([
            'success' => true,
            'id'      => $newId,
            'message' => 'Payslip saved successfully.'
        ]);

    endif;

    if($key === "update_payroll"):

        $stmt = $pdo->prepare("
            UPDATE payroll SET
                period_start    = :period_start,
                period_end      = :period_end,
                daily_rate      = :daily_rate,
                basic_pay       = :basic_pay,
                ot_pay          = :ot_pay,
                sss             = :sss,
                philhealth      = :philhealth,
                pagibig         = :pagibig,
                late_deduction  = :late_deduction,
                other_deduction = :other_deduction,
                total_deduction = :total_deduction,
                net_pay         = :net_pay,
                status          = :status
            WHERE id = :id
        ");

        $stmt->execute([
            ':period_start'    => $_POST['period_start'],
            ':period_end'      => $_POST['period_end'],
            ':daily_rate'      => (float)$_POST['daily_rate'],
            ':basic_pay'       => (float)$_POST['basic_pay'],
            ':ot_pay'          => (float)($_POST['ot_pay'] ?? 0),
            ':sss'             => (float)($_POST['sss'] ?? 0),
            ':philhealth'      => (float)($_POST['philhealth'] ?? 0),
            ':pagibig'         => (float)($_POST['pagibig'] ?? 0),
            ':late_deduction'  => (float)($_POST['late_deduction'] ?? 0),
            ':other_deduction' => (float)($_POST['other_deduction'] ?? 0),
            ':total_deduction' => (float)($_POST['total_deduction'] ?? 0),
            ':net_pay'         => (float)$_POST['net_pay'],
            ':status'          => $_POST['status'] === 'paid' ? 1 : 0,
            ':id'              => (int)$_POST['id'],
        ]);

        echo json_encode(['success' => true]);

    endif;

    if($key === "delete_payroll"):

        $stmt = $pdo->prepare("DELETE FROM payroll WHERE id = ?");
        $stmt->execute([(int)$_POST['id']]);

        echo json_encode(['success' => true]);

    endif;

endif;
 
?>