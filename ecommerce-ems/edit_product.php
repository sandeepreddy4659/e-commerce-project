<?php
session_start();
require_once "config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit;
}

$id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("❌ Product not found");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);

    $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, stock=? WHERE id=?");
    $stmt->execute([$name, $description, $price, $stock, $id]);

    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Product</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="card shadow p-4">
    <h3>✏️ Edit Product</h3>
    <form method="POST">
      <div class="mb-3">
        <label>Product Name</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']); ?>" required>
      </div>
      <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control"><?= htmlspecialchars($product['description']); ?></textarea>
      </div>
      <div class="mb-3">
        <label>Price</label>
        <input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price']; ?>" required>
      </div>
      <div class="mb-3">
        <label>Stock</label>
        <input type="number" name="stock" class="form-control" value="<?= $product['stock']; ?>" required>
      </div>
      <button class="btn btn-primary">Update</button>
      <a href="admin.php" class="btn btn-secondary">Cancel</a>
    </form>
  </div>
</div>
</body>
</html>
