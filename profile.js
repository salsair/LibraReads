document.addEventListener("DOMContentLoaded", function() {
    // Menangani perubahan foto profil
    const sidebarUploadInput = document.getElementById("sidebar-upload");
    if (sidebarUploadInput) {
        sidebarUploadInput.addEventListener("change", function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Menampilkan foto profil yang diubah di sidebar
                    document.getElementById("sidebar-picture").src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Simpan perubahan form profil
    document.getElementById("profile-form").addEventListener("submit", function(event) {
        event.preventDefault();

        const fullName = document.getElementById("full-name").value;
        const email = document.getElementById("email").value;
        const phone = document.getElementById("phone").value;
        const profilePicture = document.getElementById("sidebar-upload").files[0];

        // Menyusun data untuk dikirim ke server
        const formData = new FormData();
        formData.append('full_name', fullName);
        formData.append('email', email);
        formData.append('phone', phone);
        if (profilePicture) {
            formData.append('profile_picture', profilePicture);
        }

        // Mengirim data ke backend untuk disimpan
        fetch('process_profile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log('Profile updated:', data);
            alert(data.message); // Menampilkan pesan dari server
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });

});
