<?php
session_start();
require_once "config/db.php";

// ✅ Only logged-in users
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch cart items
$stmt = $pdo->prepare("
    SELECT cart.id AS cart_id, cart.quantity, products.id AS product_id, products.name, products.price 
    FROM cart 
    JOIN products ON cart.product_id = products.id 
    WHERE cart.user_id = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cart_items)) {
    echo "<h2 style='text-align:center; margin-top:50px;'>🛒 Your cart is empty!</h2>";
    echo "<div style='text-align:center;'><a href='index.php'>⬅ Back to Shop</a></div>";
    exit;
}

// Calculate total
$grand_total = 0;
foreach ($cart_items as $item) {
    $grand_total += $item['price'] * $item['quantity'];
}

try {
    $pdo->beginTransaction();

    // Insert into orders
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
    $stmt->execute([$user_id, $grand_total]);
    $order_id = $pdo->lastInsertId();

    // Insert order items
    $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($cart_items as $item) {
        $stmt_item->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);

        // Reduce stock from products
        $stmt_stock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt_stock->execute([$item['quantity'], $item['product_id']]);
    }

    // Clear cart
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);

    $pdo->commit();

    echo "<h2 style='text-align:center; margin-top:50px; color:green;'>✅ Order placed successfully!</h2>";
    echo "<div style='text-align:center;'>
            <a href='orders.php' class='btn btn-primary'>📦 View My Orders</a>
            <a href='index.php' class='btn btn-secondary'>⬅ Continue Shopping</a>
          </div>";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "<h2 style='text-align:center; margin-top:50px; color:red;'>❌ Something went wrong: " . $e->getMessage() . "</h2>";
}
?>
