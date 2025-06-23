<?php
// app/controllers/AuthController.php

// Include file koneksi database
require_once '../../app/config/database.php';

session_start(); // Mulai sesi PHP

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Sanitasi input untuk mencegah XSS
    $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
    // Password tidak perlu disanitasi HTML karena akan di-hash

    // Menggunakan Prepared Statement untuk mencegah SQL Injection
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username); // "s" menunjukkan tipe data string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Verifikasi password yang di-hash
        if (password_verify($password, $user['password'])) {
            // Password benar, buat sesi login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect ke halaman dashboard admin
            header("Location: ../../public/admin/dashboard.php");
            exit();
        } else {
            // Password salah
            header("Location: ../../public/admin/login.php?error=1");
            exit();
        }
    } else {
        // Username tidak ditemukan
        header("Location: ../../public/admin/login.php?error=1");
        exit();
    }

    $stmt->close();
}

$conn->close();
?>