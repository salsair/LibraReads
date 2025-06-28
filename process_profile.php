<?php
// Include config.php untuk koneksi ke database
include 'config.php';

// Periksa apakah form telah dikirim dan file gambar ada
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data pengguna dari session atau tentukan ID pengguna jika tidak menggunakan session
    $user_id = $_SESSION['user_id'];  // Sesuaikan dengan session atau ID pengguna yang valid

    // Ambil data dari form
    $full_name = htmlspecialchars($_POST['full_name']);
    $email = htmlspecialchars($_POST['email']);
    $profile_picture = $_FILES['profile_picture'];

    // Variabel untuk menyimpan path gambar profil baru
    $profile_picture_path = 'images/dashboard/pp.jpg';  // Default path jika tidak ada file yang diunggah

    // Jika ada gambar yang diunggah
    if ($profile_picture['error'] === UPLOAD_ERR_OK) {
        // Tentukan direktori tempat file akan disimpan
        $upload_dir = 'uploads/';

        // Cek apakah direktori ada, jika tidak buat
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Tentukan nama file dan pastikan tidak ada duplikasi
        $profile_picture_name = time() . '_' . basename($profile_picture['name']);
        $profile_picture_path = $upload_dir . $profile_picture_name;

        // Pindahkan file ke direktori yang telah ditentukan
        if (move_uploaded_file($profile_picture['tmp_name'], $profile_picture_path)) {
            // Berhasil mengunggah gambar
        } else {
            // Jika gagal mengunggah
            echo "Error: Gagal mengunggah gambar.";
            exit;
        }
    }

    // Perbarui informasi pengguna di database
    $sql = "UPDATE users SET full_name = ?, profile_picture = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $full_name, $profile_picture_path, $user_id);

    if ($stmt->execute()) {
        // Jika berhasil memperbarui profil
        header("Location: profile.php");  // Redirect kembali ke halaman profil
        exit;
    } else {
        // Jika ada kesalahan saat memperbarui data
        echo "Error: Gagal memperbarui profil.";
    }
}
?>
