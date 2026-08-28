<?php
session_start();
require_once "database/db.php";
if (isset($_POST['kirim'])) {

  $nama = $_POST['nama'];
  $email = $_POST['email'];
  $telepon = $_POST['telepon'];
  $topik = $_POST['topik'];
  $pesan = $_POST['pesan'];

  $kode_chat = "USR" . time();

  $stmt = $pdo->prepare("
INSERT INTO pesan
(nama,email,telepon,topik,pesan,kode_chat,tanggal)

VALUES
(?,?,?,?,?,?,NOW())
");

  $stmt->execute([
    $nama,
    $email,
    $telepon,
    $topik,
    $pesan,
    $kode_chat
  ]);

  $stmt2 = $pdo->prepare("
INSERT INTO chat
(kode_chat,pengirim,pesan,tipe,waktu,dibaca)

VALUES
(?, 'user', ?, 'text', NOW(), 0)
");

  $stmt2->execute([
    $kode_chat,
    $pesan
  ]);
  $_SESSION['kode_chat'] = $kode_chat;

  header("Location: kontak.php?success=1");
  exit;
}

?>
<!DOCTYPE html>
<html>

<head>
  <title>Karang Taruna Nawasena</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@500;600;700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="responsive.css">
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

  </header><!-- HERO CONTACT -->
  <section class="contact-hero">

    <div class="container hero-contact-wrap">

      <div class="hero-contact-text">

        <a href="#form-kontak" class="badge-contact">
          💬 Hubungi Kami
        </a>

        <h1>
          Kami Siap Mendengar <br>
          dan Membantu Anda
        </h1>

        <p>
          Punya pertanyaan, masukan, atau ingin bekerja sama?
          Jangan ragu untuk menghubungi kami.
        </p>

      </div>

      <div class="hero-contact-logo">
        <img src="logokt.png">
      </div>

    </div>

  </section>


  <!-- CONTACT CONTENT -->
  <section class="contact-modern">

    <div class="container modern-grid">

      <!-- KIRI -->
      <div class="contact-left">

        <h2>Informasi Kontak</h2>

        <!-- Alamat -->
        <div class="contact-card">
          <div class="icon-contact">
            <img src="location.png" alt="Alamat">
          </div>

          <div>
            <h4>Alamat</h4>
            <p>
              Rejosari, Bendosari, Sawit, Boyolali,
              Jawa Tengah
            </p>
          </div>
        </div>

        <a href="https://www.instagram.com/kt.atmamudanawasena" target="_blank" class="contact-card instagram-link">

          <div class="icon-contact">
            <img src="instagram1.png" alt="Instagram">
          </div>

          <div>
            <h4>Instagram</h4>
            <p>@kt.atmamudanawasena</p>
          </div>

        </a>

        <!-- Email -->
        <div class="contact-card">
          <div class="icon-contact">
            <img src="mail1.png" alt="Email">
          </div>

          <div>
            <h4>Email</h4>
            <p>kt.atmamudanawasena@gmail.com</p>
          </div>
          </div>

        <!-- Jam Operasional -->
        <div class="contact-card">
          <div class="icon-contact">
            <img src="clock.png" alt="Jam Operasional">
          </div>

          <div>
            <h4>Jam Operasional</h4>
            <p>Senin - Sabtu | 08.00 - 17.00 WIB</p>
          </div>
        </div>
      </div>

      <!-- KANAN -->
      <div class="contact-right" id="form-kontak">
        <h2>Kirim Pesan</h2>

        <form method="POST">

          <div class="input-row">

            <input type="text" name="nama" placeholder="Nama Lengkap" required>

            <input type="email" name="email" placeholder="Email" required>

          </div>

          <input type="text" name="telepon" placeholder="Nomor Telepon">

          <select name="topik">
            <option>Pilih Topik</option>
            <option>Saran</option>
            <option>Kerja Sama</option>
            <option>Informasi</option>

          </select>

          <textarea name="pesan" placeholder="Pesan Anda"></textarea>

          <button type="submit" name="kirim" class="btn-kirim">
            Submit
          </button>

        </form>

      </div>
    </div>


    <!-- MAP -->
    <div class="map-modern">

      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1221.5023211071218!2d110.71365996077023!3d-7.581265818578838!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a15852f6c2e6f%3A0x7b81cde870bdfc66!2sGOR%20Pandawa%20Jabung!5e0!3m2!1sid!2sid!4v1778338517905!5m2!1sid!2sid"
        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
      </iframe>

    </div>
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

  <!-- TOAST NOTIFIKASI -->
  <?php if (isset($_GET['success'])) { ?>
  <div id="toast-notif" style="
      position: fixed;
      top: 24px;
      right: 24px;
      z-index: 9999;
      background: #003b73;
      color: #fff;
      padding: 16px 22px;
      border-radius: 12px;
      font-family: 'Inter', sans-serif;
      font-size: 15px;
      font-weight: 500;
      box-shadow: 0 8px 24px rgba(0,0,0,0.2);
      display: flex;
      align-items: center;
      gap: 10px;
      opacity: 1;
      transition: opacity 0.5s ease;
      max-width: 320px;
  ">
      <span style="font-size:20px;">✅</span>
      <span>Pesan berhasil dikirim! Kami akan segera membalas.</span>
  </div>
  <script>
      setTimeout(function() {
          var toast = document.getElementById('toast-notif');
          if (toast) {
              toast.style.opacity = '0';
              setTimeout(function() { toast.remove(); }, 500);
          }
      }, 3500);
  </script>
  <?php } ?>

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