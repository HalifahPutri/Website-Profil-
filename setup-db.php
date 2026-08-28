<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../database/db.php";

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_tables'])) {
    try {
        // 1. Buat tabel admin
        $pdo->exec("CREATE TABLE IF NOT EXISTS admin (
            id INT PRIMARY KEY AUTO_INCREMENT,
            username VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // 2. Buat tabel visitors
        $pdo->exec("CREATE TABLE IF NOT EXISTS visitors (
            id INT PRIMARY KEY AUTO_INCREMENT,
            ip_address VARCHAR(50),
            user_agent TEXT,
            visited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // 3. Buat tabel kegiatan
        $pdo->exec("CREATE TABLE IF NOT EXISTS kegiatan (
            id INT PRIMARY KEY AUTO_INCREMENT,
            judul VARCHAR(255) NOT NULL,
            deskripsi LONGTEXT,
            tanggal DATE,
            lokasi VARCHAR(255),
            gambar VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");

        // 4. Buat tabel galeri
        $pdo->exec("CREATE TABLE IF NOT EXISTS galeri (
            id INT PRIMARY KEY AUTO_INCREMENT,
            judul VARCHAR(255) NOT NULL,
            deskripsi TEXT,
            gambar VARCHAR(255) NOT NULL,
            kegiatan_id INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (kegiatan_id) REFERENCES kegiatan(id) ON DELETE CASCADE
        )");

        // 5. Buat tabel struktur
        $pdo->exec("CREATE TABLE IF NOT EXISTS struktur (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nama VARCHAR(255) NOT NULL,
            jabatan VARCHAR(255) NOT NULL,
            foto VARCHAR(255),
            kontak VARCHAR(20),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");

        // 6. Buat tabel chat/pesan
        $pdo->exec("CREATE TABLE IF NOT EXISTS chat (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nama_pengirim VARCHAR(255) NOT NULL,
            email_pengirim VARCHAR(255) NOT NULL,
            nomor_telepon VARCHAR(20),
            pesan LONGTEXT NOT NULL,
            pengirim VARCHAR(50) DEFAULT 'user',
            is_read TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // 7. Buat tabel berita (jika diperlukan)
        $pdo->exec("CREATE TABLE IF NOT EXISTS berita (
            id INT PRIMARY KEY AUTO_INCREMENT,
            judul VARCHAR(255) NOT NULL,
            konten LONGTEXT NOT NULL,
            gambar VARCHAR(255),
            penulis VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");

        $message = "✅ Semua tabel berhasil dibuat!";
    } catch (PDOException $e) {
        $error = "❌ Error: " . htmlspecialchars($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Database</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: Arial, sans-serif;
        }

        .setup-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
        }

        .setup-container h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .error-message {
            color: #d32f2f;
            padding: 15px;
            margin-bottom: 20px;
            background: #ffebee;
            border-radius: 5px;
            text-align: center;
            border-left: 4px solid #d32f2f;
        }

        .success-message {
            color: #388e3c;
            padding: 15px;
            margin-bottom: 20px;
            background: #e8f5e9;
            border-radius: 5px;
            text-align: center;
            border-left: 4px solid #388e3c;
        }

        .info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #1976d2;
            line-height: 1.6;
        }

        .table-list {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .table-list h3 {
            margin-top: 0;
            color: #333;
        }

        .table-list ul {
            margin: 10px 0;
            padding-left: 20px;
        }

        .table-list li {
            margin: 5px 0;
            color: #666;
        }

        .btn-setup {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-setup:hover {
            background: #5568d3;
        }

        .links {
            text-align: center;
            margin-top: 20px;
        }

        .links a {
            color: #667eea;
            text-decoration: none;
            margin: 0 10px;
            font-size: 14px;
        }

        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="setup-container">
        <h1>Setup Database</h1>

        <div class="info">
            <strong>ℹ️ Informasi:</strong><br>
            Halaman ini digunakan untuk membuat semua tabel database yang diperlukan aplikasi. Klik tombol di bawah
            untuk membuat tabel secara otomatis.
        </div>

        <?php if ($error): ?>
            <div class="error-message"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="success-message"><?= $message ?></div>
        <?php endif; ?>

        <div class="table-list">
            <h3>Tabel yang akan dibuat:</h3>
            <ul>
                <li><strong>admin</strong> - Untuk akun admin</li>
                <li><strong>visitors</strong> - Untuk tracking pengunjung</li>
                <li><strong>kegiatan</strong> - Untuk daftar kegiatan</li>
                <li><strong>galeri</strong> - Untuk koleksi foto/galeri</li>
                <li><strong>struktur</strong> - Untuk data pengurus organisasi</li>
                <li><strong>chat</strong> - Untuk pesan masuk dari pengunjung</li>
                <li><strong>berita</strong> - Untuk artikel/berita</li>
            </ul>
        </div>

        <form method="POST">
            <button type="submit" name="create_tables" value="1" class="btn-setup">
                Buat Semua Tabel
            </button>
        </form>

        <div class="links">
            <a href="setup.php">Buat Admin</a>
            <a href="login.php">Login</a>
        </div>
    </div>
</body>

</html>