<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to LibraReads</title>
    <link rel="stylesheet" href="prehomepage.css">
</head>
<body>

    <div class="splash-container">
        <div class="splash-content">
            <img src="images/LogoLibraReads.png" alt="LibraReads Logo" class="logo">
            <h1>WELCOME TO LIBRAREADS</h1>
        </div>
    </div>

    <script>
        // Menjalankan fungsi setelah seluruh halaman dimuat
        document.addEventListener("DOMContentLoaded", () => {          
            const splashScreenDuration = 3000;
                setTimeout(() => {
                window.location.href = "landingpage.php";
                }, splashScreenDuration);
            });
    </script>
    
</body>
</html>