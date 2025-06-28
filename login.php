<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | LibraReads</title>
    <link rel="icon" type="image/png" href="images/LogoLibraReads.png">
    <link rel="stylesheet" href="login.css">
    <script defer src="login.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="wrapper">
        <form id="login-form">
            <h1>Login</h1>
            <div class="input-box">
                <input type="email" id="email" placeholder="Email" required>
                <i class='bx bxs-envelope'></i>
            </div>
            <div class="input-box">
                <input type="password" id="password" placeholder="Password" required>
                <i class='bx bxs-hide' id="togglePassword"></i>
            </div>
            <div class="remember-forgot">
                <label><input type="checkbox" id="remember"> Remember me</label>
                <a href="forgotpassword.php">Forgot password?</a>
            </div>
            <button type="submit" class="btn">Login</button>
            <div class="register-link">
                <p>Don't have an account? <a href="signup.php">Register</a></p>
            </div>
        </form>
    </div>
</body>
</html>