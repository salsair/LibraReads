<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | LibraReads</title>
    <link rel="stylesheet" href="forgotpassword.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="wrapper">
        <form id="email-form" onsubmit="sendOTP(event)">
            <h1>Reset Password</h1>
            <p>Kami akan mengirimkan kode verifikasi ke email Anda.</p>
            <div class="input-box">
                <input type="email" id="email" placeholder="Masukkan email terdaftar Anda" required>
                <i class='bx bxs-envelope'></i>
            </div>
            <button type="submit" class="btn" id="send-code-btn">Kirim Kode</button>
        </form>

        <form id="otp-form" style="display: none;" onsubmit="verifyOTP(event)">
            <h1>Verifikasi Kode</h1>
            <p>Masukkan 6 digit kode yang kami kirimkan.</p>
            <div class="input-box">
                <input type="text" id="otp" placeholder="Enter OTP" required maxlength="6" pattern="\d{6}">
                <i class='bx bxs-key'></i>
            </div>
            <button type="submit" class="btn">Verifikasi</button>
        </form>

        <form id="password-form" style="display: none;" onsubmit="resetPassword(event)">
            <h1>Buat Password Baru</h1>
            <div class="input-box">
                <input type="password" id="password" placeholder="Password Baru" required>
                <i class='bx bxs-lock-alt'></i>
            </div>
            <div class="input-box">
                <input type="password" id="confirm-password" placeholder="Konfirmasi Password Baru" required>
                <i class='bx bxs-lock-alt'></i>
            </div>
            <button type="submit" class="btn">Reset Password</button>
        </form>

        <div class="register-link">
            <p>Ingat password Anda? <a href="login.php">Login</a></p>
        </div>
    </div>

    <script>
        const emailForm = document.getElementById("email-form");
        const otpForm = document.getElementById("otp-form");
        const passwordForm = document.getElementById("password-form");

        // Fungsi untuk mengirim OTP
        async function sendOTP(event) {
            event.preventDefault();
            const email = document.getElementById("email").value;
            const sendBtn = document.getElementById("send-code-btn");
            
            sendBtn.disabled = true;
            sendBtn.textContent = "Mengirim...";

            const formData = new URLSearchParams();
            formData.append('action', 'send_otp');
            formData.append('email', email);

            try {
                const response = await fetch('process_forgotpassword.php', { method: 'POST', body: formData });
                const data = await response.json();
                
                alert(data.message);
                if (data.status === 'success') {
                    emailForm.style.display = "none";
                    otpForm.style.display = "block";
                }
            } catch (error) {
                console.error('Error:', error);
                alert("Terjadi kesalahan. Silakan coba lagi.");
            } finally {
                sendBtn.disabled = false;
                sendBtn.textContent = "Kirim Kode";
            }
        }

        // Fungsi untuk verifikasi OTP
        async function verifyOTP(event) {
            event.preventDefault();
            const otp = document.getElementById("otp").value;

            const formData = new URLSearchParams();
            formData.append('action', 'verify_otp');
            formData.append('otp', otp);

            try {
                const response = await fetch('process_forgotpassword.php', { method: 'POST', body: formData });
                const data = await response.json();

                alert(data.message);
                if (data.status === 'success') {
                    otpForm.style.display = "none";
                    passwordForm.style.display = "block";
                }
            } catch (error) {
                console.error('Error:', error);
                alert("Terjadi kesalahan saat verifikasi OTP.");
            }
        }

        // Fungsi untuk reset password
        async function resetPassword(event) {
            event.preventDefault();
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirm-password").value;

            if (password.length < 6) {
                alert("Password minimal harus 6 karakter.");
                return;
            }
            if (password !== confirmPassword) {
                alert("Konfirmasi password tidak cocok!");
                return;
            }

            const formData = new URLSearchParams();
            formData.append('action', 'reset_password');
            formData.append('password', password);

            try {
                // PERBAIKAN DI SINI: 'process_forgot_password.php' -> 'process_forgotpassword.php'
                const response = await fetch('process_forgotpassword.php', { method: 'POST', body: formData });
                const data = await response.json();

                alert(data.message);
                if (data.status === 'success') {
                    window.location.href = "login.php"; 
                }
            } catch (error) {
                console.error('Error:', error);
                alert("Terjadi kesalahan saat mereset password.");
            }
        }
    </script>
</body>
</html>