<?php
// require 'vendor/autoload.php';

// // 1. LOAD COMPOSER AUTOLAD
// \Cloudinary\Configuration\Configuration::instance([
//     'cloud' => [
//         'cloud_name' => 'dvta9edmn',      
//         'api_key'    => '299488182638888',
//         'api_secret' => '0c3T0999vt07gg0VRtvkSXCS320', 
//     ],
//     'url' => [
//         'secure' => true 
//     ]
// ]);


// 2. LOAD DATABASE CONFIGURATION
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "librareads";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

?>