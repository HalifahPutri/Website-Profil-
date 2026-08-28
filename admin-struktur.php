<?php
session_start();
require_once __DIR__ . '/../database/db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// DELETE
if (isset($_GET['delete'])) {

    $stmt = $pdo->prepare("SELECT foto FROM pengurus WHERE id_pengurus = ?");
    $stmt->execute([$_GET['delete']]);

    $old = $stmt->fetch();

    if ($old && $old['foto'] && file_exists(__DIR__ . '/uploads/' . $old['foto'])) {
        unlink(__DIR__ . '/uploads/' . $old['foto']);
    }

    $stmt = $pdo->prepare("DELETE FROM pengurus WHERE id_pengurus = ?");
    $stmt->execute([$_GET['delete']]);

    header('Location: admin-struktur.php');
    exit;
}

// ADD / EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = $_POST['nama'];
    $jabatan = $_POST['jabatan'];
    $foto = $_POST['foto_lama'] ?? '';

    /* upload foto */
    if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === 0) {

        $upload_dir = __DIR__ . "/uploads/";

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $fileName = time() . '_' . basename($_FILES['foto']['name']);

        $filePath = $upload_dir . $fileName;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $filePath)) {

            /* hapus foto lama */
            if ($foto && file_exists($upload_dir . $foto)) {
                unlink($upload_dir . $foto);
            }

            $foto = $fileName;
        }
    }

    /* UPDATE */
    if (!empty($_POST['id'])) {
        $stmt = $pdo->prepare("
        UPDATE pengurus
        SET nama=?, jabatan=?, foto=?
        WHERE id_pengurus=?
        ");

        $stmt->execute([
            $nama,
            $jabatan,
            $foto,
            $_POST['id']
        ]);
    }

    /* INSERT */ else {
        $stmt = $pdo->prepare("
        INSERT INTO pengurus (nama, jabatan, foto)
        VALUES (?, ?, ?)
        ");

        $stmt->execute([
            $nama,
            $jabatan,
            $foto,
        ]);
    }
    header("Location: admin-struktur.php");
    exit;
}

// Edit data
$editData = null;

if (isset($_GET['edit'])) {

    $stmt = $pdo->prepare("
    SELECT * FROM pengurus
    WHERE id_pengurus=?
    ");

    $stmt->execute([$_GET['edit']]);

    $editData = $stmt->fetch();
}

// Fetch all
$stmt = $pdo->query("
SELECT * FROM pengurus
ORDER BY id_pengurus DESC
");

$strukturList = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Struktur - Admin</title>
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
                <a href="admin-struktur.php" class="active">Struktur</a>
                <a href="pesan.php">Pesan</a>
                <a href="kategori.php">Kategori</a>
                <a href="../index.php" target="_blank">Lihat Website</a>
                <a href="?logout=1" class="logout">Logout</a>
            </nav>
        </aside>

        <main class="main-content">
            <div class="content-header">
                <h1>Kelola Struktur Organisasi</h1>
            </div>

            <div class="form-container">
                <h2><?= $editData ? 'Edit' : 'Tambah' ?> Anggota</h2>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $editData['id_pengurus'] ?? '' ?>">
                    <input type="hidden" name="foto_lama" value="<?= $editData['foto'] ?? '' ?>">

                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" value="<?= $editData['nama'] ?? '' ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Jabatan</label>
                        <select name="jabatan" required>
                            <option value="">Pilih Jabatan</option>
                            <?php
                            $jabatanList = ['Ketua', 'Wakil Ketua', 'Sekretaris', 'Bendahara', 'Humas', 'Lainnya'];
                            foreach ($jabatanList as $j):
                            ?>
                                <option value="<?= $j ?>" <?= ($editData['jabatan'] ?? '') === $j ? 'selected' : '' ?>>
                                    <?= $j ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Foto</label>
                        <input type="file" name="foto" accept="image/*">
                        <?php if ($editData && $editData['foto']): ?>
                            <p class="small-text">Foto sekarang: <?= htmlspecialchars($editData['foto']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Simpan</button>
                        <?php if ($editData): ?>
                            <a href="admin-struktur.php" class="btn-secondary">Batal</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="table-container">
                <h2>Daftar Struktur Organisasi</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Foto</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($strukturList as $item): ?>
                            <tr>
                                <td><?= $item['nama'] ?></td>
                                <td><?= $item['jabatan'] ?></td>
                                <td>
                                    <?php if ($item['foto']): ?>
                                        <img src="../uploads/<?= htmlspecialchars($item['foto']) ?>" class="table-img">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?edit=<?= $item['id_pengurus'] ?>" class="btn-edit">Edit</a>
                                    <a href="?delete=<?= $item['id_pengurus'] ?>" class="btn-delete"
                                        onclick="return confirm('Yakin hapus?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </main>
    </div>

    <?php if (isset($_GET['logout'])) {
        session_destroy();
        header("Location: login.php");
    } ?>
</body>

</html>