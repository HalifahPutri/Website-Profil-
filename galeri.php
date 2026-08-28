<?php
session_start();
require_once __DIR__ . '/../database/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

/* ========= TAMBAH ========= */
if (isset($_POST['tambah'])) {
    $judul = $_POST['judul'];
    $tanggal = $_POST['tanggal_kegiatan'] ?? null;
    $link = $_POST['link'] ?? null;

    $namaFile = null;

    if (!empty($_FILES['gambar']['name'])) {
        $folder = __DIR__ . "/uploads/";
        if (!is_dir($folder))
            mkdir($folder, 0777, true);

        $namaFile = time() . "_" . $_FILES['gambar']['name'];
        move_uploaded_file($_FILES['gambar']['tmp_name'], $folder . $namaFile);
    }

    $stmt = $pdo->prepare("
        INSERT INTO galeri 
        (judul, gambar, link, tanggal_upload, tanggal_kegiatan)
        VALUES (?, ?, ?, NOW(), ?)
    ");

    $stmt->execute([$judul, $namaFile, $link, $tanggal]);
}

/* ========= HAPUS ========= */
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {

    $id = (int) $_GET['hapus'];

    // Ambil nama file dari database
    $stmt = $pdo->prepare("
        SELECT gambar
        FROM galeri
        WHERE id_galeri = ?
        LIMIT 1
    ");

    $stmt->execute([$id]);

    $ambil = $stmt->fetch();

    // Folder upload
    $folder = __DIR__ . "/uploads/";

    // Hapus file jika benar-benar ada
    if (
        $ambil &&
        !empty($ambil['gambar'])
    ) {

        $fileLama = $folder . basename($ambil['gambar']);

        if (is_file($fileLama)) {
            unlink($fileLama);
        }
    }

    // Hapus data dari database
    $stmt = $pdo->prepare("
        DELETE FROM galeri
        WHERE id_galeri = ?
    ");

    $stmt->execute([$id]);

    header("Location: galeri.php");
    exit;
}

/* ========= EDIT ========= */
$editData = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM galeri WHERE id_galeri = ?");
    $stmt->execute([$_GET['edit']]);
    $editData = $stmt->fetch();
}

/* ========= UPDATE ========= */
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $judul = $_POST['judul'];
    $tanggal = $_POST['tanggal_kegiatan'] ?? null;
    $link = $_POST['link'] ?? null;

    $folder = __DIR__ . "/uploads/";

    if (!empty($_FILES['gambar']['name'])) {
        $stmt = $pdo->prepare("SELECT gambar FROM galeri WHERE id_galeri=?");
        $stmt->execute([$id]);
        $old = $stmt->fetch();

        if ($old && $old['gambar']) {
            unlink($folder . $old['gambar']);
        }

        $namaFile = time() . "_" . $_FILES['gambar']['name'];
        move_uploaded_file($_FILES['gambar']['tmp_name'], $folder . $namaFile);

        $stmt = $pdo->prepare("
            UPDATE galeri 
            SET judul=?, gambar=?, link=?, tanggal_kegiatan=? 
            WHERE id_galeri=?
        ");

        $stmt->execute([$judul, $namaFile, $link, $tanggal, $id]);
    } else {
        $stmt = $pdo->prepare("
            UPDATE galeri 
            SET judul=?, link=?, tanggal_kegiatan=? 
            WHERE id_galeri=?
        ");

        $stmt->execute([$judul, $link, $tanggal, $id]);
    }
}
$stmt = $pdo->query("SELECT * FROM galeri ORDER BY tanggal_upload DESC");
$data = $stmt->fetchAll();

$link = $_POST['link'] ?? null;

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Galeri - Admin</title>
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
                <a href="galeri.php" class="active">Galeri</a>
                <a href="admin-struktur.php">Struktur</a>
                <a href="pesan.php">Pesan</a>
                <a href="kategori.php">Kategori</a>
                <a href="../index.php" target="_blank">Lihat Website</a>
                <a href="?logout=1" class="logout">Logout</a>
            </nav>
        </aside>

        <main class="main-content">
            <div class="content-header">
                <h1>Kelola Galeri</h1>
            </div>

            <div class="form-wrapper">
                <h2><?= $editData ? "Edit Foto" : "Tambah Foto" ?></h2>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $editData['id_galeri'] ?? '' ?>">

                    <div class="form-group">
                        <label for="judul">Judul Foto</label>
                        <input type="text" id="judul" name="judul"
                            value="<?= htmlspecialchars($editData['judul'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="gambar">Gambar</label>
                        <input type="file" id="gambar" name="gambar" accept="image/*">
                        <p class="small-text">Format: JPG, JPEG, PNG, WEBP (Max: 5MB)</p>
                    </div>

                    <div class="form-group">
                        <label>Tanggal Kegiatan</label>
                        <input type="date" name="tanggal_kegiatan" value="<?= $editData['tanggal_kegiatan'] ?? '' ?>">
                    </div>

                    <div class="form-group">
                        <label for="link">Link Google Drive</label>
                        <input type="url" id="link" name="link" value="<?= htmlspecialchars($editData['link'] ?? '') ?>"
                            placeholder="https://drive.google.com/...">
                    </div>

                    <?php if ($editData && $editData['gambar']) { ?>
                        <div class="image-preview">
                            <img src="uploads/<?= htmlspecialchars($editData['gambar']) ?>" alt="Preview">
                            <div class="image-info">
                                <p>Gambar saat ini:</p>
                                <p><?= htmlspecialchars($editData['gambar']) ?></p>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="form-actions">
                        <button type="submit" name="<?= $editData ? 'update' : 'tambah' ?>" class="btn-submit">
                            <?= $editData ? 'Update Foto' : 'Tambah Foto' ?>
                        </button>
                        <?php if ($editData) { ?>
                            <a href="galeri.php" class="btn-cancel">Batal</a>
                        <?php } ?>
                    </div>
                </form>
            </div>

            <div class="table-wrapper">
                <h2>Daftar Foto Galeri</h2>
                <?php if (empty($data)) { ?>
                    <div class="no-data">
                        <p>Belum ada foto. <a href="galeri.php">Tambah foto baru</a></p>
                    </div>
                <?php } else { ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Gambar</th>
                                <th>Judul</th>
                                <th>Link</th>
                                <th>Tanggal kegiatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($data as $d) { ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <img src="uploads/<?= htmlspecialchars($d['gambar']) ?>"
                                            alt="<?= htmlspecialchars($d['judul']) ?>" class="table-thumbnail">
                                    </td>
                                    <td><?= htmlspecialchars($d['judul']) ?></td>
                                    <td>
                                        <?php if (!empty($d['link'])) { ?>
                                            <a href="<?= htmlspecialchars($d['link']) ?>" target="_blank">Lihat</a>
                                        <?php } else {
                                            echo '-';
                                        } ?>
                                    </td>
                                    <td>
                                        <?= !empty($d['tanggal_kegiatan'])
                                            ? date('d M Y', strtotime($d['tanggal_kegiatan']))
                                            : '-' ?>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="?edit=<?= $d['id_galeri'] ?>" class="btn-action-edit">Edit</a>
                                            <a href="?hapus=<?= $d['id_galeri'] ?>" class="btn-action-delete"
                                                onclick="return confirm('Hapus foto ini?')">Hapus</a>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($d['link'])) { ?>
                                            <a href="<?= htmlspecialchars($d['link']) ?>" target="_blank">Lihat</a>
                                        <?php } else { ?>
                                            -
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } ?>
            </div>
        </main>
    </div>

    <?php if (isset($_GET['logout'])) {
        session_destroy();
        header("Location: login.php");
    } ?>
</body>

</html>