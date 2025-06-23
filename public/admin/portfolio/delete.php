<?php
// public/admin/portfolio/delete.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../../../app/controllers/PortfolioController.php'; // Memanggil controller untuk menangani logic delete
// Tidak ada kode langsung di sini karena logic hapus sudah ada di PortfolioController.php sebagai GET request

// PortfolioController.php akan menangani redirect setelah delete
// Tidak ada output HTML di sini, hanya PHP untuk memicu controller
?>