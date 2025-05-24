<?php
session_start();

// Check if user is logged in and role is consumer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'consumer') {
    header("Location: ../login.php");
    exit();
}

include('../db_connect.php');

$message = '';
$productData = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qr_data'])) {
    // Assume the consumer submits QR code JSON data as text (you can extend to file upload/scan)
    $qrDataJson = trim($_POST['qr_data']);

    // Decode JSON data
    $decoded = json_decode($qrDataJson, true);

    if ($decoded) {
        $productData = $decoded;
    } else {
        $message = "Invalid QR code data. Please enter valid JSON data.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Consumer Dashboard</title>
<style>
    body { font-family: Arial, sans-serif; max-width: 800px; margin: auto; padding: 20px; background: #f9f9f9; }
    header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    header h1 { color: #007bff; margin: 0; }
    a.logout { color: #fff; background: #dc3545; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; }
    a.logout:hover { background: #c82333; }
    .message { background: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; color: #721c24; border-radius: 5px; margin-bottom: 20px; }
    textarea { width: 100%; height: 150px; padding: 10px; font-family: monospace; font-size: 14px; border-radius: 5px; border: 1px solid #ccc; box-sizing: border-box; }
    input[type="submit"] { background: #007bff; color: white; border: none; padding: 10px 18px; border-radius: 5px; cursor: pointer; font-size: 16px; margin-top: 10px; }
    input[type="submit"]:hover { background: #0056b3; }
    .product-info { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px #ddd; }
    .product-info h2 { margin-top: 0; }
    .section { margin-bottom: 15px; }
</style>
</head>
<body>

<header>
    <h1>Consumer Dashboard</h1>
    <div>
        Welcome, <?= htmlspecialchars($_SESSION['username']); ?> (Consumer) | 
        <a href="../logout.php" class="logout">Logout</a>
    </div>
</header>

<?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message); ?></div>
<?php endif; ?>

<h2>Enter QR Code Data (JSON)</h2>
<form method="POST" action="">
    <textarea name="qr_data" placeholder='Paste QR code JSON data here...' required></textarea>
    <br />
    <input type="submit" value="View Product History" />
</form>

<?php if ($productData): ?>
    <div class="product-info">
        <h2>Product Details</h2>
        <div class="section">
            <strong>Product Code:</strong> <?= htmlspecialchars($productData['product']['product_code'] ?? 'N/A') ?><br />
            <strong>Name:</strong> <?= htmlspecialchars($productData['product']['name'] ?? 'N/A') ?><br />
            <strong>Batch:</strong> <?= htmlspecialchars($productData['product']['batch'] ?? 'N/A') ?><br />
            <strong>Harvest Date:</strong> <?= htmlspecialchars($productData['product']['harvest_date'] ?? 'N/A') ?><br />
            <strong>Location:</strong> <?= htmlspecialchars($productData['product']['location'] ?? 'N/A') ?><br />
        </div>
        <h3>Transaction History</h3>
        <div class="section">
            <strong>Farmer:</strong><br />
            Name: <?= htmlspecialchars($productData['history']['farmer']['username'] ?? $productData['history']['farmer']['name'] ?? 'N/A') ?><br />
            Email: <?= htmlspecialchars($productData['history']['farmer']['email'] ?? 'N/A') ?><br />
        </div>
        <div class="section">
            <strong>Seller:</strong><br />
            Name: <?= htmlspecialchars($productData['history']['seller']['username'] ?? $productData['history']['seller']['name'] ?? 'N/A') ?><br />
            Email: <?= htmlspecialchars($productData['history']['seller']['email'] ?? 'N/A') ?><br />
        </div>
        <div class="section">
            <strong>Consumer:</strong><br />
            Name: <?= htmlspecialchars($productData['history']['consumer']['name'] ?? 'N/A') ?><br />
            Email: <?= htmlspecialchars($productData['history']['consumer']['email'] ?? 'N/A') ?><br />
            Purchase Date: <?= htmlspecialchars($productData['history']['consumer']['purchase_date'] ?? 'N/A') ?><br />
        </div>
    </div>
<?php endif; ?>

</body>
</html>
