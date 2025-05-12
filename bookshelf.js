document.addEventListener("DOMContentLoaded", function () {
    const searchBar = document.querySelector(".search-bar");
    
    searchBar.addEventListener("input", function() {
        const searchTerm = searchBar.value.toLowerCase();
        const books = document.querySelectorAll(".book");
        
        books.forEach(function(book) {
            const bookTitle = book.querySelector("p").textContent.toLowerCase();
            if (bookTitle.includes(searchTerm)) {
                book.style.display = "block";
            } else {
                book.style.display = "none";
            }
        });
    });
});
