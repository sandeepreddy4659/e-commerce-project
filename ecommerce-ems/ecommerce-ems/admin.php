<?php
session_start();
require_once "config/db.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    body {
      background: url('assets/images/bg-main.jpg') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
    }
    .container {
      background: rgba(255, 255, 255, 0.9);
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }
  </style>
</head>
<body>
<div class="container mt-4">
  <h2>👑 Admin Panel - Manage Products</h2>
  <a href="logout.php" class="btn btn-danger float-end">Logout</a>
  <a href="add_product.php" class="btn btn-success">➕ Add Product</a>
  <table class="table table-bordered mt-3">
    <thead>
      <tr><th>ID</th><th>Name</th><th>Price</th><th>Stock</th><th>Action</th></tr>
    </thead>
    <tbody>
      <?php foreach ($products as $p): ?>
        <tr>
          <td><?= $p['id']; ?></td>
          <td><?= htmlspecialchars($p['name']); ?></td>
          <td>₹<?= $p['price']; ?></td>
          <td><?= $p['stock']; ?></td>
          <td>
            <a href="edit_product.php?id=<?= $p['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
            <a href="delete_product.php?id=<?= $p['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
