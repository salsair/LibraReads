<?php
// Mulai sesi
session_start();

// Inisialisasi respons
$response = array('success' => false, 'message' => '', 'redirect' => '');

// Pastikan request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sertakan koneksi database
    require_once 'config.php';

    // Ambil data dari form
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    // Konversi string 'true'/'false' dari JS ke boolean
    $remember = isset($_POST['remember']) && $_POST['remember'] === 'true';

    // Validasi input
    if (empty($email) || empty($password)) {
        $response['message'] = 'Email dan password harus diisi';
        echo json_encode($response);
        exit;
    }

    // Cari pengguna berdasarkan email
    $stmt = $conn->prepare("SELECT id, email, full_name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Buat sesi untuk pengguna
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['logged_in'] = true;

            // Logika "Remember Me"
            if ($remember) {
                // Buat token yang aman secara kriptografis
                $token = bin2hex(random_bytes(32));
                $token_hash = hash('sha256', $token);
                $user_id = $user['id'];
                // Set token kedaluwarsa dalam 30 hari
                $expires_at = date('Y-m-d H:i:s', time() + 86400 * 30);

                // Simpan hash token ke database
                $stmt_token = $conn->prepare("INSERT INTO remember_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)");
                $stmt_token->bind_param("iss", $user_id, $token_hash, $expires_at);
                $stmt_token->execute();
                $stmt_token->close();

                // Set cookie di browser pengguna (user_id:token)
                $cookie_value = base64_encode($user_id . ':' . $token);
                setcookie('remember_me', $cookie_value, time() + (86400 * 30), "/"); // Cookie berlaku 30 hari
            }

            // Atur URL redirect berdasarkan peran
            if ($user['full_name'] == 'admin') {
                $response['redirect'] = 'admindashboard.php';
            } else {
                $response['redirect'] = 'homepage.php';
            }

            $response['success'] = true;
            $response['message'] = 'Login berhasil!';

        } else {
            $response['message'] = 'Password salah';
        }
    } else {
        $response['message'] = 'Email tidak ditemukan';
    }

    $stmt->close();
    $conn->close();
} else {
    $response['message'] = 'Metode request tidak valid';
}

// Kembalikan respons dalam format JSON
header('Content-Type: application/json');
echo json_encode($response);
?>