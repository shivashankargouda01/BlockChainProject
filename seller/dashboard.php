<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    header("Location: ../login.php");
    exit();
}

include('../db_connect.php');
include('../phpqrcode/qrlib.php');  // Adjust path as needed

$seller_id = $_SESSION['user_id'];
$message = '';

// Process form submission for marking product sold
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['final_sold'])) {
    $product_id = intval($_POST['product_id']);
    $sold_date = $_POST['sold_date'];
    $consumer_name = trim($_POST['consumer_name']);
    $consumer_email = trim($_POST['consumer_email']);

    // Update product status to 'sold', set sold_date and seller_id
    $stmt = $conn->prepare("UPDATE products SET status = 'sold', sold_date = ?, seller_id = ? WHERE id = ?");
    $stmt->bind_param("sii", $sold_date, $seller_id, $product_id);
    if ($stmt->execute()) {
        // Fetch product info
        $productSql = "SELECT * FROM products WHERE id = ?";
        $stmt2 = $conn->prepare($productSql);
        $stmt2->bind_param("i", $product_id);
        $stmt2->execute();
        $productResult = $stmt2->get_result();
        $product = $productResult->fetch_assoc();
        $stmt2->close();

        // Fetch farmer info (users table has username, email)
        $farmerSql = "SELECT username, email FROM users WHERE id = ?";
        $stmt3 = $conn->prepare($farmerSql);
        $stmt3->bind_param("i", $product['farmer_id']);
        $stmt3->execute();
        $farmerResult = $stmt3->get_result();
        $farmer = $farmerResult->fetch_assoc();
        $stmt3->close();

        // Fetch seller info (changed name → username)
        $sellerSql = "SELECT username, email FROM users WHERE id = ?";
        $stmt4 = $conn->prepare($sellerSql);
        $stmt4->bind_param("i", $seller_id);
        $stmt4->execute();
        $sellerResult = $stmt4->get_result();
        $seller = $sellerResult->fetch_assoc();
        $stmt4->close();

        // Prepare JSON data for QR code (blockchain style)
        $qrData = json_encode([
            'product' => [
                'product_code' => $product['product_code'],
                'name' => $product['name'],
                'batch' => $product['batch'],
                'harvest_date' => $product['harvest_date'],
                'location' => $product['location']
            ],
            'history' => [
                'farmer' => $farmer,
                'seller' => $seller,
                'consumer' => [
                    'name' => $consumer_name,
                    'email' => $consumer_email,
                    'purchase_date' => $sold_date
                ]
            ]
        ], JSON_PRETTY_PRINT);

        // Generate QR code image file
        $qrDir = "../qrcodes/";
        if (!file_exists($qrDir)) mkdir($qrDir, 0755, true);
        $qrFile = $qrDir . "product_" . $product_id . ".png";
        QRcode::png($qrData, $qrFile);

        $message = "Product marked as sold and QR Code generated successfully.";
    } else {
        $message = "Error marking product as sold.";
    }
    $stmt->close();
}

// Fetch assigned products for this seller
$sql = "SELECT * FROM products WHERE seller_id = ? AND status = 'assigned' ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$products_result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Seller Dashboard</title>
<style>
    body { font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px; max-width: 900px; margin: auto; }
    header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    header h1 { color: #28a745; margin: 0; }
    a.logout { color: #fff; background-color: #dc3545; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; }
    a.logout:hover { background-color: #c82333; }
    table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 0 10px #ddd; }
    th, td { padding: 12px 15px; border: 1px solid #ccc; text-align: left; }
    th { background-color: #28a745; color: white; }
    tbody tr:hover { background-color: #f1f1f1; }
    .message { background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; color: #155724; margin-bottom: 20px; border-radius: 5px; }
    form { background: white; padding: 20px; margin-top: 40px; box-shadow: 0 0 10px #ddd; border-radius: 8px; }
    form label { display: block; margin-top: 15px; font-weight: bold; }
    form input[type="text"], form input[type="email"], form input[type="date"], form select {
        width: 100%; padding: 8px 10px; margin-top: 6px; border-radius: 4px; border: 1px solid #ccc; box-sizing: border-box;
    }
    form input[type="submit"] {
        margin-top: 20px; background-color: #28a745; color: white; padding: 10px 18px; border: none; cursor: pointer; font-size: 16px; border-radius: 5px; transition: background-color 0.3s ease;
    }
    form input[type="submit"]:hover { background-color: #218838; }
</style>
</head>
<body>

<header>
    <h1>Seller Dashboard</h1>
    <div>
        Welcome, <?= htmlspecialchars($_SESSION['username']); ?> (Seller) | 
        <a href="../logout.php" class="logout">Logout</a>
    </div>
</header>

<?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message); ?></div>
<?php endif; ?>

<h2>Assigned Products</h2>
<?php if ($products_result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Product Code</th>
                <th>Name</th>
                <th>Batch</th>
                <th>Harvest Date</th>
                <th>Location</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $products_result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['product_code']); ?></td>
                    <td><?= htmlspecialchars($row['name']); ?></td>
                    <td><?= htmlspecialchars($row['batch']); ?></td>
                    <td><?= htmlspecialchars($row['harvest_date']); ?></td>
                    <td><?= htmlspecialchars($row['location']); ?></td>
                    <td><?= htmlspecialchars(ucfirst($row['status'])); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No assigned products found.</p>
<?php endif; ?>

<h3>Mark Product as Sold</h3>
<form method="POST" action="">
    <label for="product_id">Select Product:</label>
    <select name="product_id" id="product_id" required>
        <option value="" disabled selected>-- Select Assigned Product --</option>
        <?php
        // Re-run query to get products for dropdown
        $stmt->execute();
        $products_result = $stmt->get_result();
        while ($product = $products_result->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($product['id']) . '">' . htmlspecialchars($product['name']) . ' (Code: ' . htmlspecialchars($product['product_code']) . ')</option>';
        }
        ?>
    </select>

    <label for="sold_date">Sold Date:</label>
    <input type="date" id="sold_date" name="sold_date" required />

    <label for="consumer_name">Consumer Name:</label>
    <input type="text" id="consumer_name" name="consumer_name" required />

    <label for="consumer_email">Consumer Email:</label>
    <input type="email" id="consumer_email" name="consumer_email" required />

    <input type="submit" name="final_sold" value="Mark as Sold & Generate QR Code" />
</form>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
