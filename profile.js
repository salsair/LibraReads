// Simpan perubahan profile form
document.getElementById("profile-form").addEventListener("submit", function(event) {
    event.preventDefault();

    const username = document.getElementById("username").value;
    const email = document.getElementById("email").value;
    const phone = document.getElementById("phone").value;

    alert(`Perubahan Disimpan:\nUsername: ${username}\nEmail: ${email}\nNomor HP: ${phone}`);
});

// Logout function
function logout() {
    alert("Anda telah logout.");
    window.location.href = "login.html";
}

// Upload foto di sidebar
document.getElementById("sidebar-upload").addEventListener("change", function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById("sidebar-picture").src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});
