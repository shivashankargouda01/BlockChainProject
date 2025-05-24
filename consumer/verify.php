<?php
session_start();
include '../blockchain.php';
include '../db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'consumer') {
    header("Location: ../login.php");
    exit();
}

$scan_result = "";
$blockchain_data = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['qr_image']) && $_FILES['qr_image']['tmp_name']) {
        include_once '../phpqrcode/qrlib.php';
        include_once '../phpqrcode/qrreader.php';  // Needs to be included from library
        $qr_text = decodeQRCode($_FILES['qr_image']['tmp_name']);  // Implement or use a library
        if ($qr_text) {
            $scan_result = htmlspecialchars($qr_text);
            $product_id = getProductIdFromQR($qr_text);  // Extract ID from QR

            $blockchain_data = getProductBlockchainHistory($product_id);  // Function from blockchain.php
        } else {
            $scan_result = "❌ Unable to read QR code.";
        }
    }
}

function getProductIdFromQR($text) {
    if (preg_match('/Product ID:\s*(\d+)/', $text, $matches)) {
        return $matches[1];
    }
    return null;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify Product</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f9;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #444;
        }
        form {
            text-align: center;
            margin-bottom: 30px;
        }
        input[type="file"], input[type="submit"] {
            margin: 10px;
            padding: 10px;
            border-radius: 8px;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
        }
        .result, .block {
            background: white;
            padding: 20px;
            margin: 0 auto 20px;
            width: 80%;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .block {
            margin-bottom: 15px;
        }
        .back {
            display: block;
            text-align: center;
            margin-top: 30px;
            text-decoration: none;
            background: #007bff;
            color: white;
            padding: 10px;
            border-radius: 8px;
            width: 150px;
            margin-left: auto;
            margin-right: auto;
        }
        .back:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
<h2>Welcome, <?php echo $_SESSION['email']; ?> (Consumer)</h2>

<form method="post" enctype="multipart/form-data">
    <label>Select QR Code Image:</label>
    <input type="file" name="qr_image" accept="image/*" required>
    <input type="submit" value="Upload & Verify">
</form>

<?php if ($scan_result): ?>
    <div class="result">
        <h3>Scanned QR:</h3>
        <p><?= $scan_result ?></p>
    </div>
<?php endif; ?>

<?php if ($blockchain_data): ?>
    <h3 style="text-align:center;">Product Blockchain History</h3>
    <?php foreach ($blockchain_data as $block): ?>
        <div class="block">
            <p><strong>Block #<?= $block['index'] ?></strong></p>
            <p><strong>Timestamp:</strong> <?= $block['timestamp'] ?></p>
            <p><strong>Data:</strong></p>
            <pre><?= print_r($block['data'], true) ?></pre>
            <p><strong>Previous Hash:</strong> <?= $block['previous_hash'] ?></p>
            <p><strong>Hash:</strong> <?= $block['hash'] ?></p>
        </div>
    <?php endforeach; ?>
<?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <div class="result">
        <p>❌ Product not found in blockchain.</p>
    </div>
<?php endif; ?>

<a class="back" href="../logout.php">Logout</a>
</body>
</html>
