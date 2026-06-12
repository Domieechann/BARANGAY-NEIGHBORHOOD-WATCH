<?php
// db_config.php
// Database connection settings — adjust to match your XAMPP/hosting setup

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "barangay_watch";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>