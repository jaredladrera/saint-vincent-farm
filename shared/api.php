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
              "created_at" => date("Y-m-d")
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
                p.name, 
                p.price, 
                p.quantity AS stock_quantity
            FROM cart c
            JOIN livestock p ON c.product_id = p.id
            WHERE c.user_id = ?
        ");
        $stmt->execute([$user_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    endif;

    if ($key === 'updateCartQty'):
        $cart_id  = $_POST['cart_id'];
        $quantity = $_POST['quantity'];
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->execute([$quantity, $cart_id]);
        echo 'ok';
    endif;

    if ($key === 'removeFromCart'): 
        $cart_id = $_POST['cart_id'];
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ?");
        $stmt->execute([$cart_id]);
        echo 'ok';
    endif;

endif;
 
?>