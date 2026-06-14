<?php
// get_reports.php
// Returns all reports as JSON for the admin dashboard.
// Uses an INNER JOIN to map user IDs to actual usernames.
session_start();

$host = 'localhost';
$db   = 'barangay_watch';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([]);
    exit;
}

// RESTORING QUERY WITH USERNAME JOIN PROTECTION
$query = "SELECT 
            r.id, 
            u.username AS user, 
            r.report_type, 
            r.priority, 
            r.description, 
            r.location, 
            r.status, 
            r.created_at
          FROM reports r
          INNER JOIN users u ON r.user = u.id
          ORDER BY r.created_at DESC";

$result = $conn->query($query);
$reports = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($reports);

$conn->close();
?>