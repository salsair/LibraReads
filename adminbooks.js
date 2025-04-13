let books = [
    { title: "App Development with Flutter", image: "images/AppDevelopmentFlutter.jpg" },
    { title: "Computer Programming", image: "images/ComputerProgramming.jpg" },
    { title: "Data Science 101", image: "images/DataScience101.jpg" },
    { title: "Functional Programming", image: "images/FunctionalProgramming.jpg" },
    { title: "Machine Learning", image: "images/MachineLearning.jpg" },
    { title: "Master Coding", image: "images/MasterCoding.jpg" },
    { title: "PHP by Example", image: "images/PHPbyExample.jpg" },
    { title: "Pragmatic Programmer", image: "images/PragmaticProgrammer.jpg" },
    { title: "Python Data Analysis", image: "images/PythonDataAnalysis.jpg" },
    { title: "Think Python", image: "images/ThinkPython.jpg" },
    { title: "Web Development", image: "images/WebDevelopment.jpg" }
];

function displayBooks() {
    const booksGrid = document.getElementById("booksGrid");
    booksGrid.innerHTML = "";
    
    books.forEach((book, index) => {
        const bookCard = document.createElement("div");
        bookCard.classList.add("book-card");
        
        bookCard.innerHTML = `
            <img src="${book.image}" alt="${book.title}">
            <h3>${book.title}</h3>
            <div class="actions">
                <i class="bx bx-edit" onclick="editBook(${index})"></i>
                <i class="bx bx-trash" onclick="deleteBook(${index})"></i>
            </div>
        `;

        booksGrid.appendChild(bookCard);
    });
}

function openModal(index = null) {
    document.getElementById("bookModal").style.display = "flex";
    if (index !== null) {
        document.getElementById("modalTitle").innerText = "Edit Book";
        document.getElementById("bookTitle").value = books[index].title;
        document.getElementById("bookImage").value = books[index].image;
        document.getElementById("editIndex").value = index;
    }
}

function closeModal() {
    document.getElementById("bookModal").style.display = "none";
}

function saveBook() {
    const title = document.getElementById("bookTitle").value;
    const image = document.getElementById("bookImage").value;
    const index = document.getElementById("editIndex").value;

    if (index) books[index] = { title, image };
    else books.push({ title, image });

    closeModal();
    displayBooks();
}

function deleteBook(index) {
    books.splice(index, 1);
    displayBooks();
}

displayBooks();
