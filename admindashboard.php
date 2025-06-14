<?php
session_start();

// --- PENJAGA LOGIN ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// --- AKHIR PENJAGA LOGIN ---

require_once 'config.php';

// Ambil data statistik dinamis dari database
$book_count = 0;
$user_count = 0;
$event_count = 0;

// Hitung jumlah buku
$result_books = $conn->query("SELECT COUNT(book_id) AS total FROM books");
if ($result_books) {
    $book_count = $result_books->fetch_assoc()['total'];
}

// Hitung jumlah pengguna
$result_users = $conn->query("SELECT COUNT(id) AS total FROM users");
if ($result_users) {
    $user_count = $result_users->fetch_assoc()['total'];
}

// Hitung jumlah event
$result_events = $conn->query("SELECT COUNT(event_id) AS total FROM events");
if ($result_events) {
    $event_count = $result_events->fetch_assoc()['total'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | LibraReads</title>
    <link rel="stylesheet" href="admindashboard.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
    <nav class="sidebar">
        <div class="logo">
            <img src="images/LogoLibraReads.png" alt="LibraReads">
        </div>
        
        <ul class="nav-links">
            <li><a href="admindashboard.php" class="active"><i class='bx bxs-dashboard'></i>Dashboard</a></li>
            <li><a href="adminbooks.php"><i class="bx bx-book"></i>Books</a></li>
            <li><a href="adminusers.php"><i class="bx bx-user"></i>Users</a></li>
            <li><a href="adminevents.php"><i class='bx bx-calendar-event'></i>Events</a></li>
            <li><a href="adminsettings.php"><i class="bx bx-cog"></i>Settings</a></li>
            <li><a href="#" onclick="logout()" class="logout-link"><i class="bx bx-log-out"></i>Logout</a></li>
        </ul>
        
        <button class="menu-toggle" onclick="toggleMobileMenu()">
            <i class="bx bx-menu"></i>
        </button>
    </nav>

    <main class="main">
        <div class="content">
            <section class="welcome-section">
                <div class="welcome-card">
                    <h1>Welcome back, Admin!</h1>
                    <p>Here's your LibraReads overview for today.</p>
                </div>
            </section>

            <section class="main-stats">
                <div class="stat-card">
                    <div class="stat-icon"><i class="bx bx-book"></i></div>
                    <div class="stat-info">
                        <h3>Books Available</h3>
                        <p class="stat-number"><?php echo number_format($book_count); ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon"><i class="bx bx-group"></i></div>
                    <div class="stat-info">
                        <h3>Active Users</h3>
                        <p class="stat-number"><?php echo number_format($user_count); ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon"><i class="bx bx-calendar-event"></i></div>
                    <div class="stat-info">
                        <h3>Upcoming Events</h3>
                        <p class="stat-number"><?php echo number_format($event_count); ?></p>
                    </div>
                </div>
            </section>

            <section class="quick-actions">
                <h3>Quick Actions</h3>
                <div class="action-buttons">
                    <button class="action-btn" onclick="window.location.href='adminbooks.php'">
                        <i class="bx bx-plus-circle"></i>
                        Add New Book
                    </button>
                    <button class="action-btn" onclick="window.location.href='adminevents.php'">
                        <i class="bx bx-calendar-plus"></i>
                        Add New Event
                    </button>
                    <button class="action-btn" onclick="window.location.href='adminusers.php'">
                        <i class="bx bx-user-plus"></i>
                        Manage Users
                    </button>
                </div>
            </section>
        </div>
    </main>

    <script>
        function logout() {
            if(confirm("Are you sure you want to log out?")) {
                window.location.href = "logout.php"; // Sesuaikan dengan file logout Anda
            }
        }
        
        function toggleMobileMenu() {
            document.querySelector('.nav-links').classList.toggle('active');
            const icon = document.querySelector('.menu-toggle i');
            icon.className = icon.className === 'bx bx-menu' ? 'bx bx-x' : 'bx bx-menu';
        }

        // JavaScript lainnya bisa ditambahkan di sini jika perlu
    </script>
</body>
</html>