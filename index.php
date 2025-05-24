<?php
session_start();
if (isset($_SESSION['role'])) {
    // Redirect to respective dashboard if already logged in
    switch ($_SESSION['role']) {
        case 'farmer':
            header("Location: farmer/view_products.php");
            break;
        case 'seller':
            header("Location: seller/view_products.php");
            break;
        case 'consumer':
            header("Location: consumer/verify.php");
            break;
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agriculture Supply Chain</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: url('https://images.unsplash.com/photo-1528825871115-3581a5387919?auto=format&fit=crop&w=1470&q=80') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
        }

        .overlay {
            background-color: rgba(0, 100, 0, 0.7);
            padding: 80px 20px;
            min-height: 100vh;
            text-align: center;
        }

        h1 {
            font-size: 3em;
            margin-bottom: 0.5em;
        }

        p {
            font-size: 1.2em;
            margin-bottom: 2em;
        }

        .btn-group {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 15px 30px;
            background-color: #f4f4f4;
            color: #2c3e50;
            text-decoration: none;
            font-weight: bold;
            border-radius: 30px;
            transition: 0.3s;
        }

        .btn:hover {
            background-color: #2ecc71;
            color: white;
        }

        footer {
            position: fixed;
            bottom: 10px;
            width: 100%;
            text-align: center;
            font-size: 0.9em;
            color: #ddd;
        }

        @media (max-width: 600px) {
            h1 { font-size: 2em; }
            p { font-size: 1em; }
        }
    </style>
</head>
<body>
    <div class="overlay">
        <h1>Welcome to AgriChain</h1>
        <p>Secure. Transparent. Verified. Empowering farmers, sellers, and consumers through blockchain & QR technology.</p>
        
        <div class="btn-group">
            <a href="login.php" class="btn">Login</a>
            <a href="signup.php" class="btn">Sign Up</a>
            <a href="consumer/verify.php" class="btn">Verify Product</a>
        </div>
    </div>

    <footer>
        &copy; 2025 AgriChain | Developed with 🌱 by Your Team
    </footer>
</body>
</html>
