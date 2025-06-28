<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | LibraReads</title>
    <link rel="icon" type="image/png" href="images/LogoLibraReads.png">
    <link rel="stylesheet" href="forgotpassword.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="wrapper">
        <form id="email-form">
            <h1>Reset Password</h1>
            <p>We will send a verification code to your email.</p>
            <div class="input-box">
                <input type="email" id="email" name="email" placeholder="Enter your registered email" required>
                <i class='bx bxs-envelope'></i>
            </div>
            <button type="submit" class="btn" id="send-code-btn">Send Code</button>
        </form>

        <form id="otp-form" style="display: none;">
            <h1>Verify Code</h1>
            <p>Enter the 6-digit code we sent you.</p>
            <div class="input-box">
                <input type="text" id="otp" name="otp" placeholder="Enter OTP" required maxlength="6" pattern="\d{6}">
                <i class='bx bxs-key'></i>
            </div>
            <button type="submit" class="btn">Verify</button>
        </form>

        <form id="password-form" style="display: none;">
            <h1>Create New Password</h1>
            <div class="input-box">
                <input type="password" id="password" name="password" placeholder="New Password" required>
                <i class='bx bxs-hide' id="togglePassword"></i>
            </div>
            <div class="input-box">
                <input type="password" id="confirm-password" name="confirmPassword" placeholder="Confirm New Password" required>
                <i class='bx bxs-hide' id="toggleConfirmPassword"></i>
            </div>
            <button type="submit" class="btn">Reset Password</button>
        </form>

        <div class="register-link">
            <p>Remember your password? <a href="login.php">Login</a></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Referensi ke semua form
            const emailForm = document.getElementById("email-form");
            const otpForm = document.getElementById("otp-form");
            const passwordForm = document.getElementById("password-form");

            // ===================================================
            // Fitur Tampilkan/Sembunyikan Password
            // ===================================================
            const passwordField = document.getElementById('password');
            const togglePassword = document.getElementById('togglePassword');
            const confirmPasswordField = document.getElementById('confirm-password');
            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');

            function addPasswordToggleListener(field, icon) {
                if (field && icon) {
                    icon.addEventListener('click', function () {
                        // Cek tipe input saat ini
                        const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
                        field.setAttribute('type', type);
                        
                        // Ganti ikon mata
                        this.classList.toggle('bxs-show');
                        this.classList.toggle('bxs-hide');
                    });
                }
            }

            addPasswordToggleListener(passwordField, togglePassword);
            addPasswordToggleListener(confirmPasswordField, toggleConfirmPassword);
            // ===================================================
            // AKHIR DARI FITUR BARU
            // ===================================================

            // Menambahkan event listener ke setiap form
            if(emailForm) emailForm.addEventListener('submit', sendOTP);
            if(otpForm) otpForm.addEventListener('submit', verifyOTP);
            if(passwordForm) passwordForm.addEventListener('submit', resetPassword);
        });

        // Fungsi untuk mengirim OTP
        async function sendOTP(event) {
            event.preventDefault();
            const email = document.getElementById("email").value;
            const sendBtn = document.getElementById("send-code-btn");
            
            sendBtn.disabled = true;
            sendBtn.textContent = "Sending...";

            const formData = new URLSearchParams();
            formData.append('action', 'send_otp');
            formData.append('email', email);

            try {
                const response = await fetch('process_forgotpassword.php', { method: 'POST', body: formData });
                const data = await response.json();
                
                alert(data.message); // Menampilkan pesan dari server
                if (data.status === 'success') {
                    document.getElementById("email-form").style.display = "none";
                    document.getElementById("otp-form").style.display = "block";
                }
            } catch (error) {
                console.error('Error:', error);
                alert("An error occurred. Please try again.");
            } finally {
                sendBtn.disabled = false;
                sendBtn.textContent = "Send Code";
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
                    document.getElementById("otp-form").style.display = "none";
                    document.getElementById("password-form").style.display = "block";
                }
            } catch (error) {
                console.error('Error:', error);
                alert("An error occurred during OTP verification.");
            }
        }

        // Fungsi untuk reset password
        async function resetPassword(event) {
            event.preventDefault();
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirm-password").value;

            if (password.length < 8) {
                alert("Password must be at least 8 characters long.");
                return;
            }
            if (password !== confirmPassword) {
                alert("Passwords do not match!");
                return;
            }

            const formData = new URLSearchParams();
            formData.append('action', 'reset_password');
            formData.append('password', password);

            try {
                const response = await fetch('process_forgotpassword.php', { method: 'POST', body: formData });
                const data = await response.json();

                alert(data.message);
                if (data.status === 'success') {
                    window.location.href = "login.php"; 
                }
            } catch (error) {
                console.error('Error:', error);
                alert("An error occurred while resetting the password.");
            }
        }
    </script>
</body>
</html>