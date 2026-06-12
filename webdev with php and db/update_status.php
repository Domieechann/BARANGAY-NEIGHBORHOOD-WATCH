<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['loggedInUser'])) {
    echo json_encode(['success' => false]);
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "barangay_watch");
if (!$conn) {
    echo json_encode(['success' => false]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = intval($_POST['id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $allowed = ['Pending', 'In Progress', 'Under Review', 'Resolved'];
    if (!in_array($status, $allowed)) {
        echo json_encode(['success' => false, 'error' => 'Invalid status']);
        exit();
    }

    $query = "UPDATE reports SET status = '$status' WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}

mysqli_close($conn);
?>