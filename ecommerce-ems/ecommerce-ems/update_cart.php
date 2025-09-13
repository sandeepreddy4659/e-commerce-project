<?php
session_start();
require_once "config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['quantities'])) {
    foreach ($_POST['quantities'] as $cart_id => $quantity) {
        $quantity = max(1, intval($quantity));
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->execute([$quantity, $cart_id]);
    }
}

header("Location: cart.php");
exit;
?>
