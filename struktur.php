<?php
require_once "database/db.php";

$ketua = $pdo->query("
SELECT * FROM pengurus 
WHERE jabatan='Ketua'
")->fetchAll();

$wakil = $pdo->query("
SELECT * FROM pengurus
WHERE jabatan='Wakil Ketua'
")->fetchAll();

$sekretaris = $pdo->query("
SELECT * FROM pengurus
WHERE jabatan LIKE 'Sekretaris%'
")->fetchAll();

$bendahara = $pdo->query("
SELECT * FROM pengurus
WHERE jabatan LIKE 'Bendahara%'
")->fetchAll();

$divisi = $pdo->query("
SELECT * FROM pengurus
WHERE jabatan NOT IN ('Ketua','Wakil Ketua')
AND jabatan NOT LIKE 'Sekretaris%'
AND jabatan NOT LIKE 'Bendahara%'
")->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Struktur Organisasi</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="responsive.css">
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@500;600;700&display=swap"
    rel="stylesheet">
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


  <div class="struktur-container">

    <h2 class="judul-struktur">
      Struktur Organisasi <br>
      Karang Taruna Atma Muda Nawasena
    </h2>

    <!-- Ketua -->
    <div class="level ketua">
      <?php foreach ($ketua as $k): ?>
        <div class="card-struktur">
          <img src="admin/uploads/<?= $k['foto']; ?>" alt="">
          <h3><?= $k['nama']; ?></h3>
          <p><?= $k['jabatan']; ?></p>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="line-down"></div>

    <!-- Wakil -->
    <div class="level wakil">
      <?php foreach ($wakil as $w): ?>
        <div class="card-struktur">
          <img src="admin/uploads/<?= $w['foto']; ?>" alt="">
          <h3><?= $w['nama']; ?></h3>
          <p><?= $w['jabatan']; ?></p>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- STRUKTUR SEKRETARIS + BENDAHARA -->
    <div class="group-top">
      <div class="main-line"></div>

      <div class="line-horizontal"></div>

      <div class="level row-4">

        <?php foreach ($sekretaris as $s): ?>
          <div class="item-atas">
            <div class="line-vertical"></div>
            <div class="card-struktur">
              <img src="admin/uploads/<?= $s['foto']; ?>" alt="">
              <h3><?= $s['nama']; ?></h3>
              <p><?= $s['jabatan']; ?></p>
            </div>
          </div>
        <?php endforeach; ?>

        <?php foreach ($bendahara as $b): ?>
          <div class="item-atas">
            <div class="line-vertical"></div>
            <div class="card-struktur">
              <img src="admin/uploads/<?= $b['foto']; ?>" alt="">
              <h3><?= $b['nama']; ?></h3>
              <p><?= $b['jabatan']; ?></p>
            </div>
          </div>
        <?php endforeach; ?>

      </div>

    </div>

    <!-- DIVISI -->
    <div class="group-bottom">

      <div class="line-center"></div>

      <div class="line-horizontal-bottom"></div>

      <div class="level row-3">

        <?php foreach ($divisi as $d): ?>
          <div class="item-bawah">

            <div class="line-vertical"></div>

            <div class="card-struktur">
              <img src="admin/uploads/<?= $d['foto']; ?>" alt="">
              <h3><?= $d['nama']; ?></h3>
              <p><?= $d['jabatan']; ?></p>
            </div>

          </div>
        <?php endforeach; ?>

      </div>

    </div>

  </div>

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