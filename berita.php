<?php

session_start();

require_once __DIR__ . '/../database/db.php';


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
   FUNGSI CEK KATEGORI
   ========================================================= */

function getNamaKategori($pdo, $idKategori)
{
    $stmt = $pdo->prepare("
        SELECT nama_kategori
        FROM kategori
        WHERE id_kategori = ?
        LIMIT 1
    ");

    $stmt->execute([$idKategori]);

    $data = $stmt->fetch();

    return $data
        ? strtolower(trim($data['nama_kategori']))
        : '';
}


/* =========================================================
   FUNGSI NAMA FILE FOTO
   ========================================================= */

function uploadFoto($file, $folder)
{
    if (
        !isset($file) ||
        empty($file['name']) ||
        $file['error'] !== UPLOAD_ERR_OK
    ) {
        return null;
    }


    $allowed = [
        'jpg',
        'jpeg',
        'png',
        'webp'
    ];


    $ext = strtolower(
        pathinfo($file['name'], PATHINFO_EXTENSION)
    );


    if (!in_array($ext, $allowed, true)) {

        die("Format gambar tidak didukung!");

    }


    if (!is_dir($folder)) {

        mkdir($folder, 0777, true);

    }


    $namaFile =
        time() .
        '_' .
        uniqid() .
        '.' .
        $ext;


    $tujuan =
        $folder .
        $namaFile;


    if (!move_uploaded_file(
        $file['tmp_name'],
        $tujuan
    )) {

        die("Foto gagal diupload!");

    }


    return $namaFile;
}


/* =========================================================
   FOLDER UPLOAD
   ========================================================= */

$folderUpload =
    __DIR__ . "/uploads/";


if (!is_dir($folderUpload)) {

    mkdir(
        $folderUpload,
        0777,
        true
    );

}


/* =========================================================
   TAMBAH KEGIATAN
   ========================================================= */

if (isset($_POST['tambah'])) {


    $judul =
        trim($_POST['judul'] ?? '');


    $tanggal =
        $_POST['tanggal_kegiatan'] ?? '';


    $jam =
        $_POST['jam'] ?? '';


    $lokasi =
        trim($_POST['lokasi'] ?? '');


    $contact =
        trim($_POST['contact_person'] ?? '');


    $deskripsi =
        trim($_POST['deskripsi'] ?? '');


    $deskripsiDetail =
        trim($_POST['deskripsi_detail'] ?? '');


    $cara =
        trim($_POST['cara_pendaftaran'] ?? '');


    $syarat =
        trim($_POST['persyaratan'] ?? '');


    $maps =
        trim($_POST['maps'] ?? '');


    $kategori =
        $_POST['kategori'] ?? '';


    $namaFile = null;


    /* =====================================================
       CEK NAMA KATEGORI
       ===================================================== */

   if (!empty($_FILES['gambar']['name'])) {

    $namaFile = uploadFoto(
        $_FILES['gambar'],
        $folderUpload
    );

}


    /* =====================================================
       INSERT DATABASE
       ===================================================== */

    $stmt = $pdo->prepare("
        INSERT INTO berita
        (
            judul,
            tanggal_kegiatan,
            jam,
            lokasi,
            contact_person,
            deskripsi,
            deskripsi_detail,
            cara_pendaftaran,
            persyaratan,
            maps,
            gambar,
            id_kategori
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
        )
    ");


    $stmt->execute([

        $judul,

        $tanggal,

        $jam,

        $lokasi,

        $contact,

        $deskripsi,

        $deskripsiDetail,

        $cara,

        $syarat,

        $maps,

        $namaFile,

        $kategori

    ]);


    header("Location: berita.php");

    exit;

}


/* =========================================================
   HAPUS KEGIATAN
   ========================================================= */

if (
    isset($_GET['hapus']) &&
    is_numeric($_GET['hapus'])
) {


    $id =
        (int) $_GET['hapus'];


    /* Ambil foto lama */

    $stmt =
        $pdo->prepare("
            SELECT gambar
            FROM berita
            WHERE id_berita = ?
        ");


    $stmt->execute([$id]);


    $old =
        $stmt->fetch();


    /* Hapus file foto */

    if (
        $old &&
        !empty($old['gambar'])
    ) {

        $fileLama =
            $folderUpload .
            $old['gambar'];


        if (file_exists($fileLama)) {

            unlink($fileLama);

        }

    }


    /* Hapus database */

    $stmt =
        $pdo->prepare("
            DELETE FROM berita
            WHERE id_berita = ?
        ");


    $stmt->execute([$id]);


    header("Location: berita.php");

    exit;

}


/* =========================================================
   EDIT DATA
   ========================================================= */

$editData = null;


if (
    isset($_GET['edit']) &&
    is_numeric($_GET['edit'])
) {


    $stmt =
        $pdo->prepare("
            SELECT *
            FROM berita
            WHERE id_berita = ?
        ");


    $stmt->execute([
        (int) $_GET['edit']
    ]);


    $editData =
        $stmt->fetch();

}


/* =========================================================
   UPDATE KEGIATAN
   ========================================================= */

if (isset($_POST['update'])) {


    $id =
        (int) ($_POST['id'] ?? 0);


    $judul =
        trim($_POST['judul'] ?? '');


    $tanggal =
        $_POST['tanggal_kegiatan'] ?? '';


    $jam =
        $_POST['jam'] ?? '';


    $lokasi =
        trim($_POST['lokasi'] ?? '');


    $contact =
        trim($_POST['contact_person'] ?? '');


    $deskripsi =
        trim($_POST['deskripsi'] ?? '');


    $deskripsiDetail =
        trim($_POST['deskripsi_detail'] ?? '');


    $cara =
        trim($_POST['cara_pendaftaran'] ?? '');


    $syarat =
        trim($_POST['persyaratan'] ?? '');


    $maps =
        trim($_POST['maps'] ?? '');


    $kategori =
        $_POST['kategori'] ?? '';


    /* =====================================================
       AMBIL DATA LAMA
       ===================================================== */

    $stmtOld =
        $pdo->prepare("
            SELECT gambar
            FROM berita
            WHERE id_berita = ?
        ");


    $stmtOld->execute([$id]);


    $oldData =
        $stmtOld->fetch();


    $namaFile =
        $oldData['gambar'] ?? null;


    /* =====================================================
       CEK KATEGORI
       ===================================================== */

    $namaKategori =
        getNamaKategori(
            $pdo,
            $kategori
        );
/* =====================================================
   UPLOAD FOTO BARU
   SEMUA KATEGORI BOLEH MEMILIKI FOTO
   ===================================================== */

if (!empty($_FILES['gambar']['name'])) {

    /* Simpan nama foto lama */
    $fotoLama = $namaFile;


    /* Upload foto baru */
    $fotoBaru = uploadFoto(
        $_FILES['gambar'],
        $folderUpload
    );


    /* Kalau upload berhasil */
    if ($fotoBaru) {

        $namaFile = $fotoBaru;


        /* Hapus foto lama */
        if (!empty($fotoLama)) {

            $fileLama =
                $folderUpload . $fotoLama;

            if (file_exists($fileLama)) {

                unlink($fileLama);

            }

        }

    }

}

    /* =====================================================
       UPDATE DATABASE
       ===================================================== */

    $stmt =
        $pdo->prepare("
            UPDATE berita SET

                judul = ?,

                tanggal_kegiatan = ?,

                jam = ?,

                lokasi = ?,

                contact_person = ?,

                deskripsi = ?,

                deskripsi_detail = ?,

                cara_pendaftaran = ?,

                persyaratan = ?,

                maps = ?,

                gambar = ?,

                id_kategori = ?

            WHERE id_berita = ?
        ");


    $stmt->execute([

        $judul,

        $tanggal,

        $jam,

        $lokasi,

        $contact,

        $deskripsi,

        $deskripsiDetail,

        $cara,

        $syarat,

        $maps,

        $namaFile,

        $kategori,

        $id

    ]);


    header("Location: berita.php");

    exit;

}


/* =========================================================
   AMBIL DATA KEGIATAN
   ========================================================= */

$stmt =
    $pdo->query("
        SELECT

            berita.*,

            kategori.nama_kategori

        FROM berita

        LEFT JOIN kategori

            ON berita.id_kategori =
               kategori.id_kategori

        ORDER BY
            berita.tanggal_kegiatan DESC
    ");


$data =
    $stmt->fetchAll();


/* =========================================================
   AMBIL DATA KATEGORI
   ========================================================= */

$stmt =
    $pdo->query("
        SELECT
            id_kategori,
            nama_kategori

        FROM kategori

        ORDER BY
            nama_kategori ASC
    ");


$kategoriList =
    $stmt->fetchAll();

?>

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Kelola Kegiatan
    </title>

    <link
        rel="stylesheet"
        href="admin-style.css"
    >

</head>


<body>


<div class="admin-wrapper">


    <!-- =====================================================
         MENU MOBILE
         ===================================================== -->

    <button
        class="menu-toggle"
        id="menuToggle"
        type="button"
    >
        ☰
    </button>


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

            <h2>
                Admin Panel
            </h2>

            <p>

                Halo,
                <?= htmlspecialchars(
                    $_SESSION['admin'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </p>

        </div>


        <nav class="sidebar-nav">


            <a href="admin.php">
                Dashboard
            </a>


            <a
                href="berita.php"
                class="active"
            >
                Kegiatan
            </a>


            <a href="galeri.php">
                Galeri
            </a>


            <a href="admin-struktur.php">
                Struktur
            </a>


            <a href="pesan.php">
                Pesan
            </a>


            <a href="kategori.php">
                Kategori
            </a>


            <a
                href="../index.php"
                target="_blank"
                rel="noopener noreferrer"
            >
                Lihat Website
            </a>


            <a
                href="?logout=1"
                class="logout"
            >
                Logout
            </a>


        </nav>

    </aside>


    <!-- =====================================================
         MAIN
         ===================================================== -->

    <main class="main-content">


        <div class="content-header">

            <h1>
                Kelola Kegiatan
            </h1>

        </div>


        <!-- =================================================
             FORM KEGIATAN
             ================================================= -->

        <div class="form-wrapper">


            <h2>

                <?= $editData
                    ? 'Edit'
                    : 'Tambah'
                ?>

                Kegiatan

            </h2>


            <form
                method="POST"
                enctype="multipart/form-data"
            >


                <!-- ID EDIT -->

                <input
                    type="hidden"
                    name="id"
                    value="<?= $editData['id_berita'] ?? '' ?>"
                >


                <!-- =================================================
                     JUDUL
                     ================================================= -->

                <div class="form-group">

                    <label>
                        Judul Kegiatan
                    </label>


                    <input
                        type="text"
                        name="judul"
                        value="<?= htmlspecialchars(
                            $editData['judul'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        required
                    >

                </div>


                <!-- =================================================
                     TANGGAL
                     ================================================= -->

                <div class="form-group">

                    <label>
                        Tanggal Kegiatan
                    </label>


                    <input
                        type="date"
                        name="tanggal_kegiatan"
                        value="<?= htmlspecialchars(
                            $editData['tanggal_kegiatan'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        required
                    >

                </div>


                <!-- =================================================
                     JAM
                     ================================================= -->

                <div class="form-group">

                    <label>
                        Jam Kegiatan
                    </label>


                    <input
                        type="time"
                        name="jam"
                        value="<?= htmlspecialchars(
                            $editData['jam'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                </div>


                <!-- =================================================
                     LOKASI
                     ================================================= -->

                <div class="form-group">

                    <label>
                        Lokasi
                    </label>


                    <input
                        type="text"
                        name="lokasi"
                        value="<?= htmlspecialchars(
                            $editData['lokasi'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                </div>


                <!-- =================================================
                     CONTACT PERSON
                     ================================================= -->

                <div class="form-group">

                    <label>
                        Contact Person
                    </label>


                    <input
                        type="text"
                        name="contact_person"
                        value="<?= htmlspecialchars(
                            $editData['contact_person'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                </div>


                <!-- =================================================
                     DESKRIPSI
                     ================================================= -->

                <div class="form-group">

                    <label>
                        Deskripsi
                    </label>


                    <textarea
                        name="deskripsi"
                        rows="5"
                    ><?= htmlspecialchars(
                        $editData['deskripsi'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?></textarea>

                </div>


                <!-- =================================================
                     DESKRIPSI DETAIL
                     ================================================= -->

                <div class="form-group">

                    <label>
                        Deskripsi Detail
                    </label>


                    <textarea
                        name="deskripsi_detail"
                        rows="8"
                    ><?= htmlspecialchars(
                        $editData['deskripsi_detail'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?></textarea>

                </div>


                <!-- =================================================
                     CARA PENDAFTARAN
                     ================================================= -->

                <div class="form-group">

                    <label>
                        Cara Pendaftaran
                    </label>


                    <textarea
                        name="cara_pendaftaran"
                        rows="5"
                    ><?= htmlspecialchars(
                        $editData['cara_pendaftaran'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?></textarea>

                </div>


                <!-- =================================================
                     PERSYARATAN
                     ================================================= -->

                <div class="form-group">

                    <label>
                        Persyaratan
                    </label>


                    <textarea
                        name="persyaratan"
                        rows="5"
                    ><?= htmlspecialchars(
                        $editData['persyaratan'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?></textarea>

                </div>


                <!-- =================================================
                     GOOGLE MAPS
                     ================================================= -->

                <div class="form-group">

                    <label>
                        Link Google Maps
                    </label>


                    <input
                        type="url"
                        name="maps"
                        placeholder="https://www.google.com/maps/embed?pb=..."
                        value="<?= htmlspecialchars(
                            $editData['maps'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                </div>


                <!-- =================================================
                     FOTO
                     ================================================= -->

                <div
                    class="form-group foto-group"
                    id="fotoGroup"
                >

                    <label>
                        Foto Kegiatan
                    </label>


                    <input
                        type="file"
                        name="gambar"
                        accept=".jpg,.jpeg,.png,.webp"
                    >


                    <?php if (
                        $editData &&
                        !empty($editData['gambar'])
                    ): ?>


                        <img
                            src="uploads/<?= htmlspecialchars(
                                $editData['gambar'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            style="
                                width:120px;
                                height:120px;
                                object-fit:cover;
                                border-radius:10px;
                                margin-top:10px;
                            "
                            alt="Foto kegiatan"
                        >


                    <?php endif; ?>


                </div>


                <!-- =================================================
                     KATEGORI
                     ================================================= -->

                <div class="form-group">

                    <label>
                        Kategori
                    </label>


                    <select
                        name="kategori"
                        id="kategori"
                        required
                    >


                        <option value="">
                            -- Pilih Kategori --
                        </option>


                        <?php foreach (
                            $kategoriList
                            as $k
                        ): ?>


                            <option
                                <?= (
                                    ($editData['id_kategori'] ?? '') ==
                                    $k['id_kategori']
                                )
                                    ? 'selected'
                                    : ''
                                ?>
                            >

                                <?= htmlspecialchars(
                                    $k['nama_kategori'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </option>


                        <?php endforeach; ?>


                    </select>

                </div>


                <!-- =================================================
                     BUTTON
                     ================================================= -->

                <div class="form-actions">


                    <button
                        type="submit"
                        name="<?= $editData
                            ? 'update'
                            : 'tambah'
                        ?>"
                        class="btn-submit"
                    >

                        <?= $editData
                            ? 'Update'
                            : 'Tambah'
                        ?>

                        Kegiatan

                    </button>


                    <?php if ($editData): ?>


                        <a
                            href="berita.php"
                            class="btn-cancel"
                        >
                            Batal
                        </a>


                    <?php endif; ?>


                </div>


            </form>


        </div>


        <!-- =================================================
             DAFTAR KEGIATAN
             ================================================= -->

        <div class="table-wrapper">


            <h2>
                Daftar Kegiatan
            </h2>


            <table class="data-table">


                <thead>

                    <tr>

                        <th>
                            No
                        </th>

                        <th>
                            Foto
                        </th>

                        <th>
                            Judul
                        </th>

                        <th>
                            Tanggal
                        </th>

                        <th>
                            Lokasi
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>


                    <?php

                    $no = 1;

                    foreach ($data as $d):

                    ?>


                        <tr>


                            <td>
                                <?= $no++ ?>
                            </td>


                            <td>


                                <?php if (
                                    !empty($d['gambar'])
                                ): ?>


                                    <img
                                        src="uploads/<?= htmlspecialchars(
                                            $d['gambar'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        style="
                                            width:70px;
                                            height:70px;
                                            object-fit:cover;
                                            border-radius:8px;
                                        "
                                        alt="Foto kegiatan"
                                    >


                                <?php else: ?>

                                    -

                                <?php endif; ?>


                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $d['judul'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </td>


                            <td>

                                <?= date(
                                    'd M Y',
                                    strtotime(
                                        $d['tanggal_kegiatan']
                                    )
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $d['lokasi'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </td>


                            <td>


                                <?php if (
                                    strtolower(
                                        $d['nama_kategori'] ?? ''
                                    ) === 'rencana'
                                ): ?>


                                    <span class="badge badge-belum">
                                        Rencana
                                    </span>


                                <?php else: ?>


                                    <span class="badge badge-sudah">
                                        Terlaksana
                                    </span>


                                <?php endif; ?>


                            </td>


                            <td>


                                <div class="table-actions">


                                    <a
                                        href="?edit=<?= $d['id_berita'] ?>"
                                        class="btn-action-edit"
                                    >
                                        Edit
                                    </a>


                                    <a
                                        href="?hapus=<?= $d['id_berita'] ?>"
                                        class="btn-action-delete"
                                        onclick="return confirm('Hapus kegiatan ini?')"
                                    >
                                        Hapus
                                    </a>


                                </div>


                            </td>


                        </tr>


                    <?php endforeach; ?>


                </tbody>


            </table>


        </div>


    </main>


</div>


<!-- =========================================================
     JAVASCRIPT
     ========================================================= -->

<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {


        /* =====================================================
           SIDEBAR MOBILE
           ===================================================== */

        const menuToggle =
            document.getElementById("menuToggle");

        const sidebar =
            document.getElementById("sidebar");

        const overlay =
            document.getElementById("overlay");


        if (
            menuToggle &&
            sidebar &&
            overlay
        ) {


            menuToggle.addEventListener(
                "click",
                function () {

                    sidebar.classList.toggle(
                        "show"
                    );

                    overlay.classList.toggle(
                        "show"
                    );

                }
            );


            overlay.addEventListener(
                "click",
                function () {

                    sidebar.classList.remove(
                        "show"
                    );

                    overlay.classList.remove(
                        "show"
                    );

                }
            );

        }


        /* =====================================================
           KATEGORI / FOTO
           ===================================================== */

        const kategori =
            document.getElementById("kategori");
        
        const fotoGroup =
            document.getElementById("fotoGroup");
        
        
        function toggleFoto() {
        
            if (!fotoGroup) {
                return;
            }
        
            fotoGroup.style.display = "block";
        }
        
        
        if (kategori) {
        
            kategori.addEventListener(
                "change",
                toggleFoto
            );
        
            toggleFoto();
        
        }
}
);

</script>


</body>

</html>