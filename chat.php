<?php

require_once '../database/db.php';

/* =========================================================
   KODE CHAT
   ========================================================= */

$kode = $_GET['id'] ?? $_GET['kode'] ?? '';

if (empty($kode)) {
    die("Kode chat tidak ditemukan.");
}


/* =========================================================
   FOLDER UPLOAD
   ========================================================= */

$folder = __DIR__ . "/admin/uploads_chat/";

if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}


/* =========================================================
   KIRIM PESAN USER
   ========================================================= */

if (isset($_POST['kirim'])) {

    $pesan = trim($_POST['pesan'] ?? '');

    /* =========================
       KIRIM FILE
       ========================= */

    if (
        isset($_FILES['file']) &&
        !empty($_FILES['file']['name']) &&
        $_FILES['file']['error'] === UPLOAD_ERR_OK
    ) {

        $allowed = [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'webp',
            'pdf',
            'doc',
            'docx'
        ];

        $ext = strtolower(
            pathinfo(
                $_FILES['file']['name'],
                PATHINFO_EXTENSION
            )
        );

        if (!in_array($ext, $allowed, true)) {
            die("Format file tidak didukung.");
        }

        $namaFile =
            time() .
            "_" .
            uniqid() .
            "." .
            $ext;

        $tujuan =
            $folder .
            $namaFile;

        if (
            move_uploaded_file(
                $_FILES['file']['tmp_name'],
                $tujuan
            )
        ) {

            $stmt = $pdo->prepare("
                INSERT INTO chat
                (
                    kode_chat,
                    pengirim,
                    pesan,
                    waktu,
                    dibaca,
                    tipe,
                    status
                )
                VALUES
                (
                    ?,
                    'user',
                    ?,
                    NOW(),
                    0,
                    'file',
                    'belum'
                )
            ");

            $stmt->execute([
                $kode,
                $namaFile
            ]);
        }

    }


    /* =========================
       KIRIM TEXT
       ========================= */

    elseif ($pesan !== '') {

        $stmt = $pdo->prepare("
            INSERT INTO chat
            (
                kode_chat,
                pengirim,
                pesan,
                waktu,
                dibaca,
                tipe,
                status
            )
            VALUES
            (
                ?,
                'user',
                ?,
                NOW(),
                0,
                'text',
                'belum'
            )
        ");

        $stmt->execute([
            $kode,
            $pesan
        ]);
    }

    exit;
}


/* =========================================================
   AMBIL CHAT
   ========================================================= */

$stmt = $pdo->prepare("
    SELECT *
    FROM chat
    WHERE kode_chat = ?
    ORDER BY waktu ASC
");

$stmt->execute([
    $kode
]);

$dataChat = $stmt->fetchAll();


/* =========================================================
   TANDAI PESAN ADMIN SUDAH DIBACA
   ========================================================= */

$pdo->prepare("
    UPDATE chat
    SET dibaca = 1
    WHERE kode_chat = ?
    AND pengirim = 'admin'
")
->execute([
    $kode
]);

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
        Chat - Nawasena
    </title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background:
                #f3f6f9;

            height: 100vh;

        }


        /* =================================================
           CONTAINER
           ================================================= */

        .chat-wrapper {

            width: 100%;

            max-width: 650px;

            height: 100vh;

            margin: auto;

            background: white;

            display: flex;

            flex-direction: column;

            box-shadow:
                0 0 20px
                rgba(0,0,0,.08);

        }


        /* =================================================
           HEADER
           ================================================= */

        .chat-header {

            background:
                #2f4356;

            color: white;

            padding:
                20px;

            display: flex;

            align-items: center;

            gap: 14px;

        }


        .chat-avatar {

            width: 48px;

            height: 48px;

            border-radius: 50%;

            background:
                #ffffff;

            color:
                #2f4356;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 20px;

            font-weight: bold;

        }


        .chat-header-info {

            flex: 1;

        }


        .chat-header-info h2 {

            margin: 0 0 4px;

            font-size: 18px;

        }


        .chat-header-info p {

            margin: 0;

            font-size: 13px;

            opacity: .85;

        }


        .online-status {

            font-size: 12px;

            margin-top: 4px;

        }


        /* =================================================
           CHAT AREA
           ================================================= */

        .chat-box {

            flex: 1;

            overflow-y: auto;

            padding:
                20px;

            background:
                #f7f9fb;

        }


        /* =================================================
           BUBBLE
           ================================================= */

        .message {

            display: flex;

            margin-bottom: 14px;

        }


        .message.admin {

            justify-content:
                flex-start;

        }


        .message.user {

            justify-content:
                flex-end;

        }


        .bubble {

            max-width: 75%;

            padding:
                10px 14px;

            border-radius:
                14px;

            font-size: 14px;

            line-height: 1.5;

            word-wrap: break-word;

        }


        .message.admin .bubble {

            background:
                white;

            color:
                #333;

            border:
                1px solid #e5e5e5;

            border-bottom-left-radius:
                4px;

        }


        .message.user .bubble {

            background:
                #2f4356;

            color:
                white;

            border-bottom-right-radius:
                4px;

        }


        .message-time {

            display: block;

            font-size: 10px;

            margin-top: 5px;

            opacity: .6;

            text-align: right;

        }


        /* =================================================
           FILE
           ================================================= */

        .chat-image {

            max-width: 220px;

            max-height: 220px;

            border-radius: 10px;

            display: block;

            margin-bottom: 5px;

        }


        .file-link {

            color: inherit;

            text-decoration: none;

            font-weight: bold;

        }


        /* =================================================
           FOOTER
           ================================================= */

        .chat-footer {

            background:
                white;

            border-top:
                1px solid #e5e5e5;

            padding:
                12px;

        }


        .chat-form {

            display: flex;

            align-items: center;

            gap: 8px;

        }


        .message-input {

            flex: 1;

            border:
                1px solid #ddd;

            border-radius:
                20px;

            padding:
                11px 15px;

            outline: none;

            font-size: 14px;

        }


        .message-input:focus {

            border-color:
                #2f4356;

        }


        .file-button {

            width: 40px;

            height: 40px;

            border: none;

            border-radius: 50%;

            background:
                #eef2f5;

            cursor: pointer;

            font-size: 18px;

            display: flex;

            align-items: center;

            justify-content: center;

        }


        .send-button {

            width: 42px;

            height: 42px;

            border: none;

            border-radius: 50%;

            background:
                #2f4356;

            color: white;

            cursor: pointer;

            font-size: 17px;

        }


        .send-button:hover {

            opacity: .9;

        }


        .file-name {

            font-size: 11px;

            color: #777;

            margin:
                5px 10px 0;

        }


        /* =================================================
           MOBILE
           ================================================= */

        @media (max-width: 600px) {

            .chat-wrapper {

                max-width: 100%;

            }


            .chat-box {

                padding:
                    14px;

            }


            .bubble {

                max-width:
                    85%;

            }

        }

    </style>

</head>


<body>


<div class="chat-wrapper">


    <!-- =================================================
         HEADER
         ================================================= -->

    <div class="chat-header">

        <div class="chat-avatar">
            N
        </div>


        <div class="chat-header-info">

            <h2>
                Nawasena
            </h2>

            <p>
                Notifikasi Chat
            </p>

            <div class="online-status" id="statusAdmin">
                🟢 Admin
            </div>
        </div>

    </div>


    <!-- =================================================
         CHAT
         ================================================= -->

    <div
        class="chat-box"
        id="chatBox"
    >

        <?php foreach ($dataChat as $c): ?>


            <div
                class="message <?= $c['pengirim'] === 'admin'
                    ? 'admin'
                    : 'user'
                ?>"
            >

                <div class="bubble">


                    <?php if ($c['tipe'] === 'file'): ?>

                        <?php

                        $fileUrl =
                            "admin/uploads_chat/" .
                            htmlspecialchars(
                                $c['pesan'],
                                ENT_QUOTES,
                                'UTF-8'
                            );

                        $ext =
                            strtolower(
                                pathinfo(
                                    $c['pesan'],
                                    PATHINFO_EXTENSION
                                )
                            );

                        ?>


                        <?php if (
                            in_array(
                                $ext,
                                [
                                    'jpg',
                                    'jpeg',
                                    'png',
                                    'gif',
                                    'webp'
                                ],
                                true
                            )
                        ): ?>

                            <img
                                src="<?= $fileUrl ?>"
                                class="chat-image"
                                alt="File"
                            >
                        <?php else: ?>
                         📎
                            <a
                                href="<?= $fileUrl ?>"
                                target="_blank"
                                class="file-link"
                            >
                                Lihat File
                            </a>

                        <?php endif; ?>


                    <?php else: ?>

                        <?= nl2br(
                            htmlspecialchars(
                                $c['pesan'],
                                ENT_QUOTES,
                                'UTF-8'
                            )
                        ) ?>

                    <?php endif; ?>


                    <span class="message-time">

                        <?= date(
                            'H:i',
                            strtotime($c['waktu'])
                        ) ?>


                        <?php if (
                            $c['pengirim'] === 'user'
                        ): ?>
                            ✓
                        <?php endif; ?>

                    </span>


                </div>

            </div>


        <?php endforeach; ?>

    </div>


    <!-- =================================================
         FOOTER
         ================================================= -->

    <div class="chat-footer">


        <form
            method="POST"
            enctype="multipart/form-data"
            class="chat-form"
            id="chatForm"
        >


            <label
                class="file-button"
                title="Kirim file"
            >
                📎
                <input
                    type="file"
                    name="file"
                    id="fileInput"
                    hidden
                >

            </label>


            <input
                type="text"
                name="pesan"
                class="message-input"
                id="pesanInput"
                placeholder="Tulis pesan..."
                autocomplete="off"
            >


            <button
                type="submit"
                name="kirim"
                class="send-button"
            >
                ➤
            </button>


        </form>


        <div
            class="file-name"
            id="fileName"
        ></div>


    </div>


</div>


<script>

/* =========================================================
   FILE NAME
   ========================================================= */

const fileInput =
    document.getElementById(
        "fileInput"
    );

const fileName =
    document.getElementById(
        "fileName"
    );


if (fileInput) {

    fileInput.addEventListener(
        "change",
        function () {

            if (
                this.files &&
                this.files.length > 0
            ) {

                fileName.innerText =
                    this.files[0].name;

            } else {

                fileName.innerText = "";

            }

        }
    );

}


/* =========================================================
   ENTER UNTUK KIRIM
   ========================================================= */

const pesanInput =
    document.getElementById(
        "pesanInput"
    );


if (pesanInput) {

    pesanInput.addEventListener(
        "keydown",
        function (e) {

            if (
                e.key === "Enter" &&
                !e.shiftKey
            ) {

                e.preventDefault();

                document
                    .getElementById(
                        "chatForm"
                    )
                    .submit();

            }

        }
    );

}


/* =========================================================
   SCROLL KE PESAN TERAKHIR
   ========================================================= */

const chatBox =
    document.getElementById(
        "chatBox"
    );


if (chatBox) {

    chatBox.scrollTop =
        chatBox.scrollHeight;

}

</script>


</body>

</html>