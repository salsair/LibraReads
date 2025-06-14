<?php
// toggle_bookshelf.php

session_start();
require_once 'config.php';
header('Content-Type: application/json');

// 1. Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Anda harus login untuk melakukan aksi ini.']);
    exit();
}

// 2. Validasi input dari frontend
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['book_id']) || !isset($_POST['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'Permintaan tidak valid.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$book_id = filter_var($_POST['book_id'], FILTER_VALIDATE_INT);
$action = $_POST['action']; // Aksi yang diminta: 'save' atau 'unsave'

if ($book_id === false) {
    echo json_encode(['status' => 'error', 'message' => 'ID buku tidak valid.']);
    exit();
}

// 3. Logika untuk melakukan SIMPAN (save)
if ($action === 'save') {
    $sql = "INSERT IGNORE INTO mybooks (user_id, book_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $book_id);

    if ($stmt->execute()) {
        // Kirim 'newState' untuk memberitahu JS cara update tombol
        echo json_encode(['status' => 'success', 'newState' => 'saved', 'message' => 'Buku berhasil disimpan.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan buku.']);
    }
    $stmt->close();

// 4. Logika untuk melakukan HAPUS/BATAL SIMPAN (unsave)
} elseif ($action === 'unsave') {
    $sql = "DELETE FROM mybooks WHERE user_id = ? AND book_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $book_id);

    if ($stmt->execute()) {
        // Kirim 'newState' untuk memberitahu JS cara update tombol
        echo json_encode(['status' => 'success', 'newState' => 'unsaved', 'message' => 'Buku dihapus dari bookshelf.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus buku.']);
    }
    $stmt->close();

} else {
    echo json_encode(['status' => 'error', 'message' => 'Aksi tidak dikenal.']);
}

$conn->close();
?>