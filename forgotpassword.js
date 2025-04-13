// Simpan OTP yang dibuat
let generatedOTP = "";

// Ketika tombol "Send Code" diklik
document.getElementById("send-code-btn").addEventListener("click", function () {
    let email = document.getElementById("email").value;
    if (!email.includes("@")) {
        alert("Please enter a valid email address.");
        return;
    }

    // Simulasi pengiriman kode OTP
    generatedOTP = Math.floor(1000 + Math.random() * 9000).toString();
    alert("Verification code sent to " + email + ". Your OTP: " + generatedOTP); 

    // Sembunyikan form email dan tampilkan form OTP
    document.getElementById("email-form").style.display = "none";
    document.getElementById("otp-form").style.display = "block";
});

// Ketika OTP dikirim dan user mencoba verifikasi
document.getElementById("otp-form").addEventListener("submit", function (event) {
    event.preventDefault(); // Mencegah submit default form

    let enteredOTP = document.getElementById("otp").value;
    if (enteredOTP !== generatedOTP) {
        alert("Incorrect OTP! Please try again.");
        return;
    }

    // Jika OTP benar, tampilkan form reset password
    alert("OTP Verified! You can now reset your password.");
    document.getElementById("otp-form").style.display = "none";
    document.getElementById("password-form").style.display = "block";
});

// Ketika user mengisi password baru
document.getElementById("password-form").addEventListener("submit", function (event) {
    event.preventDefault();

    let password = document.getElementById("password").value;
    let confirmPassword = document.getElementById("confirm-password").value;

    if (password.length < 6) {
        alert("Password must be at least 6 characters.");
        return;
    }

    if (password !== confirmPassword) {
        alert("Passwords do not match!");
        return;
    }

    alert("Password successfully reset! You can now log in.");
    window.location.href = "login.html"; // Redirect ke halaman login
});