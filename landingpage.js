document.addEventListener("DOMContentLoaded", function () {
    console.log("Landing page loaded successfully!");

    // Smooth scroll effect for navbar links
    document.querySelectorAll('nav ul li a').forEach(anchor => {
        anchor.addEventListener('click', function (event) {
            if (this.getAttribute('href').startsWith('#')) {
                event.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({ 
                    behavior: 'smooth' 
                });
            }
        });
    });

    // Hover effect for category buttons
    document.querySelectorAll('.category').forEach(category => {
        category.addEventListener('mouseenter', function () {
            this.style.transform = "scale(1.1)";
        });
        category.addEventListener('mouseleave', function () {
            this.style.transform = "scale(1)";
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    console.log("Landing page loaded successfully!");

    // Toggle hamburger menu
    const hamburger = document.getElementById("hamburger");
    const navMenu = document.getElementById("nav-menu");

    hamburger.addEventListener("click", () => {
        navMenu.classList.toggle("active");
    });

    // Smooth scroll effect for navbar links
    document.querySelectorAll('nav ul li a').forEach(anchor => {
        anchor.addEventListener('click', function (event) {
            if (this.getAttribute('href').startsWith('#')) {
                event.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });

                // Auto-close menu di mobile setelah klik link
                if (window.innerWidth <= 768) {
                    navMenu.classList.remove("active");
                }
            }
        });
    });

    // Hover effect for category buttons
    document.querySelectorAll('.category').forEach(category => {
        category.addEventListener('mouseenter', function () {
            this.style.transform = "scale(1.1)";
        });
        category.addEventListener('mouseleave', function () {
            this.style.transform = "scale(1)";
        });
    });
});
