<?php
// Start the session
session_start();

// Include config.php to connect to the database
include 'config.php';

// Ensure the user is logged in by checking the session
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the login page
    header("Location: login.php");
    exit;
}

// Get the user_id from the session
$user_id = $_SESSION['user_id'];  // Retrieve the user_id from the active session

// Fetch user data based on the user_id
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Determine the path to the profile picture
$profile_picture_path = $user['profile_picture'] ? $user['profile_picture'] : 'images/dashboard/pp.jpg';

// Handle name change when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_full_name = $_POST['full_name'];

    // Update full name in the database
    $sql = "UPDATE users SET full_name = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_full_name, $user_id);
    if ($stmt->execute()) {
        header("Location: profile.php");
        exit;
    } else {
        echo "Error: Failed to update name.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | LibraReads</title>
    <link rel="stylesheet" href="profile.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard">
            <!-- Top Navigation Bar -->
            <aside class="navbar">
                <div class="logo">
                    <a href="homepage.php"><img src="images/LogoLibraReads.png" alt="LibraReads Logo"></a>
                </div>
                
                <ul class="nav-links">
                    <li><a href="homepage.php"><i class='bx bxs-home'></i> Home</a></li>
                    <li><a href="catalog.php"><i class='bx bxs-book'></i> Catalog</a></li>
                    <li><a href="mybooks.php"><i class='bx bxs-bookmark'></i> Bookshelf</a></li>
                    <li><a href="profile.php" class="active"><i class='bx bxs-user'></i> Profile</a></li>
                </ul>
                
                <div class="menu-toggle">
                    <i class='bx bx-menu'></i>
                </div>
            </aside>

        <!-- Main Content Area -->
        <div class="main">
            <div class="container">
                <!-- Sidebar -->
                <div class="sidebar">
                    <h3 id="sidebar-name"><?php echo htmlspecialchars($user['full_name']); ?></h3>
                    <!-- Display profile picture and option to change photo -->
                    <div class="sidebar-profile-pic">
                        <img id="sidebar-picture" src="<?php echo $profile_picture_path; ?>" alt="Profile Picture">
                        <a href="upload_profile.php" class="custom-upload">
                            <i class='bx bx-camera'></i> Change Photo
                        </a>
                    </div>
                </div>

                <!-- Profile Content -->
                <div class="profile-content">
                    <h3>PROFILE</h3>
                    <hr>
                    <form id="profile-form" method="POST" action="profile.php">
                        <label>Full Name</label>
                        <input type="text" id="full-name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>

                        <label>Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly disabled>

                        <button type="submit" class="save-btn">Save Changes</button>
                        <button type="button" class="logout-btn" onclick="logout()">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Logout function to redirect to login.php
    function logout() {
        alert("You have logged out.");
        window.location.href = "landingpage.php"; // Redirect to the login page
    }

    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.querySelector('.menu-toggle');
        const navLinks = document.querySelector('.nav-links');

        // Toggle mobile menu
        menuToggle.addEventListener('click', function(e) {
            e.stopPropagation(); // Mencegah event bubbling
            navLinks.classList.toggle('active');
            
            // Toggle icon menu
            const icon = menuToggle.querySelector('i');
            if (navLinks.classList.contains('active')) {
                icon.classList.remove('bx-menu');
                icon.classList.add('bx-x');
            } else {
                icon.classList.remove('bx-x');
                icon.classList.add('bx-menu');
            }
        });

        // Close menu when clicking on a link
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', function() {
                navLinks.classList.remove('active');
                const icon = menuToggle.querySelector('i');
                icon.classList.remove('bx-x');
                icon.classList.add('bx-menu');
            });
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.sidebar') && !event.target.closest('.nav-links')) {
                navLinks.classList.remove('active');
                const icon = menuToggle.querySelector('i');
                icon.classList.remove('bx-x');
                icon.classList.add('bx-menu');
            }
        });
    });
    </script>
</body>
</html>