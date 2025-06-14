document.addEventListener('DOMContentLoaded', function() {
    // --- KODE UNTUK MENU (TIDAK DIUBAH) ---
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');

    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            navLinks.classList.toggle('active');
            const icon = menuToggle.querySelector('i');
            if (navLinks.classList.contains('active')) {
                icon.classList.remove('bx-menu');
                icon.classList.add('bx-x');
            } else {
                icon.classList.remove('bx-x');
                icon.classList.add('bx-menu');
            }
        });
    }
    
    // --- KODE BARU UNTUK FITUR PENCARIAN ---
    const searchForm = document.getElementById('search-form');
    const searchBar = document.getElementById('search-bar');
    let typingTimer; // Timer
    const doneTypingInterval = 500; // Waktu dalam ms (0.5 detik)

    // Jalankan pencarian saat pengguna selesai mengetik
    if (searchBar) {
        searchBar.addEventListener('keyup', () => {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                searchForm.submit(); // Kirim form setelah selesai mengetik
            }, doneTypingInterval);
        });

        // Hapus timer jika pengguna sedang mengetik
        searchBar.addEventListener('keydown', () => {
            clearTimeout(typingTimer);
        });
    }

    // Mencegah pengiriman form kosong jika pengguna menekan Enter secara tidak sengaja
    if (searchForm) {
        searchForm.addEventListener('submit', (e) => {
            if (searchBar.value.trim() === '') {
                // Jika input kosong, arahkan ke halaman katalog tanpa parameter search
                e.preventDefault();
                window.location.href = 'catalog.php';
            }
        });
    }
});