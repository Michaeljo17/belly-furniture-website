<?php
// app/controllers/ProductController.php

session_start();
// Cek apakah admin sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../public/admin/login.php"); // Redirect ke halaman login jika belum login
    exit();
}

require_once '../../app/config/database.php'; // Koneksi ke database

// Fungsi bantuan untuk redirect dengan pesan
function redirectWithMessage($location, $message, $type = 'success') {
    header("Location: " . $location . "?message=" . urlencode($message) . "&type=" . $type);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- Logika Tambah Produk ---
    if (isset($_POST['add_product'])) {
        $name = $_POST['name'];
        $category = $_POST['category'];
        $description = $_POST['description'];

        // Sanitasi input
        $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $category = htmlspecialchars($category, ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');

        $uploadDir = 'public/uploads/products/'; // Direktori untuk menyimpan gambar
        $imagePath = ''; // Akan menyimpan jalur gambar di database

        // Pastikan direktori upload ada
        if (!is_dir('../../' . $uploadDir)) {
            mkdir('../../' . $uploadDir, 0777, true);
        }

        // Logika Upload Gambar
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_name'];
            $fileName = $_FILES['image']['name'];
            $fileSize = $_FILES['image']['size'];
            $fileType = $_FILES['image']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Validasi ekstensi file
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
            if (in_array($fileExtension, $allowedfileExtensions)) {
                // Validasi ukuran file (misal: max 2MB)
                if ($fileSize <= 2 * 1024 * 1024) { // 2MB
                    // Buat nama file unik untuk mencegah konflik nama
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $destPath = '../../' . $uploadDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $imagePath = $uploadDir . $newFileName; // Jalur yang akan disimpan di DB
                    } else {
                        redirectWithMessage('../../public/admin/products/create.php', "Gagal mengunggah gambar.", "error");
                    }
                } else {
                    redirectWithMessage('../../public/admin/products/create.php', "Ukuran gambar terlalu besar. Maksimal 2MB.", "error");
                }
            } else {
                redirectWithMessage('../../public/admin/products/create.php', "Format gambar tidak didukung. Hanya JPG, JPEG, PNG, GIF.", "error");
            }
        } else {
            redirectWithMessage('../../public/admin/products/create.php', "Gagal mengunggah gambar: " . $_FILES['image']['error'], "error");
        }

        // Masukkan data ke database menggunakan Prepared Statement
        $stmt = $conn->prepare("INSERT INTO products (name, description, category, image_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $description, $category, $imagePath);

        if ($stmt->execute()) {
            redirectWithMessage('../../public/admin/products/manage.php', "Produk berhasil ditambahkan!", "success");
        } else {
            redirectWithMessage('../../public/admin/products/create.php', "Error: " . $stmt->error, "error");
        }

        $stmt->close();
        $conn->close();

    } // --- END Logika Tambah Produk ---

    // --- Logika Edit Produk ---
    elseif (isset($_POST['edit_product'])) {
        $productId = $_POST['product_id'];
        $name = $_POST['name'];
        $category = $_POST['category'];
        $description = $_POST['description'];
        $oldImagePath = $_POST['old_image_path']; // Jalur gambar lama

        // Sanitasi input
        $productId = htmlspecialchars($productId, ENT_QUOTES, 'UTF-8');
        $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $category = htmlspecialchars($category, ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
        $newImagePath = $oldImagePath; // Default ke gambar lama

        $uploadDir = 'public/uploads/products/';

        // Logika Upload Gambar Baru (jika ada)
        if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['new_image']['tmp_name'];
            $fileName = $_FILES['new_image']['name'];
            $fileSize = $_FILES['new_image']['size'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
            if (in_array($fileExtension, $allowedfileExtensions)) {
                if ($fileSize <= 2 * 1024 * 1024) { // 2MB
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $destPath = '../../' . $uploadDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $newImagePath = $uploadDir . $newFileName; // Jalur gambar baru
                        // Hapus gambar lama jika ada dan berbeda dari yang baru
                        if (!empty($oldImagePath) && $oldImagePath != $newImagePath && file_exists('../../' . $oldImagePath)) {
                            unlink('../../' . $oldImagePath);
                        }
                    } else {
                        redirectWithMessage('../../public/admin/products/edit.php?id=' . $productId, "Gagal mengunggah gambar baru.", "error");
                    }
                } else {
                    redirectWithMessage('../../public/admin/products/edit.php?id=' . $productId, "Ukuran gambar baru terlalu besar.", "error");
                }
            } else {
                redirectWithMessage('../../public/admin/products/edit.php?id=' . $productId, "Format gambar baru tidak didukung.", "error");
            }
        } elseif (isset($_POST['remove_current_image']) && $_POST['remove_current_image'] == 'true') {
            // Logika untuk menghapus gambar yang ada tanpa mengunggah yang baru
            if (!empty($oldImagePath) && file_exists('../../' . $oldImagePath)) {
                unlink('../../' . $oldImagePath);
            }
            $newImagePath = ''; // Atur path gambar menjadi kosong di DB
        }

        // Update data di database menggunakan Prepared Statement
        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, category = ?, image_path = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $name, $description, $category, $newImagePath, $productId);

        if ($stmt->execute()) {
            redirectWithMessage('../../public/admin/products/manage.php', "Produk berhasil diperbarui!", "success");
        } else {
            redirectWithMessage('../../public/admin/products/edit.php?id=' . $productId, "Error: " . $stmt->error, "error");
        }

        $stmt->close();
        $conn->close();
    } // --- END Logika Edit Produk ---

} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {

    // --- Logika Hapus Produk (via GET request) ---
    if (isset($_GET['action']) && $_GET['action'] == 'delete_product' && isset($_GET['id'])) {
        $productId = $_GET['id'];

        // Ambil jalur gambar sebelum menghapus dari DB
        $imagePath = '';
        $stmt = $conn->prepare("SELECT image_path FROM products WHERE id = ?");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $imagePath = $row['image_path'];
        }
        $stmt->close();

        // Hapus dari database
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $productId);

        if ($stmt->execute()) {
            // Jika ada gambar terkait, hapus juga file gambarnya
            if (!empty($imagePath) && file_exists('../../' . $imagePath)) {
                unlink('../../' . $imagePath); // Hapus file gambar fisik
            }
            redirectWithMessage('../../public/admin/products/manage.php', "Produk berhasil dihapus.", "success");
        } else {
            redirectWithMessage('../../public/admin/products/manage.php', "Gagal menghapus produk: " . $stmt->error, "error");
        }

        $stmt->close();
        $conn->close();
    } // --- END Logika Hapus Produk ---

} else {
    // Redirect ke dashboard jika bukan POST atau GET request yang valid
    header("Location: ../../public/admin/dashboard.php");
    exit();
}
?>