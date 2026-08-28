<?php
session_start();
require_once __DIR__ . '/../database/db.php';

if (!isset($_SESSION['admin'])) {
    exit("Akses ditolak");
}

function slug($text)
{
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
    return trim($text, '-');
}

$notif = "";

/* ================= TAMBAH ================= */
if (isset($_POST['tambah'])) {
    $nama = trim($_POST['nama']);
    $slug = slug($nama);

    // cek unik
    $cek = $pdo->prepare("SELECT COUNT(*) FROM kategori WHERE nama_kategori=?");
    $cek->execute([$nama]);

    if ($cek->fetchColumn() > 0) {
        $notif = "Kategori sudah ada!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO kategori (nama_kategori, slug) VALUES (?, ?)");
        $stmt->execute([$nama, $slug]);
        $notif = "Kategori berhasil ditambahkan!";
    }
}

/* ================= HAPUS ================= */
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    // cek apakah dipakai berita
    $cek = $pdo->prepare("SELECT COUNT(*) FROM berita WHERE id_kategori=?");
    $cek->execute([$id]);

    if ($cek->fetchColumn() > 0) {
        $notif = "Kategori tidak bisa dihapus karena masih dipakai berita!";
    } else {
        $stmt = $pdo->prepare("DELETE FROM kategori WHERE id_kategori=?");
        $stmt->execute([$id]);
        $notif = "Kategori berhasil dihapus!";
    }
}

/* ================= UPDATE ================= */
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = trim($_POST['nama']);
    $slug = slug($nama);

    $stmt = $pdo->prepare("UPDATE kategori SET nama_kategori=?, slug=? WHERE id_kategori=?");
    $stmt->execute([$nama, $slug, $id]);

    $notif = "Kategori berhasil diupdate!";
}

/* ================= PAGINATION ================= */
$limit = 5;
$page = $_GET['page'] ?? 1;
$start = ($page - 1) * $limit;

$total = $pdo->query("SELECT COUNT(*) FROM kategori")->fetchColumn();
$pages = ceil($total / $limit);

$stmt = $pdo->prepare("SELECT * FROM kategori ORDER BY created_at DESC LIMIT $start,$limit");
$stmt->execute();
$data = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori - Admin Panel</title>
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
                <a href="pesan.php">Pesan</a>
                <a href="kategori.php" class="active">Kategori</a>
                <a href="../index.php" target="_blank">Lihat Website</a>
                <a href="?logout=1" class="logout">Logout</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="content-header">
                <h1>Kelola Kategori</h1>
            </div>

            <?php if ($notif): ?>
                <div class="alert-success"><?= htmlspecialchars($notif) ?></div>
            <?php endif; ?>

            <!-- Form Tambah Kategori -->
            <div class="form-card">
                <h3 style="margin-top: 0; color: var(--color-primary); margin-bottom: 15px;">Tambah Kategori Baru</h3>
                <form method="POST">
                    <div class="form-group-inline">
                        <input type="text" name="nama" placeholder="Nama kategori..." required>
                        <button type="submit" name="tambah">Tambah</button>
                    </div>
                </form>
            </div>

            <!-- Search -->
            <div class="search-card">
                <input type="text" id="searchInput" placeholder="🔍 Cari kategori...">
            </div>

            <!-- Tabel Data -->
            <div class="table-card">
                <?php if (count($data) > 0): ?>
                    <table id="kategoriTable">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Kategori</th>
                                <th>Slug</th>
                                <th>Tanggal Dibuat</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($data as $d) { ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= htmlspecialchars($d['nama_kategori']) ?></strong></td>
                                    <td><code
                                            style="background: #f5f5f5; padding: 2px 6px; border-radius: 3px;"><?= htmlspecialchars($d['slug']) ?></code>
                                    </td>
                                    <td><?= date('d M Y', strtotime($d['created_at'])) ?></td>
                                    <td>
                                        <a href="?hapus=<?= $d['id_kategori'] ?>" class="btn-danger"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <?php if ($pages > 1): ?>
                        <div class="pagination-wrapper">
                            <div class="pagination">
                                <?php for ($i = 1; $i <= $pages; $i++): ?>
                                    <a href="?page=<?= $i ?>" class="<?= $page == $i ? 'active' : '' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="no-data">
                        <p>Belum ada kategori. <strong>Tambahkan kategori baru menggunakan form di atas.</strong></p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        document.getElementById("searchInput").addEventListener("keyup", function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll("#kategoriTable tbody tr");

            if (rows.length === 0) return;

            rows.forEach((row) => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? "" : "none";
            });
        });
    </script>
</body>

</html>