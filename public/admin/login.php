<?php
session_start();
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
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Belly Furniture</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body{font-family:'Poppins',sans-serif;background-color:#f4f4f4;display:flex;justify-content:center;align-items:center;min-height:100vh;margin:0;}
        .login-container{background-color:#fff;padding:40px;border-radius:8px;box-shadow:0 4px 15px rgba(0,0,0,0.1);width:350px;text-align:center;}
        .login-container img{max-width:180px;margin-bottom:20px;}
        .login-container h2{margin-bottom:25px;color:#333;}
        .login-container .form-group{text-align:left; margin-bottom: 20px;}
        .login-container label{display:block;text-align:left;margin-bottom:8px;font-weight:bold;color:#555;}
        .login-container input[type="text"],.login-container input[type="password"]{width:100%;padding:12px;border:1px solid #ddd;border-radius:4px;box-sizing:border-box;}
        .login-container button{background-color:#f7a000;color:#212529;width:100%;padding:12px 25px;border:none;border-radius:5px;cursor:pointer;font-size:16px;font-weight:bold;transition:background-color 0.3s ease;}
        .login-container button:hover{background-color:#e09000;}
        .error-message{color:red;margin-top:15px;font-size:0.9em;}
    </style>
</head>
<body>
    <div class="login-container">
        <img src="../assets/images/belly_logo_color.png" alt="Belly Furniture Logo">
        <h2>Admin Login</h2>
        <form action="../../app/controllers/AuthController.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="login">Login</button>
            <?php if ($errorMessage): ?>
                <p class="error-message"><?php echo $errorMessage; ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>