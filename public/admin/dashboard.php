<?php
// public/admin/dashboard.php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Belly Furniture</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding-top: 60px; /* Space for fixed header */
        }
        .admin-header {
            background-color: #333;
            color: #fff;
            padding: 15px 20px;
            text-align: center;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-header h1 {
            margin: 0;
            font-size: 1.5em;
        }
        .admin-header .logout-btn { /* Digunakan juga untuk "Dashboard" dan "Lihat Website" */
            background-color: #f7a000;
            color: #333;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .admin-header .logout-btn:hover {
            background-color: #e09000;
        }
        .admin-header .logout-btn.view-website-btn { /* Gaya khusus untuk tombol Lihat Website */
            background-color: #007bff;
            color: #fff;
        }
        .admin-header .logout-btn.view-website-btn:hover {
            background-color: #0056b3;
        }
        .admin-dashboard-container {
            padding: 20px;
            max-width: 1200px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .dashboard-item {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.05);
            text-align: center;
        }
        .dashboard-item h3 {
            color: #f7a000;
            margin-bottom: 15px;
        }
        .dashboard-item a {
            display: block;
            background-color: #555;
            color: #fff;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 15px;
            transition: background-color 0.3s ease;
        }
        .dashboard-item a:hover {
            background-color: #777;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>Admin Dashboard - Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <div>
            <a href="../../public/index.php" class="logout-btn view-website-btn" style="margin-right: 10px;">Lihat Website</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="admin-dashboard-container">
        <h2>Kelola Konten Website</h2>
        <div class="dashboard-grid">
            <div class="dashboard-item">
                <h3>Produk & Katalog</h3>
                <p>Tambah, edit, atau hapus item produk dan katalog.</p>
                <a href="products/manage.php">Kelola Produk</a>
            </div>
            <div class="dashboard-item">
                <h3>Portofolio</h3>
                <p>Tambah, edit, atau hapus item portofolio.</p>
                <a href="portfolio/manage.php">Kelola Portofolio</a>
            </div>
            
            <div class="dashboard-item">
                <h3>Pengaturan Umum Website</h3>
                <p>Ubah informasi kontak, Tentang Kami, Visi & Misi, dan Kompetensi.</p>
                <a href="settings.php">Kelola Pengaturan</a>
            </div>
            </div>
    </div>
</body>
</html>