    <?php
        
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    session_start();
    require_once __DIR__ . '/../database/db.php';
    
    
    require '../PHPMailer/src/Exception.php';
    require '../PHPMailer/src/PHPMailer.php';
    require '../PHPMailer/src/SMTP.php';
    
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    
    
    if (!isset($_SESSION['admin'])) {
        exit("Akses ditolak");
    }
    
    $kode = $_GET['kode'] ?? '';
    $folder = __DIR__ . "/uploads_chat/";
    if (!is_dir($folder))
        mkdir($folder, 0777, true);
    
    if (isset($_GET['kode'])) {
    
        $kode = $_GET['kode'];
    
        // otomatis ubah jadi sudah dibaca
        $update = $pdo->prepare("
            UPDATE chat 
            SET status='sudah'
            WHERE kode_chat=? 
            AND pengirim='user'
        ");
    
        $update->execute([$kode]);
    }
    
    /* ================= LOAD CHAT ================= */
    if (isset($_GET['load'])) {
        $pdo->prepare("UPDATE chat SET dibaca=1 
                       WHERE kode_chat=? AND pengirim='user'")
            ->execute([$kode]);
    
        $stmt = $pdo->prepare("SELECT * FROM chat WHERE kode_chat=? ORDER BY waktu ASC");
        $stmt->execute([$kode]);
        $data = $stmt->fetchAll();
    
        foreach ($data as $p) {
            $align = $p['pengirim'] == 'admin' ? 'right' : 'left';
            $bg = $p['pengirim'] == 'admin' ? '#d4f8d4' : '#f1f1f1';
    
            echo "<div style='margin:5px 0; text-align:$align'>
                    <span style='background:$bg;padding:6px 10px;border-radius:10px;display:inline-block;max-width:70%;word-wrap:break-word;'>";
    
            if ($p['tipe'] == 'file') {
                $file = "uploads_chat/" . $p['pesan'];
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                    echo "<img src='$file' width='150'><br>";
                } else {
                    echo "📎 <a href='$file' target='_blank'>Download File</a><br>";
                }
            } else {
                echo nl2br(htmlspecialchars($p['pesan']));
            }
    
            if ($p['pengirim'] == 'admin') {
                $centang = $p['dibaca'] ? "✔✔" : "✔";
                echo "<br><small>{$p['waktu']} $centang</small>";
            } else {
                echo "<br><small>{$p['waktu']}</small>";
            }
    
            echo "</span></div>";
        }
        exit;
    }
    
    /* ================= STATUS USER ================= */
    if (isset($_GET['status'])) {
        $stmt = $pdo->prepare("SELECT MAX(waktu) as last FROM chat WHERE kode_chat=? AND pengirim='user'");
        $stmt->execute([$kode]);
        $last = strtotime($stmt->fetch()['last'] ?? '2000-01-01');
        echo (time() - $last < 30) ? "online" : "offline";
        exit;
    }
    
    /* ================= KIRIM PESAN ================= */
    
    if (isset($_POST['kirim'])) {
    
        if (!empty($_FILES['file']['name'])) {
    
            $namaFile =
                time() . "_" .
                uniqid() . "_" .
                basename($_FILES['file']['name']);
    
            move_uploaded_file(
                $_FILES['file']['tmp_name'],
                $folder . $namaFile
            );
    
            $stmt = $pdo->prepare("
                INSERT INTO chat
                (
                    kode_chat,
                    pengirim,
                    pesan,
                    tipe,
                    waktu,
                    dibaca
                )
                VALUES
                (
                    ?,
                    'admin',
                    ?,
                    'file',
                    NOW(),
                    0
                )
            ");
    
            $stmt->execute([
                $kode,
                $namaFile
            ]);
    
        } elseif (!empty($_POST['pesan'])) {
    
            $pesanAdmin =
                trim($_POST['pesan']);
    
            /* Simpan pesan */
    
            $stmt = $pdo->prepare("
                INSERT INTO chat
                (
                    kode_chat,
                    pengirim,
                    pesan,
                    tipe,
                    waktu,
                    dibaca
                )
                VALUES
                (
                    ?,
                    'admin',
                    ?,
                    'text',
                    NOW(),
                    0
                )
            ");
    
            $stmt->execute([
                $kode,
                $pesanAdmin
            ]);
    
    
            /* =========================
               AMBIL EMAIL USER
               ========================= */
    
            $getUser = $pdo->prepare("
                SELECT nama, email
                FROM pesan
                WHERE kode_chat = ?
                LIMIT 1
            ");
    
            $getUser->execute([
                $kode
            ]);
    
            $user =
                $getUser->fetch();
    
    
            /* =========================
               KIRIM EMAIL
               ========================= */
    
            if (
                $user &&
                !empty($user['email'])
            ) {
    
                try {
    
                    $mail =
                        new PHPMailer(true);
    
                    $mail->isSMTP();
    
                    $mail->Host =
                        'smtp.gmail.com';
    
                    $mail->SMTPAuth =
                        true;
    
                    $mail->Username =
                        'adminnawasena6@gmail.com';
    
                    $mail->Password =
                        'iods snhp jgde pmmi';
    
                    $mail->SMTPSecure =
                        PHPMailer::ENCRYPTION_STARTTLS;
    
                    $mail->Port =
                        587;
    
    
                    $mail->setFrom(
                        'adminnawasena6@gmail.com',
                        'Karang Taruna Atma Muda Nawasena'
                    );
    
                    $mail->addAddress(
                        $user['email'],
                        $user['nama']
                    );
    
    
                    $mail->isHTML(true);
    
                    $mail->CharSet =
                        'UTF-8';
    
                    $mail->Subject =
                        'Balasan Pesan - Karang Taruna Atma Muda Nawasena';
    
    
                    $pesanEmail =
                        nl2br(
                            htmlspecialchars(
                                $pesanAdmin,
                                ENT_QUOTES,
                                'UTF-8'
                            )
                        );
    
    
       /* =========================
       ISI EMAIL
       ========================= */
    
            $namaUser = htmlspecialchars(
                $user['nama'],
                ENT_QUOTES,
                'UTF-8'
            );
            
            $topikUser = htmlspecialchars(
                $user['topik'] ?? 'Informasi',
                ENT_QUOTES,
                'UTF-8'
            );
            
            $pesanEmail = nl2br(
                htmlspecialchars(
                    $pesanAdmin,
                    ENT_QUOTES,
                    'UTF-8'
                )
            );
            
            
            /* Link menuju website */
            
            $linkWebsite =
                'https://atmamudanawasena.my.id/' .
                $_SERVER['HTTP_HOST'];
            
            
            /* =========================
               SUBJECT
               ========================= */
            
            $mail->Subject =
                'Balasan Admin - ' . ($user['topik'] ?? 'Informasi');
            
            
            /* =========================
               HTML EMAIL
               ========================= */
            
            $mail->Body = "
            
            <!DOCTYPE html>
            
            <html lang='id'>
            
            <head>
            
            <meta charset='UTF-8'>
            
            <meta name='viewport'
                  content='width=device-width, initial-scale=1.0'>
            
            <title>Balasan Admin</title>
            
            </head>
            
            
            <body style='
                margin:0;
                padding:0;
                background:#f4f6f8;
                font-family:Arial, Helvetica, sans-serif;
            '>
            
            
            <table
                width='100%'
                cellpadding='0'
                cellspacing='0'
                border='0'
                style='
                    background:#f4f6f8;
                    padding:30px 10px;
                '
            >
            
            <tr>
            
            <td align='center'>
            
            
            <table
                width='100%'
                cellpadding='0'
                cellspacing='0'
                border='0'
                style='
                    max-width:700px;
                    background:#ffffff;
                    border-radius:8px;
                    overflow:hidden;
                '
            >
            
            
            <!-- =========================
                 HEADER
                 ========================= -->
            
            <tr>
            
            <td style='
                background:#2f4356;
                padding:32px 25px;
                text-align:center;
            '>
            
            
            <div style='
                color:#ffffff;
                font-size:28px;
                font-weight:bold;
                margin-bottom:8px;
            '>
            
            Nawasena
            
            </div>
            
            
            <div style='
                color:#cbd5df;
                font-size:17px;
            '>
            
            Notifikasi Chat
            
            </div>
            
            
            </td>
            
            </tr>
            
            
            <!-- =========================
                 CONTENT
                 ========================= -->
            
            <tr>
            
            <td style='
                padding:40px 32px;
                color:#555555;
                font-size:16px;
                line-height:1.7;
            '>
            
            
            <p style='
                margin:0 0 20px 0;
            '>
            
            Halo
            <strong style='color:#333333;'>
            
            {$namaUser}
            
            </strong>,
            
            </p>
            
            
            <p style='
                margin:0 0 22px 0;
            '>
            
            Admin telah membalas pesan Anda
            mengenai topik:
            
            <strong style='color:#333333;'>
            
            {$topikUser}
            
            </strong>.
            
            </p>
            
            
            <!-- =========================
                 PESAN ADMIN
                 ========================= -->
            
            <table
                width='100%'
                cellpadding='0'
                cellspacing='0'
                border='0'
                style='
                    margin:20px 0;
                '
            >
            
            <tr>
            
            <td style='
                background:#eefaf1;
                border-left:5px solid #20b866;
                padding:18px 20px;
                color:#333333;
                font-size:16px;
                font-style:italic;
            '>
            
            &ldquo;{$pesanEmail}&rdquo;
            
            </td>
            
            </tr>
            
            </table>
            
            
            <p style='
                margin:22px 0;
            '>
            
            Silakan masuk ke website untuk melihat
            balasan lengkap dan melanjutkan percakapan.
            
            </p>
            
            
            <!-- =========================
                 BUTTON
                 ========================= -->
            
            <table
                width='100%'
                cellpadding='0'
                cellspacing='0'
                border='0'
            >
            
            <tr>
            
            <td align='center'
                style='padding:15px 0 25px 0;'
            >
            
            
            <a
                href='{$linkWebsite}'
                style='
                    display:inline-block;
                    background:#20b866;
                    color:#ffffff;
                    text-decoration:none;
                    padding:14px 32px;
                    border-radius:7px;
                    font-size:17px;
                    font-weight:bold;
                '
            >
            
            Lihat Pesan
            
            </a>
            
            
            </td>
            
            </tr>
            
            </table>
            
            
            <p style='
                margin:10px 0 0 0;
                color:#777777;
                font-size:14px;
            '>
            
            Jika tombol tidak dapat digunakan,
            silakan buka website Karang Taruna
            Atma Muda Nawasena secara langsung.
            
            </p>
            
            
            </td>
            
            </tr>
            
            
            <!-- =========================
                 FOOTER
                 ========================= -->
            
            <tr>
            
            <td style='
                background:#f8f9fa;
                padding:20px 30px;
                text-align:center;
                color:#999999;
                font-size:13px;
            '>
            
            
            Karang Taruna Atma Muda Nawasena
            
            
            <br>
            
            
            Email ini dikirim secara otomatis
            oleh sistem website.
            
            
            </td>
            
            </tr>
            
            
            </table>
            
            
            </td>
            
            </tr>
            
            </table>
            
            
            </body>
            
            </html>
            
            ";
    
    
    /* =========================
       VERSI TEXT
       ========================= */
    
            $mail->AltBody =
                "Halo {$user['nama']},\n\n" .
                "Admin telah membalas pesan Anda " .
                "mengenai topik: " .
                ($user['topik'] ?? 'Informasi') .
                ".\n\n" .
                "Balasan Admin:\n" .
                $pesanAdmin .
                "\n\n" .
                "Silakan masuk ke website untuk " .
                "melihat balasan lengkap dan melanjutkan percakapan.";
            
            
                            $mail->send();
            
            
                        } catch (Exception $e) {
            
                            file_put_contents(
                                __DIR__ . '/mail_error.txt',
                                date('Y-m-d H:i:s') .
                                " | " .
                                $mail->ErrorInfo .
                                PHP_EOL,
                                FILE_APPEND
                            );
            
                        }
            
                    }
            
                }
            
                exit;
            
            }
    
    
    /* =========================
       DAFTAR CHAT
       ========================= */
    $stmt = $pdo->query("
    SELECT 
        c.kode_chat,
        MAX(c.waktu) as last_time,
    
        SUBSTRING_INDEX(
            GROUP_CONCAT(c.pesan ORDER BY c.waktu DESC SEPARATOR '|||'),
            '|||',1
        ) as last_chat,
    
        p.nama,
        p.email,
        p.topik,
    
        (
            SELECT COUNT(*)
            FROM chat x
            WHERE x.kode_chat = c.kode_chat
            AND x.pengirim='user'
            AND x.dibaca=0
        ) as unread
    
    FROM chat c
    
    LEFT JOIN pesan p 
    ON p.kode_chat = c.kode_chat
    
    GROUP BY c.kode_chat
    
    ORDER BY last_time DESC
    ");
    
    $chatRooms = $stmt->fetchAll();
    
    ?>
    
    <!DOCTYPE html>
    <html lang="id">
    
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pesan - Admin</title>
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
                    <a href="pesan.php" class="active">Pesan</a>
                    <a href="">Kategori</a>
                    <a href="../index.php" target="_blank">Lihat Website</a>
                    <a href="?logout=1" class="logout">Logout</a>
                </nav>
            </aside>
    
            <main class="main-content">
                <div class="content-header">
                    <h1>Pesan Masuk</h1>
                </div>
    
                <div class="chat-container">

    <!-- ================= SIDEBAR CHAT ================= -->
    <div class="chat-sidebar">

        <div class="chat-sidebar-header">
            <h3>Chat Room</h3>
        </div>

        <div class="chat-list">

            <?php if (empty($chatRooms)) { ?>

                <div class="empty-chat">
                    Tidak ada pesan
                </div>

            <?php } else { ?>

                <?php foreach ($chatRooms as $room) { ?>

                    <a href="?kode=<?= urlencode($room['kode_chat']) ?>"
                        class="chat-item <?= $kode == $room['kode_chat'] ? 'active' : '' ?>">

                        <div class="chat-top">

                            <div class="chat-avatar">
                                <?= strtoupper(substr($room['nama'] ?? 'U', 0, 1)) ?>
                            </div>

                            <div class="chat-info">

                                <div class="chat-name-row">

                                    <h4>
                                        <?= htmlspecialchars($room['nama'] ?? 'User') ?>
                                    </h4>

                                    <span class="chat-time">
                                        <?= date('H:i', strtotime($room['last_time'])) ?>
                                    </span>

                                </div>

                                <div class="chat-email">
                                    <?= htmlspecialchars($room['email'] ?? '-') ?>
                                </div>

                                <div class="chat-subject">
                                    <?= htmlspecialchars($room['topik'] ?? 'Tanpa Topik') ?>
                                </div>

                               <div class="chat-preview">
                                <?= htmlspecialchars(
                                    mb_strimwidth(
                                        $room['last_chat'] ?? '',
                                        0,
                                        35,
                                        '...',
                                        'UTF-8'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>

                            </div>

                        </div>

                        <?php if ($room['unread'] > 0) { ?>

                            <div class="chat-badge">
                                <?= $room['unread'] ?>
                            </div>

                        <?php } ?>

                    </a>

                <?php } ?>

            <?php } ?>

        </div>

    </div>


    <!-- ================= CHAT UTAMA ================= -->

    <?php if ($kode) { ?>

        <div class="chat-main">

            <div class="chat-header">

                <div>

                    <h3>
                        <?= htmlspecialchars($kode) ?>
                    </h3>

                    <small id="statusUser">
                        Status: ...
                    </small>

                </div>

            </div>


            <!-- ISI CHAT -->

            <div id="chatBox" class="chat-messages"></div>


            <!-- FORM CHAT -->

            <div class="chat-footer">

                <form id="formChat" enctype="multipart/form-data">

                    <div class="input-group">

                        <textarea
                            id="pesanBox"
                            name="pesan"
                            placeholder="Tulis pesan..."
                            class="message-input"></textarea>

                        <button
                            type="submit"
                            class="btn-send">
                            Kirim
                        </button>

                    </div>


                    <div class="input-actions">

                        <button
                            type="button"
                            class="btn-emoji"
                            onclick="toggleEmoji()">
                            😊 Emoji
                        </button>

                        <input
                            type="file"
                            name="file"
                            class="btn-file"
                            title="Pilih file">

                    </div>


                    <div
                        id="emojiBox"
                        class="emoji-box">
                    </div>

                </form>

            </div>

        </div>


    <?php } else { ?>


        <!-- ================= BELUM PILIH CHAT ================= -->

        <div class="chat-main chat-empty-state">

            <div>

                <div class="empty-chat-icon">
                    💬
                </div>

                <h3>
                    Pilih Chat
                </h3>

                <p>
                    Pilih salah satu chat di sebelah kiri
                    untuk memulai percakapan.
                </p>

            </div>

        </div>


    <?php } ?>

</div>
            </main>
        </div>
    
    
        <script>
            let kodeChat = "<?= $kode ?>";
    
            function loadChat() {
                fetch("pesan.php?kode=" + kodeChat + "&load=1")
                    .then(res => res.text())
                    .then(data => {
                        let box = document.getElementById("chatBox");
    
                        // cek apakah user lagi di bawah
                        let atBottom = box.scrollTop + box.clientHeight >= box.scrollHeight - 20;
    
                        box.innerHTML = data;
    
                        // scroll halus hanya kalau memang di bawah
                        if (atBottom) {
                            box.scrollTo({
                                top: box.scrollHeight,
                                behavior: "smooth"
                            });
                        }
                    });
            }
    
            function loadStatus() {
                fetch("pesan.php?kode=" + kodeChat + "&status=1")
                    .then(res => res.text())
                    .then(s => {
                        document.getElementById("statusUser").innerHTML =
                            s == "online" ? "🟢 Online" : "⚪ Offline";
                    });
            }
    
            function initEmoji() {
                let emojiBox = document.getElementById("emojiBox");
                if (emojiBox && emojiBox.innerHTML === "") {
                    let emojis = ['😀', '😁', '😂', '🤣', '😍', '😎', '😢', '😡', '👍', '👎', '❤️', '🔥', '🎉', '🤝', '🙏', '💯', '😴', '🤯', '😇', '🤖', '🍕', '☕', '🚀'];
                    emojis.forEach(emoji => {
                        let span = document.createElement('span');
                        span.innerText = emoji;
                        span.onclick = () => {
                            let textarea = document.getElementById("pesanBox");
                            textarea.value += emoji;
                            textarea.focus();
                        };
                        emojiBox.appendChild(span);
                    });
                }
            }
    
            if (kodeChat) {
                setInterval(loadChat, 2000);
                setInterval(loadStatus, 5000);
                loadChat();
                loadStatus();
            }
    
            let formChat = document.getElementById("formChat");
            if (formChat) {
                formChat.addEventListener("submit", function(e) {
                    e.preventDefault();
                    let formData = new FormData(this);
                    formData.append("kirim", 1);
    
                    fetch("pesan.php?kode=" + encodeURIComponent(kodeChat), {
                            method: "POST",
                            body: formData
                        })
                        .then(() => {
                            this.reset();
                            loadChat();
                        });
                });
            }
    
            function toggleEmoji() {
                let box = document.getElementById("emojiBox");
                if (box) {
                    initEmoji();
                    box.style.display = box.style.display === "none" ? "flex" : "none";
                }
            }
        </script>
    
        <?php if (isset($_GET['logout'])) {
            session_destroy();
            header("Location: login.php");
        } ?>
    </body>
    
    </html>