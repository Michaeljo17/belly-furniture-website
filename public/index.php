<?php
// public/index.php
define('BASE_URL', 'http://localhost/belly_furniture_website/');

require_once '../app/config/database.php';

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
$contactEmail = htmlspecialchars($settings['contact_email'] ?? '');
$whatsappNumber = htmlspecialchars($settings['whatsapp_number'] ?? '');
$instagramLink = htmlspecialchars($settings['instagram_link'] ?? '');
$aboutUsText = htmlspecialchars($settings['about_us_text'] ?? '');
$visiText = htmlspecialchars($settings['visi_text'] ?? '');
$misiTexts = [];
for ($i = 1; $i <= 4; $i++) {
    if (isset($settings['misi_text_' . $i])) {
        $misiTexts[] = htmlspecialchars($settings['misi_text_' . $i]);
    }
}
$kompetensiList = htmlspecialchars($settings['kompetensi_list'] ?? '');
$kompetensiArray = array_map('trim', explode(';', $kompetensiList));
$kompetensiArray = array_filter($kompetensiArray);
function createWhatsappLink($number) {
    $cleanedNumber = preg_replace('/[^0-9]/', '', $number);
    if (substr($cleanedNumber, 0, 1) == '0') {
        $cleanedNumber = '62' . substr($cleanedNumber, 1);
    } elseif (substr($cleanedNumber, 0, 2) != '62') {
        $cleanedNumber = '62' . $cleanedNumber;
    }
    return 'https://wa.me/' . $cleanedNumber;
}
function createEmailLink($email) {
    return 'mailto:' . $email;
}
$products = [];
$sqlProducts = "SELECT id, name, description, image_path, category FROM products ORDER BY created_at DESC";
$resultProducts = $conn->query($sqlProducts);
if ($resultProducts && $resultProducts->num_rows > 0) {
    while($row = $resultProducts->fetch_assoc()) {
        $products[] = $row;
    }
}
$portfolioItems = [];
$sqlPortfolio = "SELECT id, title, description, image_path, client_name, project_date FROM portfolio_items ORDER BY created_at DESC";
$resultPortfolio = $conn->query($sqlPortfolio);
if ($resultPortfolio && $resultPortfolio->num_rows > 0) {
    while($row = $resultPortfolio->fetch_assoc()) {
        $portfolioItems[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belly Furniture - PT Belly Inovasi Group</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <a href="#home-section" class="logo">
                <img src="<?php echo BASE_URL; ?>assets/images/belly_logo.png" alt="Belly Furniture Logo">
            </a>
            <nav>
                <ul>
                    <li><a href="#home-section">Beranda</a></li>
                    <li><a href="#products-section">Produk</a></li>
                    <li><a href="#portfolio-section">Portofolio</a></li>
                    <li><a href="#about-section">Tentang Kami</a></li>
                    <li><a href="#kompetensi-section">Kompetensi</a></li>
                    <li><a href="#contact-section">Hubungi Kami</a></li>
                </ul>
            </nav>
            <div class="hamburger-menu">
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
            </div>
        </div>
    </header>

    <main>
        <section id="home-section" class="hero-section fade-in-on-scroll">
            <div class="hero-content">
                <h1>Mewujudkan Ruang Impian Anda dengan Furnitur Berkualitas</h1>
                <p>Desain elegan, fungsional, dan personal untuk setiap kebutuhan interior Anda.</p>
                <a href="#products-section" class="button">Jelajahi Produk</a>
            </div>
        </section>

        <section id="products-section" class="main-content-section fade-in-on-scroll">
            <div class="section-header">
                <h2>Produk Pilihan Kami</h2>
                <?php if (!empty($products) && count($products) > 1): ?>
                <div class="slider-controls">
                    <button class="prev-btn" aria-label="Produk Sebelumnya">&#10094;</button>
                    <button class="next-btn" aria-label="Produk Berikutnya">&#10095;</button>
                </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($products)): ?>
                <div class="slider-container products-grid-container">
                    <div class="slider-grid products-grid">
                        <?php foreach ($products as $product): ?>
                            <div class="card product-card">
                                <img src="<?php echo BASE_URL . htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <div class="card-content">
                                    <p class="category"><?php echo htmlspecialchars($product['category']); ?></p>
                                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <p class="no-items">Belum ada produk yang ditambahkan.</p>
            <?php endif; ?>
        </section>

        <section id="portfolio-section" class="main-content-section fade-in-on-scroll">
            <div class="section-header">
                <h2>Portofolio Proyek</h2>
                <?php if (!empty($portfolioItems) && count($portfolioItems) > 1): ?>
                <div class="slider-controls">
                    <button class="prev-btn" aria-label="Portofolio Sebelumnya">&#10094;</button>
                    <button class="next-btn" aria-label="Portofolio Berikutnya">&#10095;</button>
                </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($portfolioItems)): ?>
                <div class="slider-container portfolio-grid-container">
                    <div class="slider-grid portfolio-grid">
                        <?php foreach ($portfolioItems as $item): ?>
                            <div class="card portfolio-card">
                                <img src="<?php echo BASE_URL . htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                <div class="card-content">
                                    <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                                    <p><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                                    <?php if (!empty($item['client_name']) || !empty($item['project_date'])): ?>
                                        <p class="client-info">
                                            <?php echo !empty($item['client_name']) ? 'Klien: ' . htmlspecialchars($item['client_name']) : ''; ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <p class="no-items">Belum ada item portofolio yang ditambahkan.</p>
            <?php endif; ?>
        </section>
        
        <section id="about-section" class="main-content-section fade-in-on-scroll">
            <div class="about-container">
                <div class="about-image">
                    <img src="<?php echo BASE_URL; ?>assets/images/about_us_image.jpg" alt="Tentang Belly Furniture">
                </div>
                <div class="about-text">
                    <h2>Tentang Kami</h2>
                    <p><?php echo nl2br($aboutUsText); ?></p>
                    <div class="vision-mission">
                        <h4>Visi</h4>
                        <p><?php echo nl2br($visiText); ?></p>
                        <h4>Misi</h4>
                        <ul>
                            <?php foreach ($misiTexts as $misi): ?>
                                <li><?php echo nl2br($misi); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section id="kompetensi-section" class="main-content-section fade-in-on-scroll">
            <div class="section-header" style="justify-content: center; text-align: center;">
                 <h2>Keahlian & Kompetensi Kami</h2>
            </div>
            <?php if (!empty($kompetensiArray)): ?>
                <div class="kompetensi-grid">
                    <?php foreach ($kompetensiArray as $kompetensi): ?>
                        <div class="kompetensi-card">
                            <div class="kompetensi-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                            </div>
                            <p><?php echo $kompetensi; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section id="contact-section" class="main-content-section fade-in-on-scroll contact-section-style">
            <h2>Siap Membuat Ruang Impian Anda?</h2>
            <p>Hubungi kami untuk konsultasi gratis dan mulailah proyek Anda bersama kami hari ini.</p>
            <div class="contact-buttons">
                <?php if ($whatsappNumber): ?>
                    <a href="<?php echo createWhatsappLink($whatsappNumber); ?>" class="button contact-button" target="_blank">Chat via WhatsApp</a>
                <?php endif; ?>
                <?php if ($contactEmail): ?>
                    <a href="<?php echo createEmailLink($contactEmail); ?>" class="button button-secondary">Kirim Email</a>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> PT Belly Inovasi Group. Semua Hak Cipta Dilindungi.</p>
        <div class="social-links">
             <?php if ($instagramLink): ?>
                <a href="<?php echo $instagramLink; ?>" target="_blank">Instagram</a>
            <?php endif; ?>
        </div>
        <p class="admin-login"><a href="admin/login.php">Admin Login</a></p>
    </footer>

    <button id="back-to-top" title="Kembali ke Atas">&#9650;</button>

    <script src="assets/js/script.js"></script> 
</body>
</html>