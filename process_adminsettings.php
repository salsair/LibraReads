<?php
session_start();
include 'config.php';

// Fungsi untuk mengirim response error dan menghentikan script
function send_error($message) {
    http_response_code(400); // Bad Request
    echo $message;
    exit();
}

// ==================================================================
// PENJAGA ADMIN BARU - Disesuaikan dengan struktur tabel Anda
// ==================================================================

// 1. Cek apakah ada sesi login 'user_id'
if (!isset($_SESSION['user_id'])) {
    send_error('Authentication required. Please login again.');
}

// 2. Ambil user_id dari sesi
$user_id_from_session = $_SESSION['user_id'];

// 3. DIUBAH: Memeriksa kolom 'full_name', bukan 'role'
$stmt_role = $conn->prepare("SELECT full_name FROM users WHERE id = ?");
$stmt_role->bind_param("i", $user_id_from_session);
$stmt_role->execute();
$result_role = $stmt_role->get_result();
$user_check = $result_role->fetch_assoc();
$stmt_role->close();

// 4. DIUBAH: Kondisi disesuaikan. Jika nama lengkap BUKAN 'admin', tolak akses
if (!$user_check || $user_check['full_name'] !== 'admin') {
    send_error('Authorization failed. You do not have admin privileges.');
}
// Jika lolos, berarti pengguna adalah admin yang sah.
// ==================================================================


// Validasi Awal & Keamanan Input
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_error('Invalid request method.');
}

// Ambil Data dari Form
$admin_id_from_form = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$full_name = trim($_POST['full_name'] ?? '');
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
//... (sisa kode tetap sama)

// Keamanan tambahan: Pastikan admin hanya bisa mengedit profilnya sendiri
if ($user_id_from_session !== $admin_id_from_form) {
    send_error('Security check failed. You can only edit your own profile.');
}

// ... (sisa kode untuk update password, foto, dll tidak perlu diubah)
// Bangun Query SQL secara Dinamis
$sql_parts = [];
$params = [];
$types = "";

$sql_parts[] = "full_name = ?";
$params[] = $full_name;
$types .= "s";

$sql_parts[] = "email = ?";
$params[] = $email;
$types .= "s";

// Logika untuk Mengganti Password
if (!empty($_POST['new_password'])) {
    if (empty($_POST['current_password'])) {
        send_error('Current password is required to set a new one.');
    }
    if ($_POST['new_password'] !== $_POST['confirm_password']) {
        send_error('New passwords do not match.');
    }

    $stmt_pass = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt_pass->bind_param("i", $admin_id_from_form);
    $stmt_pass->execute();
    $user_pass_result = $stmt_pass->get_result();
    $user_pass = $user_pass_result->fetch_assoc();
    $stmt_pass->close();

    if (!$user_pass || !password_verify($_POST['current_password'], $user_pass['password'])) {
        send_error('Incorrect current password.');
    }

    $new_password_hashed = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $sql_parts[] = "password = ?";
    $params[] = $new_password_hashed;
    $types .= "s";
}


// Logika untuk Meng-upload Profile Picture
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $upload_dir = 'uploads/profile_pictures/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_name = uniqid() . '_' . basename($_FILES['profile_picture']['name']);
    $target_file = $upload_dir . $file_name;
    
    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
        $sql_parts[] = "profile_picture = ?";
        $params[] = $target_file;
        $types .= "s";
    } else {
        send_error('Failed to upload profile picture.');
    }
}

// Eksekusi Query Final
if (!empty($sql_parts)) {
    $sql = "UPDATE users SET " . implode(', ', $sql_parts) . " WHERE id = ?";
    $types .= "i";
    $params[] = $admin_id_from_form;

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        http_response_code(200);
        echo "Settings updated successfully!";
    } else {
        send_error('Database error: ' . $stmt->error);
    }
    $stmt->close();
} else {
    http_response_code(200);
    echo "No changes were made.";
}

$conn->close();
?>