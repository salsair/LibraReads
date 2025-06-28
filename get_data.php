<?php
// get_data.php

header('Content-Type: application/json');
require_once 'config.php';

$response = [
    'popular_books' => [],
    'popular_categories' => []
];

// --- Algoritma untuk "Most Popular" ---
// Untuk saat ini, kita akan mengambil 8 buku secara acak sebagai "Most Popular".
// Anda dapat mengganti query ini dengan algoritma yang lebih kompleks jika diperlukan,
// misalnya berdasarkan jumlah peminjaman atau rating.
$popular_books_query = "SELECT book_id, title, cover_book FROM books ORDER BY RAND() LIMIT 8";
$popular_books_result = $conn->query($popular_books_query);

if ($popular_books_result && $popular_books_result->num_rows > 0) {
    while ($row = $popular_books_result->fetch_assoc()) {
        $response['popular_books'][] = $row;
    }
}

// --- Fitur untuk "Popular Categories" ---
// Mengambil genre unik dari tabel buku.
$categories_query = "SELECT DISTINCT genre FROM books WHERE genre IS NOT NULL AND genre != '' ORDER BY genre LIMIT 7";
$categories_result = $conn->query($categories_query);

if ($categories_result && $categories_result->num_rows > 0) {
    while ($row = $categories_result->fetch_assoc()) {
        $response['popular_categories'][] = $row['genre'];
    }
}

$conn->close();

echo json_encode($response);
?>