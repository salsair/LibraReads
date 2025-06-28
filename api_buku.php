<?php
/**
 * Backend API untuk Manajemen Buku - Versi Final
 *
 * Perbaikan meliputi:
 * - Penanganan error yang lebih baik untuk kegagalan koneksi database.
 * - Penanganan error untuk kegagalan proses `json_encode`.
 * - Pengecekan sesi login yang konsisten.
 * - Praktik terbaik dengan menempatkan session_start() di awal.
 */

// Memulai sesi di paling atas adalah praktik terbaik.
session_start();

// Selalu tampilkan error untuk kemudahan debugging selama masa pengembangan.
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Header default untuk semua respons adalah JSON.
header('Content-Type: application/json');

// Memuat file konfigurasi database.
require_once 'config.php';

// Pengecekan koneksi database di awal, hentikan eksekusi jika gagal.
if ($conn->connect_error) {
    http_response_code(503); // 503 Service Unavailable
    // Kirim pesan error dalam format JSON dan hentikan script.
    echo json_encode(['success' => false, 'message' => 'Koneksi Database Gagal: ' . $conn->connect_error]);
    exit();
}

// Tentukan metode request HTTP.
$method = $_SERVER['REQUEST_METHOD'];

// ==================================================================
// PENANGANAN UNTUK REQUEST GET (MENGAMBIL SEMUA BUKU)
// ==================================================================
if ($method === 'GET') {
    
    // Cek sesi login. Jika tidak ada, tolak akses.
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401); // 401 Unauthorized
        echo json_encode(['success' => false, 'message' => 'Akses ditolak. Sesi login Anda tidak ditemukan.']);
        exit();
    }

    $books_data = [];
    $sql = "SELECT book_id, title, cover_book, author, description, content, genre, publication_year, total_pages, url_book FROM books ORDER BY title ASC";
    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $books_data[] = [
                'id'               => (int)$row['book_id'],
                'title'            => $row['title'] ?? '',
                'image'            => $row['cover_book'] ?? 'images/DefaultBook.jpg',
                'author'           => $row['author'] ?? '',
                'genre'            => $row['genre'] ?? '',
                'description'      => $row['description'] ?? '',
                'content'          => $row['content'] ?? '',
                'publication_year' => $row['publication_year'] === null ? null : (int)$row['publication_year'],
                'total_pages'      => $row['total_pages'] === null ? null : (int)$row['total_pages'],
                'url_book'         => $row['url_book'] ?? '',
                'has_content'      => !empty($row['content'])
            ];
        }
        $result->free();

        // --- PENANGANAN ERROR JSON ENCODE ---
        // Ini adalah perbaikan utama untuk masalah respons kosong.
        $json_output = json_encode(['success' => true, 'books' => $books_data]);

        if ($json_output === false) {
            // Jika encoding gagal, kirim pesan error yang jelas.
            http_response_code(500); // Internal Server Error
            $error_message = 'JSON Encode Error: ' . json_last_error_msg();
            echo json_encode(['success' => false, 'message' => $error_message]);
        } else {
            // Jika berhasil, kirim output seperti biasa.
            echo $json_output;
        }

    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Gagal mengambil data buku: ' . $conn->error]);
    }

// ==================================================================
// PENANGANAN UNTUK REQUEST POST (TAMBAH, EDIT, HAPUS)
// ==================================================================
} elseif ($method === 'POST') {
    
    // Cek sesi login untuk semua aksi POST.
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401); // 401 Unauthorized
        echo json_encode(['success' => false, 'message' => 'Akses ditolak. Sesi login Anda tidak ditemukan.']);
        exit();
    }
    
    if (!isset($_POST['action'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'Aksi tidak ditentukan.']);
        exit();
    }

    $action = $_POST['action'];
    $response = ['success' => false, 'message' => 'Aksi tidak valid.'];

    switch ($action) {
        case 'add_book':
        case 'edit_book':
            // Logika untuk tambah dan edit digabungkan karena sangat mirip.
            $is_edit = ($action === 'edit_book');
            $id = $is_edit ? (isset($_POST['id']) ? intval($_POST['id']) : 0) : 0;
            
            // Validasi untuk mode edit
            if ($is_edit && $id <= 0) {
                http_response_code(400);
                $response['message'] = 'ID buku tidak valid untuk diedit.';
                break;
            }

            $title = trim($_POST['title'] ?? '');
            $author = trim($_POST['author'] ?? '');
            
            // Validasi field wajib
            if (empty($title) || empty($author)) {
                http_response_code(400);
                $response['message'] = 'Judul dan Penulis wajib diisi.';
                break;
            }

            // Ambil semua data dari POST
            $genre = trim($_POST['genre'] ?? '');
            $image = trim($_POST['image'] ?? 'images/DefaultBook.jpg');
            if(empty($image)) $image = 'images/DefaultBook.jpg'; // Pastikan tidak kosong
            $description = trim($_POST['description'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $publication_year = isset($_POST['publication_year']) && $_POST['publication_year'] !== '' ? intval($_POST['publication_year']) : null;
            $total_pages = isset($_POST['total_pages']) && $_POST['total_pages'] !== '' ? intval($_POST['total_pages']) : null;
            $url_book = trim($_POST['url_book'] ?? '');

            // Siapkan SQL berdasarkan aksi
            if ($is_edit) {
                $sql = "UPDATE books SET title=?, author=?, genre=?, cover_book=?, description=?, content=?, publication_year=?, total_pages=?, url_book=? WHERE book_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssissi", $title, $author, $genre, $image, $description, $content, $publication_year, $total_pages, $url_book, $id);
            } else {
                $sql = "INSERT INTO books (title, author, genre, cover_book, description, content, publication_year, total_pages, url_book) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssiss", $title, $author, $genre, $image, $description, $content, $publication_year, $total_pages, $url_book);
            }

            if ($stmt->execute()) {
                $new_id = $is_edit ? $id : $stmt->insert_id;
                $response = [
                    'success' => true, 
                    'message' => 'Buku berhasil ' . ($is_edit ? 'diperbarui' : 'ditambahkan') . '!',
                    'book' => [
                        'id' => $new_id, 'title' => $title, 'author' => $author, 'genre' => $genre, 'image' => $image, 'description' => $description, 'content' => $content, 'publication_year' => $publication_year, 'total_pages' => $total_pages, 'url_book' => $url_book, 'has_content' => !empty($content)
                    ]
                ];
            } else {
                http_response_code(500);
                $response['message'] = 'Gagal menyimpan ke database: ' . $stmt->error;
            }
            $stmt->close();
            break;

        case 'delete_book':
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            if ($id > 0) {
                $sql = "DELETE FROM books WHERE book_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    $response = ['success' => true, 'message' => 'Buku berhasil dihapus!'];
                } else {
                    http_response_code(500);
                    $response['message'] = 'Gagal menghapus dari database: ' . $stmt->error;
                }
                $stmt->close();
            } else {
                http_response_code(400);
                $response['message'] = 'ID buku tidak valid.';
            }
            break;
        
        default:
            http_response_code(400);
            $response['message'] = 'Aksi tidak dikenali.';
            break;
    }
    echo json_encode($response);

} else {
    // Jika metode request bukan GET atau POST.
    http_response_code(405); // 405 Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Metode request tidak diizinkan.']);
}

$conn->close();
exit();
?>