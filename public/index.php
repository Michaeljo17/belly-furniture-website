<?php
// public/index.php - Single Page Website for Public View
require_once '../app/config/database.php'; // Koneksi ke database

// ==========================================================
// Logika Pengambilan Data dari Database Settings
// ==========================================================
$settings = [];
$sqlSettings = "SELECT setting_key, setting_value FROM settings";
$resultSettings = $conn->query($sqlSettings);

if ($resultSettings && $resultSettings->num_rows > 0) {
    while ($row = $resultSettings->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

// Data Kontak
$contactEmail = htmlspecialchars($settings['contact_email'] ?? '');
$whatsappNumber = htmlspecialchars($settings['whatsapp_number'] ?? '');
$instagramLink = htmlspecialchars($settings['instagram_link'] ?? '');

// Teks Tentang Kami
$aboutUsText = htmlspecialchars($settings['about_us_text'] ?? '');

// Teks Visi & Misi
$visiText = htmlspecialchars($settings['visi_text'] ?? '');
$misiTexts = [];
for ($i = 1; $i <= 4; $i++) {
    if (isset($settings['misi_text_' . $i])) {
        $misiTexts[] = htmlspecialchars($settings['misi_text_' . $i]);
    }
}

// Daftar Kompetensi
$kompetensiList = htmlspecialchars($settings['kompetensi_list'] ?? '');
$kompetensiArray = array_map('trim', explode(';', $kompetensiList));
$kompetensiArray = array_filter($kompetensiArray);

// Fungsi untuk membuat link WhatsApp yang bisa diklik
function createWhatsappLink($number) {
    $cleanedNumber = preg_replace('/[^0-9]/', '', $number);
    if (substr($cleanedNumber, 0, 1) == '0') {
        $cleanedNumber = '62' . substr($cleanedNumber, 1);
    } elseif (substr($cleanedNumber, 0, 2) != '62') {
        $cleanedNumber = '62' . $cleanedNumber;
    }
    return 'https://wa.me/' . $cleanedNumber;
}

// Fungsi untuk membuat link email
function createEmailLink($email) {
    return 'mailto:' . $email;
}

// ==========================================================
// Logika Pengambilan Data Produk
// ==========================================================
$products = [];
$sqlProducts = "SELECT id, name, description, image_path, category FROM products ORDER BY created_at DESC";
$resultProducts = $conn->query($sqlProducts);

if ($resultProducts && $resultProducts->num_rows > 0) {
    while($row = $resultProducts->fetch_assoc()) {
        $products[] = $row;
    }
}

// ==========================================================
// Logika Pengambilan Data Portofolio
// ==========================================================
$portfolioItems = [];
$sqlPortfolio = "SELECT id, title, description, image_path, client_name, project_date FROM portfolio_items ORDER BY created_at DESC";
$resultPortfolio = $conn->query($sqlPortfolio);

if ($resultPortfolio && $resultPortfolio->num_rows > 0) {
    while($row = $resultPortfolio->fetch_assoc()) {
        $portfolioItems[] = $row;
    }
}

$conn->close(); // Tutup koneksi database setelah semua data diambil
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belly Furniture - PT Belly Inovasi Group</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="assets/images/belly_logo.png" alt="Belly Furniture Logo" style="width: 200px;"> 
            <h1>BELLY Furniture</h1>
            <p>PT BELLY INOVASI GROUP</p>
        </div>
        <div class="hamburger-menu">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </div>
    </header>
    <nav>
        <ul>
            <li><a href="#home-section">Beranda</a></li>
            <li><a href="#products-section">Produk</a></li>
            <li><a href="#portfolio-section">Portofolio</a></li>
            <li><a href="#visi-misi-section">Visi & Misi</a></li>
            <li><a href="#kompetensi-section">Kompetensi</a></li>
            <li><a href="#about-section">Tentang Kami</a></li>
            <li><a href="#contact-section">Hubungi Kami</a></li>
        </ul>
    </nav>
    <main id="home-section">
        <section class="hero-section fade-in-on-scroll" style="background-image: url('assets/images/hero_bg.jpg');">
            <h2>Solusi Interior & Furniture Berkualitas untuk Segala Kebutuhan Anda</h2>
            <p>Berpengalaman dalam bidang interior serta furniture untuk rumah, apartemen, sekolah, kantor, rumah kantor, restaurant, cafe, gudang dan sebagainya.</p>
            <a href="#products-section" class="button">Lihat Produk Kami</a>
        </section>

        <section id="products-section" class="main-content-section fade-in-on-scroll">
            <h2>Produk Kami</h2>
            <?php if (!empty($products)): ?>
                <div class="products-grid-container"> <div class="products-grid">
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <?php if (!empty($product['image_path'])): ?>
                                    <img src="http://localhost/belly_furniture_website/<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php else: ?>
                                    <img src="assets/images/placeholder.jpg" alt="No Image">
                                <?php endif; ?>
                                <div class="product-card-content">
                                    <p class="category"><?php echo htmlspecialchars($product['category']); ?></p>
                                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <p class="no-products">Belum ada produk yang ditambahkan. Silakan hubungi admin.</p>
            <?php endif; ?>
        </section>

        <section id="portfolio-section" class="main-content-section fade-in-on-scroll">
            <h2>Portofolio Kami</h2>
            <?php if (!empty($portfolioItems)): ?>
                <div class="portfolio-grid-container"> <div class="portfolio-grid">
                        <?php foreach ($portfolioItems as $item): ?>
                            <div class="portfolio-card">
                                <?php if (!empty($item['image_path'])): ?>
                                    <img src="http://localhost/belly_furniture_website/<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                <?php else: ?>
                                    <img src="assets/images/placeholder.jpg" alt="No Image">
                                <?php endif; ?>
                                <div class="portfolio-card-content">
                                    <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                                    <p><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                                    <?php if (!empty($item['client_name']) || !empty($item['project_date'])): ?>
                                        <p class="client-info">
                                            <?php echo !empty($item['client_name']) ? 'Klien: ' . htmlspecialchars($item['client_name']) : ''; ?>
                                            <?php echo (!empty($item['client_name']) && !empty($item['project_date'])) ? ' - ' : ''; ?>
                                            <?php !empty($item['project_date']) ? 'Tanggal: ' . htmlspecialchars($item['project_date']) : ''; ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <p class="no-portfolio-items">Belum ada item portofolio yang ditambahkan. Silakan hubungi admin.</p>
            <?php endif; ?>
        </section>

        <section id="visi-misi-section" class="main-content-section fade-in-on-scroll">
            <h2>Visi</h2>
            <p><?php echo nl2br($visiText); ?></p>

            <h2>Misi</h2>
            <ul>
                <?php if (!empty($misiTexts)): ?>
                    <?php foreach ($misiTexts as $misi): ?>
                        <li><?php echo nl2br($misi); ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>[PLACEHOLDER: Tambahkan teks Misi dari database]</li>
                <?php endif; ?>
            </ul>
        </section>

        <section id="kompetensi-section" class="main-content-section fade-in-on-scroll">
            <h2>Kompetensi</h2>
            <ul>
                <?php if (!empty($kompetensiArray)): ?>
                    <?php foreach ($kompetensiArray as $kompetensi): ?>
                        <li><?php echo $kompetensi; ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>[PLACEHOLDER: Tambahkan daftar kompetensi dari database]</li>
                <?php endif; ?>
            </ul>
        </section>

        <section id="about-section" class="main-content-section fade-in-on-scroll">
            <h2>Tentang Kami</h2>
            <p><?php echo nl2br($aboutUsText); ?></p>
            <p>[PLACEHOLDER: Ini adalah teks tambahan untuk Tentang Kami. Anda bisa menghapus atau mengeditnya di admin panel jika diperlukan.]</p>
        </section>

        <section id="contact-section" class="main-content-section fade-in-on-scroll">
            <h2>Hubungi Kami</h2>
            <div class="contact-info">
                <?php if ($contactEmail): ?>
                    <p>Email: <a href="<?php echo createEmailLink($contactEmail); ?>"><?php echo $contactEmail; ?></a></p>
                <?php else: ?>
                    <p>Email: [PLACEHOLDER: Tambahkan Email Kontak di Admin Panel]</p>
                <?php endif; ?>

                <?php if ($whatsappNumber): ?>
                    <p>WhatsApp: <a href="<?php echo createWhatsappLink($whatsappNumber); ?>" target="_blank">Chat via WhatsApp</a></p>
                <?php else: ?>
                    <p>WhatsApp: [PLACEHOLDER: Tambahkan Nomor WhatsApp di Admin Panel]</p>
                <?php endif; ?>

                <?php if ($instagramLink): ?>
                    <p>Instagram: <a href="<?php echo $instagramLink; ?>" target="_blank">Kunjungi Instagram Kami</a></p>
                <?php else: ?>
                    <p>Instagram: [PLACEHOLDER: Tambahkan Link Instagram di Admin Panel]</p>
                <?php endif; ?>

                <p>[PLACEHOLDER: Tambahkan alamat fisik atau detail lain dari Company Profile jika diperlukan]</p>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 PT Belly Inovasi Group. Semua Hak Cipta Dilindungi.</p>
        <p><a href="admin/login.php" style="color: #f7a000; font-weight: bold;">Login Admin</a></p>
    </footer>

    <button id="back-to-top" title="Kembali ke Atas">&#9650;</button> <script src="assets/js/script.js"></script> 
</body>
</html>