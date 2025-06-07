<?php
// Database connection parameters
$servername = "localhost"; // Or "127.0.0.1" - this is usually the case for XAMPP
$username = "root";        // Default username for XAMPP MySQL
$password = "";            // Default password for XAMPP MySQL is empty
$dbname = "control_data";      // The name of your database

// Create connection using mysqli
$koneksi = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$koneksi) {
    // If connection fails, stop the script and show an error message
    die("Connection failed: " . mysqli_connect_error());
}

// Optional: Set character set to utf8mb4 for better Unicode support
// mysqli_set_charset($koneksi, "utf8mb4");

// You can uncomment the line below for testing if the connection is successful
// echo "Connected successfully to database: " . $dbname;
?>