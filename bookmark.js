document.addEventListener("DOMContentLoaded", function () {
    const bookmarkList = document.getElementById("bookmark-list");
    let bookmarks = JSON.parse(localStorage.getItem("bookmarks")) || [];

    if (bookmarks.length === 0) {
        bookmarkList.innerHTML = "<p>No bookmarks yet.</p>";
    } else {
        bookmarks.forEach(book => {
            let bookItem = document.createElement("div");
            bookItem.classList.add("book");
            bookItem.innerHTML = `
                <img src="${book.img}" alt="${book.title}">
                <p>${book.title}</p>
            `;
            bookmarkList.appendChild(bookItem);
        });
    }
});
