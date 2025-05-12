<?php
// Start session for authentication
session_start();
// Include config.php for database connection
include 'config.php';
// Check if user is logged in (replace with your actual authentication method)
if (!isset($_SESSION['user_id'])) {
// Redirect to login page if not logged in
header("Location: login.html");
exit();
}
// Get user ID from session
$user_id = $_SESSION['user_id'];
// Fetch user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Determine profile picture path
// Default picture for users without profile picture
profilepicturepath=!empty(profile_picture_path = !empty(
profilep​icturep​ath=!empty(user['profile_picture'])
    ? $user['profile_picture']
    : 'images/dashboard/pp.jpg';

// Ensure phone is displayed or set to empty string
$user_phone = $user['phone'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - LibraReads</title>
    <link rel="stylesheet" href="profile.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <div class="dashboard">
        <!-- Top Navigation Bar -->
        <div class="navbar">
            <div class="logo">
                <a href="dashboard.html"><img src="images/LogoLibraReads.png" alt="LibraReads Logo"></a>
            </div>
            <ul>
                <li><a href="homepage.html"><i class='bx bxs-home'></i> Home</a></li>
                <li><a href="catalog.html"><i class='bx bxs-book'></i> Catalog</a></li>
                <li><a href="bookshelf.html"><i class='bx bxs-bookmark'></i> Bookshelf</a></li>
                <li><a href="profile.php"><i class='bx bxs-user'></i> Profile</a></li>
            </ul>
        </div>
    <!-- Main Content Area -->
    <div class="main">
        <div class="container">
            <!-- Sidebar -->
            <div class="sidebar">
                <h3 id="sidebar-name"><?php echo htmlspecialchars($user['full_name']); ?></h3>
                <div class="sidebar-profile-pic">
                    <img id="current-profile-picture" 
                         src="<?php echo htmlspecialchars($profile_picture_path); ?>" 
                         alt="Profile Picture">
                </div>
            </div>

            <!-- Profile Content -->
            <div class="profile-content">
                <h3>PROFILE</h3>
                <hr>
                <form id="profile-form" enctype="multipart/form-data">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" 
                           value="<?php echo htmlspecialchars($user['full_name']); ?>" required>

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($user['email']); ?>" readonly disabled>

                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" 
                           value="<?php echo htmlspecialchars($user_phone); ?>">

                    <label>Profile Picture</label>
                    <div class="profile-upload">
                        <input type="file" id="profile_picture" name="profile_picture" 
                               accept="image/jpeg,image/png,image/gif" class="file-input">
                        <label for="profile_picture" class="file-label">
                            <i class='bx bx-upload'></i> Choose File
                        </label>
                        <span id="file-chosen">No file chosen</span>
                    </div>

                    <div id="message"></div>

                    <button type="submit" class="save-btn">Save Changes</button>
                    <button type="button" class="logout-btn" onclick="logout()">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// File input display logic
const fileInput = document.getElementById('profile_picture');
const fileChosen = document.getElementById('file-chosen');

fileInput.addEventListener('change', function() {
    if (this.files && this.files.length > 0) {
        fileChosen.textContent = this.files[0].name;
    } else {
        fileChosen.textContent = 'No file chosen';
    }
});

// Profile form submission
document.getElementById('profile-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    var formData = new FormData(this);
    var messageDiv = document.getElementById('message');

    axios.post('process_profile.php', formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    })
    .then(function (response) {
        var result = response.data;
        
        if (result.status === 'success') {
            // Success message
            messageDiv.innerHTML = '<p style="color: green;">' + result.message + '</p>';
            
            // Update name in sidebar
            document.getElementById('sidebar-name').textContent = result.full_name;
            
            // Update profile picture if a new one was uploaded
            if (result.profile_picture) {
                document.getElementById('current-profile-picture').src = result.profile_picture;
            }
        } else {
            // Error message
            messageDiv.innerHTML = '<p style="color: red;">' + result.message + '</p>';
        }
    })
    .catch(function (error) {
        console.error('Error:', error);
        messageDiv.innerHTML = '<p style="color: red;">Terjadi kesalahan saat mengirim data</p>';
    });
});

// Logout function
function logout() {
    // You should implement proper logout logic here
    // This might involve destroying the session on the server-side
    alert("Anda telah logout.");
    window.location.href = "login.html";
}
</script>