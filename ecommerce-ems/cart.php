<?php
session_start();
require_once "config/db.php";

// ✅ Only logged-in users
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch cart items with product details
$stmt = $pdo->prepare("
    SELECT cart.id AS cart_id, cart.quantity, products.name, products.price 
    FROM cart 
    JOIN products ON cart.product_id = products.id 
    WHERE cart.user_id = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>🛒 My Cart</h2>
    <div>
      <a href="index.php" class="btn btn-secondary">⬅ Back to Shop</a>
      <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
  </div>

  <?php if (empty($cart_items)): ?>
    <div class="alert alert-info">Your cart is empty.</div>
  <?php else: ?>
    <form method="POST" action="update_cart.php">
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>Product</th>
            <th>Price (₹)</th>
            <th>Quantity</th>
            <th>Total</th>
            <th>Remove</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $grand_total = 0;
          foreach ($cart_items as $item): 
              $total = $item['price'] * $item['quantity'];
              $grand_total += $total;
          ?>
            <tr>
              <td><?= htmlspecialchars($item['name']) ?></td>
              <td>₹<?= number_format($item['price'], 2) ?></td>
              <td>
                <input type="number" name="quantities[<?= $item['cart_id'] ?>]" 
                       value="<?= $item['quantity'] ?>" min="1" class="form-control" style="width:80px;">
              </td>
              <td>₹<?= number_format($total, 2) ?></td>
              <td>
                <a href="remove_from_cart.php?id=<?= $item['cart_id'] ?>" class="btn btn-danger btn-sm">❌</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <h4 class="text-end">Grand Total: ₹<?= number_format($grand_total, 2) ?></h4>
      <div class="d-flex justify-content-between">
        <button type="submit" class="btn btn-primary">🔄 Update Cart</button>
        <a href="checkout.php" class="btn btn-success">✅ Checkout</a>
      </div>
    </form>
  <?php endif; ?>
</div>
</body>
</html>
