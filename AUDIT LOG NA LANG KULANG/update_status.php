<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['loggedInUser'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "barangay_watch");
if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'DB connection failed']);
    exit();
}

$id     = intval($_POST['id'] ?? 0);
$status = mysqli_real_escape_string($conn, $_POST['status'] ?? '');

$allowed = ['Pending', 'In Progress', 'Under Review', 'Resolved'];
if (!$id || !in_array($status, $allowed)) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit();
}

$query = "UPDATE reports SET status = '$status' WHERE id = $id";

if (mysqli_query($conn, $query)) {
    if (mysqli_affected_rows($conn) > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'ID not found: ' . $id]);
    }
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
}

mysqli_close($conn);
?>