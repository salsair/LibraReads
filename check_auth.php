<?php
session_start();
require_once 'config.php'; // Butuh koneksi database

// Jika pengguna belum login via sesi
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Periksa apakah ada cookie "remember_me"
    if (isset($_COOKIE['remember_me'])) {
        // Dekode cookie
        list($user_id, $token) = explode(':', base64_decode($_COOKIE['remember_me']), 2);

        if ($user_id && $token) {
            $token_hash = hash('sha256', $token);

            // Cari token di database
            $stmt = $conn->prepare("SELECT * FROM remember_tokens WHERE user_id = ? AND token_hash = ? AND expires_at > NOW()");
            $stmt->bind_param("is", $user_id, $token_hash);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                // Token valid, login pengguna secara otomatis
                $user_stmt = $conn->prepare("SELECT id, email, full_name FROM users WHERE id = ?");
                $user_stmt->bind_param("i", $user_id);
                $user_stmt->execute();
                $user_result = $user_stmt->get_result();
                $user = $user_result->fetch_assoc();

                // Buat ulang sesi
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['logged_in'] = true;

                $user_stmt->close();
            } else {
                // Token tidak valid atau kedaluwarsa, hapus cookie
                setcookie('remember_me', '', time() - 3600, "/");
            }
            $stmt->close();
        }
    }
}

// Jika setelah semua pengecekan pengguna tetap tidak login, redirect ke halaman login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.html');
    exit;
}
?>