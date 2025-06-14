<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = $conn->real_escape_string($_GET['search']);
}

// Inisialisasi array untuk menyimpan hasil
$recently_read_books = [];
$bookshelf_books = [];

// === QUERY UNTUK BUKU YANG SEDANG DIBACA (RECENTLY READ) ===
$sql_reading = "SELECT b.book_id, b.title, b.cover_book, b.author 
                FROM mybooks mb
                JOIN books b ON mb.book_id = b.book_id
                WHERE mb.user_id = ? AND mb.status = 'reading'";
if (!empty($search_query)) {
    $sql_reading .= " AND (b.title LIKE ? OR b.author LIKE ?)";
}
$sql_reading .= " ORDER BY mb.last_read_at DESC";

$stmt_reading = $conn->prepare($sql_reading);
if (!empty($search_query)) {
    $search_param = "%" . $search_query . "%";
    $stmt_reading->bind_param("iss", $user_id, $search_param, $search_param);
} else {
    $stmt_reading->bind_param("i", $user_id);
}
$stmt_reading->execute();
$result_reading = $stmt_reading->get_result();
while ($row = $result_reading->fetch_assoc()) {
    $recently_read_books[] = $row;
}
$stmt_reading->close();

// === QUERY UNTUK BUKU DI BOOKSHELF (YANG DISIMPAN) ===
$sql_saved = "SELECT b.book_id, b.title, b.cover_book, b.author 
              FROM mybooks mb
              JOIN books b ON mb.book_id = b.book_id
              WHERE mb.user_id = ? AND mb.status = 'saved'";
if (!empty($search_query)) {
    $sql_saved .= " AND (b.title LIKE ? OR b.author LIKE ?)";
}
$sql_saved .= " ORDER BY mb.added_at DESC";

$stmt_saved = $conn->prepare($sql_saved);
if (!empty($search_query)) {
    $search_param = "%" . $search_query . "%";
    $stmt_saved->bind_param("iss", $user_id, $search_param, $search_param);
} else {
    $stmt_saved->bind_param("i", $user_id);
}
$stmt_saved->execute();
$result_saved = $stmt_saved->get_result();
while ($row = $result_saved->fetch_assoc()) {
    $bookshelf_books[] = $row;
}
$stmt_saved->close();
$conn->close();

$total_books_found = count($recently_read_books) + count($bookshelf_books);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Books | LibraReads</title>
    <link rel="stylesheet" href="mybook.css"> 
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
            <li><a href="catalog.php"><i class='bx bxs-book'></i> Catalog</a></li>
            <li><a href="mybooks.php" class="active"><i class='bx bxs-bookmark'></i> Bookshelf</a></li>
            <li><a href="profile.php"><i class='bx bxs-user'></i> Profile</a></li>
        </ul>
        <div class="menu-toggle">
            <i class='bx bx-menu'></i>
        </div>
    </aside>
    <div class="main">
        <header class="top-nav">
             <form action="mybooks.php" method="GET">
                <input type="search" name="search" class="search-bar" placeholder="Cari buku di bookshelf Anda..." value="<?php echo htmlspecialchars($search_query); ?>">
                 <button type="submit" style="display: none;"></button>
            </form>
        </header>

        <main class="content">
            <section class="book-section">
                <h2>Recently Read</h2>
                <div class="book-list">
                    <?php if (count($recently_read_books) > 0): ?>
                        <?php foreach ($recently_read_books as $book): ?>
                            <a href="read-it.php?id=<?php echo $book['book_id']; ?>" class="book">
                                <img src="<?php echo htmlspecialchars($book['cover_book']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                                <p><?php echo htmlspecialchars($book['title']); ?></p>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php if (empty($search_query)): ?>
                            <p class="empty-section-message">Anda belum mulai membaca buku apa pun. Mulai dari <a href="catalog.php">katalog</a>!</p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </section>

            <section class="book-section">
                 <h2>Your Bookshelf</h2>
                <div class="book-list">
                    <?php if (count($bookshelf_books) > 0): ?>
                        <?php foreach ($bookshelf_books as $book): ?>
                            <a href="read-it.php?id=<?php echo $book['book_id']; ?>" class="book">
                                <img src="<?php echo htmlspecialchars($book['cover_book']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                                <p><?php echo htmlspecialchars($book['title']); ?></p>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if ($total_books_found == 0): ?>
                        <div class="empty-bookshelf">
                            <i class='bx bx-search-alt-2'></i>
                            <?php if (!empty($search_query)): ?>
                                <h3>Tidak Ada Hasil Ditemukan</h3>
                                <p>Kami tidak dapat menemukan buku yang cocok dengan "<?php echo htmlspecialchars($search_query); ?>".</p>
                                <a href="mybooks.php" class="btn-primary">Hapus Pencarian</a>
                            <?php else: ?>
                                <h3>Bookshelf Anda Kosong</h3>
                                <p>Tambahkan buku dari katalog untuk melihatnya di sini.</p>
                                <a href="catalog.php" class="btn-primary">Jelajahi Katalog</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>
</div>
<script src="mybooks.js"></script>
</body>
</html>