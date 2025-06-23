<?php
// public/admin/login.php
session_start(); // Mulai sesi PHP di awal

// Redirect jika admin sudah login
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$errorMessage = '';
if (isset($_GET['error'])) {
    $errorMessage = 'Username atau password salah!';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Belly Furniture</title>
    <link rel="stylesheet" href="../assets/css/style.css"> <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .login-container label {
            display: block;
            text-align: left;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .login-container button {
            background-color: #f7a000;
            color: #333;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .login-container button:hover {
            background-color: #e09000;
        }
        .error-message {
            color: red;
            margin-top: 15px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <form action="../../app/controllers/AuthController.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" name="login">Login</button>
            <?php if ($errorMessage): ?>
                <p class="error-message"><?php echo $errorMessage; ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>