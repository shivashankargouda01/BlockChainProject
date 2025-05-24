<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../db_connect.php';
include '../phpqrcode/qrlib.php'; // Make sure this path is correct and qrlib.php exists

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = trim($_POST['product_id']);
    $seller_id = trim($_POST['seller_id']);
    $sold_date = date('Y-m-d');

    $stmt = $conn->prepare("UPDATE products SET seller_id = ?, sold_date = ? WHERE id = ?");
    $stmt->bind_param("isi", $seller_id, $sold_date, $product_id);

    if ($stmt->execute()) {
        // Generate QR with full data
        $stmt2 = $conn->prepare("SELECT p.*, u.email as seller_email FROM products p JOIN users u ON p.seller_id = u.id WHERE p.id = ?");
        $stmt2->bind_param("i", $product_id);
        $stmt2->execute();
        $result = $stmt2->get_result();
        $data = $result->fetch_assoc();

        if ($data) {
            $qr_content = "Product ID: {$data['id']}\nCrop Name: {$data['name']}\nBatch No: {$data['batch']}\nLocation: {$data['location']}\nSold Date: {$data['sold_date']}\nSeller Email: {$data['seller_email']}";
            $qr_path = "../qrcodes/qr_{$product_id}.png";
            QRcode::png($qr_content, $qr_path);

            $message = "<p class='success'>✅ Product assigned to seller successfully and QR generated.</p><img src='$qr_path' width='200' alt='QR Code'>";
        }
    } else {
        $message = "<p class='error'>❌ Error assigning product to seller.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Product to Seller</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f1f4f8;
            padding: 30px;
        }
        h2 {
            color: #2c3e50;
        }
        form {
            background: white;
            padding: 25px;
            max-width: 500px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .btn {
            margin-top: 20px;
            background: #3498db;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
        }
        .btn:hover {
            background: #2980b9;
        }
        .success {
            background: #d4edda;
            padding: 10px;
            color: #155724;
            border-left: 5px solid #28a745;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .error {
            background: #f8d7da;
            padding: 10px;
            color: #721c24;
            border-left: 5px solid #dc3545;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .back-btn {
            display: block;
            margin-top: 30px;
            text-align: center;
        }
        .back-btn a {
            color: #2980b9;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>Assign Product to Seller</h2>

<?php echo $message; ?>

<form method="POST">
    <label for="product_id">Product ID:</label>
    <input type="number" name="product_id" required>

    <label for="seller_id">Select Seller:</label>
    <select name="seller_id" required>
        <option value="">-- Choose Seller --</option>
        <?php
        $result = $conn->query("SELECT id, email FROM users WHERE role = 'seller'");
        while ($row = $result->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['email']} (ID: {$row['id']})</option>";
        }
        ?>
    </select>

    <button type="submit" class="btn">Assign Product</button>
</form>

<div class="back-btn">
    <a href="dashboard.php">⬅️ Back to Dashboard</a>
</div>

</body>
</html>
