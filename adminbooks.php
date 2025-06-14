<?php
session_start();

// --- PENJAGA LOGIN ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// --- AKHIR PENJAGA LOGIN ---

require_once 'config.php';


// --- AJAX HANDLER ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];
    $response = ['success' => false, 'message' => 'Invalid action specified.'];

    switch ($action) {
        case 'add_book':
            $title = $_POST['title'] ?? '';
            $author = $_POST['author'] ?? '';
            $genre = $_POST['genre'] ?? '';
            $image = $_POST['image'] ?? 'images/DefaultBook.jpg'; // Corresponds to 'cover_book'
            $description = $_POST['description'] ?? '';
            $content = $_POST['content'] ?? ''; // Kolom content baru
            $publication_year = isset($_POST['publication_year']) && $_POST['publication_year'] !== '' ? intval($_POST['publication_year']) : null;
            $total_pages = isset($_POST['total_pages']) && $_POST['total_pages'] !== '' ? intval($_POST['total_pages']) : null;
            $url_book = $_POST['url_book'] ?? '';

            if (!empty($title) && !empty($author)) {
                $sql = "INSERT INTO books (title, author, genre, cover_book, description, content, publication_year, total_pages, url_book) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("ssssssiss", $title, $author, $genre, $image, $description, $content, $publication_year, $total_pages, $url_book);
                    if ($stmt->execute()) {
                        $new_book_id = $stmt->insert_id;
                        // Mengambil data buku yang baru saja dimasukkan untuk dikirim kembali
                        $fetch_new_book_sql = "SELECT book_id, title, cover_book, author, description, content, genre, publication_year, total_pages, url_book FROM books WHERE book_id = ?";
                        $fetch_stmt = $conn->prepare($fetch_new_book_sql);
                        $fetch_stmt->bind_param("i", $new_book_id);
                        $fetch_stmt->execute();
                        $new_book_result = $fetch_stmt->get_result();
                        $new_book_data = $new_book_result->fetch_assoc();
                        $fetch_stmt->close();

                        $response = [
                            'success' => true,
                            'message' => 'Book added successfully!',
                            'book' => [
                                'id'                => (int)$new_book_data['book_id'],
                                'title'             => $new_book_data['title'] ?? '',
                                'image'             => $new_book_data['cover_book'] ?? 'images/DefaultBook.jpg',
                                'author'            => $new_book_data['author'] ?? '',
                                'genre'             => $new_book_data['genre'] ?? '',
                                'description'       => $new_book_data['description'] ?? '',
                                'content'           => $new_book_data['content'] ?? '',
                                'publication_year'  => $new_book_data['publication_year'] === null ? '' : (int)$new_book_data['publication_year'],
                                'total_pages'       => $new_book_data['total_pages'] === null ? '' : (int)$new_book_data['total_pages'],
                                'url_book'          => $new_book_data['url_book'] ?? '',
                                'has_content'       => !empty($new_book_data['content'])
                            ]
                        ];
                    } else {
                        $response['message'] = 'Database error (Execute Add): ' . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $response['message'] = 'Database error (Prepare Add): ' . $conn->error;
                }
            } else {
                $response['message'] = 'Missing required fields: Title and Author are mandatory.';
            }
            break;

        case 'edit_book':
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $title = $_POST['title'] ?? '';
            $author = $_POST['author'] ?? '';
            $genre = $_POST['genre'] ?? '';
            $image = $_POST['image'] ?? 'images/DefaultBook.jpg';
            $description = $_POST['description'] ?? '';
            $content = $_POST['content'] ?? ''; // Kolom content baru
            $publication_year = isset($_POST['publication_year']) && $_POST['publication_year'] !== '' ? intval($_POST['publication_year']) : null;
            $total_pages = isset($_POST['total_pages']) && $_POST['total_pages'] !== '' ? intval($_POST['total_pages']) : null;
            $url_book = $_POST['url_book'] ?? '';

            if ($id > 0 && !empty($title) && !empty($author)) {
                $sql = "UPDATE books SET title=?, author=?, genre=?, cover_book=?, description=?, content=?, publication_year=?, total_pages=?, url_book=? WHERE book_id=?";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("ssssssissi", $title, $author, $genre, $image, $description, $content, $publication_year, $total_pages, $url_book, $id);
                    if ($stmt->execute()) {
                         // Mengambil data buku yang baru saja diupdate untuk dikirim kembali
                        $fetch_updated_book_sql = "SELECT book_id, title, cover_book, author, description, content, genre, publication_year, total_pages, url_book FROM books WHERE book_id = ?";
                        $fetch_stmt = $conn->prepare($fetch_updated_book_sql);
                        $fetch_stmt->bind_param("i", $id);
                        $fetch_stmt->execute();
                        $updated_book_result = $fetch_stmt->get_result();
                        $updated_book_data = $updated_book_result->fetch_assoc();
                        $fetch_stmt->close();

                        $response = [
                            'success' => true,
                            'message' => 'Book updated successfully!',
                            'book' => [
                                'id'                => (int)$updated_book_data['book_id'],
                                'title'             => $updated_book_data['title'] ?? '',
                                'image'             => $updated_book_data['cover_book'] ?? 'images/DefaultBook.jpg',
                                'author'            => $updated_book_data['author'] ?? '',
                                'genre'             => $updated_book_data['genre'] ?? '',
                                'description'       => $updated_book_data['description'] ?? '',
                                'content'           => $updated_book_data['content'] ?? '',
                                'publication_year'  => $updated_book_data['publication_year'] === null ? '' : (int)$updated_book_data['publication_year'],
                                'total_pages'       => $updated_book_data['total_pages'] === null ? '' : (int)$updated_book_data['total_pages'],
                                'url_book'          => $updated_book_data['url_book'] ?? '',
                                'has_content'       => !empty($updated_book_data['content'])
                            ]
                        ];
                    } else {
                        $response['message'] = 'Database error (Execute Edit): ' . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $response['message'] = 'Database error (Prepare Edit): ' . $conn->error;
                }
            } else {
                $response['message'] = 'Invalid data or missing required fields for updating book.';
            }
            break;

        case 'delete_book':
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            if ($id > 0) {
                $sql = "DELETE FROM books WHERE book_id=?";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("i", $id);
                    if ($stmt->execute()) {
                        $response = ['success' => true, 'message' => 'Book deleted successfully!'];
                    } else {
                        $response['message'] = 'Database error (Execute Delete): ' . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $response['message'] = 'Database error (Prepare Delete): ' . $conn->error;
                }
            } else {
                $response['message'] = 'Invalid Book ID for deletion.';
            }
            break;
    }
    echo json_encode($response);
    $conn->close();
    exit;
}

