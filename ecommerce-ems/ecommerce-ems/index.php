<?php
session_start();
require_once "config/db.php";

// ✅ Only logged-in users can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

// Fetch products
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Available Products</title>
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
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>🛍️ Available Products</h2>
    <div>
      <a href="cart.php" class="btn btn-warning">🛒 View Cart</a>
      <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
  </div>

  <div class="row">
    <?php foreach ($products as $p): ?>
      <div class="col-md-4">
        <div class="card mb-4 shadow-sm">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($p['name']) ?></h5>
            <p class="card-text"><?= htmlspecialchars($p['description']) ?></p>
            <p><strong>₹<?= number_format($p['price'], 2) ?></strong></p>
            <form method="POST" action="add_to_cart.php">
              <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
              <input type="number" name="quantity" value="1" min="1" class="form-control mb-2" style="width:100px;">
              <button type="submit" class="btn btn-success">➕ Add to Cart</button>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
</body>
</html>
