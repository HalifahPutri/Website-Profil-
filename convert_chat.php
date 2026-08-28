<?php
session_start();
require_once __DIR__ . '/../database/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    exit("Akses ditolak");
}

$id = $_GET['id'] ?? 0;

// Ambil data dari tabel pesan
$stmt = $pdo->prepare("SELECT * FROM pesan WHERE id=?");
$stmt->execute([$id]);
$pesan = $stmt->fetch();

if (!$pesan) {
    exit("Pesan tidak ditemukan");
}

// Gunakan email sebagai kode_chat (atau buat kode unik)
$kode_chat = md5($pesan['email']);

// Cek apakah chat sudah ada
$cek = $pdo->prepare("SELECT COUNT(*) FROM chat WHERE kode_chat=?");
$cek->execute([$kode_chat]);
$sudahAda = $cek->fetchColumn();

if (!$sudahAda) {
    // Masukkan pesan pertama ke tabel chat
    $stmt = $pdo->prepare("INSERT INTO chat 
        (kode_chat,pengirim,pesan,tipe,waktu,dibaca)
        VALUES (?, 'user', ?, 'text', NOW(), 0)");
    $stmt->execute([$kode_chat, $pesan['isi']]);
}

// Tandai pesan sudah dibaca
$pdo->prepare("UPDATE pesan SET dibaca=1 WHERE id=?")
    ->execute([$id]);

// Redirect ke halaman chat admin
header("Location: pesan.php?kode=" . $kode_chat);
exit;
