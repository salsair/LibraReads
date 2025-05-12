<?php
// Start session
session_start();

// Initialize response array
$response = array('success' => false, 'message' => '', 'redirect' => '');

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database configuration
    require_once 'config.php';
    
    // Get form data
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? $_POST['remember'] : false;
    
    // Validate input
    if (empty($email) || empty($password)) {
        $response['message'] = 'Email and password are required';
        echo json_encode($response);
        exit;
    }
    
    // Check if user exists
    $stmt = $conn->prepare("SELECT id, email, full_name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct, create session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['logged_in'] = true;
            
            // Check if the email is 'admin', then redirect to admin page
            if ($user['full_name'] == 'admin') {
                $response['redirect'] = 'admin.html'; // Redirect to admin page
            } else {
                $response['redirect'] = 'homepage.html'; // Redirect to dashboard
            }

            // Set remember-me cookie if requested
            if ($remember === 'true') {
                $token = bin2hex(random_bytes(32)); // Generate a secure token
                
                // Store token in database (you would need a 'remember_tokens' table)
                // This is a simplified example - in production, implement proper token handling
                
                // Set cookie to expire in 30 days
                setcookie('remember_token', $token, time() + (86400 * 30), "/");
            }
            
            $response['success'] = true;
            $response['message'] = 'Login successful!';
        } else {
            $response['message'] = 'Invalid password';
        }
    } else {
        $response['message'] = 'User not found';
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
