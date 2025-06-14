<?php
// --- BAGIAN 1: LOGIKA BACKEND PHP ---
session_start();
require_once "config.php";

// Logika untuk MEMPROSES FORM (Create/Update)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title'])) {
    
    $event_id = $_POST['event_id'];
    $title = trim($_POST['title']);
    $image_url = trim($_POST['image_url']);
    $event_date = $_POST['event_date'];
    $registration_link = trim($_POST['registration_link']);

    // Validasi dasar (tanpa description)
    if (empty($title) || empty($image_url) || empty($event_date)) {
        $_SESSION['status_message'] = "Error: Please fill all required fields.";
        $_SESSION['status_type'] = "error";
    } else {
        // Proses UPDATE jika ada event_id
        if (!empty($event_id)) {
            $sql = "UPDATE events SET title = ?, image_url = ?, event_date = ?, registration_link = ? WHERE event_id = ?";
            if ($stmt = $conn->prepare($sql)) {
                // bind_param string disesuaikan (s s s i)
                $stmt->bind_param("ssssi", $title, $image_url, $event_date, $registration_link, $event_id);
                if ($stmt->execute()) {
                    $_SESSION['status_message'] = "Event updated successfully.";
                } else {
                    $_SESSION['status_message'] = "Error: Could not update event. " . $stmt->error;
                    $_SESSION['status_type'] = "error";
                }
                $stmt->close();
            }
        } 
        // Proses INSERT jika tidak ada event_id
        else {
            $sql = "INSERT INTO events (title, image_url, event_date, registration_link) VALUES (?, ?, ?, ?)";
            if ($stmt = $conn->prepare($sql)) {
                 // bind_param string disesuaikan (s s s)
                $stmt->bind_param("ssss", $title, $image_url, $event_date, $registration_link);
                if ($stmt->execute()) {
                    $_SESSION['status_message'] = "New event added successfully.";
                } else {
                    $_SESSION['status_message'] = "Error: Could not add new event. " . $stmt->error;
                    $_SESSION['status_type'] = "error";
                }
                $stmt->close();
            }
        }
    }
    
    header("location: adminevents.php");
    exit;
}

// Logika untuk MENGHAPUS event
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $event_id = $_GET['id'];
    $sql = "DELETE FROM events WHERE event_id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $event_id);
        if ($stmt->execute()) {
            $_SESSION['status_message'] = "Event deleted successfully.";
        } else {
            $_SESSION['status_message'] = "Error: Could not delete event.";
            $_SESSION['status_type'] = "error";
        }
        $stmt->close();
    }
    header("location: adminevents.php");
    exit;
}

