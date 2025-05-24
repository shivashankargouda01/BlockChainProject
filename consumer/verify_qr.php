<?php
session_start();

include('../db/db_connection.php'); // if needed for user session
include('../blockchain/get_history.php'); // file containing blockchain reading function

// Function to get product history from blockchain file
function getProductHistory($productId) {
    $chainFile = '../blockchain/chain.json';
    if (!file_exists($chainFile)) {
        return false;
    }
    $chainData = json_decode(file_get_contents($chainFile), true);
    if (!$chainData) return false;

    $history = [];
    foreach ($chainData as $block) {
        if (isset($block['product_id']) && $block['product_id'] == $productId) {
            $history[] = $block;
        }
    }
    return count($history) > 0 ? $history : false;
}

// Get product ID from GET or POST (from QR)
$productId = isset($_GET['id']) ? $_GET['id'] : '';
$message = '';
$history = false;

if ($productId) {
    $history = getProductHistory($productId);
    if (!$history) {
        $message = "❌ Product history not found.";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify Product QR</title>
</head>
<body>
    <h2>Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'Guest') ?> (Consumer)</h2>
    <a href="../logout.php">Logout</a>
    <h3>Upload or Scan QR Code</h3>

    <form method="GET" enctype="multipart/form-data" action="">
        Product ID: <input type="text" name="id" value="<?= htmlspecialchars($productId) ?>" required>
        <button type="submit">Verify</button>
    </form>

    <?php if ($productId): ?>
        <h3>Scanned QR: Product ID: <?= htmlspecialchars($productId) ?></h3>

        <?php if ($history): ?>
            <h3>Product History:</h3>
            <ul>
                <?php foreach ($history as $index => $block): ?>
                    <li>
                        <strong><?= ($index + 1) ?>) [<?= $block['timestamp'] ?? 'N/A' ?>] <?= htmlspecialchars($block['event'] ?? 'Event Unknown') ?> by <?= htmlspecialchars($block['actor'] ?? 'Unknown') ?></strong><br>
                        <?php if (isset($block['details']) && is_array($block['details'])): ?>
                            <ul>
                            <?php foreach ($block['details'] as $key => $val): ?>
                                <li><?= htmlspecialchars(ucwords(str_replace('_', ' ', $key))) ?>: <?= htmlspecialchars($val) ?></li>
                            <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p><?= $message ?></p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
