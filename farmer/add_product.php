<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farmer') {
    header("Location: ../login.php");
    exit;
}

include '../db_connect.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_code = uniqid('P');
    $name = trim($_POST['name']);
    $batch = trim($_POST['batch']);
    $harvest = $_POST['harvest'];
    $location = trim($_POST['location']);
    $farmer_id = $_SESSION['user_id'];

    if ($name && $batch && $harvest && $location) {
        $stmt = $conn->prepare("INSERT INTO products (product_code, name, batch, harvest_date, location, farmer_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $product_code, $name, $batch, $harvest, $location, $farmer_id);
        if ($stmt->execute()) {
            $message = "✅ Product added successfully with Product Code: " . htmlspecialchars($product_code);
        } else {
            if (strpos($stmt->error, 'Duplicate entry') !== false) {
                $message = "❌ Product code already exists. Try again.";
            } else {
                $message = "❌ Error: " . $stmt->error;
            }
        }
        $stmt->close();
    } else {
        $message = "❌ Please fill all fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Product</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f3f4f6;
            padding: 20px;
            color: #111827;
        }
        h2 {
            color: #1f2937;
        }
        .container {
            max-width: 500px;
            margin: auto;
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        input[type="text"], input[type="date"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 8px;
            border: 1px solid #d1d5db;
        }
        input[type="submit"] {
            background-color: #3b82f6;
            color: white;
            border: none;
            cursor: pointer;
        }
        .back-btn {
            margin-top: 15px;
            display: inline-block;
            text-decoration: none;
            color: #3b82f6;
        }
        .message {
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Product</h2>
        <p>Welcome, <?= isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'Farmer' ?> | <a href="dashboard.php">Dashboard</a> | <a href="../logout.php">Logout</a></p>

        <form method="post" action="">
            <label>Product Name:</label>
            <input type="text" name="name" required>

            <label>Batch Number:</label>
            <input type="text" name="batch" required>

            <label>Harvest Date:</label>
            <input type="date" name="harvest" required>

            <label>Location:</label>
            <input type="text" name="location" required>

            <input type="submit" value="Add Product">
        </form>

        <?php if ($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>

        <a class="back-btn" href="dashboard.php">⬅️ Back to Dashboard</a>
    </div>
</body>
</html>
