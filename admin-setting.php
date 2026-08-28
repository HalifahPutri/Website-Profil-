<?php
session_start();
require_once __DIR__ . '/../database/db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// Handle Update Settings
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        if ($key !== 'submit') {
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([$value, $key]);
        }
    }
    $success = "Pengaturan berhasil disimpan!";
}

// Get all settings
$stmt = $pdo->query("SELECT * FROM settings");
$setting = [];
while ($row = $stmt->fetch()) {
    $setting[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - Admin</title>
    <link rel="stylesheet" href="admin-style.css">
</head>

<body>
    <div class="admin-wrapper">
    <button class="menu-toggle" id="menuToggle">
        ☰
    </button>

    <div class="overlay" id="overlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>Admin Panel</h2>
            <p>Halo, <?= $_SESSION['admin'] ?></p>
        </div>
            <nav class="sidebar-nav">
                <a href="admin.php">Dashboard</a>
                <a href="berita.php">Berita</a>
                <a href="galeri.php">Galeri</a>
                <a href="admin-struktur.php">Struktur</a>
                <a href="?logout=1" class="logout">Logout</a>
            </nav>
        </aside>

        <main class="main-content">
            <div class="content-header">
                <h1>Pengaturan Website</h1>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert success"><?= $success ?></div>
            <?php endif; ?>

            <div class="form-container">
                <form method="POST">
                    <h3>Informasi Umum</h3>

                    <div class="form-group">
                        <label>Judul Website</label>
                        <input type="text" name="site_title" value="<?= $setting['site_title'] ?? '' ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Subtitle/Tagline</label>
                        <input type="text" name="site_subtitle" value="<?= $setting['site_subtitle'] ?? '' ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Tentang Kami</label>
                        <textarea name="about_text" rows="4" required><?= $setting['about_text'] ?? '' ?></textarea>
                    </div>

                    <h3>Kontak</h3>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="contact_email" value="<?= $setting['contact_email'] ?? '' ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Instagram</label>
                        <input type="text" name="contact_instagram" value="<?= $setting['contact_instagram'] ?? '' ?>"
                            placeholder="@username">
                    </div>

                    <div class="form-group">
                        <label>Alamat</label>
                        <input type="text" name="contact_address" value="<?= $setting['contact_address'] ?? '' ?>"
                            required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="submit" class="btn-primary">Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <?php if (isset($_GET['logout'])) {
        session_destroy();
        header("Location: login.php");
    } ?>

</body>

</html>