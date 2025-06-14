<?php
// reader.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';

$book = null;
$user_id = $_SESSION['user_id'] ?? null;

// Cek apakah user sudah login
if (!$user_id) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $book_id = intval($_GET['id']);
    
    // Ambil detail buku dengan content
    $sql_book = "SELECT book_id, title AS book_title, cover_book, author, description, content, genre, publication_year, url_book, total_pages FROM books WHERE book_id = ?";
    $stmt_book = $conn->prepare($sql_book);
    $stmt_book->bind_param("i", $book_id);
    $stmt_book->execute();
    $result_book = $stmt_book->get_result();
    
    if ($result_book->num_rows > 0) {
        $book = $result_book->fetch_assoc();
        
        // Cek apakah ada reading progress sebelumnya
        $current_page = 1;
        $sql_progress_check = "SELECT current_page FROM reading_progress WHERE user_id = ? AND book_id = ?";
        $stmt_progress_check = $conn->prepare($sql_progress_check);
        $stmt_progress_check->bind_param("ii", $user_id, $book_id);
        $stmt_progress_check->execute();
        $result_progress = $stmt_progress_check->get_result();
        
        if ($result_progress->num_rows > 0) {
            $progress_data = $result_progress->fetch_assoc();
            $current_page = $progress_data['current_page'];
        }
        $stmt_progress_check->close();
        
        // Update reading progress
        $sql_progress = "INSERT INTO reading_progress (user_id, book_id, last_read, current_page) 
                        VALUES (?, ?, NOW(), ?) 
                        ON DUPLICATE KEY UPDATE last_read = NOW()";
        $stmt_progress = $conn->prepare($sql_progress);
        $stmt_progress->bind_param("iii", $user_id, $book_id, $current_page);
        $stmt_progress->execute();
        $stmt_progress->close();
    } else {
        header('Location: index.php');
        exit();
    }
    $stmt_book->close();
} else {
    header('Location: index.php');
    exit();
}

