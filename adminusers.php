<?php
session_start();

// --- PENJAGA LOGIN ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// --- AKHIR PENJAGA LOGIN ---

require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users | LibraReads</title>
    <link rel="stylesheet" href="adminuser.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>

    <!-- Top Navigation -->
    <nav class="sidebar">
        <div class="logo">
            <img src="images/LogoLibraReads.png" alt="LibraReads">
        </div>
        
        <ul class="nav-links">
            <li><a href="admindashboard.php"><i class='bx bxs-home'></i>Dashboard</a></li>
            <li><a href="adminbooks.php"><i class="bx bx-book"></i>Books</a></li>
            <li><a href="adminusers.php" class="active"><i class="bx bx-user"></i>Users</a></li>
            <li><a href="adminevents.php"><i class='bx bx-calendar-event'></i>Events</a></li>
            <li><a href="adminsettings.php"><i class="bx bx-cog"></i>Settings</a></li>
            <li><a href="#" onclick="logout()" class="logout-link"><i class="bx bx-log-out"></i>Logout</a></li>
        </ul>
        
        <button class="menu-toggle" onclick="toggleMobileMenu()">
            <i class="bx bx-menu"></i>
        </button>
    </nav>

    <!-- Main Content -->
    <main class="main">
        <div class="content">
            <header class="page-header">
                <h2>Users Management</h2>
                <button class="add-user-btn" onclick="openAddUserModal()">
                    <i class="bx bx-plus"></i>
                    Add User
                </button>
            </header>

            <!-- Search and Filter Section -->
            <section class="search-filter-section">
                <div class="search-bar">
                    <i class="bx bx-search"></i>
                    <input type="text" id="searchInput" placeholder="Search users by name or email..." onkeyup="filterUsers()">
                </div>
            </section>

            <!-- Users Table -->
            <section class="users-table-section">
                <div class="table-container">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Profile</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <?php
                            // Include config file
                            require_once "config.php";
                            
                            // Attempt select query execution
                            $sql = "SELECT id, full_name, email, profile_picture FROM users";
                            if($result = $conn->query($sql)){
                                if($result->num_rows > 0){
                                    while($row = $result->fetch_assoc()){
                                        echo "<tr>";
                                        echo "<td class='id-cell'>" . $row['id'] . "</td>";
                                        echo "<td class='profile-cell'><img src='" . ($row['profile_picture'] ? $row['profile_picture'] : 'images/dashboard/default.jpg') . "' alt='" . $row['full_name'] . "' class='user-profile'></td>";
                                        echo "<td class='name-cell'>" . $row['full_name'] . "</td>";
                                        echo "<td class='email-cell'>" . $row['email'] . "</td>";
                                        echo "<td class='actions-cell'>
                                                <div class='action-buttons'>
                                                    <button class='btn-edit' onclick='editUser(" . $row['id'] . ")' title='Edit'>
                                                        <i class='bx bx-edit'></i>
                                                    </button>
                                                    <button class='btn-delete' onclick='deleteUser(" . $row['id'] . ")' title='Delete'>
                                                        <i class='bx bx-trash'></i>
                                                    </button>
                                                </div>
                                              </td>";
                                        echo "</tr>";
                                    }
                                    // Free result set
                                    $result->free();
                                } else{
                                    echo "<tr><td colspan='5'>No users found.</td></tr>";
                                }
                            } else{
                                echo "<tr><td colspan='5'>ERROR: Could not able to execute $sql. " . $conn->error . "</td></tr>";
                            }
                            
                            // Close connection
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

    <!-- Add/Edit User Modal -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add New User</h3>
                <span class="close" onclick="closeUserModal()">&times;</span>
            </div>
            <form id="userForm" action="manage_user.php" method="POST">
                <input type="hidden" id="userId" name="userId">
                <div class="form-row">
                    <div class="form-group">
                        <label for="userName">Full Name *</label>
                        <input type="text" id="userName" name="userName" required>
                    </div>
                    <div class="form-group">
                        <label for="userEmail">Email *</label>
                        <input type="email" id="userEmail" name="userEmail" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="userPassword">Password *</label>
                        <input type="password" id="userPassword" name="userPassword" required>
                    </div>
                    <div class="form-group">
                        <label for="userProfile">Profile Image</label>
                        <input type="text" id="userProfile" name="userProfile" placeholder="images/dashboard/profile.jpg">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeUserModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Save User</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function logout() {
            alert("You have logged out.");
            window.location.href = "landingpage.php"; // Redirect to the login page
        }

        let editingUserId = null;

        // Toggle Mobile Menu Function
        function toggleMobileMenu() {
            const navLinks = document.querySelector('.nav-links');
            const menuToggle = document.querySelector('.menu-toggle i');
            
            navLinks.classList.toggle('active');
            
            if (navLinks.classList.contains('active')) {
                menuToggle.className = 'bx bx-x';
            } else {
                menuToggle.className = 'bx bx-menu';
            }
        }

        // Filter users
        function filterUsers() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#usersTableBody tr');

            rows.forEach(row => {
                const name = row.querySelector('.name-cell').textContent.toLowerCase();
                const email = row.querySelector('.email-cell').textContent.toLowerCase();
                
                if (name.includes(searchTerm) || email.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Modal functions
        function openAddUserModal() {
            document.getElementById('modalTitle').textContent = 'Add New User';
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            editingUserId = null;
            document.getElementById('userModal').style.display = 'flex';
        }

        function closeUserModal() {
            document.getElementById('userModal').style.display = 'none';
            editingUserId = null;
        }

        function editUser(id) {
            // Find the row with the matching ID
            const rows = document.querySelectorAll('#usersTableBody tr');
            let userData = null;
            
            rows.forEach(row => {
                if (parseInt(row.querySelector('.id-cell').textContent) === id) {
                    userData = {
                        id: id,
                        name: row.querySelector('.name-cell').textContent,
                        email: row.querySelector('.email-cell').textContent,
                        profile: row.querySelector('.profile-cell img').src
                    };
                }
            });
            
            if (userData) {
                document.getElementById('modalTitle').textContent = 'Edit User';
                document.getElementById('userId').value = userData.id;
                document.getElementById('userName').value = userData.name;
                document.getElementById('userEmail').value = userData.email;
                document.getElementById('userProfile').value = userData.profile;
                document.getElementById('userPassword').required = false;
                editingUserId = id;
                document.getElementById('userModal').style.display = 'block';
            }
        }

        function deleteUser(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                window.location.href = 'manage_user.php?action=delete&id=' + id;
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('userModal');
            if (event.target === modal) {
                closeUserModal();
            }
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const navLinks = document.querySelector('.nav-links');
            const sidebar = document.querySelector('.sidebar');
            
            if (!sidebar.contains(event.target) && navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
                document.querySelector('.menu-toggle i').className = 'bx bx-menu';
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            const navLinks = document.querySelector('.nav-links');
            
            if (window.innerWidth > 768 && navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
                document.querySelector('.menu-toggle i').className = 'bx bx-menu';
            }
        });

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            closeUserModal();
        });
    </script>

</body>
</html>