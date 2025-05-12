// Simpan OTP yang dibuat
let generatedOTP = "";

// Ketika tombol "Send Code" diklik
document.getElementById("send-code-btn").addEventListener("click", function () {
    let email = document.getElementById("email").value;
    if (!email.includes("@")) {
        alert("Please enter a valid email address.");
        return;
    }

    // Kirim permintaan POST untuk mengirim OTP ke email
    fetch('process_forgotpassword.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'email=' + encodeURIComponent(email)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);  // Memberi tahu pengguna bahwa OTP telah dikirim
            document.getElementById("email-form").style.display = "none";
            document.getElementById("otp-form").style.display = "block"; // Menampilkan form OTP
        } else {
            alert(data.message);  // Jika gagal, tampilkan pesan kesalahan
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});

// Ketika OTP dikirim dan user mencoba verifikasi
document.getElementById("otp-form").addEventListener("submit", function (event) {
    event.preventDefault(); // Mencegah submit default form

    let enteredOTP = document.getElementById("otp").value;
    
    // Memverifikasi OTP
    fetch('process_forgotpassword.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'otp=' + encodeURIComponent(enteredOTP)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert("OTP Verified! You can now reset your password.");
            document.getElementById("otp-form").style.display = "none";
            document.getElementById("password-form").style.display = "block"; // Menampilkan form password
        } else {
            alert("Incorrect OTP! Please try again.");
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});

// Reset Password
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
