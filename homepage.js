document.addEventListener('DOMContentLoaded', function() {
    // --- KODE UNTUK MENU SIDEBAR MOBILE ---
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');

    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', function(event) {
            event.stopPropagation();
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

        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', function() {
                if (navLinks.classList.contains('active')) {
                    navLinks.classList.remove('active');
                    const icon = menuToggle.querySelector('i');
                    icon.classList.remove('bx-x');
                    icon.classList.add('bx-menu');
                }
            });
        });

        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            if (sidebar && !sidebar.contains(event.target) && navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
                const icon = menuToggle.querySelector('i');
                icon.classList.remove('bx-x');
                icon.classList.add('bx-menu');
            }
        });
    }

    // --- KODE EVENT SLIDER DENGAN AUTOPLAY ---
    const sliderContainer = document.querySelector('.event-slider-container');
    const track = document.querySelector('.event-slider-track');

    if (sliderContainer && track) {
        const slides = Array.from(track.children);
        const nextButton = document.querySelector('.slider-nav.next');
        const prevButton = document.querySelector('.slider-nav.prev');
        
        if (slides.length > 1) {
            let currentIndex = 0;
            let autoplayInterval = null;

            const updateSlidePosition = () => {
                if (slides.length > 0) {
                    const slideWidth = slides[0].getBoundingClientRect().width;
                    track.style.transform = 'translateX(' + (-slideWidth * currentIndex) + 'px)';
                }
            };

            const moveToNextSlide = () => {
                currentIndex++;
                if (currentIndex >= slides.length) {
                    currentIndex = 0;
                }
                updateSlidePosition();
            };
            
            // Fungsi untuk memulai autoplay
            const startAutoplay = () => {
                stopAutoplay(); // Hentikan dulu jika sudah ada
                autoplayInterval = setInterval(moveToNextSlide, 5000); // Ganti slide setiap 5 detik
            };

            // Fungsi untuk menghentikan autoplay
            const stopAutoplay = () => {
                clearInterval(autoplayInterval);
            };

            // Event listener untuk tombol
            nextButton.addEventListener('click', () => {
                moveToNextSlide();
                startAutoplay(); // Reset interval setelah klik manual
            });

            prevButton.addEventListener('click', () => {
                currentIndex--;
                if (currentIndex < 0) {
                    currentIndex = slides.length - 1;
                }
                updateSlidePosition();
                startAutoplay(); // Reset interval setelah klik manual
            });

            // Hentikan autoplay saat mouse di atas slider, jalankan lagi saat mouse keluar
            sliderContainer.addEventListener('mouseenter', stopAutoplay);
            sliderContainer.addEventListener('mouseleave', startAutoplay);

            window.addEventListener('resize', updateSlidePosition);
            
            updateSlidePosition(); // Atur posisi awal
            startAutoplay(); // Mulai autoplay saat halaman dimuat

        } else {
            if(nextButton) nextButton.style.display = 'none';
            if(prevButton) prevButton.style.display = 'none';
        }
    }
});