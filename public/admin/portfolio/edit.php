<?php
// public/admin/portfolio/edit.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../../../app/config/database.php'; // Koneksi ke database

$portfolioItem = null;
$portfolioId = $_GET['id'] ?? 0; // Ambil ID dari URL

if ($portfolioId) {
    $stmt = $conn->prepare("SELECT id, title, description, client_name, project_date, image_path FROM portfolio_items WHERE id = ?");
    $stmt->bind_param("i", $portfolioId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $portfolioItem = $result->fetch_assoc();
    }
    $stmt->close();
}
$conn->close();

if (!$portfolioItem) {
    // Item Portofolio tidak ditemukan, redirect atau tampilkan pesan error
    header("Location: manage.php?message=" . urlencode("Item Portofolio tidak ditemukan.") . "&type=error");
    exit();
}

$message = '';
$messageType = '';
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
    <title>Edit Item Portofolio - Admin Belly Furniture</title>
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
        .current-image {
            margin-top: 10px;
            text-align: center;
        }
        .current-image img {
            max-width: 200px;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>Admin Dashboard - Edit Item Portofolio</h1>
        <div>
            <a href="../dashboard.php" class="logout-btn" style="margin-right: 10px;">Dashboard</a>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="admin-container">
        <h2>Edit Item Portofolio: <?php echo htmlspecialchars($portfolioItem['title']); ?></h2>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="../../../app/controllers/PortfolioController.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="portfolio_id" value="<?php echo htmlspecialchars($portfolioItem['id']); ?>">
            <input type="hidden" name="old_image_path" value="<?php echo htmlspecialchars($portfolioItem['image_path']); ?>">

            <div class="form-group">
                <label for="title">Judul Proyek:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($portfolioItem['title']); ?>" required>
            </div>

            <div class="form-group">
                <label for="client_name">Nama Klien:</label>
                <input type="text" id="client_name" name="client_name" value="<?php echo htmlspecialchars($portfolioItem['client_name']); ?>">
            </div>

            <div class="form-group">
                <label for="project_date">Tanggal Proyek Selesai:</label>
                <input type="date" id="project_date" name="project_date" value="<?php echo htmlspecialchars($portfolioItem['project_date']); ?>">
            </div>

            <div class="form-group">
                <label for="description">Deskripsi Proyek:</label>
                <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($portfolioItem['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Gambar Saat Ini:</label>
                <?php if (!empty($portfolioItem['image_path'])): ?>
                    <div class="current-image">
                        <img src="http://localhost/belly_furniture_website/<?php echo htmlspecialchars($portfolioItem['image_path']); ?>" alt="Gambar Portofolio Saat Ini">
                    </div>
                    <input type="checkbox" id="remove_current_image" name="remove_current_image" value="true">
                    <label for="remove_current_image">Hapus Gambar Saat Ini</label>
                <?php else: ?>
                    <p>Tidak ada gambar saat ini.</p>
                <?php endif; ?>
                <label for="new_image">Ganti Gambar (opsional):</label>
                <input type="file" id="new_image" name="new_image" accept="image/*">
                <small>Format: JPG, JPEG, PNG. Maksimal ukuran: 2MB. Kosongkan jika tidak ingin mengganti.</small>
            </div>

            <div class="form-actions">
                <button type="submit" name="edit_portfolio_item">Simpan Perubahan</button>
                <a href="manage.php" class="back-btn">Batal / Kembali</a>
            </div>
        </form>
    </div>
</body>
</html>