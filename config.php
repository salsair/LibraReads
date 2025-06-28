<?php

$servername = "localhost";
$username = "librare1_librareads";
$password = "libra999reads";
$dbname = "librare1_librareads";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// if needed
$conn->set_charset("utf8mb4");

?>