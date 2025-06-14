<?php
session_start();

// --- PENJAGA LOGIN ---
// Cek apakah user sudah login.
if (!isset($_SESSION['user_id'])) {
    // Jika belum, redirect (alihkan) ke halaman login.
    header("Location: login.php");
    exit(); // Pastikan script berhenti di sini.
}
// --- AKHIR PENJAGA LOGIN ---

include 'config.php'; // Sertakan file koneksi database Anda

// Karena sudah pasti login, kita bisa langsung ambil user_id
$user_id = $_SESSION['user_id'];

// Ambil nama user untuk sapaan
$sql_user = "SELECT full_name FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
if ($row_user = $result_user->fetch_assoc()) {
    $first_name = explode(' ', $row_user['full_name'])[0]; // Ambil nama depan
    $welcome_message = "Welcome back, " . htmlspecialchars($first_name) . "!";
} else {
    // Fallback jika user tidak ditemukan (meski kecil kemungkinan)
    $welcome_message = "Welcome to LibraReads";
}
$stmt_user->close();


// --- Query untuk Fitur Baru: Event Slider ---
$sql_events = "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 5";
$result_events = $conn->query($sql_events);


// --- Query untuk Most Popular ---
$sql_popular = "
    SELECT b.book_id, b.title, b.cover_book, COUNT(mb.book_id) AS popularity
    FROM books b
    LEFT JOIN mybooks mb ON b.book_id = mb.book_id
    GROUP BY b.book_id
    ORDER BY popularity DESC, b.title ASC
    LIMIT 8;
";
$result_popular = $conn->query($sql_popular);

// --- Query untuk Popular Categories ---
$sql_categories = "SELECT DISTINCT genre FROM books WHERE genre IS NOT NULL AND genre != '' ORDER BY genre ASC";
$result_categories = $conn->query($sql_categories);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage | LibraReads</title>
    <link rel="stylesheet" href="homepage.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script defer src="homepage.js"></script>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <div class="logo">
                <a href="landingpage.php"><img src="images/LogoLibraReads.png" alt="LibraReads Logo"></a>
            </div>
            <ul class="nav-links">
                <li><a href="homepage.php" class="active"><i class='bx bxs-home'></i> Home</a></li>
                <li><a href="catalog.php"><i class='bx bxs-book'></i> Catalog</a></li>
                <li><a href="mybooks.php"><i class='bx bxs-bookmark'></i> Bookshelf</a></li>
                <li><a href="profile.php"><i class='bx bxs-user'></i> Profile</a></li>
            </ul>
            <div class="menu-toggle">
                <i class='bx bx-menu'></i>
            </div>
        </aside>

        <div class="main">
            <header class="top-nav">
                <form action="catalog.php" method="GET" class="search-form">
                    <input type="text" name="search" class="search-bar" placeholder="Find books to boost your skills...">
                    <button type="submit" class="search-button"><i class='bx bx-search'></i></button>
                </form>
            </header>

            <main class="content">
                <h1><?php echo $welcome_message; ?></h1>
                <p>Explore our best book recommendations and upcoming tech events for you.</p>

                <section class="event-section">
                    <h2>Upcoming Events</h2>
                    <div class="event-slider-container">
                        <div class="event-slider-viewport">
                            <div class="event-slider-track">
                                <?php if ($result_events && $result_events->num_rows > 0): ?>
                                    <?php while($event = $result_events->fetch_assoc()): ?>
                                    <div class="event-slide">
                                        <img src="<?php echo htmlspecialchars($event['image_url']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                                        <div class="event-info">
                                            <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                                            <p><?php echo date("F j, Y", strtotime($event['event_date'])); ?></p>
                                            <a href="<?php echo htmlspecialchars($event['registration_link']); ?>" class="btn-event" target="_blank">Register Now</a>
                                        </div>
                                    </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <p>No upcoming events at the moment. Stay tuned!</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <button class="slider-nav prev"><i class='bx bx-chevron-left'></i></button>
                        <button class="slider-nav next"><i class='bx bx-chevron-right'></i></button>
                    </div>
                </section>

                <section class="book-section">
                    <h2>Most Popular</h2>
                    <div class="book-list">
                        <?php if ($result_popular && $result_popular->num_rows > 0): ?>
                            <?php while($book = $result_popular->fetch_assoc()): ?>
                            <div class="book">
                                <a href="read-it.php?id=<?php echo $book['book_id']; ?>">
                                    <img src="<?php echo htmlspecialchars($book['cover_book']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                                    <p><?php echo htmlspecialchars($book['title']); ?></p>
                                </a>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>No popular books to show right now.</p>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="categories">
                    <h2>Popular Categories</h2>
                    <div class="category-list">
                        <?php if ($result_categories && $result_categories->num_rows > 0): ?>
                            <?php while($category = $result_categories->fetch_assoc()): ?>
                                <a href="catalog.php?search=<?php echo urlencode($category['genre']); ?>" class="category">
                                    <?php echo htmlspecialchars($category['genre']); ?>
                                </a>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>No categories found.</p>
                        <?php endif; ?>
                    </div>
                </section>
            </main>
        </div>
    </div>
</body>
</html>
<?php
if(isset($conn)) {
    $conn->close();
}
?>