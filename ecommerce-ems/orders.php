<?php
session_start();
require_once "config/db.php";

// ✅ Only logged-in users
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Orders</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>📦 My Orders</h2>
    <a href="index.php" class="btn btn-secondary">⬅ Back to Shop</a>
  </div>

  <?php if (empty($orders)): ?>
    <div class="alert alert-info">You have no orders yet.</div>
  <?php else: ?>
    <?php foreach ($orders as $order): ?>
      <div class="card mb-3">
        <div class="card-header">
          Order #<?= $order['id'] ?> | Total: ₹<?= number_format($order['total'], 2) ?> | Date: <?= $order['created_at'] ?>
        </div>
        <div class="card-body">
          <?php
          $stmt_items = $pdo->prepare("
              SELECT order_items.quantity, order_items.price, products.name
              FROM order_items 
              JOIN products ON order_items.product_id = products.id
              WHERE order_items.order_id = ?
          ");
          $stmt_items->execute([$order['id']]);
          $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
          ?>
          <ul>
            <?php foreach ($items as $i): ?>
              <li><?= htmlspecialchars($i['name']) ?> (x<?= $i['quantity'] ?>) - ₹<?= number_format($i['price'] * $i['quantity'], 2) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
</body>
</html>
