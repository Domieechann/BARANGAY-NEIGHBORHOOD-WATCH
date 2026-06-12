<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "barangay_watch");
if (!$conn) {
    die("Koneksyon failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['admin_username']);
    $password = mysqli_real_escape_string($conn, $_POST['admin_password']);

    $query = "SELECT * FROM admins WHERE username = '$username' AND password = '$password' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);

        // I-set Active sa DB
        mysqli_query($conn, "UPDATE admins SET status = 'Active' WHERE id = {$admin['id']}");

        $_SESSION['loggedInUser'] = [
            'username'  => $admin['username'],
            'full_name' => $admin['full_name']
        ];
        header("Location: admin_dashboard.html");
        exit();

    } else {
        header("Location: admin-login.html?error=invalid");
        exit();
    }
} else {
    header("Location: admin-login.html");
    exit();
}
?>