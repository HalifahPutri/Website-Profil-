<?php
include "config.php";

session_start();

$ip = $_SERVER['REMOTE_ADDR'];
$halaman = $_SERVER['REQUEST_URI'];

mysqli_query($conn, "
    INSERT INTO visitor(ip_addres, halaman)
    VALUES('$ip','$halaman')
");

/* statistik */
$rencana = mysqli_query($conn, "
SELECT berita.*, kategori.nama_kategori
FROM berita

LEFT JOIN kategori
ON berita.id_kategori = kategori.id_kategori

WHERE kategori.nama_kategori='Rencana'

ORDER BY tanggal_kegiatan ASC
");

$jml_pengurus = mysqli_num_rows(mysqli_query($conn, "SELECT id_pengurus FROM pengurus"));
$jml_galeri = mysqli_num_rows(mysqli_query($conn, "SELECT id_galeri FROM galeri"));
$jml_visitor = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM visitor"));
$jml_kegiatan = mysqli_num_rows(mysqli_query($conn, "SELECT id_berita FROM berita"));

/* kegiatan terlaksana */
$terlaksana = mysqli_query($conn, "
SELECT 
    berita.*,
    kategori.nama_kategori

FROM berita

LEFT JOIN kategori
ON berita.id_kategori = kategori.id_kategori

WHERE kategori.nama_kategori='Terlaksana'

ORDER BY berita.tanggal_kegiatan DESC
");

/* rencana kegiatan */
$rencana = mysqli_query($conn, "
SELECT 
    berita.*,
    kategori.nama_kategori

FROM berita

LEFT JOIN kategori
ON berita.id_kategori = kategori.id_kategori

WHERE kategori.nama_kategori='Rencana'

ORDER BY berita.tanggal_kegiatan ASC
");
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



    <section class="hero">

        <div class="hero-wrap">
            <style>
                .hero {
                    background-image: url("IMG_7355.jpg");
                    background-size: cover;
                }
            </style>

            <h1>
                Karang Taruna Atma Muda Nawasena
            </h1>

            <p class="desa">
                Dukuh Rejosari
            </p>

            <p class="tagline">
                Gemah Ripah, Loh Jinawi
            </p>

        </div>

    </section>

    <!-- Tentang Kami -->
    <section id="tentang" class="tentang-section">
        <div class="tentang-container">

            <!-- Gambar -->
            <div class="tentang-img">
                <img src="logokt.png" alt="Tentang Kami">
            </div>

            <!-- Teks -->
            <div class="tentang-text animate-on-scroll">
                <h2>Tentang Kami</h2>
                <p>
                    Karang Taruna Atma Muda Nawasena adalah organisasi sosial kepemudaan yang berkomitmen untuk
                    menjadi wadah pengembangan generasi muda dalam berbagai aspek, termasuk sosial, ekonomi, dan budaya.
                </p>
                <p>
                    Karang Taruna Atma Muda Nawasena juga memiliki misi utama untuk menciptakan pemuda yang mandiri,
                    kreatif,
                    serta memiliki kontribusi nyata dalam pembangunan lingkungan dukuh Rejosari.
                </p>
                <a href="kegiatan.php" class="btn-biru">Lihat Kegiatan Kami</a>
            </div>
        </div>
    </section>

    <section class="statistik">
        <div class="container stat-wrap">

            <!-- KEGIATAN -->
            <div class="stat-card">
                <div class="icon">
                    <img src="activity.png" alt="Kegiatan">
                </div>

                <div>
                    <h3><?= $jml_kegiatan ?></h3>
                    <p>Kegiatan</p>
                </div>
            </div>

            <!-- PENGURUS -->
            <div class="stat-card">
                <div class="icon">
                    <img src="pengurus.png" alt="Pengurus">
                </div>

                <div>
                    <h3><?= $jml_pengurus ?></h3>
                    <p>Anggota Pengurus</p>
                </div>
            </div>

            <!-- GALERI -->
            <div class="stat-card">
                <div class="icon">
                    <img src="picture.png" alt="Galeri">
                </div>

                <div>
                    <h3><?= $jml_galeri ?></h3>
                    <p>Galeri Terbaru</p>
                </div>
            </div>

            <!-- VISITOR -->
            <div class="stat-card">
                <div class="icon">
                    <img src="visitors.png" alt="Visitor">
                </div>

                <div>
                    <h3><?= $jml_visitor ?></h3>
                    <p>Visitor Website</p>
                </div>
            </div>

        </div>
    </section>

    <!--Galeri-->
    <?php
    $galeri = mysqli_query($conn, "
SELECT judul, gambar, tanggal_kegiatan, link
FROM galeri
ORDER BY tanggal_kegiatan DESC
");
    ?>

    <section id="galeri" class="galeri-home">

        <div class="container">

            <h2>Galeri Kegiatan</h2>
            <p class="sub-title">Karang Taruna Atma Muda Nawasena</p>

            <div class="galeri-grid">

                <?php
                $no = 0;

                while ($g = mysqli_fetch_assoc($galeri)) {

                    $isHidden = $no >= 4;
                ?>

                    <div class="galeri-card <?= $isHidden ? 'more-card' : '' ?>" <?= $isHidden ? 'style="display:none;"' : '' ?>>

                        <?php if (!empty($g['link'])) { ?>
                            <a href="<?= htmlspecialchars($g['link']) ?>" target="_blank" class="img-wrap">
                                <img src="admin/uploads/<?= htmlspecialchars($g['gambar']) ?>?test=123">
                                <div class="overlay">Lihat</div>
                            </a>
                        <?php } else { ?>
                            <div class="img-wrap">
                                <img src="admin/uploads/<?= htmlspecialchars($g['gambar']) ?>?test=123">
                            </div>
                        <?php } ?>

                        <div class="card-body">
                            <h3><?= htmlspecialchars($g['judul']) ?></h3>
                            <p><?= date('d F Y', strtotime($g['tanggal_kegiatan'])) ?></p>
                        </div>

                    </div>

                <?php
                    $no++;
                }
                ?>

            </div>

            <div class="btn-wrap">
                <button id="showMoreBtn" class="btn-galeri">
                    Lihat Selengkapnya
                </button>
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

    <script>
        const btn = document.getElementById('showMoreBtn');

        btn.addEventListener('click', function() {

            const hiddenCards = document.querySelectorAll('.more-card');

            // CEK APAKAH SUDAH TERBUKA
            if (btn.innerText === 'Lihat Selengkapnya') {

                hiddenCards.forEach(card => {

                    card.style.display = 'block';
                    card.style.animation = 'fadeUp .5s ease';

                });

                btn.innerText = 'Lihat Lebih Sedikit';

            } else {

                hiddenCards.forEach(card => {

                    card.style.display = 'none';

                });

                btn.innerText = 'Lihat Selengkapnya';

                // SCROLL BALIK KE ATAS SECTION
                document.querySelector('.galeri-home')
                    .scrollIntoView({
                        behavior: 'smooth'
                    });

            }

        });

        const toggle = document.getElementById('menu-toggle');
        const menu = document.getElementById('menu');

        toggle.addEventListener('click', () => {
            menu.classList.toggle('active');
            toggle.classList.toggle('active');
        });
    </script>

</body>

</html>