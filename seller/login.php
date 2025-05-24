<?php
session_start();
include('../db/db_connection.php');

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($id, $hashed_password, $role);

    if ($stmt->fetch() && password_verify($password, $hashed_password) && $role === 'seller') {
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        header("Location: view_products.php");
        exit();
    } else {
        $error = "Invalid seller credentials.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Seller Login</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; }
        .login-container {
            max-width: 350px; margin: 80px auto; padding: 30px; background: white; box-shadow: 0 0 10px #ccc;
            border-radius: 8px;
        }
        h2 { text-align: center; margin-bottom: 20px; }
        input[type=text], input[type=password] {
            width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 4px;
        }
        button {
            width: 100%; padding: 10px; background: #4CAF50; color: white; border: none; border-radius: 4px;
            font-size: 16px; cursor: pointer;
        }
        button:hover { background: #45a049; }
        .error { color: red; text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="login-container">
    <h2>Seller Login</h2>
    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>
