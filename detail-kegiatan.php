<?php
require_once "database/db.php";

if (!isset($_GET['id'])) {
    header("Location:kegiatan.php");
    exit;
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("
SELECT
berita.*,
kategori.nama_kategori
FROM berita
LEFT JOIN kategori
ON berita.id_kategori = kategori.id_kategori
WHERE id_berita = ?
");

$stmt->execute([$id]);

$kegiatan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kegiatan) {
    die("Kegiatan tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
    content="width=device-width,initial-scale=1.0">
    <title><?= htmlspecialchars($kegiatan['judul']); ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="responsive.css"
    <link rel="stylesheet" href="detail-kegiatan.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
</head>
<body>
    <!-- NAVBAR -->
  <header>
    <div class="container nav">
      <div class="logo-area">
        <img src="logokt.png" class="logo">
        <div>
          <h3>Karang Taruna</h3>
          <p>Atma Muda Nawasena</p>
        </div>

      </div>

      <!-- Tombol Hamburger -->
      <div class="menu-toggle" id="menu-toggle">
        <span></span>
        <span></span>
        <span></span>
      </div>

      <nav id="menu">
        <a class="active" href="index.php">Beranda</a>
        <a href="struktur.php">Struktur</a>
        <a href="kegiatan.php">Kegiatan</a>
        <a href="kontak.php">Kontak</a>
        <a href="admin/login.php" class="login-btn">
            LOGIN
        </a>
      </nav>
    </div>
  </header>
 
<section class="detail-hero">
    <img src="admin/uploads/<?= htmlspecialchars($kegiatan['gambar']); ?>" alt="<?= htmlspecialchars($kegiatan['judul']); ?>">
    <div class="detail-hero-overlay">
        <?php if(strtolower($kegiatan['nama_kategori'])=="rencana"){ ?>
            <span class="status rencana">Rencana</span>
        <?php }else{ ?>
            <span class="status terlaksana">Terlaksana</span>
        <?php } ?>
        <h1><?= htmlspecialchars($kegiatan['judul']); ?></h1>
    </div>
</section>

<section class="detail-container">
    <!-- KONTEN KIRI -->
    <div class="detail-content">
        <div class="detail-card">
            <h2>Tentang Kegiatan</h2>
            <p><?= nl2br(htmlspecialchars($kegiatan['deskripsi_detail'])) ?></p>
        </div>
        <div class="detail-card">
            <h2>Cara Pendaftaran</h2>
            <p><?= nl2br(htmlspecialchars($kegiatan['cara_pendaftaran'])) ?></p>
        </div>
        <div class="detail-card">
            <h2>Persyaratan</h2>
            <p><?= nl2br(htmlspecialchars($kegiatan['persyaratan'])) ?></p>
        </div>
        <?php if(!empty($kegiatan['maps'])){ ?>
        <div class="detail-card">
            <h2>Lokasi Kegiatan</h2>
            <iframe
                src="<?= htmlspecialchars($kegiatan['maps']); ?>"
                allowfullscreen
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    <?php } ?>
    </div>
    
    <aside class="detail-sidebar">

    <div class="detail-sidebar-card">
        <h3>Informasi Kegiatan</h3>

        <div class="detail-info">
            <div class="detail-icon">📅</div>
            <div class="detail-info-text">
                <span>Tanggal</span>
                <strong><?= date('d F Y', strtotime($kegiatan['tanggal_kegiatan'])) ?></strong>
            </div>
        </div>

        <div class="detail-info">
            <div class="detail-icon">🕒</div>
            <div class="detail-info-text">
                <span>Jam</span>
                <strong><?= date('H.i', strtotime($kegiatan['jam'])) ?> WIB</strong>
            </div>
        </div>

        <div class="detail-info">
            <div class="detail-icon">📍</div>
            <div class="detail-info-text">
                <span>Lokasi</span>
                <strong><?= htmlspecialchars($kegiatan['lokasi']); ?></strong>
            </div>
        </div>

        <div class="detail-info">
            <div class="detail-icon">👤</div>
            <div class="detail-info-text">
                <span>Contact Person</span>
                <strong><?= htmlspecialchars($kegiatan['contact_person']); ?></strong>
            </div>
        </div>

    </div>

    <div class="detail-sidebar-card">
        <h3>Countdown</h3>

        <div
            id="countdown"
            data-tanggal="<?= $kegiatan['tanggal_kegiatan']; ?>"
            data-jam="<?= $kegiatan['jam']; ?>">
        </div>

    </div>

    <div class="detail-back">
        <a href="kegiatan.php">
            <i class="ri-arrow-left-line"></i>
            <span>Kembali ke Kegiatan</span>
        </a>
    </div>

</aside>
</section>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const countdown = document.getElementById("countdown");
    if (!countdown) return;

    const tanggal = countdown.dataset.tanggal;
    const jam = countdown.dataset.jam;

    if (!tanggal || !jam) {
        countdown.innerHTML = "<b>Data waktu belum tersedia</b>";
        return;
    }

    // Format: 2026-08-17 08:00:00
    const target = new Date(`${tanggal}T${jam}`);
    function updateCountdown() {
        const sekarang = new Date();
        const selisih = target - sekarang;
        if (selisih <= 0) {
            countdown.innerHTML = `
                <div class="count-finish">
                    Kegiatan Telah Terlaksana
                </div>
            `;
            return;
        }

        const hari = Math.floor(selisih / (1000 * 60 * 60 * 24));
        const jam = Math.floor((selisih % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const menit = Math.floor((selisih % (1000 * 60 * 60)) / (1000 * 60));
        const detik = Math.floor((selisih % (1000 * 60)) / 1000);
        countdown.innerHTML = `
            <div class="count-box">
                <h2>${hari}</h2>
                <span>Hari</span>
            </div>
            <div class="count-box">
                <h2>${jam}</h2>
                <span>Jam</span>
            </div>
            <div class="count-box">
                <h2>${menit}</h2>
                <span>Menit</span>
            </div>
            <div class="count-box">
                <h2>${detik}</h2>
                <span>Detik</span>
            </div>
        `;
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
});

const toggle = document.getElementById("menu-toggle");
const menu = document.getElementById("menu");

toggle.addEventListener("click",()=>{

    menu.classList.toggle("active");
    toggle.classList.toggle("active");

});

</script>
</body>
</html>