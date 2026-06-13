<?php
header('Content-Type: application/json');

$conn = mysqli_connect("localhost", "root", "", "barangay_watch");
if (!$conn) {
    echo json_encode([]);
    exit();
}

$result = mysqli_query($conn, "SELECT id, username, requested_at FROM password_reset_requests WHERE status = 'Pending' ORDER BY requested_at DESC");

$requests = [];
while ($row = mysqli_fetch_assoc($result)) {
    $requests[] = $row;
}

echo json_encode($requests);
mysqli_close($conn);
?>