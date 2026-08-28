<?php
require_once "database/db.php";

$stmt = $pdo->query("
SELECT 
  berita.*,
  berita.gambar,
  kategori.nama_kategori
FROM berita
LEFT JOIN kategori
ON berita.id_kategori = kategori.id_kategori
ORDER BY berita.tanggal_kegiatan ASC
");

$data = $stmt->fetchAll();

/* fungsi ubah bulan ke Indonesia */
function bulanIndo($tanggal)
{
  $bulan = [
    1 => "Januari",
    "Februari",
    "Maret",
    "April",
    "Mei",
    "Juni",
    "Juli",
    "Agustus",
    "September",
    "Oktober",
    "November",
    "Desember"
  ];
  $b = date('n', strtotime($tanggal));
  $t = date('Y', strtotime($tanggal));
  return $bulan[$b] . " " . $t;
}

/* grouping per bulan */
$grouped = [];
foreach ($data as $d) {
  $bulan = bulanIndo($d['tanggal_kegiatan']);
  $grouped[$bulan][] = $d;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kegiatan</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="responsive.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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


  <section class="kegiatan-section">
    <h2>Kegiatan</h2>
    <p class="sub">Karang Taruna Atma Muda Nawasena<br>Tahun <?= date('Y') ?></p>

    <div class="timeline">

      <?php if (empty($grouped)) { ?>
        <p style="text-align:center;">Belum ada kegiatan</p>
      <?php } ?>

      <?php foreach ($grouped as $bulan => $items) { ?>

        <div class="timeline-item">

          <!-- TITIK -->
          <div class="timeline-dot"></div>

          <!-- BULAN -->
          <div class="timeline-date">
            <?= $bulan ?>
          </div>

          <!-- KONTEN -->
          <div class="timeline-content">

            <?php foreach ($items as $d) { ?>
              <div class="card">

                <!-- STATUS -->
                <?php if ($d['nama_kategori'] == 'Rencana') { ?>
                  <span class="badge-belum">Rencana</span>
                <?php } else { ?>
                  <span class="badge-sudah">Terlaksana</span>
                <?php } ?>

                <!-- FOTO KHUSUS TERLAKSANA -->
                <?php if (
                  $d['nama_kategori'] == 'Terlaksana'
                  && !empty($d['gambar'])
                ) { ?>
                  <div class="kegiatan-img">
                    <img src="admin/uploads/<?= htmlspecialchars($d['gambar']) ?>">
                  </div>
                <?php } ?>

                <h4 class="judul-kegiatan">
                  <?= htmlspecialchars($d['judul']) ?>
                </h4>
                <p>📅 <?= date('d M Y', strtotime($d['tanggal_kegiatan'])) ?></p>
                <a href="detail-kegiatan.php?id=<?= $d['id_berita']; ?>" class="btn-detail">
                    Lihat Detail →
                </a>

              </div>
            <?php } ?>

          </div>

        </div>

      <?php } ?>

    </div>

  </section>

  <!-- FOOTER -->
  <footer>

    <div class="footer-simple">

      <img src="logokt.png" alt="Logo Karang Taruna">

      <h2>Karang Taruna Atma Muda Nawasena</h2>

      <p class="alamat">
        Dukuh Rejosari
      </p>

      <div class="footer-social">
        <a href="https://www.instagram.com/kt.atmamudanawasena" target="_blank">
          Instagram
        </a>

        <span>•</span>

        <a href="mailto:kt.atmamudanawasena@gmail.com">
          Email
        </a>
      </div>

      <div class="footer-menu">

        <a href="index.php">Beranda</a>
        <a href="struktur.php">Struktur</a>
        <a href="kegiatan.php">Kegiatan</a>
        <a href="kontak.php">Kontak</a>

      </div>

      <div class="copyright">
        © 2026 Karang Taruna Atma Muda Nawasena | Dukuh Rejosari
      </div>

    </div>

  </footer>

  <script>
    const toggle = document.getElementById('menu-toggle');
    const menu = document.getElementById('menu');

    toggle.addEventListener('click', () => {
      menu.classList.toggle('active');
      toggle.classList.toggle('active');
    });
  </script>
</body>

</html>