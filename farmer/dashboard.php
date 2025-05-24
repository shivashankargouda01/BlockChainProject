<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farmer') {
    header("Location: ../login.php");
    exit();
}
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Farmer Dashboard</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap');

        body {
            font-family: 'Nunito', 'Segoe UI', sans-serif;
            margin: 0;
            background: linear-gradient(135deg, #e0f7fa, #80deea);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 50px 20px;
            color: #1b262c;
        }

        .container {
            background: #ffffffdd;
            padding: 40px 35px;
            border-radius: 18px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 650px;
            box-sizing: border-box;
            position: relative;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #a7ffeb;
            padding-bottom: 10px;
        }

        header h2 {
            margin: 0;
            font-weight: 800;
            font-size: 1.8rem;
            color: #00796b;
            letter-spacing: 1.2px;
        }

        .welcome {
            font-size: 1.1rem;
            color: #004d40;
            font-weight: 600;
        }

        .logout-link {
            background: #e53935;
            color: white;
            padding: 8px 18px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: background-color 0.3s ease;
            box-shadow: 0 4px 8px rgba(229, 57, 53, 0.3);
        }
        .logout-link:hover {
            background: #b71c1c;
            box-shadow: 0 6px 12px rgba(183, 28, 28, 0.5);
        }

        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        a {
            display: block;
            background: #00796b;
            color: white;
            padding: 16px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 121, 107, 0.3);
            transition: background-color 0.35s ease, box-shadow 0.35s ease, transform 0.2s ease;
            user-select: none;
        }

        a:hover, a:focus {
            background: #004d40;
            box-shadow: 0 8px 25px rgba(0, 77, 64, 0.5);
            transform: translateY(-3px);
        }

        /* Responsive */
        @media (max-width: 480px) {
            body {
                padding: 30px 10px;
            }
            .container {
                padding: 30px 25px;
                width: 100%;
            }
            a {
                font-size: 1rem;
                padding: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h2>Farmer Dashboard</h2>
            <a href="../logout.php" class="logout-link" title="Logout">Logout</a>
        </header>
        <p class="welcome">Welcome, <strong><?= htmlspecialchars($username) ?></strong> 🌾</p>
        <div class="btn-group">
            <a href="add_product.php">➕ Add New Product</a>
            <a href="assign_seller.php">📦 Assign Product to Seller</a>
            <a href="view_products.php">📋 View My Products</a>
        </div>
    </div>
</body>
</html>