// --- INITIAL PAGE LOAD - FETCH BOOKS ---
$books_data_for_js = [];
// Ambil semua kolom yang relevan dari database termasuk content
$sql_select_all = "SELECT book_id, title, cover_book, author, description, content, genre, publication_year, total_pages, url_book FROM books ORDER BY title ASC";
$result = $conn->query($sql_select_all);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Siapkan data untuk JavaScript, pastikan konsisten
        $books_data_for_js[] = [
            'id'                => (int)$row['book_id'],
            'title'             => $row['title'] ?? '', 
            'image'             => $row['cover_book'] ?? 'images/DefaultBook.jpg', 
            'author'            => $row['author'] ?? '',
            'genre'             => $row['genre'] ?? '',
            'description'       => $row['description'] ?? '',
            'content'           => $row['content'] ?? '',
            'publication_year'  => $row['publication_year'] === null ? '' : (int)$row['publication_year'], 
            'total_pages'       => $row['total_pages'] === null ? '' : (int)$row['total_pages'], 
            'url_book'          => $row['url_book'] ?? '',
            'has_content'       => !empty($row['content'])
        ];
    }
    $result->free();
} else {
    error_log("Error fetching books: " . $conn->error); 
}
$conn->close(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books Management | LibraReads</title>
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
                        <tbody id="booksTableBody">
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="books-grid" id="booksGrid">
            </section>
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
                    <small class="form-hint">Use HTML tags like &lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;, etc. Leave empty if book is PDF or external link.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeBookModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Save Book</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function logout() {
            if(confirm("Are you sure you want to logout?")) {
                window.location.href = "landingpage.php"; 
            }
        }

        // Data buku diinisialisasi dari PHP
        let books = <?php echo json_encode($books_data_for_js, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>;
        let editingBookId = null; 

        function toggleMobileMenu() {
            const navLinks = document.querySelector('.nav-links');
            const menuToggle = document.querySelector('.menu-toggle i');
            navLinks.classList.toggle('active');
            menuToggle.className = navLinks.classList.contains('active') ? 'bx bx-x' : 'bx bx-menu';
        }

        // Fungsi untuk escape HTML, mencegah XSS
        function escapeHtml(unsafe) {
            if (unsafe === null || typeof unsafe === 'undefined') return '';
            return String(unsafe).replace(/[&<>"']/g, function (match) {
                const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
                return map[match];
            });
        }

        function loadBooks() {
            displayFilteredBooks(books); 
        }

        function filterBooks() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const genreFilter = document.getElementById('genreFilter').value;
            const contentFilter = document.getElementById('contentFilter').value;
            
            const filteredBooks = books.filter(book => {
                const titleMatch = book.title ? book.title.toLowerCase().includes(searchTerm) : false;
                const authorMatch = book.author ? book.author.toLowerCase().includes(searchTerm) : false;
                const genreTextMatch = book.genre ? book.genre.toLowerCase().includes(searchTerm) : false; 
                
                const matchesSearch = titleMatch || authorMatch || genreTextMatch;
                const matchesGenre = !genreFilter || (book.genre && book.genre === genreFilter);
                
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
            
            if (!Array.isArray(booksToDisplay)) booksToDisplay = [];

            booksToDisplay.forEach(book => {
                const imageSrc = book.image || 'images/DefaultBook.jpg'; 
                const title = escapeHtml(book.title);
                const author = escapeHtml(book.author);
                const genre = escapeHtml(book.genre);
                const pubYear = (book.publication_year !== null && book.publication_year !== '') ? escapeHtml(book.publication_year) : '-';
                const totalPages = (book.total_pages !== null && book.total_pages !== '') ? escapeHtml(book.total_pages) : '-';
                const bookUrl = book.url_book ? escapeHtml(book.url_book) : '';
                const hasContent = book.has_content;

                // Content status
                const contentStatus = hasContent ? 
                    '<span class="status-badge status-available">Has Content</span>' : 
                    '<span class="status-badge status-borrowed">No Content</span>';

                // Baris untuk tabel
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="cover-cell">
                        <img src="${escapeHtml(imageSrc)}" alt="${title}" class="book-cover" onerror="this.onerror=null; this.src='images/DefaultBook.jpg';">
                    </td>
                    <td class="title-cell">
                        ${bookUrl ? `<a href="${bookUrl}" target="_blank" rel="noopener noreferrer" title="Visit book page">${title}</a>` : title}
                    </td>
                    <td class="author-cell">${author}</td>
                    <td class="genre-cell">${genre}</td>
                    <td class="year-cell">${pubYear}</td>
                    <td class="pages-cell">${totalPages}</td>
                    <td class="content-cell">${contentStatus}</td>
                    <td class="actions-cell">
                        <div class="action-buttons">
                            <button class="btn-view" onclick="viewBook(${book.id})" title="View/Read">
                                <i class="bx bx-show"></i>
                            </button>
                            <button class="btn-edit" onclick="editBook(${book.id})" title="Edit">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button class="btn-delete" onclick="deleteBook(${book.id})" title="Delete">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>
                    </td>`;
                tableBody.appendChild(row);

                // Kartu untuk grid (tampilan mobile)
                const card = document.createElement('div');
                card.className = 'book-card';
                card.innerHTML = `
                    <img src="${escapeHtml(imageSrc)}" alt="${title}" class="book-cover" onerror="this.onerror=null; this.src='images/DefaultBook.jpg';">
                    <div class="book-info">
                        <h4>${bookUrl ? `<a href="${bookUrl}" target="_blank" rel="noopener noreferrer">${title}</a>` : title}</h4>
                        <p class="author">by ${author}</p>
                        <p class="genre">${genre}</p>
                        <p class="details">Year: ${pubYear} | Pages: ${totalPages}</p>
                        <div class="content-status">${contentStatus}</div>
                        <div class="book-actions">
                            <button class="btn-view" onclick="viewBook(${book.id})" title="View">
                                <i class="bx bx-show"></i>
                            </button>
                            <button class="btn-edit" onclick="editBook(${book.id})" title="Edit">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button class="btn-delete" onclick="deleteBook(${book.id})" title="Delete">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>
                    </div>`;
                booksGrid.appendChild(card);
            });
        }

        function viewBook(id) {
            window.open(`reader.php?id=${id}`, '_blank');
        }

        function openAddBookModal() {
            document.getElementById('modalTitle').textContent = 'Add New Book';
            document.getElementById('bookForm').reset(); 
            editingBookId = null; 
            document.getElementById('bookModal').style.display = 'block';
        }

        function closeBookModal() {
            document.getElementById('bookModal').style.display = 'none';
            editingBookId = null; 
        }

        function editBook(id) {
            const book = books.find(b => b.id === id);
            if (book) {
                document.getElementById('modalTitle').textContent = 'Edit Book';
                document.getElementById('formBookTitle').value = book.title || '';
                document.getElementById('formBookAuthor').value = book.author || '';
                document.getElementById('formBookGenre').value = book.genre || '';
                document.getElementById('formBookCover').value = book.image === 'images/DefaultBook.jpg' ? '' : (book.image || ''); 
                document.getElementById('formBookDescription').value = book.description || '';
                document.getElementById('formBookContent').value = book.content || '';
                document.getElementById('formBookPubYear').value = book.publication_year || '';
                document.getElementById('formBookPages').value = book.total_pages || '';
                document.getElementById('formBookUrl').value = book.url_book || '';
                
                editingBookId = id; 
                document.getElementById('bookModal').style.display = 'block';
            } else {
                console.error("Book not found for ID:", id);
                alert("Error: Book not found for editing.");
            }
        }

        async function saveBook(event) {
            event.preventDefault(); 
            
            const bookData = {
                title: document.getElementById('formBookTitle').value,
                author: document.getElementById('formBookAuthor').value,
                genre: document.getElementById('formBookGenre').value,
                image: document.getElementById('formBookCover').value || 'images/DefaultBook.jpg', 
                description: document.getElementById('formBookDescription').value,
                content: document.getElementById('formBookContent').value,
                publication_year: document.getElementById('formBookPubYear').value,
                total_pages: document.getElementById('formBookPages').value,
                url_book: document.getElementById('formBookUrl').value
            };

            const formData = new FormData();
            let actionType = editingBookId ? 'edit_book' : 'add_book';
            formData.append('action', actionType);

            if (editingBookId) {
                formData.append('id', editingBookId);
            }
            for (const key in bookData) {
                formData.append(key, bookData[key]);
            }

            try {
                const response = await fetch('adminbooks.php', { method: 'POST', body: formData });
                const result = await response.json(); 

                if (result.success) {
                    alert(result.message); 
                    if (actionType === 'add_book' && result.book) {
                        books.push(result.book); 
                    } else if (actionType === 'edit_book' && result.book) {
                        const bookIndex = books.findIndex(b => b.id === editingBookId);
                        if (bookIndex !== -1) {
                            books[bookIndex] = result.book; 
                        }
                    }
                    displayFilteredBooks(books); 
                    closeBookModal(); 
                } else {
                    alert('Operation failed: ' + (result.message || 'Unknown error from server.'));
                }
            } catch (error) {
                console.error('Error saving book:', error);
                alert('An error occurred while saving. Check console for details. Error: ' + error.message);
            }
        }

        async function deleteBook(id) {
            if (confirm('Are you sure you want to delete this book? This action cannot be undone.')) {
                const formData = new FormData();
                formData.append('action', 'delete_book');
                formData.append('id', id);
                try {
                    const response = await fetch('adminbooks.php', { method: 'POST', body: formData });
                    const result = await response.json();
                    if (result.success) {
                        alert(result.message);
                        books = books.filter(book => book.id !== id); 
                        displayFilteredBooks(books); 
                    } else {
                        alert('Deletion failed: ' + (result.message || 'Unknown error from server.'));
                    }
                } catch (error) {
                    console.error('Error deleting book:', error);
                    alert('An error occurred while deleting. Check console for details. Error: ' + error.message);
                }
            }
        }

        // Event listeners
        window.onclick = function(event) {
            const modal = document.getElementById('bookModal');
            if (event.target === modal) {
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
            loadBooks(); 
        });
    </script>
</body>
</html>