<?php
// public/admin/settings.php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../../app/config/database.php';

$message = '';
$messageType = '';

// Tangani pesan dari redirect setelah penyimpanan
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
    $messageType = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'info';
}

// Ambil semua pengaturan dari database
$settings = [];
$sql = "SELECT setting_key, setting_value FROM settings";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}
$conn->close();

// Fungsi helper untuk mendapatkan nilai pengaturan
// Digunakan untuk mengisi nilai awal di form input
function getSetting($key, $default = '', $settingsArray) {
    return isset($settingsArray[$key]) ? htmlspecialchars($settingsArray[$key]) : $default;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengaturan - Admin Belly Furniture</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
            max-width: 900px;
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
        .form-group textarea {
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
            text-align: right;
        }
        .form-actions button {
            background-color: #f7a000;
            color: #333;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .form-actions button:hover {
            background-color: #e09000;
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
        <h1>Admin Dashboard - Kelola Pengaturan</h1>
        <div>
            <a href="dashboard.php" class="logout-btn" style="margin-right: 10px;">Dashboard</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="admin-container">
        <h2>Kelola Informasi Umum Website</h2>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="../../app/controllers/SettingController.php" method="POST">
            <input type="hidden" name="action" value="save_all_settings"> <h3>Kontak Info</h3>
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
</body>
</html>