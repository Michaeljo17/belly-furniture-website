<?php
// public/admin/portfolio/manage.php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); // Redirect ke halaman login jika belum login
    exit();
}

// Include file koneksi database
require_once '../../../app/config/database.php';

// Ambil data portofolio dari database
$portfolioItems = [];
$sql = "SELECT id, title, client_name, project_date, image_path FROM portfolio_items ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $portfolioItems[] = $row;
    }
}
$conn->close();

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
    <title>Kelola Portofolio - Admin Belly Furniture</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* Re-use general admin styles from style.css and add specific ones */
        .portfolio-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .portfolio-table th, .portfolio-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .portfolio-table th {
            background-color: #f2f2f2;
        }
        .portfolio-table img {
            width: 80px;
            height: auto;
            border-radius: 4px;
        }
        .portfolio-table .actions a {
            margin-right: 10px;
            color: #007bff;
            text-decoration: none;
        }
        .portfolio-table .actions a.delete {
            color: #dc3545;
        }
        .add-item-btn {
            display: inline-block;
            background-color: #f7a000;
            color: #333;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 20px;
            transition: background-color 0.3s ease;
        }
        .add-item-btn:hover {
            background-color: #e09000;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>Admin Dashboard - Kelola Portofolio</h1>
        <div>
            <a href="../dashboard.php" class="logout-btn" style="margin-right: 10px;">Dashboard</a>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="admin-container">
        <h2>Daftar Portofolio</h2>
        <a href="create.php" class="add-item-btn">Tambah Item Portofolio Baru</a>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if (count($portfolioItems) > 0): ?>
        <table class="portfolio-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul Proyek</th>
                    <th>Klien</th>
                    <th>Tanggal Proyek</th>
                    <th>Gambar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($portfolioItems as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['id']); ?></td>
                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                    <td><?php echo htmlspecialchars($item['client_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['project_date']); ?></td>
                    <td>
                        <?php if (!empty($item['image_path'])): ?>
                            <img src="../../<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <?php else: ?>
                            Tidak ada gambar
                        <?php endif; ?>
                    </td>
                    <td class="actions">
                        <a href="edit.php?id=<?php echo $item['id']; ?>">Edit</a> |
                       <a href="../../../app/controllers/PortfolioController.php?action=delete_portfolio_item&id=<?php echo $item['id']; ?>" class="delete" onclick="return confirm('Apakah Anda yakin ingin menghapus item portofolio ini?');">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p>Belum ada item portofolio yang ditambahkan. Silakan <a href="create.php">tambah item portofolio baru</a>.</p>
        <?php endif; ?>
    </div>
</body>
</html>