// Tentukan jenis konten (PDF atau teks)
$is_pdf = false;
$has_content = !empty($book['content']);
if (!empty($book['url_book'])) {
    $url_lower = strtolower($book['url_book']);
    $is_pdf = strpos($url_lower, '.pdf') !== false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reading: <?php echo htmlspecialchars($book['book_title']); ?> | LibraReads</title>
    <link rel="stylesheet" href="reader.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
    <!-- Reader Header -->
    <header class="reader-header">
        <div class="reader-nav">
            <div class="nav-left">
                <a href="read-it.php?id=<?php echo $book['book_id']; ?>" class="nav-btn">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
                <div class="book-info">
                    <h1><?php echo htmlspecialchars($book['book_title']); ?></h1>
                    <p>oleh <?php echo htmlspecialchars($book['author']); ?></p>
                </div>
            </div>
            <div class="nav-right">
                <?php if ($has_content): ?>
                <button id="settingsBtn" class="nav-btn">
                    <i class="fas fa-cog"></i>
                </button>
                <?php endif; ?>
                <button id="fullscreenBtn" class="nav-btn">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
        </div>
        
        <!-- Progress Bar -->
        <?php if ($has_content): ?>
        <div class="progress-container">
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            <span class="progress-text" id="progressText">0%</span>
        </div>
        <?php endif; ?>
    </header>

    <!-- Reader Content -->
    <main class="reader-main">
        <?php if ($has_content): ?>
        <!-- Settings Panel untuk Text Reader -->
        <div id="settingsPanel" class="settings-panel">
            <div class="settings-content">
                <h3>Pengaturan Pembaca</h3>
                <div class="setting-group">
                    <label>Ukuran Font:</label>
                    <div class="font-size-controls">
                        <button id="fontSmaller">A-</button>
                        <span id="fontSizeDisplay">16px</span>
                        <button id="fontLarger">A+</button>
                    </div>
                </div>
                <div class="setting-group">
                    <label>Tema:</label>
                    <div class="theme-controls">
                        <button class="theme-btn active" data-theme="light">Terang</button>
                        <button class="theme-btn" data-theme="dark">Gelap</button>
                        <button class="theme-btn" data-theme="sepia">Sepia</button>
                    </div>
                </div>
                <div class="setting-group">
                    <label>Lebar Konten:</label>
                    <input type="range" id="contentWidth" min="60" max="100" value="80">
                </div>
            </div>
        </div>

        <!-- Book Content Area untuk Text -->
        <div class="book-container" id="bookContainer">
            <div class="book-content" id="bookContent">
                <!-- Konten buku akan dimuat di sini -->
                <div class="loading-content">
                    <div class="loading-spinner">
                        <i class="fas fa-book-open"></i>
                        <p>Memuat buku...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Controls untuk Text -->
        <div class="reader-controls">
            <button id="prevBtn" class="control-btn" disabled>
                <i class="fas fa-chevron-left"></i>
                <span>Sebelumnya</span>
            </button>
            <div class="page-info">
                <span id="currentPage">1</span> / <span id="totalPages">--</span>
            </div>
            <button id="nextBtn" class="control-btn">
                <span>Selanjutnya</span>
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <?php elseif ($is_pdf): ?>
        <!-- PDF Viewer -->
        <div class="pdf-container" id="pdfContainer">
            <!-- Loading Overlay -->
            <div class="loading-overlay" id="loadingOverlay">
                <div class="loading-spinner"></div>
                <div class="loading-text">Memuat PDF...</div>
            </div>

            <!-- Error Container -->
            <div class="error-container" id="errorContainer">
                <div class="error-icon">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <h2 class="error-title">Tidak Dapat Memuat PDF</h2>
                <p class="error-message">
                    PDF tidak dapat ditampilkan dalam browser. Ini mungkin karena pembatasan CORS atau format file yang tidak didukung.
                </p>
                <div class="error-actions">
                    <button class="error-btn" id="retryBtn">
                        <i class="fas fa-redo"></i>
                        Coba Lagi
                    </button>
                    <a href="<?php echo htmlspecialchars($book['url_book']); ?>" target="_blank" class="error-btn">
                        <i class="fas fa-external-link-alt"></i>
                        Buka di Tab Baru
                    </a>
                    <button class="error-btn secondary" id="downloadErrorBtn">
                        <i class="fas fa-download"></i>
                        Download PDF
                    </button>
                </div>
            </div>

            <!-- PDF Iframe -->
            <iframe 
                id="pdfFrame" 
                class="pdf-iframe" 
                src="" 
                title="<?php echo htmlspecialchars($book['book_title']); ?>">
            </iframe>
        </div>

        <!-- Floating Controls untuk PDF -->
        <div class="floating-controls" id="floatingControls">
            <div class="zoom-control">
                <button class="zoom-btn" id="zoomOutBtn" title="Zoom Out">
                    <i class="fas fa-minus"></i>
                </button>
                <div class="zoom-level" id="zoomLevel">100%</div>
                <button class="zoom-btn" id="zoomInBtn" title="Zoom In">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>

        <?php else: ?>
        <!-- No Content Available -->
        <div class="no-content-container">
            <div class="no-content-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <h2>Konten Tidak Tersedia</h2>
            <p>Maaf, konten buku ini belum tersedia untuk dibaca online.</p>
            <div class="no-content-actions">
                <a href="read-it.php?id=<?php echo $book['book_id']; ?>" class="error-btn">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Detail
                </a>
                <?php if (!empty($book['url_book'])): ?>
                <a href="<?php echo htmlspecialchars($book['url_book']); ?>" target="_blank" class="error-btn">
                    <i class="fas fa-external-link-alt"></i>
                    Buka Link Asli
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <!-- Error Modal untuk Text Reader -->
    <?php if ($has_content): ?>
    <div id="errorModal" class="error-modal">
        <div class="error-content">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3>Tidak Dapat Memuat Buku</h3>
            <p id="errorMessage">Terjadi kesalahan saat memuat buku. Silakan coba lagi.</p>
            <div class="error-actions">
                <button onclick="retryLoad()" class="btn-primary">Coba Lagi</button>
                <button onclick="goBack()" class="btn-secondary">Kembali</button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Pass PHP data to JavaScript -->
    <script>
        const bookData = {
            id: <?php echo $book['book_id']; ?>,
            title: <?php echo json_encode($book['book_title']); ?>,
            author: <?php echo json_encode($book['author']); ?>,
            content: <?php echo json_encode($book['content'] ?? ''); ?>,
            url: <?php echo json_encode($book['url_book'] ?? ''); ?>,
            totalPages: <?php echo $book['total_pages'] ?: 100; ?>,
            currentPage: <?php echo $current_page; ?>,
            hasContent: <?php echo $has_content ? 'true' : 'false'; ?>,
            isPdf: <?php echo $is_pdf ? 'true' : 'false'; ?>
        };
    </script>
    
    <!-- Load appropriate JavaScript -->
    <?php if ($has_content): ?>
    <script src="reader.js"></script>
    <?php elseif ($is_pdf): ?>
    <script src="pdf-reader.js"></script>
    <?php endif; ?>
</body>
</html>
<?php
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>