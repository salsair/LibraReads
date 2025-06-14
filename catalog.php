<?php
session_start();

// --- PENJAGA LOGIN ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// --- AKHIR PENJAGA LOGIN ---

require_once 'config.php';

// Fungsi untuk mencari buku berdasarkan judul, penulis, atau deskripsi
function searchBooks($conn, $query) {
    $searchTerm = "%" . $query . "%";
    $sql = "SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR genre LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    $stmt->close();
    return $books;
}

// Fungsi untuk mengambil buku berdasarkan genre
function getBooksByGenre($conn, $genre) {
    $sql = "SELECT * FROM books WHERE genre = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $genre);
    $stmt->execute();
    $result = $stmt->get_result();
    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    $stmt->close();
    return $books;
}

// Fungsi untuk mengambil buku terbaru
function getNewestBooks($conn, $limit = 9) {
    $sql = "SELECT * FROM books ORDER BY publication_year DESC LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    $stmt->close();
    return $books;
}

// Fungsi untuk mengambil buku yang paling sering dipinjam (contoh)
function getMostBorrowedBooks($conn, $limit = 9) {
    $sql = "SELECT * FROM books ORDER BY RAND() LIMIT ?"; // Menggunakan RAND() untuk contoh
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    $stmt->close();
    return $books;
}

// --- LOGIKA UTAMA ---
$is_search = false;
$search_query = '';

// Cek apakah ada parameter 'search' di URL dan tidak kosong
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $is_search = true;
    $search_query = trim($_GET['search']);
    $searchResults = searchBooks($conn, $search_query);
} else {
    // Jika tidak ada pencarian, ambil data untuk tampilan default
    $programmingBooks = getBooksByGenre($conn, 'Programming');
    $mostBorrowed = getMostBorrowedBooks($conn);
    $newArrivals = getNewestBooks($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books Catalog | LibraReads</title>
    <link rel="stylesheet" href="catalogs.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <div class="dashboard">
        <aside class="sidebar">
            <div class="logo">
                <a href="homepage.php"><img src="images/LogoLibraReads.png" alt="LibraReads Logo"></a>
            </div>
            <ul class="nav-links">
                <li><a href="homepage.php"><i class='bx bxs-home'></i> Home</a></li>
                <li><a href="catalog.php" class="active"><i class='bx bxs-book'></i> Catalog</a></li>
                <li><a href="mybooks.php"><i class='bx bxs-bookmark'></i> Bookshelf</a></li>
                <li><a href="profile.php"><i class='bx bxs-user'></i> Profile</a></li>
            </ul>
            <div class="menu-toggle">
                <i class='bx bx-menu'></i>
            </div>
        </aside>

        <div class="main">
            <header class="top-nav">
                <form action="catalog.php" method="get" id="search-form">
                    <input type="text" name="search" id="search-bar" class="search-bar" placeholder="Cari judul, penulis, atau genre..."
                           value="<?php echo htmlspecialchars($search_query); ?>">
                    <button type="submit" style="display: none;"></button>
                </form>
            </header>

            <main class="content">
                <?php if ($is_search): ?>
                    <section class="book-section">
                        <h2>Hasil Pencarian untuk "<?php echo htmlspecialchars($search_query); ?>"</h2>
                        <div class="book-list">
                            <?php if (!empty($searchResults)): ?>
                                <?php foreach ($searchResults as $book): ?>
                                    <a href="read-it.php?id=<?php echo htmlspecialchars($book['book_id']); ?>" class="book">
                                        <img src="<?php echo htmlspecialchars($book['cover_book']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                                        <p><?php echo htmlspecialchars($book['title']); ?></p>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>Tidak ada buku yang cocok dengan pencarian Anda.</p>
                            <?php endif; ?>
                        </div>
                    </section>
                <?php else: ?>
                    <section class="book-section">
                        <h2>Hot Picks for Coders</h2>
                        <div class="book-list">
                            <?php foreach ($programmingBooks as $book): ?>
                                <a href="read-it.php?id=<?php echo htmlspecialchars($book['book_id']); ?>" class="book">
                                    <img src="<?php echo htmlspecialchars($book['cover_book']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                                    <p><?php echo htmlspecialchars($book['title']); ?></p>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <section class="book-section">
                        <h2>Most Borrowed This Month</h2>
                        <div class="book-list">
                            <?php foreach ($mostBorrowed as $book): ?>
                                <a href="read-it.php?id=<?php echo htmlspecialchars($book['book_id']); ?>" class="book">
                                    <img src="<?php echo htmlspecialchars($book['cover_book']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                                    <p><?php echo htmlspecialchars($book['title']); ?></p>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <section class="book-section">
                        <h2>New Arrivals</h2>
                        <div class="book-list">
                            <?php foreach ($newArrivals as $book): ?>
                                <a href="read-it.php?id=<?php echo htmlspecialchars($book['book_id']); ?>" class="book">
                                    <img src="<?php echo htmlspecialchars($book['cover_book']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                                    <p><?php echo htmlspecialchars($book['title']); ?></p>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="catalog.js"></script>
</body>
</html>
<?php
// Tutup koneksi database
$conn->close();
?>