<?php
// Mulai session
session_start();

// Include config.php untuk koneksi ke database
include 'config.php';

// Pastikan user sudah login dengan memeriksa session
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: login.php");
    exit;
}

// Ambil user_id dari session
$user_id = $_SESSION['user_id'];  // Mengambil user_id dari session yang aktif

// Ambil data pengguna berdasarkan ID pengguna yang ada di session
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Tentukan gambar profil default
$profile_picture_path = 'images/dashboard/pp.jpg';  // Gambar default jika tidak ada gambar

// Cek apakah pengguna memiliki gambar profil
if (isset($user['profile_picture']) && !empty($user['profile_picture'])) {
    $profile_picture_path = $user['profile_picture'];  // Gambar profil yang sudah ada
}

// Jika form dikirim, proses pengunggahan gambar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data gambar yang diunggah
    $profile_picture = $_FILES['profile_picture'];

    // Tentukan direktori tempat file akan disimpan
    $upload_dir = 'uploads/profile_pictures/';

    // Cek apakah direktori ada, jika tidak buat
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Tentukan nama file gambar
    $profile_picture_name = time() . '_' . basename($profile_picture['name']);
    $profile_picture_path = $upload_dir . $profile_picture_name;

    // Pindahkan file ke direktori yang telah ditentukan
    if (move_uploaded_file($profile_picture['tmp_name'], $profile_picture_path)) {
        // Berhasil mengunggah gambar, perbarui informasi di database
        $sql = "UPDATE users SET profile_picture = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $profile_picture_path, $user_id);

        if ($stmt->execute()) {
            // Jika berhasil, arahkan kembali ke halaman profil
            header("Location: profile.php");
            exit;
        } else {
            echo "Error: Gagal memperbarui gambar profil.";
        }
    } else {
        echo "Error: Gagal mengunggah gambar.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Profile Picture</title>
    <link rel="stylesheet" href="profile.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard">
        <!-- Top Navigation Bar -->
            <aside class="navbar">
                <div class="logo">
                    <a href="homepage.html"><img src="images/LogoLibraReads.png" alt="LibraReads Logo"></a>
                </div>
                
                <ul class="nav-links">
                    <li><a href="homepage.html"><i class='bx bxs-home'></i> Home</a></li>
                    <li><a href="catalog.html"><i class='bx bxs-book'></i> Catalog</a></li>
                    <li><a href="mybooks.html"><i class='bx bxs-bookmark'></i> Bookshelf</a></li>
                    <li><a href="profile.php" class="active"><i class='bx bxs-user'></i> Profile</a></li>
                </ul>
                
                <div class="menu-toggle">
                    <i class='bx bx-menu'></i>
                </div>
            </aside>

        <!-- Main Content Area -->
        <div class="main">
            <div class="container">
                <!-- Sidebar -->
                <div class="sidebar">
                    <h3 id="sidebar-name"><?php echo htmlspecialchars($user['full_name']); ?></h3>
                    <!-- Menampilkan gambar profil dan tombol untuk mengganti foto -->
                    <div class="sidebar-profile-pic">
                        <img id="sidebar-picture" src="<?php echo $profile_picture_path; ?>" alt="Profile Picture">
                        <a href="upload_profile.php" class="custom-upload">
                            <i class='bx bx-camera'></i> Change Photo
                        </a>
                    </div>
                </div>

                <!-- Profile Content -->
                <div class="profile-content">
                    <h3>Upload Profile Picture</h3>
                    <hr>
                    <form action="upload_profile.php" method="POST" enctype="multipart/form-data">
                        <label for="profile_picture">Choose a new profile picture</label>
                        <input type="file" name="profile_picture" id="profile_picture" accept="image/*" required>
                        <button type="submit" class="save-btn">Upload</button>
                    </form>
                    <a href="profile.php" class="back-btn">Back to Profile</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
