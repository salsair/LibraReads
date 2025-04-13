document.addEventListener("DOMContentLoaded", function () {
    document.querySelector("form").addEventListener("submit", function (event) {
        event.preventDefault(); // Mencegah submit langsung

        let password = document.getElementById("password").value;
        let confirmPassword = document.getElementById("confirm-password").value;

        // Validasi panjang password
        if (password.length < 6) {
            alert("Password must be at least 6 characters long!");
            return;
        }

        // Cek apakah password dan konfirmasi tidak cocok
        if (password !== confirmPassword) {
            alert("Passwords do not match!");
            return;
        }

        // Jika semua validasi lolos, arahkan ke homepage
        alert("Account created successfully!");
        window.location.href = "homepage.html";
    });
});
