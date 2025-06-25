<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$page_title = 'Edit Produk';
require_once '../../../app/config/database.php';

// --- Ambil data produk yang akan diedit ---
$product = null;
$productId = $_GET['id'] ?? 0;
if ($productId) {
    $stmt = $conn->prepare("SELECT id, name, description, category, image_path FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $product = $result->fetch_assoc();
    }
    $stmt->close();
}
$conn->close();

// Jika produk tidak ditemukan, redirect kembali ke halaman manage
if (!$product) {
    header("Location: manage.php?message=Produk tidak ditemukan.&type=error");
    exit();
}

// Untuk pesan notifikasi
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
        .form-group{margin-bottom:15px;}
        .form-group label{display:block;margin-bottom:5px;font-weight:bold;color:#6C757D;}
        .form-group input[type="text"],.form-group input[type="password"],.form-group input[type="date"],.form-group input[type="file"],.form-group textarea,.form-group select{width:100%;padding:10px;border:1px solid #ddd;border-radius:4px;box-sizing:border-box;font-family:'Poppins',sans-serif;}
        .form-actions{margin-top:20px;text-align:right;}
        .form-actions button{background-color:#f7a000;color:#212529;padding:10px 20px;border:none;border-radius:5px;cursor:pointer;font-weight:bold;}
        .form-actions .btn.back-btn{background-color:#6c757d;color:#fff;padding:10px 20px;border-radius:5px;text-decoration:none;}
        .form-actions .btn.back-btn:hover{background-color:#5a6268;}
        .message{padding:10px 15px;margin-bottom:15px;border-radius:4px;border:1px solid transparent;}
        .message.success{color:#155724;background-color:#d4edda;border-color:#c3e6cb;}
        .message.error{color:#721c24;background-color:#f8d7da;border-color:#f5c6cb;}
        .current-image{margin-top:10px;}
        .current-image img{max-width:200px;border:1px solid #ddd;border-radius:4px;}
    </style>
</head>
<body class="admin-body">
    <header class="admin-header-main">
        <div class="header-container">
            <a href="../dashboard.php" class="logo"><img src="../../assets/images/belly_logo.png" alt="Belly Furniture Logo"></a>
            <span class="admin-welcome-text">Admin Panel</span>
        </div>
    </header>
    <div class="admin-actions-bar">
        <div class="admin-actions-container">
            <a href="../dashboard.php" class="button">Dashboard</a>
            <a href="../../index.php" class="button" target="_blank">Lihat Website</a>
            <a href="../logout.php" class="button button-secondary">Logout</a>
        </div>
    </div>
    <main class="admin-main-content">
        <div class="admin-container">
            <h2>Edit Produk: <?php echo htmlspecialchars($product['name']); ?></h2>
            <?php if ($message): ?>
                <div class="message <?php echo htmlspecialchars($messageType); ?>"><?php echo $message; ?></div>
            <?php endif; ?>
            <form action="../../../app/controllers/ProductController.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                <input type="hidden" name="old_image_path" value="<?php echo htmlspecialchars($product['image_path']); ?>">
                <div class="form-group">
                    <label for="name">Nama Produk:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="category">Kategori:</label>
                    <select id="category" name="category" required>
                        <option value="Kitchen Set" <?php echo ($product['category'] == 'Kitchen Set') ? 'selected' : ''; ?>>Kitchen Set</option>
                        <option value="Wardrobe" <?php echo ($product['category'] == 'Wardrobe') ? 'selected' : ''; ?>>Wardrobe</option>
                        <option value="Sofa" <?php echo ($product['category'] == 'Sofa') ? 'selected' : ''; ?>>Sofa</option>
                        <option value="Resin Table" <?php echo ($product['category'] == 'Resin Table') ? 'selected' : ''; ?>>Resin Table</option>
                        <option value="Others" <?php echo ($product['category'] == 'Others') ? 'selected' : ''; ?>>Lain-lain</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="description">Deskripsi:</label>
                    <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Gambar Saat Ini:</label>
                    <?php if (!empty($product['image_path'])): ?>
                        <div class="current-image">
                            <img src="/belly_furniture_website/<?php echo htmlspecialchars($product['image_path']); ?>" alt="Gambar Saat Ini">
                        </div>
                        <input type="checkbox" id="remove_current_image" name="remove_current_image" value="true">
                        <label for="remove_current_image">Hapus Gambar Saat Ini</label>
                    <?php else: ?>
                        <p>Tidak ada gambar saat ini.</p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="new_image">Ganti Gambar (opsional):</label>
                    <input type="file" id="new_image" name="new_image" accept="image/*">
                </div>
                <div class="form-actions">
                    <button type="submit" name="edit_product">Simpan Perubahan</button>
                    <a href="manage.php" class="btn back-btn">Batal</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>