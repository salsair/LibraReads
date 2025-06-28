document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('login-form');
    const emailField = document.getElementById('email');
    const passwordField = document.getElementById('password');
    const rememberMeCheckbox = document.getElementById('remember');
    const togglePassword = document.getElementById('togglePassword');

    // ===================================================
    // BARU: Fitur Tampilkan/Sembunyikan Password
    // ===================================================
    if (togglePassword) {
        togglePassword.addEventListener('click', function () {
            // Cek tipe input saat ini
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            
            // Ganti ikon mata
            this.classList.toggle('bxs-show');
            this.classList.toggle('bxs-hide');
        });
    }
    // ===================================================
    // AKHIR DARI FITUR BARU
    // ===================================================

    // 1. Fungsi untuk mendapatkan cookie berdasarkan nama
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    // 2. Periksa cookie 'remember_me' saat halaman dimuat
    const rememberedUser = getCookie('remember_email');
    if (rememberedUser) {
        emailField.value = decodeURIComponent(rememberedUser);
        rememberMeCheckbox.checked = true;
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const email = emailField.value.trim();
        const password = passwordField.value;
        const rememberMe = rememberMeCheckbox.checked;

        // Validasi dasar
        if (!email || !password) {
            showMessage('Please fill in all fields', 'error');
            return;
        }

        if (!isValidEmail(email)) {
            showMessage('Please enter a valid email address', 'error');
            return;
        }

        // 3. Simpan atau hapus cookie email saat login
        if (rememberMe) {
            document.cookie = `remember_email=${encodeURIComponent(email)}; max-age=2592000; path=/`;
        } else {
            document.cookie = 'remember_email=; max-age=-1; path=/';
        }

        const formData = new FormData();
        formData.append('email', email);
        formData.append('password', password);
        formData.append('remember', rememberMe);

        fetch('process_login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('Login successful! Redirecting...', 'success');
                setTimeout(() => {
                    window.location.href = data.redirect || 'index.html';
                }, 1500);
            } else {
                showMessage(data.message || 'Incorrect email or password', 'error');
            }
        })
        .catch(error => {
            showMessage('Connection error. Please try again later.', 'error');
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
            messageElement.style.padding = '10px';
            messageElement.style.marginTop = '15px';
            messageElement.style.marginBottom = '15px';
            messageElement.style.borderRadius = '5px';
            messageElement.style.textAlign = 'center';
            messageElement.style.color = '#fff';
            messageElement.classList.add('message-container');
            form.parentNode.insertBefore(messageElement, form);
        }
        messageElement.textContent = message;
        messageElement.style.backgroundColor = type === 'success' ? '#28a745' : '#dc3545';
        setTimeout(() => {
            messageElement.remove();
        }, 3000);
    }
});