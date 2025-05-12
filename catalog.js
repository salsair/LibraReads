document.addEventListener("DOMContentLoaded", function () {
    const menuToggle = document.querySelector(".menu-toggle");
    const sidebar = document.getElementById("sidebar");
    const closeBtn = document.querySelector(".close-btn");
    const overlay = document.getElementById("overlay");

    menuToggle.addEventListener("click", function () {
        sidebar.classList.add("open");
    });

    closeBtn.addEventListener("click", function () {
        sidebar.classList.remove("open");
    });

    overlay.addEventListener("click", function () {
        sidebar.classList.remove("open");
    });
});


