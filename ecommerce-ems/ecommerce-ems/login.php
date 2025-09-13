<?php
session_start();
require_once "config/db.php"; // ensure this file exists and provides $pdo

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = "⚠️ Please enter both email and password.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $stored = $user['password'];

                // Accept password_hash() (password_verify) OR old MD5 (compatibility)
                $isValid = false;
                if (password_verify($password, $stored)) {
                    $isValid = true;
                } elseif ($stored === md5($password)) {
                    $isValid = true;
                }

                if ($isValid) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['user'] = $user;
                    $_SESSION['name'] = $user['name'];

                    // Redirect by role
                    if ($user['role'] === 'admin') {
                        header("Location: admin.php");
                    } else {
                        header("Location: index.php");
                    }
                    exit;
                } else {
                    $error = "❌ Invalid email or password.";
                }
            } else {
                $error = "❌ Invalid email or password.";
            }
        } catch (PDOException $e) {
            $error = "❌ Something went wrong. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Login — E-Commerce EMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">
  <div class="login-box">
    <h3 class="mb-3 text-center">🔑 Login</h3>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required 
               value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>

    <div class="mt-3 text-center">
      <small>No account? <a href="register.php">Register here</a></small>
    </div>
  </div>
</body>
</html>
