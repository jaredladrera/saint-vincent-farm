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

endif;
 
?>