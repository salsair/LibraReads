document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('login-form');
    const emailField = document.getElementById('email');
    const rememberMeCheckbox = document.getElementById('remember');

    // 1. Fungsi untuk mendapatkan cookie berdasarkan nama
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    // 2. Periksa cookie 'remember_me' saat halaman dimuat
    const rememberedUser = getCookie('remember_email');
    if (rememberedUser) {
        emailField.value = decodeURIComponent(rememberedUser); // Isi email yang tersimpan
        rememberMeCheckbox.checked = true; // Centang kotaknya
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const email = emailField.value.trim();
        const password = document.getElementById('password').value;
        const rememberMe = rememberMeCheckbox.checked;

        // Validasi dasar
        if (!email || !password) {
            showMessage('Silakan isi semua kolom', 'error');
            return;
        }

        if (!isValidEmail(email)) {
            showMessage('Masukkan alamat email yang valid', 'error');
            return;
        }

        // 3. Simpan atau hapus cookie email saat login
        if (rememberMe) {
            // Simpan email di cookie terpisah selama 30 hari
            document.cookie = `remember_email=${encodeURIComponent(email)}; max-age=2592000; path=/`;
        } else {
            // Hapus cookie email jika tidak dicentang
            document.cookie = 'remember_email=; max-age=-1; path=/';
        }

        const formData = new FormData();
        formData.append('email', email);
        formData.append('password', password);
        formData.append('remember', rememberMe); // Kirim status checkbox

        fetch('process_login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('Login berhasil! Mengalihkan...', 'success');
                setTimeout(() => {
                    window.location.href = data.redirect || 'index.html';
                }, 1500);
            } else {
                showMessage(data.message || 'Email atau password salah', 'error');
            }
        })
        .catch(error => {
            showMessage('Kesalahan koneksi. Coba lagi nanti.', 'error');
            console.error('Error:', error);
        });
    });

    // Fungsi helper
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function showMessage(message, type = 'info') {
        let messageElement = document.querySelector('.message-container');
        if (!messageElement) {
            messageElement = document.createElement('div');
            // Menambahkan style langsung agar tidak perlu edit CSS
            messageElement.style.padding = '10px';
            messageElement.style.marginTop = '15px';
            messageElement.style.marginBottom = '15px';
            messageElement.style.borderRadius = '5px';
            messageElement.style.textAlign = 'center';
            messageElement.style.color = '#fff';
            form.parentNode.insertBefore(messageElement, form);
        }
        messageElement.textContent = message;
        messageElement.style.backgroundColor = type === 'success' ? '#28a745' : '#dc3545';
        setTimeout(() => {
            messageElement.remove();
        }, 3000);
    }
});