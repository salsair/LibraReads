<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';  // Jika menggunakan Composer

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil email dan OTP dari form
    if (isset($_POST['email'])) {
        $email = $_POST['email'];

        // Membuat OTP
        $otp = rand(1000, 9999); // Membuat OTP secara acak

        // Kirim OTP ke email pengguna
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP(); 
            $mail->Host = 'smtp.gmail.com'; // Ganti dengan SMTP server yang kamu gunakan
            $mail->SMTPAuth = true;
            $mail->Username = 'your-email@gmail.com';  // Ganti dengan email pengirim
            $mail->Password = 'your-email-password';  // Ganti dengan password email pengirim
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Pengirim dan penerima email
            $mail->setFrom('your-email@gmail.com', 'LibraReads');
            $mail->addAddress($email);

            // Konten email
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset OTP';
            $mail->Body    = "Your OTP for password reset is: <b>$otp</b>";

            $mail->send();

            // Simpan OTP ke sesi agar bisa digunakan untuk verifikasi
            $_SESSION['otp'] = $otp;

            echo json_encode(['status' => 'success', 'message' => 'OTP sent successfully to your email!']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Message could not be sent. Mailer Error: '.$mail->ErrorInfo]);
        }
    }

    // Verifikasi OTP
    if (isset($_POST['otp'])) {
        $enteredOTP = $_POST['otp'];
        if ($enteredOTP == $_SESSION['otp']) {
            echo json_encode(['status' => 'success', 'message' => 'OTP verified']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Incorrect OTP']);
        }
    }
}
?>
