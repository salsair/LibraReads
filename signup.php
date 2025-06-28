<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | Librareads</title>
    <link rel="icon" type="image/png" href="images/LogoLibraReads.png">
    <link rel="stylesheet" href="signup.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="wrapper">
        <form id="signup-form" action="process_signup.php" method="POST">
            <h1>Create Account</h1>
            <div class="input-box">
                <input type="email" id="email" name="email" placeholder="Email" required>
                <i class='bx bxs-envelope'></i>
            </div>
            <div class="input-box">
                <input type="text" id="full-name" name="fullName" placeholder="Full Name" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <i class='bx bxs-hide' id="togglePassword"></i>
            </div>
            <div class="input-box">
                <input type="password" id="confirm-password" name="confirmPassword" placeholder="Confirm Password" required>
                <i class='bx bxs-hide' id="toggleConfirmPassword"></i>
            </div>
            <button type="submit" class="btn">Create Account</button>
            <div class="register-link">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </form>
    </div>

    <script src="signup.js"></script>
</body>
</html>