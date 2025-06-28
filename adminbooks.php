<?php
// --- PENJAGA LOGIN ---
// Memulai sesi untuk mengakses variabel session
session_start();

// Memeriksa apakah pengguna sudah login dengan melihat apakah 'user_id' ada di session
if (!isset($_SESSION['user_id'])) {
    // Jika tidak ada session 'user_id', alihkan pengguna ke halaman login
    header("Location: login.php");
    // Hentikan eksekusi skrip lebih lanjut
    exit();
}
// --- AKHIR PENJAGA LOGIN ---
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books Management | LibraReads</title>
    <link rel="icon" type="image/png" href="images/LogoLibraReads.png">
    <link rel="stylesheet" href="adminbook.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>

    <nav class="sidebar">
        <div class="logo">
            <img src="images/LogoLibraReads.png" alt="LibraReads">
        </div>
        <ul class="nav-links">
            <li><a href="admindashboard.php"><i class='bx bxs-home'></i>Dashboard</a></li>
            <li><a href="adminbooks.php" class="active"><i class="bx bx-book"></i>Books</a></li> 
            <li><a href="adminusers.php"><i class="bx bx-user"></i>Users</a></li>
            <li><a href="adminevents.php"><i class='bx bx-calendar-event'></i>Events</a></li>
            <li><a href="adminsettings.php"><i class="bx bx-cog"></i>Settings</a></li>
            <li><a href="#" onclick="logout()" class="logout-link"><i class="bx bx-log-out"></i>Logout</a></li>
        </ul>
        <button class="menu-toggle" onclick="toggleMobileMenu()">
            <i class="bx bx-menu"></i>
        </button>
    </nav>

    <main class="main">
        <div class="content">
            <header class="page-header">
                <h2>Books Management</h2>
                <div class="header-actions">
                    <button class="add-book-btn" onclick="openAddBookModal()">
                        <i class="bx bx-plus"></i> Add Book
                    </button>
                </div>
            </header>

            <section class="search-filter-section">
                <div class="search-bar">
                    <i class="bx bx-search"></i>
                    <input type="text" id="searchInput" placeholder="Search books by title, author, or genre..." onkeyup="filterBooks()">
                </div>
                <div class="filter-controls">
                    <select id="genreFilter" onchange="filterBooks()">
                        <option value="">All Genres</option>
                        <option value="Programming">Programming</option>
                        <option value="Data Science">Data Science</option>
                        <option value="Machine Learning">Machine Learning</option>
                        <option value="Web Development">Web Development</option>
                        <option value="Computer Science">Computer Science</option>
                        <option value="Mobile Development">Mobile Development</option>
                        <option value="Database">Database</option>
                        <option value="IoT">IoT</option>
                        <option value="Artificial Intelligence">Artificial Intelligence</option>
                        <option value="Education">Education</option>
                    </select>
                    <select id="contentFilter" onchange="filterBooks()">
                        <option value="">All Books</option>
                        <option value="has_content">With Content</option>
                        <option value="no_content">Without Content</option>
                    </select>
                </div>
            </section>

            <section class="books-table-section">
                <div class="table-container">
                    <table class="books-table">
                        <thead>
                            <tr>
                                <th>Cover</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Genre</th>
                                <th>Year</th>
                                <th>Pages</th>
                                <th>Content</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="booksTableBody"></tbody>
                    </table>
                </div>
            </section>

            <section class="books-grid" id="booksGrid"></section>
        </div>
    </main>

    <div id="bookModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add New Book</h3>
                <span class="close" onclick="closeBookModal()">&times;</span>
            </div>
            <form id="bookForm" onsubmit="saveBook(event)">
                <div class="form-row">
                    <div class="form-group">
                        <label for="formBookTitle">Title *</label>
                        <input type="text" id="formBookTitle" required>
                    </div>
                    <div class="form-group">
                        <label for="formBookAuthor">Author *</label>
                        <input type="text" id="formBookAuthor" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="formBookGenre">Genre</label>
                        <select id="formBookGenre">
                            <option value="">Select Genre (Optional)</option>
                            <option value="Programming">Programming</option>
                            <option value="Data Science">Data Science</option>
                            <option value="Machine Learning">Machine Learning</option>
                            <option value="Web Development">Web Development</option>
                            <option value="Computer Science">Computer Science</option>
                            <option value="Mobile Development">Mobile Development</option>
                            <option value="Database">Database</option>
                            <option value="IoT">IoT</option>
                            <option value="Artificial Intelligence">Artificial Intelligence</option>
                            <option value="Education">Education</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="formBookCover">Cover Image URL</label>
                        <input type="text" id="formBookCover" placeholder="e.g., images/cover.jpg or https://url.com/image.png">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="formBookPubYear">Publication Year</label>
                        <input type="number" id="formBookPubYear" placeholder="e.g., 2023" min="1000" max="2099">
                    </div>
                    <div class="form-group">
                        <label for="formBookPages">Total Pages</label>
                        <input type="number" id="formBookPages" placeholder="e.g., 350" min="0">
                    </div>
                </div>
                <div class="form-group">
                    <label for="formBookUrl">Book URL</label>
                    <input type="url" id="formBookUrl" placeholder="e.g., https://www.example.com/book.pdf">
                </div>
                <div class="form-group">
                    <label for="formBookDescription">Description</label>
                    <textarea id="formBookDescription" rows="3" placeholder="Book description..."></textarea>
                </div>
                <div class="form-group">
                    <label for="formBookContent">Content (HTML)</label>
                    <textarea id="formBookContent" rows="6" placeholder="Enter book content in HTML format..."></textarea>
                    <small class="form-hint">Use HTML tags like &lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;, etc.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeBookModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Save Book</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let books = [];
        let editingBookId = null;

        async function fetchInitialBooks() {
            try {
                const response = await fetch('api_buku.php', { credentials: 'same-origin' });
                
                if (!response.ok) {
                    let errorMessage = `Gagal mengambil data: Server merespons dengan status ${response.status}`;
                    if (response.status === 401) {
                        errorMessage = `Sesi login Anda tidak valid atau telah berakhir. Silakan logout dan login kembali.`;
                    }
                    throw new Error(errorMessage);
                }

                const result = await response.json();
                if (result.success) {
                    books = result.books;
                    filterBooks();
                } else {
                    alert('Error dari server: ' + (result.message || 'Pesan error tidak diketahui.'));
                }
            } catch (error) {
                console.error('Terjadi error saat mengambil data buku:', error);
                alert('Tidak dapat memuat data buku. ' + error.message);
            }
        }

        async function saveBook(event) {
            event.preventDefault();
            const bookData = {
                title: document.getElementById('formBookTitle').value,
                author: document.getElementById('formBookAuthor').value,
                genre: document.getElementById('formBookGenre').value,
                image: document.getElementById('formBookCover').value,
                description: document.getElementById('formBookDescription').value,
                content: document.getElementById('formBookContent').value,
                publication_year: document.getElementById('formBookPubYear').value,
                total_pages: document.getElementById('formBookPages').value,
                url_book: document.getElementById('formBookUrl').value
            };

            const formData = new FormData();
            const actionType = editingBookId ? 'edit_book' : 'add_book';
            formData.append('action', actionType);

            if (editingBookId) {
                formData.append('id', editingBookId);
            }
            for (const key in bookData) {
                formData.append(key, bookData[key]);
            }

            try {
                const response = await fetch('api_buku.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`Server Error: ${response.status}`);
                }
                const result = await response.json();
                if (result.success && result.book) {
                    alert(result.message);
                    if (actionType === 'add_book') {
                        books.push(result.book);
                    } else {
                        const bookIndex = books.findIndex(b => b.id === editingBookId);
                        if (bookIndex !== -1) books[bookIndex] = result.book;
                    }
                    filterBooks();
                    closeBookModal();
                } else {
                    alert('Operasi gagal: ' + (result.message || 'Pesan error tidak diketahui.'));
                }
            } catch (error) {
                console.error('Error saat menyimpan buku:', error);
                alert('Terjadi kesalahan saat menyimpan buku. Cek console.');
            }
        }

        async function deleteBook(id) {
            if (confirm('Anda yakin ingin menghapus buku ini?')) {
                const formData = new FormData();
                formData.append('action', 'delete_book');
                formData.append('id', id);

                try {
                    const response = await fetch('api_buku.php', {
                        method: 'POST',
                        credentials: 'same-origin',
                        body: formData
                    });

                    if (!response.ok) {
                        throw new Error(`Server Error: ${response.status}`);
                    }
                    const result = await response.json();
                    if (result.success) {
                        alert(result.message);
                        books = books.filter(book => book.id !== id);
                        filterBooks();
                    } else {
                        alert('Gagal menghapus: ' + (result.message || 'Pesan error tidak diketahui.'));
                    }
                } catch (error) {
                    console.error('Error saat menghapus buku:', error);
                    alert('Terjadi kesalahan saat menghapus buku. Cek console.');
                }
            }
        }
        
        function logout() {
            if(confirm("Are you sure you want to log out?")) {
                window.location.href = "landingpage.php"; 
            }
        }

        function toggleMobileMenu() { 
            const navLinks = document.querySelector('.nav-links'); 
            const menuToggle = document.querySelector('.menu-toggle i'); 
            navLinks.classList.toggle('active'); 
            menuToggle.className = navLinks.classList.contains('active') ? 'bx bx-x' : 'bx bx-menu'; 
        }

        function escapeHtml(unsafe) { 
            if (unsafe === null || typeof unsafe === 'undefined') return ''; 
            return String(unsafe).replace(/[&<>"']/g, function (match) { 
                const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }; 
                return map[match]; 
            }); 
        }

        function filterBooks() { 
            const searchTerm = document.getElementById('searchInput').value.toLowerCase(); 
            const genreFilter = document.getElementById('genreFilter').value; 
            const contentFilter = document.getElementById('contentFilter').value; 
            const filteredBooks = books.filter(book => { 
                const titleMatch = book.title.toLowerCase().includes(searchTerm); 
                const authorMatch = book.author.toLowerCase().includes(searchTerm); 
                const genreTextMatch = book.genre.toLowerCase().includes(searchTerm); 
                const matchesSearch = titleMatch || authorMatch || genreTextMatch; 
                const matchesGenre = !genreFilter || (book.genre === genreFilter); 
                let matchesContent = true; 
                if (contentFilter === 'has_content') { 
                    matchesContent = book.has_content; 
                } else if (contentFilter === 'no_content') { 
                    matchesContent = !book.has_content; 
                } 
                return matchesSearch && matchesGenre && matchesContent; 
            }); 
            displayFilteredBooks(filteredBooks); 
        }

        function displayFilteredBooks(booksToDisplay) { 
            const tableBody = document.getElementById('booksTableBody'); 
            const booksGrid = document.getElementById('booksGrid'); 
            tableBody.innerHTML = ''; 
            booksGrid.innerHTML = ''; 
            if (!Array.isArray(booksToDisplay)) { 
                console.error("Data to display is not an array:", booksToDisplay); 
                return; 
            } 
            booksToDisplay.forEach(book => { 
                const imageSrc = book.image || 'images/DefaultBook.jpg'; 
                const title = escapeHtml(book.title); 
                const author = escapeHtml(book.author); 
                const genre = escapeHtml(book.genre); 
                const pubYear = book.publication_year || '-'; 
                const totalPages = book.total_pages || '-'; 
                const bookUrl = escapeHtml(book.url_book); 
                const hasContent = book.has_content; 
                const contentStatus = hasContent ? '<span class="status-badge status-available">Has Content</span>' : '<span class="status-badge status-borrowed">No Content</span>'; 
                
                const actionsHTML = `
                    <div class="action-buttons">
                        <button class="btn-view" onclick="viewBook(${book.id})" title="View/Read"><i class="bx bx-show"></i></button>
                        <button class="btn-edit" onclick="editBook(${book.id})" title="Edit"><i class="bx bx-edit"></i></button>
                        <button class="btn-delete" onclick="deleteBook(${book.id})" title="Delete"><i class="bx bx-trash"></i></button>
                    </div>`;

                const row = document.createElement('tr'); 
                row.innerHTML = `
                    <td><img src="${imageSrc}" alt="${title}" class="book-cover" onerror="this.onerror=null; this.src='images/DefaultBook.jpg';"></td>
                    <td>${bookUrl ? `<a href="${bookUrl}" target="_blank" rel="noopener noreferrer">${title}</a>` : title}</td>
                    <td>${author}</td>
                    <td>${genre}</td>
                    <td>${pubYear}</td>
                    <td>${totalPages}</td>
                    <td>${contentStatus}</td>
                    <td>${actionsHTML}</td>`; 
                tableBody.appendChild(row); 
                
                const card = document.createElement('div'); 
                card.className = 'book-card'; 
                card.innerHTML = `
                    <img src="${imageSrc}" alt="${title}" class="book-cover" onerror="this.onerror=null; this.src='images/DefaultBook.jpg';">
                    <div class="book-info">
                        <h4>${bookUrl ? `<a href="${bookUrl}" target="_blank" rel="noopener noreferrer">${title}</a>` : title}</h4>
                        <p class="author">by ${author}</p>
                        <p class="genre">${genre}</p>
                        <p class="details">Year: ${pubYear} | Pages: ${totalPages}</p>
                        <div class="content-status">${contentStatus}</div>
                        ${actionsHTML}
                    </div>`; 
                booksGrid.appendChild(card); 
            }); 
        }

        function viewBook(id) { window.open(`reader.php?id=${id}`, '_blank'); }
        
        function openAddBookModal() { 
            document.getElementById('modalTitle').textContent = 'Add New Book'; 
            document.getElementById('bookForm').reset(); 
            editingBookId = null; 
            document.getElementById('bookModal').style.display = 'flex'; 
        }
        
        function closeBookModal() { 
            document.getElementById('bookModal').style.display = 'none'; 
        }

        function editBook(id) { 
            const book = books.find(b => b.id === id); 
            if (book) { 
                document.getElementById('modalTitle').textContent = 'Edit Book'; 
                document.getElementById('formBookTitle').value = book.title; 
                document.getElementById('formBookAuthor').value = book.author; 
                document.getElementById('formBookGenre').value = book.genre; 
                document.getElementById('formBookCover').value = book.image === 'images/DefaultBook.jpg' ? '' : book.image; 
                document.getElementById('formBookDescription').value = book.description; 
                document.getElementById('formBookContent').value = book.content; 
                document.getElementById('formBookPubYear').value = book.publication_year; 
                document.getElementById('formBookPages').value = book.total_pages; 
                document.getElementById('formBookUrl').value = book.url_book; 
                editingBookId = id; 
                document.getElementById('bookModal').style.display = 'flex'; 
            } 
        }
        
        window.onclick = function(event) { 
            const modal = document.getElementById('bookModal');
            if (event.target == modal) { 
                closeBookModal(); 
            } 
        }

        document.addEventListener('click', function(event) { 
            const navLinks = document.querySelector('.nav-links'); 
            const sidebar = document.querySelector('.sidebar'); 
            const menuToggle = document.querySelector('.menu-toggle i'); 
            if (menuToggle && !sidebar.contains(event.target) && navLinks.classList.contains('active')) { 
                navLinks.classList.remove('active'); 
                menuToggle.className = 'bx bx-menu'; 
            } 
        });

        window.addEventListener('resize', function() { 
            const navLinks = document.querySelector('.nav-links'); 
            const menuToggle = document.querySelector('.menu-toggle i'); 
            if (menuToggle && window.innerWidth > 768 && navLinks.classList.contains('active')) { 
                navLinks.classList.remove('active'); 
                menuToggle.className = 'bx bx-menu'; 
            } 
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            fetchInitialBooks();
        });
    </script>
</body>
</html>