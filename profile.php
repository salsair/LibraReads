<?php
// Include config.php untuk koneksi ke database
include 'config.php';

// Ambil ID pengguna dari session atau tentukan ID pengguna jika tidak menggunakan session
$user_id = 1;  // Sesuaikan dengan session atau ID pengguna yang valid

// Ambil data pengguna berdasarkan ID pengguna yang ada di session
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Menentukan path gambar yang akan ditampilkan
// Jika user tidak memiliki gambar profil, tampilkan gambar default
$profile_picture_path = $user['profile_picture'] ? $user['profile_picture'] : 'images/dashboard/pp.jpg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - LibraReads</title>
    <link rel="stylesheet" href="profile.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard">
        <!-- Top Navigation Bar -->
        <div class="navbar">
            <div class="logo">
                <a href="dashboard.html"><img src="images/LogoLibraReads.png" alt="LibraReads Logo"></a>
            </div>
            <ul>
                <li><a href="homepage.html"><i class='bx bxs-home'></i> Home</a></li>
                <li><a href="catalog.html"><i class='bx bxs-book'></i> Catalog</a></li>
                <li><a href="bookshelf.html"><i class='bx bxs-bookmark'></i> Bookshelf</a></li>
                <li><a href="profile.php"><i class='bx bxs-user'></i> Profile</a></li>
            </ul>
        </div>

        <!-- Main Content Area -->
        <div class="main">
            <div class="container">
                <!-- Sidebar -->
                <div class="sidebar">
                    <h3 id="sidebar-name"><?php echo htmlspecialchars($user['full_name']); ?></h3>
                    <div class="sidebar-profile-pic">
                        <!-- Menampilkan gambar profil dari path yang disimpan di database -->
                        <img id="sidebar-picture" src="<?php echo $profile_picture_path; ?>" alt="Profile Picture">
                        <label for="sidebar-upload" class="custom-upload">
                            <i class='bx bx-camera'></i> Change Photo
                        </label>
                        <input type="file" id="sidebar-upload" name="profile_picture" accept="image/*">
                    </div>
                </div>

                <!-- Profile Content -->
                <div class="profile-content">
                    <h3>PROFILE</h3>
                    <hr>
                    <form id="profile-form" method="POST" action="process_profile.php" enctype="multipart/form-data">
                        <label>Full Name</label>
                        <input type="text" id="full-name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>

                        <label>Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly disabled>  <!-- Email hanya tampil, tidak bisa diubah -->

                        <label>Profile Picture</label>
                        <div class="sidebar-profile-pic">
                            <img id="sidebar-picture" src="<?php echo $profile_picture_path; ?>" alt="Profile Picture">
                            <label for="sidebar-upload" class="custom-upload">
                                <i class='bx bx-camera'></i> Change Photo
                            </label>
                            <input type="file" id="sidebar-upload" name="profile_picture" accept="image/*">
                        </div>

                        <button type="submit" class="save-btn">Save Changes</button>
                        <button type="button" class="logout-btn" onclick="logout()">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<script>
// Fungsi logout untuk mengarahkan ke login.html
function logout() {
    alert("Anda telah logout.");
    window.location.href = "login.html"; // Redirect ke halaman login
}
</script>
