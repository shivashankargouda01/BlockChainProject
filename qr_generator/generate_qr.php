<?php
// qr_generator/generate_qr.php
require_once('../phpqrcode/qrlib.php');
require_once('../blockchain.php');
include('../db/db_connection.php');

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("Invalid product ID.");
}
$product_id = intval($_GET['id']);

// Fetch complete blockchain history for this product
$history = getProductBlockchainHistory($product_id);
if (!$history) {
    die("No blockchain history found for product $product_id.");
}

// Encode history as JSON string
$qrData = json_encode($history, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

// Prepare folder
$folder = '../qr_codes/';
if (!is_dir($folder)) mkdir($folder, 0755, true);

// File path
$filename = $folder . 'product_' . $product_id . '.png';

// Generate QR
QRcode::png($qrData, $filename, QR_ECLEVEL_Q, 4);

?>
<!DOCTYPE html>
<html>
<head>
    <title>QR Code for Product <?= $product_id ?></title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f9f9f9;
            padding: 40px;
            text-align: center;
        }
        .qr-img {
            border: 1px solid #ccc;
            padding: 10px;
            background: white;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
        }
        a:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h2>QR Code for Product #<?= $product_id ?></h2>
    <img class="qr-img" src="<?= $filename ?>" alt="QR Code"><br>
    <a href="../farmer/dashboard.php">⬅️ Back to Farmer Dashboard</a>
</body>
</html>
