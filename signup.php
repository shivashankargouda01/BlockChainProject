<?php
session_start();
require_once 'db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $role = $_POST["role"];

    $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $password, $role);

    if ($stmt->execute()) {
        $message = "✅ Signup successful. You're now part of the revolution!";
    } else {
        $message = "❌ Error: Email already exists. Try logging in instead!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Join the AgriChain Revolution 🌱</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #74ebd5, #9face6);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        form {
            background: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 350px;
            animation: fadeIn 1s ease-in-out;
        }
        h2 {
            text-align: center;
            color: #007BFF;
            margin-bottom: 5px;
        }
        .tagline {
            text-align: center;
            font-size: 14px;
            color: #555;
            margin-bottom: 20px;
            font-style: italic;
        }
        input, select {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        button {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background-color: #007BFF;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .msg {
            margin-top: 15px;
            text-align: center;
            color: green;
            font-weight: bold;
        }
        p {
            text-align: center;
            margin-top: 12px;
            font-size: 14px;
        }
        a {
            color: #007BFF;
            text-decoration: none;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>
    <form method="POST">
        <h2>AgriChain Signup</h2>
        <div class="tagline">🌾 Where agriculture meets blockchain — transparency you can taste! 🍅🔐</div>
        <input type="email" name="email" placeholder="Your Email (e.g. you@farm.com)" required>
        <input type="password" name="password" placeholder="Create a strong password" required>
        <select name="role" required>
            <option value="">Select Role</option>
            <option value="farmer">🚜 Farmer</option>
            <option value="seller">📦 Seller</option>
            <option value="consumer">🧑‍🌾 Consumer</option>
        </select>
        <button type="submit">Join Now</button>
        <div class="msg"><?= $message ?></div>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </form>
</body>
</html>
