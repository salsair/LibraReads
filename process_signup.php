<?php
// Initialize response array
$response = array('success' => false, 'message' => '');

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database configuration
    require_once 'config.php';
    
    // Get form data
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $fullName = $_POST['fullName'];
    $password = $_POST['password'];
    
    // Additional validation
    if (empty($email) || empty($fullName) || empty($password)) {
        $response['message'] = 'All fields are required';
        echo json_encode($response);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid email format';
        echo json_encode($response);
        exit;
    }
    
    if (strlen($password) < 8) {
        $response['message'] = 'Password must be at least 8 characters long';
        echo json_encode($response);
        exit;
    }
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $response['message'] = 'Email already exists. Please use a different email or login.';
        echo json_encode($response);
        exit;
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Create user
    $stmt = $conn->prepare("INSERT INTO users (email, full_name, password, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $email, $fullName, $hashedPassword);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Account created successfully!';
    } else {
        $response['message'] = 'Error: ' . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
} else {
    $response['message'] = 'Invalid request method';
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>