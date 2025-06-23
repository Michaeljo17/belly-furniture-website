<?php
// app/controllers/PortfolioController.php
error_reporting(E_ALL); // Aktifkan semua laporan error
ini_set('display_errors', 1); // Tampilkan error di browser

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

    // --- Logika Tambah Item Portofolio ---
    if (isset($_POST['add_portfolio_item'])) {
        $title = $_POST['title'];
        $clientName = $_POST['client_name'] ?? null; // Bisa kosong
        $projectDate = $_POST['project_date'] ?? null; // Bisa kosong
        $description = $_POST['description'];

        // Sanitasi input
        $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $clientName = htmlspecialchars($clientName, ENT_QUOTES, 'UTF-8');
        $projectDate = htmlspecialchars($projectDate, ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');

        $uploadDir = 'public/uploads/portfolio/'; // Direktori untuk menyimpan gambar portofolio
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
            $fileType = $_FILES['image']['type']; // Tidak digunakan, bisa dihapus
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
                        $conn->close(); // Tutup koneksi sebelum redirect
                        redirectWithMessage('../../public/admin/portfolio/create.php', "Gagal mengunggah gambar.", "error");
                    }
                } else {
                    $conn->close(); // Tutup koneksi sebelum redirect
                    redirectWithMessage('../../public/admin/portfolio/create.php', "Ukuran gambar terlalu besar. Maksimal 2MB.", "error");
                }
            } else {
                $conn->close(); // Tutup koneksi sebelum redirect
                redirectWithMessage('../../public/admin/portfolio/create.php', "Format gambar tidak didukung. Hanya JPG, JPEG, PNG, GIF.", "error");
            }
        } else {
            $conn->close(); // Tutup koneksi sebelum redirect
            $errorMessage = "Gagal mengunggah gambar. Error code: " . $_FILES['image']['error'];
            redirectWithMessage('../../public/admin/portfolio/create.php', $errorMessage, "error");
        }

        // Masukkan data ke database menggunakan Prepared Statement
        $stmt = $conn->prepare("INSERT INTO portfolio_items (title, description, client_name, project_date, image_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $title, $description, $clientName, $projectDate, $imagePath);

        if ($stmt->execute()) {
            $conn->close(); // Tutup koneksi sebelum redirect
            redirectWithMessage('../../public/admin/portfolio/manage.php', "Item Portofolio berhasil ditambahkan!", "success");
        } else {
            $conn->close(); // Tutup koneksi sebelum redirect
            redirectWithMessage('../../public/admin/portfolio/create.php', "Error: " . $stmt->error, "error");
        }

        $stmt->close(); // Statement ditutup setelah execute
        // $conn->close(); // Koneksi ditutup di dalam if/else success/error

    } // --- END Logika Tambah Item Portofolio ---

    // --- Logika Edit Item Portofolio ---
    elseif (isset($_POST['edit_portfolio_item'])) {
        $portfolioId = $_POST['portfolio_id'];
        $title = $_POST['title'];
        $clientName = $_POST['client_name'] ?? null;
        $projectDate = $_POST['project_date'] ?? null;
        $description = $_POST['description'];
        $oldImagePath = $_POST['old_image_path']; // Jalur gambar lama

        // Sanitasi input
        $portfolioId = htmlspecialchars($portfolioId, ENT_QUOTES, 'UTF-8');
        $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $clientName = htmlspecialchars($clientName, ENT_QUOTES, 'UTF-8');
        $projectDate = htmlspecialchars($projectDate, ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
        $newImagePath = $oldImagePath; // Default ke gambar lama

        $uploadDir = 'public/uploads/portfolio/';

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
                        $conn->close();
                        redirectWithMessage('../../public/admin/portfolio/edit.php?id=' . $portfolioId, "Gagal mengunggah gambar baru.", "error");
                    }
                } else {
                    $conn->close();
                    redirectWithMessage('../../public/admin/portfolio/edit.php?id=' . $portfolioId, "Ukuran gambar baru terlalu besar.", "error");
                }
            } else {
                $conn->close();
                redirectWithMessage('../../public/admin/portfolio/edit.php?id=' . $portfolioId, "Format gambar baru tidak didukung.", "error");
            }
        } elseif (isset($_POST['remove_current_image']) && $_POST['remove_current_image'] == 'true') {
            // Logika untuk menghapus gambar yang ada tanpa mengunggah yang baru
            if (!empty($oldImagePath) && file_exists('../../' . $oldImagePath)) {
                unlink('../../' . $oldImagePath);
            }
            $newImagePath = ''; // Atur path gambar menjadi kosong di DB
        }

        // Update data di database menggunakan Prepared Statement
        $stmt = $conn->prepare("UPDATE portfolio_items SET title = ?, description = ?, client_name = ?, project_date = ?, image_path = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $title, $description, $clientName, $projectDate, $newImagePath, $portfolioId);

        if ($stmt->execute()) {
            $conn->close();
            redirectWithMessage('../../public/admin/portfolio/manage.php', "Item Portofolio berhasil diperbarui!", "success");
        } else {
            $conn->close();
            redirectWithMessage('../../public/admin/portfolio/edit.php?id=' . $portfolioId, "Error: " . $stmt->error, "error");
        }

        $stmt->close();
    } // --- END Logika Edit Item Portofolio ---

} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {

    // --- Logika Hapus Item Portofolio (via GET request) ---
    if (isset($_GET['action']) && $_GET['action'] == 'delete_portfolio_item' && isset($_GET['id'])) {
        $portfolioId = $_GET['id'];

        // Ambil jalur gambar sebelum menghapus dari DB
        $imagePath = '';
        $stmt = $conn->prepare("SELECT image_path FROM portfolio_items WHERE id = ?");
        $stmt->bind_param("i", $portfolioId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $imagePath = $row['image_path'];
        }
        $stmt->close();

        // Hapus dari database
        $stmt = $conn->prepare("DELETE FROM portfolio_items WHERE id = ?");
        $stmt->bind_param("i", $portfolioId);

        if ($stmt->execute()) {
            // Jika ada gambar terkait, hapus juga file gambarnya
            if (!empty($imagePath) && file_exists('../../' . $imagePath)) {
                unlink('../../' . $imagePath); // Hapus file gambar fisik
            }
            $conn->close();
            redirectWithMessage('../../public/admin/portfolio/manage.php', "Item Portofolio berhasil dihapus.", "success");
        } else {
            $conn->close();
            redirectWithMessage('../../public/admin/portfolio/manage.php', "Gagal menghapus item portofolio: " . $stmt->error, "error");
        }
        $stmt->close();
    } // --- END Logika Hapus Item Portofolio ---

} // --- END GET Request handling ---

// Redirect ke dashboard jika tidak ada POST atau GET request yang valid
// Pastikan koneksi ditutup sebelum ini jika belum
if (isset($conn) && $conn->ping()) {
    $conn->close();
}
header("Location: ../../public/admin/dashboard.php");
exit();
?>