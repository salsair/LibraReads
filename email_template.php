<?php

function generateOTPEmailTemplate($fullName, $otp) {
    // Ambil tahun saat ini untuk copyright
    $year = date('Y');

    // Menggunakan sintaks HEREDOC untuk membuat blok HTML lebih mudah dibaca
    $htmlBody = <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password LibraReads</title>
    <style>
        /* Style dasar untuk email */
        body { margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header { background-color: #2c3e50; color: #ffffff; padding: 30px 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; }
        .content { padding: 30px 40px; color: #333333; line-height: 1.6; }
        .content p { margin: 0 0 15px; }
        .otp-box { background-color: #ecf0f1; border-radius: 8px; padding: 20px; text-align: center; margin: 20px 0; }
        .otp-code { font-size: 36px; font-weight: bold; color: #3498db; letter-spacing: 5px; margin: 0; }
        .warning { font-size: 14px; color: #7f8c8d; }
        .footer { background-color: #34495e; color: #ecf0f1; text-align: center; padding: 20px; font-size: 12px; }
        .footer a { color: #3498db; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>LibraReads</h1>
        </div>
        <div class="content">
            <p>Halo, <strong>{$fullName}</strong>,</p>
            <p>Kami menerima permintaan untuk mereset kata sandi untuk akun Anda. Gunakan kode verifikasi (OTP) di bawah ini untuk melanjutkan.</p>
            
            <div class="otp-box">
                <p style="margin:0 0 10px; font-size: 16px; color: #333;">Kode Verifikasi Anda:</p>
                <p class="otp-code">{$otp}</p>
            </div>
            
            <p>Kode ini hanya berlaku selama <strong>10 menit</strong>. Mohon untuk tidak membagikan kode ini kepada siapapun demi keamanan akun Anda.</p>
            <p class="warning">Jika Anda tidak merasa meminta untuk mereset kata sandi, silakan abaikan email ini. Tidak ada perubahan yang akan dibuat pada akun Anda.</p>
            <br>
            <p>Terima kasih,<br>Tim Pengembang LibraReads</p>
        </div>
        <div class="footer">
            <p>&copy; {$year} LibraReads. All rights reserved.</p>
            <p>Butuh bantuan? Kunjungi <a href="#">Pusat Bantuan</a> kami.</p>
        </div>
    </div>
</body>
</html>
HTML;

    // Untuk email client yang tidak mendukung HTML, sediakan versi teks biasa
    $altBody = "Halo {$fullName},\n\n";
    $altBody .= "Gunakan kode berikut untuk mereset password Anda: {$otp}\n";
    $altBody .= "Kode ini akan kedaluwarsa dalam 10 menit.\n\n";
    $altBody .= "Jika Anda tidak meminta reset password, abaikan email ini.\n\n";
    $altBody .= "Terima kasih,\nTim LibraReads";

    return ['html' => $htmlBody, 'alt' => $altBody];
}

?>