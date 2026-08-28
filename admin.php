<?php
session_start();

require_once "../database/db.php";

/* =========================================================
   CEK LOGIN ADMIN
   ========================================================= */
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

/* =========================================================
   LOGOUT
   ========================================================= */
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

/* =========================================================
   DATA DASHBOARD
   ========================================================= */

/* Total Pengunjung */
try {
    $totalVisitors = $pdo
        ->query("SELECT COUNT(*) FROM visitor")
        ->fetchColumn();
} catch (PDOException $e) {
    $totalVisitors = 0;
}

/* Total Kegiatan / Berita */
try {
    $totalKegiatan = $pdo
        ->query("SELECT COUNT(*) FROM berita")
        ->fetchColumn();
} catch (PDOException $e) {
    $totalKegiatan = 0;
}

/* Total Galeri */
try {
    $totalGaleri = $pdo
        ->query("SELECT COUNT(*) FROM galeri")
        ->fetchColumn();
} catch (PDOException $e) {
    $totalGaleri = 0;
}

/* Total Pengurus */
try {
    $totalStruktur = $pdo
        ->query("SELECT COUNT(*) FROM pengurus")
        ->fetchColumn();
} catch (PDOException $e) {
    $totalStruktur = 0;
}

/* Total Pesan Belum Dibaca */
try {
    $totalPesanBaru = $pdo
        ->query("
            SELECT COUNT(*)
            FROM chat
            WHERE dibaca = 0
            AND pengirim = 'user'
        ")
        ->fetchColumn();
} catch (PDOException $e) {
    $totalPesanBaru = 0;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Dashboard</title>

    <link
        rel="stylesheet"
        href="admin-style.css"
    >

</head>

<body>

<div class="admin-wrapper">

    <!-- =====================================================
         MENU TOGGLE MOBILE
         ===================================================== -->

    <button
        class="menu-toggle"
        id="menuToggle"
        type="button"
        aria-label="Buka menu"
    >
        ☰
    </button>


    <!-- =====================================================
         OVERLAY MOBILE
         ===================================================== -->

    <div
        class="overlay"
        id="overlay"
    ></div>


    <!-- =====================================================
         SIDEBAR
         ===================================================== -->

    <aside
        class="sidebar"
        id="sidebar"
    >

        <div class="sidebar-header">

            <h2>Admin Panel</h2>

            <p>
                Halo,
                <?= htmlspecialchars($_SESSION['admin'], ENT_QUOTES, 'UTF-8') ?>
            </p>

        </div>


        <nav class="sidebar-nav">

            <!-- Dashboard -->
            <a
                href="admin.php"
                class="active"
            >
                Dashboard
            </a>


            <!-- Kegiatan -->
            <a href="berita.php">
                Kegiatan
            </a>


            <!-- Galeri -->
            <a href="galeri.php">
                Galeri
            </a>


            <!-- Struktur -->
            <a href="admin-struktur.php">
                Struktur
            </a>


            <!-- Pesan -->
            <a href="pesan.php">

                Pesan

                <?php if ($totalPesanBaru > 0): ?>

                    <span class="badge">
                        <?= $totalPesanBaru ?>
                    </span>

                <?php endif; ?>

            </a>


            <!-- Kategori -->
            <a href="kategori.php">
                Kategori
            </a>


            <!-- Lihat Website -->
            <a
                href="../index.php"
                target="_blank"
                rel="noopener noreferrer"
            >
                Lihat Website
            </a>


            <!-- Logout -->
            <a
                href="?logout=1"
                class="logout"
            >
                Logout
            </a>

        </nav>

    </aside>


    <!-- =====================================================
         MAIN CONTENT
         ===================================================== -->

    <main class="main-content">


        <!-- HEADER -->
        <div class="content-header">

            <h1>
                Dashboard
            </h1>

        </div>


        <!-- =================================================
             STATISTIK
             ================================================= -->

        <div class="stats-grid">


            <!-- Total Kegiatan -->
            <div class="stat-card">

                <h3>
                    Total Kegiatan
                </h3>

                <p class="stat-number">
                    <?= $totalKegiatan ?>
                </p>

            </div>


            <!-- Total Galeri -->
            <div class="stat-card">

                <h3>
                    Total Galeri
                </h3>

                <p class="stat-number">
                    <?= $totalGaleri ?>
                </p>

            </div>


            <!-- Total Pengurus -->
            <div class="stat-card">

                <h3>
                    Pengurus
                </h3>

                <p class="stat-number">
                    <?= $totalStruktur ?>
                </p>

            </div>


            <!-- Total Pengunjung -->
            <div class="stat-card">

                <h3>
                    Total Pengunjung
                </h3>

                <p class="stat-number">
                    <?= $totalVisitors ?>
                </p>

            </div>


            <!-- Pesan Masuk -->
            <div class="stat-card highlight">

                <h3>
                    Pesan Masuk
                </h3>

                <p class="stat-number">
                    <?= $totalPesanBaru ?>
                </p>

                <small>
                    Belum dibaca
                </small>

            </div>


        </div>
        <!-- END STATS GRID -->


        <!-- =================================================
             QUICK ACTIONS
             ================================================= -->

        <div class="quick-actions">

            <h2>
                Quick Actions
            </h2>


            <div class="action-buttons">

                <!-- Tambah Kegiatan -->
                <a
                    href="berita.php"
                    class="btn-action"
                >
                    Tambah Kegiatan
                </a>


                <!-- Tambah Galeri -->
                <a
                    href="galeri.php"
                    class="btn-action"
                >
                    Tambah Galeri
                </a>


                <!-- Tambah Struktur -->
                <a
                    href="admin-struktur.php"
                    class="btn-action"
                >
                    Tambah Struktur
                </a>

            </div>

        </div>


    </main>

</div>


<!-- =========================================================
     JAVASCRIPT
     ========================================================= -->

<script>

    /* =====================================================
       SIDEBAR MOBILE
       ===================================================== */

    const menuToggle =
        document.getElementById("menuToggle");

    const sidebar =
        document.getElementById("sidebar");

    const overlay =
        document.getElementById("overlay");


    if (menuToggle && sidebar && overlay) {

        menuToggle.addEventListener("click", function () {

            sidebar.classList.toggle("show");

            overlay.classList.toggle("show");

        });


        overlay.addEventListener("click", function () {

            sidebar.classList.remove("show");

            overlay.classList.remove("show");

        });

    }


    /* =====================================================
       BADGE PESAN BARU
       ===================================================== */

    function loadBadge() {

        fetch("ajax_pesan_baru.php")

            .then(function (response) {

                return response.text();

            })

            .then(function (jumlah) {

                jumlah = parseInt(jumlah, 10) || 0;


                let badge =
                    document.querySelector(".sidebar-nav .badge");


                const menuPesan =
                    document.querySelector(
                        ".sidebar-nav a[href='pesan.php']"
                    );


                /* Jika ada pesan baru */
                if (jumlah > 0) {


                    /* Buat badge jika belum ada */
                    if (!badge && menuPesan) {

                        badge =
                            document.createElement("span");

                        badge.className = "badge";

                        menuPesan.appendChild(badge);

                    }


                    if (badge) {

                        badge.textContent = jumlah;

                    }

                }


                /* Jika tidak ada pesan baru */
                else {

                    if (badge) {

                        badge.remove();

                    }

                }

            })

            .catch(function (error) {

                console.error(
                    "Gagal mengambil jumlah pesan:",
                    error
                );

            });

    }


    /* Jalankan saat halaman dibuka */
    loadBadge();


    /* Periksa setiap 5 detik */
    setInterval(
        loadBadge,
        5000
    );

</script>


</body>

</html>