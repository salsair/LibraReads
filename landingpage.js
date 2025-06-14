document.addEventListener("DOMContentLoaded", () => {
    // DIUBAH: Mencari elemen berdasarkan class-nya, bukan ID.
    const navbar = document.querySelector(".navbar"); 
    const hamburger = document.getElementById("hamburger");
    const navMenu = document.getElementById("nav-menu");

    // 1. Navbar menjadi solid saat di-scroll
    window.addEventListener("scroll", () => {
        if (window.scrollY > 50) {
            navbar.classList.add("scrolled");
        } else {
            navbar.classList.remove("scrolled");
        }
    });

    // 2. Fungsi untuk toggle menu hamburger
    hamburger.addEventListener("click", () => {
        navMenu.classList.toggle("active");
    });

    // 3. Tutup menu saat link di-klik (untuk mobile)
    document.querySelectorAll(".nav-menu a").forEach(link => {
        link.addEventListener("click", () => {
            if (navMenu.classList.contains("active")) {
                navMenu.classList.remove("active");
            }
        });
    });

    // 4. Animasi fade-in saat seksi masuk ke layar
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("visible");
            }
        });
    }, {
        threshold: 0.1
    });

    // Targetkan semua <section> untuk dianimasikan
    document.querySelectorAll("section").forEach(section => {
        observer.observe(section);
    });
});