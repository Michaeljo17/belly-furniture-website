<?php
// public/admin/products/delete.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../../../app/controllers/ProductController.php'; // Memanggil controller untuk menangani logic delete
// Tidak ada kode langsung di sini karena logic hapus sudah ada di ProductController.php sebagai GET request

// ProductController.php akan menangani redirect setelah delete
// Tidak ada output HTML di sini, hanya PHP untuk memicu controller
?>