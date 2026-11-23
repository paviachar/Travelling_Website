<?php
// Database Configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travel_db";
$port = 3307; // Configured port in XAMPP (with 3306 fallback)

// Establish MySQLi connection
$conn = @new mysqli($servername, $username, $password, $dbname, $port);

// If port 3307 fails, attempt standard port 3306 fallback
if ($conn->connect_error) {
    $conn = @new mysqli($servername, $username, $password, $dbname, 3306);
    if ($conn->connect_error) {
        die("<div style='font-family:sans-serif;padding:20px;background:#f8d7da;color:#721c24;border-radius:5px;'>
            <h3>Database Connection Failed</h3>
            <p>Error: " . htmlspecialchars($conn->connect_error) . "</p>
            <p>Please ensure MySQL service is started in XAMPP Control Panel.</p>
        </div>");
    }
}

// Set charset to utf8mb4 for universal character support
$conn->set_charset("utf8mb4");
?>