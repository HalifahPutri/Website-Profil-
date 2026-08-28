<?php
session_start();
require_once "database/db.php";

// Ambil kode_chat dari URL atau session
$kode = $_GET['kode'] ?? $_SESSION['kode_chat'] ?? '';

if (!$kode) {
    die("
    <div style='font-family:Arial;text-align:center;padding:60px;'>
        <h2>Link tidak valid</h2>
        <p>Silakan kembali ke halaman kontak untuk mengirim pesan.</p>
        <a href='kontak.php' style='color:#003b73;'>Ke Halaman Kontak</a>
    </div>
    ");
}

// Validasi kode_chat ada di database
$cek = $pdo->prepare("SELECT * FROM pesan WHERE kode_chat=? LIMIT 1");
$cek->execute([$kode]);
$dataPesan = $cek->fetch();

if (!$dataPesan) {
    die("
    <div style='font-family:Arial;text-align:center;padding:60px;'>
        <h2>Percakapan tidak ditemukan</h2>
        <a href='kontak.php' style='color:#003b73;'>Ke Halaman Kontak</a>
    </div>
    ");
}

// Tandai pesan admin sudah dibaca oleh user
$pdo->prepare("UPDATE chat SET dibaca=1 WHERE kode_chat=? AND pengirim='admin'")
    ->execute([$kode]);

// Kirim pesan balasan dari user
if (isset($_POST['kirim']) && !empty($_POST['pesan'])) {
    $pesanUser = $_POST['pesan'];
    $stmt = $pdo->prepare("
        INSERT INTO chat (kode_chat, pengirim, pesan, tipe, waktu, dibaca)
        VALUES (?, 'user', ?, 'text', NOW(), 0)
    ");
    $stmt->execute([$kode, $pesanUser]);
    header("Location: chat-user.php?kode=" . urlencode($kode));
    exit;
}

// Ambil semua chat
$stmt = $pdo->prepare("SELECT * FROM chat WHERE kode_chat=? ORDER BY waktu ASC");
$stmt->execute([$kode]);
$dataChat = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - Karang Taruna Nawasena</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="responsive.css">
    <style>
        .chat-page {
            max-width: 700px;
            margin: 40px auto;
            padding: 0 16px 40px;
            font-family: 'Inter', sans-serif;
        }

        .chat-header-card {
            background: #2c3e50;
            color: #fff;
            border-radius: 12px 12px 0 0;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chat-header-card .avatar {
            width: 44px;
            height: 44px;
            background: #003b73;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
        }

        .chat-header-card h3 {
            margin: 0;
            font-size: 16px;
        }

        .chat-header-card p {
            margin: 2px 0 0;
            font-size: 12px;
            color: #bdc3c7;
        }

        .chat-body {
            background: #f5f5f5;
            border: 1px solid #ddd;
            border-top: none;
            min-height: 380px;
            max-height: 460px;
            overflow-y: auto;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .bubble-wrap {
            display: flex;
            flex-direction: column;
        }

        .bubble-wrap.user {
            align-items: flex-end !important;
        }

        .bubble-wrap.admin {
            align-items: flex-start !important;
        }

        .bubble {
            max-width: 75%;
            padding: 10px 14px;
            border-radius: 14px;
            font-size: 14px;
            line-height: 1.5;
            word-wrap: break-word;
        }

        .bubble.user {
            background: #003b73;
            color: #fff;
            border-bottom-right-radius: 4px;
        }

        .bubble.admin {
            background: #fff;
            color: #333;
            border-bottom-left-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .bubble-time {
            font-size: 11px;
            color: #999;
            margin-top: 3px;
            padding: 0 4px;
        }

        .sender-label {
            font-size: 11px;
            color: #888;
            margin-bottom: 3px;
            padding: 0 4px;
        }

        .chat-footer-form {
            background: #fff;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 12px 12px;
            padding: 14px 16px;
        }

        .chat-footer-form textarea {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            resize: none;
            height: 80px;
            box-sizing: border-box;
            outline: none;
            transition: border 0.2s;
        }

        .chat-footer-form textarea:focus {
            border-color: #003b73;
        }

        .chat-footer-form button {
            margin-top: 10px;
            background: #003b73;
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            float: right;
        }

        .chat-footer-form button:hover {
            background: #002d5a;
        }

        .chat-info-bar {
            background: #eafaf1;
            border: 1px solid #a9dfbf;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 16px;
            font-size: 13px;
            color: #1e8449;
        }

        .empty-chat {
            text-align: center;
            color: #aaa;
            padding: 60px 20px;
            font-size: 14px;
        }

        @media (max-width: 600px) {
            .chat-page { margin: 16px auto; }
            .bubble { max-width: 88%; }
        }
    </style>
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
        <div class="menu-toggle" id="menu-toggle">
            <span></span><span></span><span></span>
        </div>
        <nav id="menu">
            <a href="index.php">Beranda</a>
            <a href="struktur.php">Struktur</a>
            <a href="kegiatan.php">Kegiatan</a>
            <a class="active" href="kontak.php">Kontak</a>
            <a href="admin/login.php" class="login-btn">LOGIN</a>
        </nav>
    </div>
</header>

<div class="chat-page">

    <div class="chat-info-bar">
        💬 Percakapan atas nama <strong><?= htmlspecialchars($dataPesan['nama']) ?></strong>
        &nbsp;·&nbsp; Topik: <strong><?= htmlspecialchars($dataPesan['topik'] ?? '-') ?></strong>
    </div>

    <!-- Header chat -->
    <div class="chat-header-card">
        <div class="avatar">A</div>
        <div>
            <h3>Admin Nawasena</h3>
            <p>Karang Taruna Atma Muda Nawasena</p>
        </div>
    </div>

    <!-- Isi chat -->
    <div class="chat-body" id="chatBox">
        <?php if (empty($dataChat)) { ?>
            <div class="empty-chat">Belum ada pesan</div>
        <?php } else { ?>
            <?php foreach ($dataChat as $c) {
                $pengirim = $c['pengirim'];
                $label = $pengirim == 'admin' ? 'Admin' : htmlspecialchars($dataPesan['nama']);
            ?>
                <?php
                    if ($pengirim == 'user') {
                        $bgColor  = '#003b73';
                        $txtColor = '#ffffff';
                        $tdAlign  = 'right';
                        $radius   = '14px 14px 4px 14px';
                    } else {
                        $bgColor  = '#ffffff';
                        $txtColor = '#333333';
                        $tdAlign  = 'left';
                        $radius   = '14px 14px 14px 4px';
                    }
                ?>
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:4px 0;">
                    <tr>
                        <td align="<?= $tdAlign ?>" style="padding:0;">
                            <div style="font-size:11px;color:#888;margin-bottom:3px;text-align:<?= $tdAlign ?>;">
                                <?= $label ?>
                            </div>
                            <div style="
                                display:inline-block;
                                max-width:70%;
                                background:<?= $bgColor ?>;
                                color:<?= $txtColor ?>;
                                padding:10px 14px;
                                border-radius:<?= $radius ?>;
                                font-size:14px;
                                line-height:1.5;
                                word-wrap:break-word;
                                box-shadow:0 1px 3px rgba(0,0,0,0.1);
                                text-align:left;
                            ">
                                <?= nl2br(htmlspecialchars($c['pesan'])) ?>
                            </div>
                            <div style="font-size:11px;color:#999;margin-top:3px;text-align:<?= $tdAlign ?>;">
                                <?= date('d M Y, H:i', strtotime($c['waktu'])) ?>
                            </div>
                        </td>
                    </tr>
                </table>
            <?php } ?>
        <?php } ?>
    </div>

    <!-- Form balas -->
    <div class="chat-footer-form">
        <form method="POST">
            <textarea name="pesan" placeholder="Tulis pesan balasan..." required></textarea>
            <button type="submit" name="kirim">Kirim Pesan</button>
            <div style="clear:both;"></div>
        </form>
    </div>

</div>

<!-- FOOTER -->
<footer>
    <div class="footer-simple">
        <img src="logokt.png" alt="Logo Karang Taruna">
        <h2>Karang Taruna Atma Muda Nawasena</h2>
        <p class="alamat">Dukuh Rejosari</p>
        <div class="footer-social">
            <a href="https://www.instagram.com/kt.atmamudanawasena" target="_blank">Instagram</a>
            <span>•</span>
            <a href="mailto:kt.atmamudanawasena@gmail.com">Email</a>
        </div>
        <div class="footer-menu">
            <a href="index.php">Beranda</a>
            <a href="struktur.php">Struktur</a>
            <a href="kegiatan.php">Kegiatan</a>
            <a href="kontak.php">Kontak</a>
        </div>
        <div class="copyright">© 2026 Karang Taruna Atma Muda Nawasena | Dukuh Rejosari</div>
    </div>
</footer>

<script>
    // Auto scroll ke bawah
    const box = document.getElementById('chatBox');
    if (box) box.scrollTop = box.scrollHeight;

    // Hamburger menu
    const toggle = document.getElementById('menu-toggle');
    const menu = document.getElementById('menu');
    toggle.addEventListener('click', () => {
        menu.classList.toggle('active');
        toggle.classList.toggle('active');
    });
</script>

</body>
</html>