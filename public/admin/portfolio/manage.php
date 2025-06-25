<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// ==================================================================
// ==    PASTIKAN ALAMAT INI SESUAI DENGAN PROYEK ANDA DI BROWSER  ==
// ==================================================================
define('BASE_URL', '/belly_furniture_website/');
// ==================================================================

$page_title = 'Kelola Portofolio';
require_once '../../../app/config/database.php';
$portfolioItems = [];
$sql = "SELECT id, title, client_name, project_date, image_path FROM portfolio_items ORDER BY created_at DESC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $portfolioItems[] = $row;
    }
}
$conn->close();
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
$messageType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'info';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Belly Furniture</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {--color-primary:#f7a000;--color-primary-light:#ffc107;--color-background:#F8F9FA;--color-surface:#FFFFFF;--color-header-bg:#181818;--color-header-text:#E9ECEF;--color-text-primary:#212529;--color-text-secondary:#6C757D;--color-border:#E9ECEF;--font-heading:'Poppins',sans-serif;--font-body:'Lora',serif;}
        body.admin-body{background-color:#F8F9FA;padding-top:80px;font-family:'Poppins',sans-serif;line-height:1.6;margin:0;}
        .admin-body h2{font-family:'Poppins',sans-serif;font-weight:600;font-size:2.2rem;color:#212529;margin-top:0;margin-bottom:1.5rem;}
        .admin-header-main{background-color:#181818;position:fixed;top:0;left:0;width:100%;z-index:1000;box-shadow:0 4px 20px rgba(0,0,0,0.25);}
        .admin-header-main .header-container{max-width:1300px;margin:0 auto;padding:0 2rem;display:flex;justify-content:space-between;align-items:center;height:80px;}
        .admin-header-main .logo img{height:40px;display:block;filter:brightness(0) invert(1);}
        .admin-welcome-text{color:#E9ECEF;font-size:1.1em;}
        .admin-actions-bar{background-color:#FFFFFF;padding:1rem 0;border-bottom:1px solid #E9ECEF;box-shadow:0 4px 10px -5px rgba(0,0,0,0.1);}
        .admin-actions-container{max-width:1300px;margin:0 auto;padding:0 2rem;display:flex;justify-content:flex-end;gap:1rem;}
        .admin-main-content{padding:2rem 0;}
        .admin-container{max-width:1300px;margin:0 auto;padding:2rem;background-color:#fff;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.05);}
        .button,.add-item-btn{display:inline-block;background-color:#f7a000;color:#212529;font-family:'Poppins',sans-serif;padding:0.7rem 1.4rem;border:1px solid #f7a000;border-radius:8px;font-weight:600;transition:all 0.3s ease;cursor:pointer;text-decoration:none;margin:0;}
        .button:hover,.add-item-btn:hover{background-color:#ffc107;border-color:#ffc107;}
        .button.button-secondary{background-color:#FFFFFF;border:1px solid #E9ECEF;}
        .button.button-secondary:hover{background-color:#F8F9FA;border-color:#212529;}
        .admin-table{width:100%;border-collapse:collapse;margin-top:20px;}
        .admin-table th,.admin-table td{border:1px solid #ddd;padding:12px 15px;text-align:left;vertical-align:middle;}
        .admin-table th{background-color:#f2f2f2;font-weight:600;}
        .admin-table img{width:80px;height:auto;border-radius:4px;}
        .admin-table .actions a{margin:0 5px;color:#f7a000;text-decoration:none;font-weight:600;}
        .admin-table .actions a.delete{color:#dc3545;}
    </style>
</head>
<body class="admin-body">
    <header class="admin-header-main">
        <div class="header-container">
            <a href="<?php echo BASE_URL; ?>public/admin/dashboard.php" class="logo"><img src="<?php echo BASE_URL; ?>public/assets/images/belly_logo.png" alt="Belly Furniture Logo"></a>
            <span class="admin-welcome-text">Admin Panel</span>
        </div>
    </header>
    <div class="admin-actions-bar">
        <div class="admin-actions-container">
            <a href="<?php echo BASE_URL; ?>public/admin/dashboard.php" class="button">Dashboard</a>
            <a href="<?php echo BASE_URL; ?>public/index.php" class="button" target="_blank">Lihat Website</a>
            <a href="<?php echo BASE_URL; ?>public/admin/logout.php" class="button button-secondary">Logout</a>
        </div>
    </div>
    <main class="admin-main-content">
        <div class="admin-container">
            <h2><?php echo htmlspecialchars($page_title); ?></h2>
            <a href="create.php" class="add-item-btn">Tambah Item Portofolio Baru</a>
            <table class="admin-table">
                <thead>
                    <tr><th>ID</th><th>Judul Proyek</th><th>Klien</th><th>Tanggal</th><th>Gambar</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if (count($portfolioItems) > 0): ?>
                        <?php foreach ($portfolioItems as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['id']); ?></td>
                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                            <td><?php echo htmlspecialchars($item['client_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['project_date']); ?></td>
                            <td>
                                <?php if (!empty($item['image_path'])): ?>
                                    <img src="<?php echo BASE_URL . htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <a href="edit.php?id=<?php echo $item['id']; ?>">Edit</a> |
                                <a href="../../../app/controllers/PortfolioController.php?action=delete_portfolio_item&id=<?php echo $item['id']; ?>" class="delete" onclick="return confirm('Yakin Hapus?');">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align: center;">Belum ada item portofolio.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>