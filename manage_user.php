<?php
// Include config file
require_once "config.php";

// Process action
if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    // Delete user
    $id = $_GET['id'];
    $sql = "DELETE FROM users WHERE id = ?";
    
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("i", $id);
        
        if($stmt->execute()){
            header("location: adminusers.php?status=deleted");
            exit();
        } else{
            header("location: adminusers.php?status=error&message=" . urlencode($conn->error));
            exit();
        }
    }
    $stmt->close();
} elseif($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add or update user
    $id = $_POST['userId'] ?? null;
    $name = trim($_POST['userName']);
    $email = trim($_POST['userEmail']);
    $password = trim($_POST['userPassword']);
    $profile = trim($_POST['userProfile']) ?: null;
    
    if(empty($name) || empty($email) || (empty($id) && empty($password))) {
        header("location: adminusers.php?status=error&message=" . urlencode("Please fill all required fields."));
        exit();
    }
    
    if(empty($id)) {
        // Insert new user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (full_name, email, password, profile_picture) VALUES (?, ?, ?, ?)";
        
        if($stmt = $conn->prepare($sql)){
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $profile);
            
            if($stmt->execute()){
                header("location: adminusers.php?status=added");
                exit();
            } else{
                header("location: adminusers.php?status=error&message=" . urlencode($conn->error));
                exit();
            }
        }
    } else {
        // Update existing user
        if(!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET full_name = ?, email = ?, password = ?, profile_picture = ? WHERE id = ?";
            
            if($stmt = $conn->prepare($sql)){
                $stmt->bind_param("ssssi", $name, $email, $hashed_password, $profile, $id);
            }
        } else {
            $sql = "UPDATE users SET full_name = ?, email = ?, profile_picture = ? WHERE id = ?";
            
            if($stmt = $conn->prepare($sql)){
                $stmt->bind_param("sssi", $name, $email, $profile, $id);
            }
        }
        
        if($stmt->execute()){
            header("location: adminusers.php?status=updated");
            exit();
        } else{
            header("location: adminusers.php?status=error&message=" . urlencode($conn->error));
            exit();
        }
    }
    
    $stmt->close();
}

$conn->close();
?>