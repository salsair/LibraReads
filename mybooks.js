// mybooks.js
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    
    // Guard clause jika elemen tidak ditemukan
    if (!menuToggle || !navLinks) {
        return;
    }

    // Toggle mobile menu
    menuToggle.addEventListener('click', function(event) {
        event.stopPropagation(); // Mencegah event bubbling ke document
        navLinks.classList.toggle('active');
        
        // Toggle menu icon
        const icon = menuToggle.querySelector('i');
        if (navLinks.classList.contains('active')) {
            icon.classList.remove('bx-menu');
            icon.classList.add('bx-x');
        } else {
            icon.classList.remove('bx-x');
            icon.classList.add('bx-menu');
        }
    });
    
    // Fungsi untuk menutup menu
    function closeMenu() {
        if (navLinks.classList.contains('active')) {
            navLinks.classList.remove('active');
            const icon = menuToggle.querySelector('i');
            icon.classList.remove('bx-x');
            icon.classList.add('bx-menu');
        }
    }

    // Tutup menu saat link di dalam navigasi diklik
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', function() {
            closeMenu();
        });
    });
    
    // Tutup menu saat mengklik di luar area navigasi
    document.addEventListener('click', function(event) {
        const isClickInsideNav = navLinks.contains(event.target);
        const isClickOnToggle = menuToggle.contains(event.target);

        if (!isClickInsideNav && !isClickOnToggle) {
            closeMenu();
        }
    });
});
