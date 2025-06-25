<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
$page_title = 'Kelola Pengaturan';
require_once '../../app/config/database.php';
$settings = [];
$sql = "SELECT setting_key, setting_value FROM settings";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}
$conn->close();
function getSetting($key, $default, $settingsArray) {
    return isset($settingsArray[$key]) ? htmlspecialchars($settingsArray[$key]) : $default;
}
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
        .admin-body h2,.admin-body h3{font-family:'Poppins',sans-serif;font-weight:600;color:#212529;margin-top:0;margin-bottom:1.5rem;}
        .admin-body h2{font-size:2.2rem;}
        .admin-body h3{font-size:1.5rem; border-bottom: 2px solid #f7a000; padding-bottom: 10px; margin-top:2rem;}
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
        .message{padding:10px 15px;margin-bottom:15px;border-radius:4px;border:1px solid transparent;}
        .message.success{color:#155724;background-color:#d4edda;border-color:#c3e6cb;}
        .message.error{color:#721c24;background-color:#f8d7da;border-color:#f5c6cb;}
    </style>
</head>
<body class="admin-body">
    <header class="admin-header-main">
        <div class="header-container">
            <a href="dashboard.php" class="logo"><img src="../assets/images/belly_logo.png" alt="Belly Furniture Logo"></a>
            <span class="admin-welcome-text">Admin Panel</span>
        </div>
    </header>
    <div class="admin-actions-bar">
        <div class="admin-actions-container">
            <a href="dashboard.php" class="button">Dashboard</a>
            <a href="../index.php" class="button" target="_blank">Lihat Website</a>
            <a href="logout.php" class="button button-secondary">Logout</a>
        </div>
    </div>
    <main class="admin-main-content">
        <div class="admin-container">
            <h2><?php echo htmlspecialchars($page_title); ?></h2>
            <?php if ($message): ?>
                <div class="message <?php echo htmlspecialchars($messageType); ?>"><?php echo $message; ?></div>
            <?php endif; ?>
            <form action="../../app/controllers/SettingController.php" method="POST">
                <h3>Kontak Info</h3>
                <div class="form-group">
                    <label for="contact_email">Email Kontak:</label>
                    <input type="text" id="contact_email" name="contact_email" value="<?php echo getSetting('contact_email', '', $settings); ?>">
                </div>
                <div class="form-group">
                    <label for="whatsapp_number">Nomor WhatsApp (misal: 628123456789):</label>
                    <input type="text" id="whatsapp_number" name="whatsapp_number" value="<?php echo getSetting('whatsapp_number', '', $settings); ?>">
                </div>
                <div class="form-group">
                    <label for="instagram_link">Link Instagram:</label>
                    <input type="text" id="instagram_link" name="instagram_link" value="<?php echo getSetting('instagram_link', '', $settings); ?>">
                </div>
                <h3>Tentang Kami</h3>
                <div class="form-group">
                    <label for="about_us_text">Teks Tentang Kami:</label>
                    <textarea id="about_us_text" name="about_us_text" rows="10"><?php echo getSetting('about_us_text', '', $settings); ?></textarea>
                </div>
                <h3>Visi & Misi</h3>
                <div class="form-group">
                    <label for="visi_text">Teks Visi:</label>
                    <textarea id="visi_text" name="visi_text" rows="5"><?php echo getSetting('visi_text', '', $settings); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="misi_text_1">Teks Misi 1:</label>
                    <textarea id="misi_text_1" name="misi_text_1" rows="3"><?php echo getSetting('misi_text_1', '', $settings); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="misi_text_2">Teks Misi 2:</label>
                    <textarea id="misi_text_2" name="misi_text_2" rows="3"><?php echo getSetting('misi_text_2', '', $settings); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="misi_text_3">Teks Misi 3:</label>
                    <textarea id="misi_text_3" name="misi_text_3" rows="3"><?php echo getSetting('misi_text_3', '', $settings); ?></textarea>
                </div>
                 <div class="form-group">
                    <label for="misi_text_4">Teks Misi 4:</label>
                    <textarea id="misi_text_4" name="misi_text_4" rows="3"><?php echo getSetting('misi_text_4', '', $settings); ?></textarea>
                </div>
                <h3>Kompetensi</h3>
                <div class="form-group">
                    <label for="kompetensi_list">Daftar Kompetensi (pisahkan dengan titik koma ";"):</label>
                    <textarea id="kompetensi_list" name="kompetensi_list" rows="5"><?php echo getSetting('kompetensi_list', '', $settings); ?></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" name="save_settings">Simpan Pengaturan</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>