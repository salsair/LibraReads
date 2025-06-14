<?php
session_start();
include 'config.php'; // Sertakan file koneksi database Anda

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Arahkan ke halaman login jika belum
    exit();
}

$user_id = $_SESSION['user_id'];
$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($book_id <= 0) {
    die("Buku tidak ditemukan.");
}

// Ambil detail buku dari database
$stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();
$stmt->close();

if (!$book) {
    die("Buku tidak ditemukan.");
}

// Cek apakah buku sudah ada di mybooks untuk menentukan status tombol "Simpan"
$stmt_check = $conn->prepare("SELECT mybook_id FROM mybooks WHERE user_id = ? AND book_id = ?");
$stmt_check->bind_param("ii", $user_id, $book_id);
$stmt_check->execute();
$is_saved = $stmt_check->get_result()->num_rows > 0;
$stmt_check->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['title']); ?> | LibraReads</title>
    <meta name="book-id" content="<?php echo $book_id; ?>">
    <link rel="stylesheet" href="read-it.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="book-detail-card">
            <div id="savedIndicator" class="saved-indicator">
                <i class="fas fa-check"></i> Tersimpan
            </div>

            <div class="book-header">
                <h1><?php echo htmlspecialchars($book['title']); ?></h1>
                <p>oleh <?php echo htmlspecialchars($book['author']); ?></p>
            </div>

            <div class="book-content">
                <div class="book-cover">
                    <img src="<?php echo htmlspecialchars($book['cover_book']); ?>" alt="Cover <?php echo htmlspecialchars($book['title']); ?>">
                </div>

                <div class="book-info">
                    <div class="book-meta">
                        <div class="meta-item">
                            <span>Genre</span>
                            <p><?php echo htmlspecialchars($book['genre']); ?></p>
                        </div>
                        <div class="meta-item">
                            <span>Tahun Terbit</span>
                            <p><?php echo htmlspecialchars($book['publication_year']); ?></p>
                        </div>
                        <div class="meta-item">
                            <span>Total Halaman</span>
                            <p><?php echo htmlspecialchars($book['total_pages']); ?></p>
                        </div>
                    </div>

                    <div class="book-description">
                        <h3>Deskripsi</h3>
                        <p><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>
                    </div>

                    <div class="action-buttons">
                        <button id="startReadingBtn" data-book-id="<?php echo $book_id; ?>">
                            <i class="fas fa-book-open"></i>
                            <span>Mulai Membaca</span>
                        </button>
                        
                        <?php if (!empty($book['url_book'])): ?>
                            <button type="button" class="btn-outline" onclick="window.open('<?php echo htmlspecialchars($book['url_book']); ?>', '_blank')">
                                <i class="fas fa-external-link-alt"></i> Buka Link Asli
                            </button>
                        <?php endif; ?>

                        <button id="toggleSaveBtn" 
                                data-action="<?php echo $is_saved ? 'unsave' : 'save'; ?>" 
                                class="<?php echo $is_saved ? 'btn-primary' : 'btn-secondary'; ?>">
                            <?php echo $is_saved ? 'Tersimpan' : 'Simpan ke Bookshelf'; ?>
                        </button>
                        </div>
                </div>
            </div>
        </div>
    </div>

    <script src="read-it.js"></script>
</body>
</html>