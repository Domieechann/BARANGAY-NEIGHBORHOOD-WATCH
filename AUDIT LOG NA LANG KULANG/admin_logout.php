<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "barangay_watch");

if (isset($_SESSION['loggedInUser'])) {
    $username = $_SESSION['loggedInUser']['username'];
    mysqli_query($conn, "UPDATE admins SET status = 'Inactive' WHERE username = '$username'");
}

session_destroy();
header("Location: admin-login.html");
exit();
?>