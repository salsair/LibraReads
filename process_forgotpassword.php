<?php
session_start();

// Menggunakan file config.php Anda untuk koneksi
require 'config.php'; 
// MEMASUKKAN FILE TEMPLATE EMAIL BARU
require 'email_template.php'; 
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Jakarta');
header('Content-Type: application/json');

function json_output($status, $message) {
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

if (!isset($_POST['action'])) {
    json_output('error', 'Aksi tidak valid.');
}

$action = $_POST['action'];

switch ($action) {
    case 'send_otp':
        if (empty($_POST['email'])) {
            json_output('error', 'Email tidak boleh kosong.');
        }

        $email = $conn->real_escape_string($_POST['email']);

        // 1. Cek email dan AMBIL 'id' SERTA 'full_name'
        $stmt = $conn->prepare("SELECT id, full_name FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            json_output('error', 'Email tidak terdaftar di sistem kami.');
        }
        
        $user = $result->fetch_assoc();
        $user_id = $user['id'];
        // SIMPAN NAMA LENGKAP PENGGUNA
        $full_name = $user['full_name'];

        // 2. Generate OTP dan waktu kedaluwarsa (10 menit)
        $otp = random_int(100000, 999999);
        $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        // 3. Simpan OTP ke tabel forgotpassword
        $conn->query("DELETE FROM forgotpassword WHERE user_id = $user_id");
        $stmt_insert = $conn->prepare("INSERT INTO forgotpassword (user_id, otp, expires_at) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("iss", $user_id, $otp, $expires_at);
        $stmt_insert->execute();

        // 4. Kirim email menggunakan PHPMailer dengan template baru
        $mail = new PHPMailer(true);
        try {
            // Pengaturan SMTP Anda
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'readslibra@gmail.com'; 
            $mail->Password   = 'ckldlemfebloaqtr'; // Pastikan ini adalah App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            // Penerima dan Pengirim
            $mail->setFrom('readslibra@gmail.com', 'LibraReads Support');
            $mail->addAddress($email, $full_name); // Menambahkan nama penerima adalah praktik yang baik

            // ================== KONTEN EMAIL DIPERBARUI ==================
            // Panggil fungsi dari email_template.php
            $emailContent = generateOTPEmailTemplate($full_name, $otp);

            $mail->isHTML(true);
            $mail->Subject = 'Kode Verifikasi Reset Password LibraReads';
            $mail->Body    = $emailContent['html']; // Gunakan template HTML
            $mail->AltBody = $emailContent['alt'];  // Gunakan versi teks biasa
            // ==========================================================

            $mail->send();
            
            $_SESSION['reset_user_id'] = $user_id;
            json_output('success', 'Kode verifikasi telah dikirim ke email Anda.');

        } catch (Exception $e) {
            // error_log("Mailer Error: {$mail->ErrorInfo}"); 
            json_output('error', "Gagal mengirim email. Pastikan konfigurasi SMTP benar.");
        }
        break;

    // ... (case 'verify_otp' dan 'reset_password' tidak perlu diubah) ...
    
    case 'verify_otp':
        if (empty($_POST['otp']) || empty($_SESSION['reset_user_id'])) {
            json_output('error', 'Sesi tidak valid atau OTP kosong.');
        }

        $otp = $conn->real_escape_string($_POST['otp']);
        $user_id = $_SESSION['reset_user_id'];

        $stmt = $conn->prepare("SELECT id FROM forgotpassword WHERE user_id = ? AND otp = ? AND expires_at > NOW()");
        $stmt->bind_param("is", $user_id, $otp);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['otp_verified'] = true;
            json_output('success', 'Verifikasi berhasil! Silakan buat password baru.');
        } else {
            json_output('error', 'Kode OTP salah atau telah kedaluwarsa.');
        }
        break;

    case 'reset_password':
        if (empty($_POST['password']) || empty($_SESSION['reset_user_id']) || empty($_SESSION['otp_verified'])) {
            json_output('error', 'Sesi tidak valid. Silakan ulangi dari awal.');
        }

        $user_id = $_SESSION['reset_user_id'];
        $password = $_POST['password'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($stmt->execute()) {
            $conn->query("DELETE FROM forgotpassword WHERE user_id = $user_id");
            
            unset($_SESSION['reset_user_id']);
            unset($_SESSION['otp_verified']);
            
            json_output('success', 'Password berhasil direset! Anda akan diarahkan ke halaman login.');
        } else {
            json_output('error', 'Gagal mereset password. Silakan coba lagi.');
        }
        break;

    default:
        json_output('error', 'Aksi tidak dikenal.');
        break;
}

$conn->close();
?>