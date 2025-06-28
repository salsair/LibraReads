<?php
// start_reading.php
session_start();
header('Content-Type: application/json');
include 'config.php'; // Sertakan file koneksi database Anda

// Fungsi untuk mengirim response error dalam format JSON
function send_error($message) {
    echo json_encode(['status' => 'error', 'message' => $message]);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    send_error('Anda harus login untuk memulai membaca.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['book_id']) || !is_numeric($_POST['book_id'])) {
    send_error('Permintaan tidak valid.');
}

$user_id = $_SESSION['user_id'];
$book_id = intval($_POST['book_id']);

/*
 * Gunakan query INSERT ... ON DUPLICATE KEY UPDATE.
 * Query ini sangat efisien:
 * 1. JIKA kombinasi user_id dan book_id BELUM ADA:
 * - Ia akan MEMASUKKAN baris baru dengan status 'reading' dan mencatat waktu `added_at` & `last_read_at`.
 * 2. JIKA kombinasi user_id dan book_id SUDAH ADA (misal, statusnya 'saved'):
 * - Ia akan MEMPERBARUI baris yang ada, mengubah status menjadi 'reading' dan memperbarui `last_read_at` ke waktu sekarang.
 */
$sql = "INSERT INTO mybooks (user_id, book_id, status, last_read_at, added_at) 
        VALUES (?, ?, 'reading', NOW(), NOW()) 
        ON DUPLICATE KEY UPDATE status = 'reading', last_read_at = NOW()";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    send_error('Gagal mempersiapkan statement: ' . $conn->error);
}

$stmt->bind_param("ii", $user_id, $book_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Buku berhasil ditambahkan ke sesi membaca.']);
} else {
    send_error('Gagal menyimpan data ke database: ' . $stmt->error);
}

$stmt->close();
$conn->close();
?>