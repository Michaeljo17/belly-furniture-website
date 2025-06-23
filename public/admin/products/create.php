<?php
// public/admin/products/create.php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = ''; // Variabel untuk menyimpan pesan notifikasi
$messageType = ''; // Variabel untuk menyimpan tipe pesan (success/error)

// Cek apakah ada pesan dari URL (setelah redirect dari ProductController)
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
    $messageType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'info';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk Baru - Admin Belly Furniture</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding-top: 60px;
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
        .admin-header .logout-btn {
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
        .admin-container {
            padding: 20px;
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="text"],
        .form-group textarea,
        .form-group select,
        .form-group input[type="file"] {
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        .form-actions {
            margin-top: 20px;
        }
        .form-actions button, .form-actions a {
            background-color: #f7a000;
            color: #333;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            margin-right: 10px;
            transition: background-color 0.3s ease;
        }
        .form-actions a.back-btn {
            background-color: #6c757d;
            color: #fff;
        }
        .form-actions button:hover {
            background-color: #e09000;
        }
        .form-actions a.back-btn:hover {
            background-color: #5a6268;
        }
        .message { /* Mengubah .message.success dan .message.error menjadi hanya .message dan kelas dinamis */
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid transparent; /* Default border */
        }
        .message.success {
            color: green;
            background-color: #e6ffe6;
            border-color: green;
        }
        .message.error {
            color: red;
            background-color: #ffe6e6;
            border-color: red;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>Admin Dashboard - Tambah Produk Baru</h1>
        <div>
            <a href="../dashboard.php" class="logout-btn" style="margin-right: 10px;">Dashboard</a>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="admin-container">
        <h2>Tambah Produk Baru</h2>

        <?php if ($message): // Menampilkan pesan jika ada ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="../../../app/controllers/ProductController.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Nama Produk:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="category">Kategori:</label>
                <select id="category" name="category" required>
                    <option value="">Pilih Kategori</option>
                    <option value="Kitchen Set">Kitchen Set</option>
                    <option value="Wardrobe">Wardrobe</option>
                    <option value="Sofa">Sofa</option>
                    <option value="Resin Table">Resin Table</option>
                    <option value="Others">Lain-lain</option>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Deskripsi:</label>
                <textarea id="description" name="description" rows="5"></textarea>
            </div>

            <div class="form-group">
                <label for="image">Gambar Produk:</label>
                <input type="file" id="image" name="image" accept="image/*" required>
                <small>Format: JPG, JPEG, PNG. Maksimal ukuran: 2MB.</small>
            </div>

            <div class="form-actions">
                <button type="submit" name="add_product">Tambah Produk</button>
                <a href="manage.php" class="back-btn">Kembali ke Daftar Produk</a>
            </div>
        </form>
    </div>
</body>
</html>