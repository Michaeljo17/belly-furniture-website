<?php
// app/controllers/SettingController.php

session_start();
// Cek apakah admin sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../public/admin/login.php");
    exit();
}

require_once '../../app/config/database.php'; // Koneksi ke database

// Fungsi untuk menyimpan satu pengaturan ke database
function saveSetting($conn, $key, $value) {
    // Sanitasi input
    $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

    // Cek apakah key sudah ada
    $stmt = $conn->prepare("SELECT COUNT(*) FROM settings WHERE setting_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        // Update jika sudah ada
        $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
        $stmt->bind_param("ss", $value, $key);
    } else {
        // Insert jika belum ada
        $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
        $stmt->bind_param("ss", $key, $value);
    }

    if (!$stmt->execute()) {
        error_log("Error saving setting '{$key}': " . $stmt->error);
        return false;
    }
    $stmt->close();
    return true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_settings'])) {
    $message = '';
    $messageType = 'error'; // Default error

    // Semua pengaturan yang diharapkan dari form settings.php
    $settingsToSave = [
        'contact_email' => $_POST['contact_email'] ?? '',
        'whatsapp_number' => $_POST['whatsapp_number'] ?? '',
        'instagram_link' => $_POST['instagram_link'] ?? '',
        'about_us_text' => $_POST['about_us_text'] ?? '',
        'visi_text' => $_POST['visi_text'] ?? '',
        'misi_text_1' => $_POST['misi_text_1'] ?? '',
        'misi_text_2' => $_POST['misi_text_2'] ?? '',
        'misi_text_3' => $_POST['misi_text_3'] ?? '',
        'misi_text_4' => $_POST['misi_text_4'] ?? '',
        'kompetensi_list' => $_POST['kompetensi_list'] ?? ''
    ];

    $allSuccessful = true;

    foreach ($settingsToSave as $key => $value) {
        if (!saveSetting($conn, $key, $value)) {
            $allSuccessful = false;
            break; // Keluar dari loop jika ada error
        }
    }

    if ($allSuccessful) {
        $message = "Pengaturan berhasil disimpan!";
        $messageType = 'success';
    } else {
        $message = "Gagal menyimpan beberapa pengaturan. Cek log server.";
        $messageType = 'error';
    }

    $conn->close();
    // Redirect selalu ke halaman settings.php
    header("Location: ../../public/admin/settings.php?message=" . urlencode($message) . "&type=" . $messageType);
    exit();
}

// Jika tidak ada POST request yang cocok, atau akses langsung ke controller
header("Location: ../../public/admin/dashboard.php");
exit();
?>