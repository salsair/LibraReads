<?php
// 1. Memulai sesi
session_start();
include 'config.php';

// ==================================================================
// PENJAGA ADMIN BARU - Disesuaikan dengan struktur tabel Anda
// ==================================================================

// 1. Cek apakah ada sesi login 'user_id'
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Ambil user_id dari sesi
$user_id_from_session = $_SESSION['user_id'];

// 3. DIUBAH: Memeriksa kolom 'full_name', bukan 'role'
$stmt = $conn->prepare("SELECT full_name FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id_from_session);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// 4. DIUBAH: Kondisi disesuaikan. Jika nama lengkap BUKAN 'admin', tendang ke homepage
if (!$user || $user['full_name'] !== 'admin') {
    header("Location: homepage.php");
    exit();
}

// Jika lolos, berarti dia adalah admin. Kita gunakan user_idnya.
$admin_id = $user_id_from_session;
// ==================================================================


// Ambil data admin dari DB untuk ditampilkan di form
$sql_admin_data = "SELECT id, full_name, email, profile_picture FROM users WHERE id = ?";
$stmt_admin_data = $conn->prepare($sql_admin_data);
$stmt_admin_data->bind_param("i", $admin_id);
$stmt_admin_data->execute();
$result_admin_data = $stmt_admin_data->get_result();
$admin = $result_admin_data->fetch_assoc();
$stmt_admin_data->close();

if (!$admin) {
    die("Error: Admin data not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings | LibraReads</title>
    <link rel="stylesheet" href="adminsetting.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
    <nav class="sidebar">
        <div class="logo">
            <a href="admindashboard.php"><img src="images/LogoLibraReads.png" alt="LibraReads"></a>
        </div>
        
        <ul class="nav-links">
            <li><a href="admindashboard.php"><i class='bx bxs-home'></i>Dashboard</a></li>
            <li><a href="adminbooks.php"><i class="bx bx-book"></i>Books</a></li>
            <li><a href="adminusers.php"><i class="bx bx-user"></i>Users</a></li>
            <li><a href="adminevents.php"><i class='bx bx-calendar-event'></i>Events</a></li>
            <li><a href="adminsettings.php" class="active"><i class="bx bx-cog"></i>Settings</a></li>
            <li><a href="#" onclick="logout()" class="logout-link"><i class="bx bx-log-out"></i>Logout</a></li>
        </ul>
        
        <button class="menu-toggle" onclick="toggleMobileMenu()">
            <i class="bx bx-menu"></i>
        </button>
    </nav>

    <main class="main">
        <div class="content">
            <header class="page-header">
                <h2>Settings</h2>
                <div class="header-actions">
                    <button class="save-all-btn" onclick="document.getElementById('account-form').requestSubmit();">
                        <i class="bx bx-save"></i>
                        Save All Changes
                    </button>
                </div>
            </header>

            <div class="settings-grid">
                <section class="settings-section">
                    <div class="section-header">
                        <i class="bx bx-user-circle"></i>
                        <h3>Account Settings</h3>
                    </div>
                    <div class="section-content">
                        <form id="account-form" class="settings-form" method="POST" action="process_adminsettings.php" enctype="multipart/form-data">
                            
                            <input type="hidden" name="id" value="<?php echo $admin['id']; ?>">
                            
                            <div class="form-group">
                                <label for="full_name">Full Name</label>
                                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($admin['full_name']); ?>" required>
                                <small class="form-help">Your display name in the system</small>
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                                <small class="form-help">Used for notifications and system alerts</small>
                            </div>

                            <div class="form-group">
                                <label for="profile_picture">Profile Picture</label>
                                <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                                <?php if ($admin['profile_picture']): ?>
                                    <small class="form-help">Current: <?php echo basename($admin['profile_picture']); ?></small>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="current_password">Current Password</label>
                                <input type="password" id="current_password" name="current_password" placeholder="Enter current password">
                                <small class="form-help">Required to change password</small>
                            </div>

                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input type="password" id="new_password" name="new_password" placeholder="Enter new password">
                                <small class="form-help">Leave blank to keep current password</small>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password">
                                <small class="form-help">Must match new password</small>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <script src="adminsettings.js"></script>
</body>
</html>