// --- BAGIAN 2: TAMPILAN FRONTEND HTML ---
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events | LibraReads Admin</title>
    <link rel="stylesheet" href="adminevents.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>

    <nav class="sidebar">
        <div class="logo">
            <a href="admindashboard.php"><img src="images/LogoLibraReads.png" alt="LibraReads"></a>
        </div>
        
        <ul class="nav-links">
            <li><a href="admindashboard.php"><i class='bx bxs-dashboard'></i>Dashboard</a></li>
            <li><a href="adminbooks.php"><i class="bx bx-book"></i>Books</a></li>
            <li><a href="adminusers.php"><i class="bx bx-user"></i>Users</a></li>
            <li><a href="adminevents.php" class="active"><i class='bx bx-calendar-event'></i>Events</a></li>
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
                <h2>Events Management</h2>
                <button class="add-item-btn" onclick="openModal()">
                    <i class="bx bx-plus"></i> Add Event
                </button>
            </header>

            <?php
            // Tampilkan notifikasi status jika ada
            if(isset($_SESSION['status_message'])){
                $status_type = isset($_SESSION['status_type']) ? $_SESSION['status_type'] : '';
                echo '<div class="status-notification ' . $status_type . '">' . $_SESSION['status_message'] . '</div>';
                unset($_SESSION['status_message']);
                unset($_SESSION['status_type']);
            }
            ?>

            <section class="search-filter-section">
                <div class="search-bar">
                    <i class="bx bx-search"></i>
                    <input type="text" id="searchInput" placeholder="Search events by title..." onkeyup="filterItems()">
                </div>
            </section>

            <section class="items-table-section">
                <div class="table-container">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Event Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <?php
                            // DIPERBAIKI: Query SELECT tanpa description dan diurutkan berdasarkan event_id
                            $sql_select = "SELECT event_id, title, image_url, event_date, registration_link FROM events ORDER BY event_id ASC";
                            $result = $conn->query($sql_select);
                            if($result && $result->num_rows > 0){
                                while($row = $result->fetch_assoc()){
                                    // DIPERBAIKI: data-description dihapus
                                    $data_attributes = "data-id='{$row['event_id']}' " .
                                                       "data-title='" . htmlspecialchars($row['title'], ENT_QUOTES) . "' " .
                                                       "data-image_url='" . htmlspecialchars($row['image_url'], ENT_QUOTES) . "' " .
                                                       "data-event_date='{$row['event_date']}' " .
                                                       "data-registration_link='" . htmlspecialchars($row['registration_link'], ENT_QUOTES) . "'";
                                    
                                    echo "<tr>";
                                        echo "<td>{$row['event_id']}</td>";
                                        echo "<td><img src='../{$row['image_url']}' alt='Event Image' class='item-image'></td>";
                                        echo "<td class='title-cell'>{$row['title']}</td>";
                                        echo "<td>" . date("d M Y", strtotime($row['event_date'])) . "</td>";
                                        echo "<td>
                                                <div class='action-buttons'>
                                                    <button class='btn-edit' onclick='openModal(this)' {$data_attributes} title='Edit'><i class='bx bx-edit'></i></button>
                                                    <a href='adminevents.php?action=delete&id={$row['event_id']}' class='btn-delete' onclick='return confirm(\"Are you sure you want to delete this event?\")' title='Delete'><i class='bx bx-trash'></i></a>
                                                </div>
                                              </td>";
                                    echo "</tr>";
                                }
                                $result->free();
                            } else {
                                echo "<tr><td colspan='5' style='text-align:center;'>No events found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

    <div id="itemModal" class="modal">
        <div class="modal-content">
            <form id="itemForm" action="adminevents.php" method="POST">
                <div class="modal-header">
                    <h3 id="modalTitle">Add New Event</h3>
                    <span class="close" onclick="closeModal()">&times;</span>
                </div>
                
                <div style="padding: 25px;">
                    <input type="hidden" id="eventId" name="event_id">

                    <div class="form-group">
                        <label for="eventTitle">Event Title *</label>
                        <input type="text" id="eventTitle" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="eventDate">Event Date *</label>
                        <input type="date" id="eventDate" name="event_date" required>
                    </div>

                    <div class="form-group">
                        <label for="imageUrl">Image URL *</label>
                        <input type="text" id="imageUrl" name="image_url" placeholder="e.g., images/event_name.jpg" required>
                    </div>

                    <div class="form-group">
                        <label for="regLink">Registration Link</label>
                        <input type="url" id="regLink" name="registration_link" placeholder="https://example.com/register">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Save Event</button>
                </div>
            </form>
        </div>
    </div>

<script>
    function logout() {
            alert("You have logged out.");
            window.location.href = "landingpage.php"; // Redirect to the login page
        }
        
    function toggleMobileMenu() { document.querySelector('.nav-links').classList.toggle('active'); }

    const modal = document.getElementById('itemModal');
    const form = document.getElementById('itemForm');
    const modalTitle = document.getElementById('modalTitle');

    function openModal(button = null) {
        form.reset();
        document.getElementById('eventId').value = '';
        if (button) { // Edit mode
            modalTitle.textContent = 'Edit Event';
            const data = button.dataset;
            document.getElementById('eventId').value = data.id;
            document.getElementById('eventTitle').value = data.title;
            document.getElementById('eventDate').value = data.event_date;
            document.getElementById('imageUrl').value = data.image_url;
            document.getElementById('regLink').value = data.registration_link;
            // DIPERBAIKI: baris untuk description dihapus
        } else { // Add mode
            modalTitle.textContent = 'Add New Event';
        }
        modal.style.display = 'flex';
    }

    function closeModal() { modal.style.display = 'none'; }
    function filterItems() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#itemsTableBody tr');
        rows.forEach(row => {
            const title = row.querySelector('.title-cell').textContent.toLowerCase();
            row.style.display = title.includes(searchTerm) ? '' : 'none';
        });
    }

    window.onclick = function(event) { if (event.target === modal) { closeModal(); } }
</script>

</body>
</html>
<?php
$conn->close();
?>