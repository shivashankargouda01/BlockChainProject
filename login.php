<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('db_connect.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT id, email, password, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $user_email, $hashed_password, $role);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $role = strtolower(trim($role));

                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $user_email;
                $_SESSION['role'] = $role;

                if ($role === 'farmer') {
                    header("Location: farmer/dashboard.php");
                    exit();
                } elseif ($role === 'seller') {
                    header("Location: seller/dashboard.php");
                    exit();
                } elseif ($role === 'consumer') {
                    header("Location: consumer/dashboard.php");
                    exit();
                } else {
                    $error = "Invalid role.";
                }
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No user found with this email.";
        }
    } else {
        $error = "Something went wrong. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap');

        body {
            font-family: 'Montserrat', Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5 0%, #ACB6E5 100%);
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            width: 360px;
            text-align: center;
        }
        h2 {
            margin-bottom: 10px;
            color: #2c3e50;
            font-weight: 700;
        }
        .tagline {
            font-size: 14px;
            margin-bottom: 25px;
            color: #007bff;
            font-weight: 600;
            letter-spacing: 0.05em;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin: 12px 0 18px 0;
            border: 1.8px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s ease;
        }
        input[type="email"]:focus, input[type="password"]:focus {
            border-color: #007bff;
            outline: none;
        }
        button {
            background-color: #007bff;
            border: none;
            color: white;
            padding: 13px;
            width: 100%;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            color: #d93025;
            margin-bottom: 15px;
            font-weight: 600;
        }
        p {
            font-size: 14px;
            color: #555;
            margin-top: 20px;
        }
        p a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
        }
        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <div class="tagline">🌾 Powering transparency in food – one block at a time 🔗</div>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" autocomplete="off">
            <input type="email" name="email" placeholder="Email" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="signup.php">Register here</a></p>
    </div>
</body>
</html>
