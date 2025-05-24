<?php
include('../db/db_connection.php');

// From file upload
if (isset($_POST['verify_image']) && isset($_FILES['qr_image'])) {
    include('../phpqrcode/qrreader.php'); // adjust if your decoder differs

    $imageData = file_get_contents($_FILES['qr_image']['tmp_name']);
    $decodedText = decodeQRCodeFromImageData($imageData); // replace with your QR lib call

    if ($decodedText) {
        header("Location: verify_qr_handler.php?qr=" . urlencode($decodedText));
        exit();
    } else {
        echo "❌ QR code could not be decoded.";
    }
    exit();
}

// From QR scan
if (isset($_GET['qr'])) {
    $product_id = intval($_GET['qr']); // QR must contain only the product ID
    $stmt = $conn->prepare("SELECT name, batch_number, harvest_date, location, status FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo "❌ Product not found.";
    } else {
        $stmt->bind_result($name, $batch, $harvest, $location, $status);
        $stmt->fetch();
        echo "
            <ul>
                <li><strong>Product ID:</strong> $product_id</li>
                <li><strong>Crop Name:</strong> $name</li>
                <li><strong>Batch No:</strong> $batch</li>
                <li><strong>Harvest Date:</strong> $harvest</li>
                <li><strong>Location:</strong> $location</li>
                <li><strong>Status:</strong> $status</li>
            </ul>
        ";
    }
}
?>
