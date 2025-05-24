<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'farmer') {
    header("Location: ../login.php");
    exit();
}

include('../db_connect.php');

$farmer_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Farmer Dashboard - Your Products</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 900px;
            margin: 30px auto;
            padding: 15px;
            background-color: #f5f7fa;
            color: #333;
        }
        nav {
            margin-bottom: 25px;
            text-align: right;
        }
        nav a {
            background-color: #007bff;
            color: white;
            padding: 8px 15px;
            margin-left: 10px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        nav a:hover {
            background-color: #0056b3;
        }
        h2 {
            margin-bottom: 15px;
            font-weight: 700;
            color: #222;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: center;
            font-size: 15px;
        }
        th {
            background-color: #007bff;
            color: white;
            font-weight: 700;
        }
        tr:hover {
            background-color: #f1f9ff;
        }
        .no-products {
            font-style: italic;
            color: #777;
            padding: 15px;
            background: #e9ecef;
            border-radius: 5px;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <nav>
        <a href="../logout.php">Logout</a>
        <a href="add_product.php">Add Product</a>
        <a href="assign_seller.php">Assign Seller</a>
    </nav>

    <h2>Your Products</h2>

    <?php
    $sql = "SELECT * FROM products WHERE farmer_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo "<p class='no-products'>Error preparing database query.</p>";
        exit;
    }
    $stmt->bind_param("i", $farmer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<p class='no-products'>No products added yet.</p>";
    } else {
        echo "<table>";
        echo "<tr>
                <th>Product ID</th>
                <th>Crop Name</th>
                <th>Batch No</th>
                <th>Harvest Date</th>
                <th>Location</th>
                <th>Status</th>
              </tr>";

        while ($row = $result->fetch_assoc()) {
            // Fix undefined index warnings by checking existence
            $batch = isset($row['batch_number']) ? htmlspecialchars($row['batch_number']) : 'N/A';
            $status = !empty($row['status']) ? htmlspecialchars($row['status']) : 'Not Assigned';

            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . $batch . "</td>";
            echo "<td>" . htmlspecialchars($row['harvest_date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['location']) . "</td>";
            echo "<td>" . $status . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    $stmt->close();
    $conn->close();
    ?>

</body>
</html>
