<?php
// public/admin/portfolio/create.php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = ''; // Untuk menampilkan pesan sukses/error
$messageType = ''; // Untuk menampilkan tipe pesan (success/error)
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
    <title>Tambah Item Portofolio - Admin Belly Furniture</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* Re-use form styles from style.css or general admin styles */
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
        .form-group input[type="date"], /* New input type for date */
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
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid transparent;
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
        <h1>Admin Dashboard - Tambah Item Portofolio</h1>
        <div>
            <a href="../dashboard.php" class="logout-btn" style="margin-right: 10px;">Dashboard</a>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="admin-container">
        <h2>Tambah Item Portofolio Baru</h2>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="../../../app/controllers/PortfolioController.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Judul Proyek:</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="client_name">Nama Klien:</label>
                <input type="text" id="client_name" name="client_name">
            </div>

            <div class="form-group">
                <label for="project_date">Tanggal Proyek Selesai:</label>
                <input type="date" id="project_date" name="project_date">
            </div>

            <div class="form-group">
                <label for="description">Deskripsi Proyek:</label>
                <textarea id="description" name="description" rows="5"></textarea>
            </div>

            <div class="form-group">
                <label for="image">Gambar Portofolio:</label>
                <input type="file" id="image" name="image" accept="image/*" required>
                <small>Format: JPG, JPEG, PNG. Maksimal ukuran: 2MB.</small>
            </div>

            <div class="form-actions">
                <button type="submit" name="add_portfolio_item">Tambah Portofolio</button>
                <a href="manage.php" class="back-btn">Kembali ke Daftar Portofolio</a>
            </div>
        </form>
    </div>
</body>
</html>