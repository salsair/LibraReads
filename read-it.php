<?php
session_start();
include 'config.php'; // Include your database connection file

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not
    exit();
}

$user_id = $_SESSION['user_id'];
$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($book_id <= 0) {
    die("Book not found.");
}

// Fetch book details from the database
$stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();
$stmt->close();

if (!$book) {
    die("Book not found.");
}

// Check if the book is already in mybooks to determine the "Save" button status
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
    <link rel="icon" type="image/png" href="images/LogoLibraReads.png">
    <link rel="stylesheet" href="read-it.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="top-bar">
        <a href="catalog.php" class="back-button">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Catalog</span>
        </a>
    </div>

    <div class="container">
        <div class="book-detail-card">
            <div id="savedIndicator" class="saved-indicator">
                <i class="fas fa-check"></i> Saved
            </div>

            <div class="book-header">
                <h1><?php echo htmlspecialchars($book['title']); ?></h1>
                <p>by <?php echo htmlspecialchars($book['author']); ?></p>
            </div>

            <div class="book-content">
                <div class="book-cover">
                    <img src="<?php echo htmlspecialchars($book['cover_book']); ?>" alt="Cover of <?php echo htmlspecialchars($book['title']); ?>">
                </div>

                <div class="book-info">
                    <div class="book-meta">
                        <div class="meta-item">
                            <span>Genre</span>
                            <p><?php echo htmlspecialchars($book['genre']); ?></p>
                        </div>
                        <div class="meta-item">
                            <span>Publication Year</span>
                            <p><?php echo htmlspecialchars($book['publication_year']); ?></p>
                        </div>
                        <div class="meta-item">
                            <span>Total Pages</span>
                            <p><?php echo htmlspecialchars($book['total_pages']); ?></p>
                        </div>
                    </div>

                    <div class="book-description">
                        <h3>Description</h3>
                        <p><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>
                    </div>

                    <div class="action-buttons">
                        <button id="startReadingBtn" data-book-id="<?php echo $book_id; ?>">
                            <i class="fas fa-book-open"></i>
                            <span>Start Reading</span>
                        </button>
                        
                        <?php if (!empty($book['url_book'])): ?>
                            <button type="button" class="btn-outline" onclick="window.open('<?php echo htmlspecialchars($book['url_book']); ?>', '_blank')">
                                <i class="fas fa-external-link-alt"></i> Open Original Link
                            </button>
                        <?php endif; ?>

                        <button id="toggleSaveBtn" 
                                data-action="<?php echo $is_saved ? 'unsave' : 'save'; ?>" 
                                class="<?php echo $is_saved ? 'btn-primary' : 'btn-secondary'; ?>">
                            <?php echo $is_saved ? 'Saved to Bookshelf' : 'Save to Bookshelf'; ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="read-it.js"></script>
</body>
</html>