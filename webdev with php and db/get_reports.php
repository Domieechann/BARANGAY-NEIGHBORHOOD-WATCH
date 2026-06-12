<?php
// get_reports.php
// Returns all reports as JSON for the admin dashboard.
// The 'user' column is returned as-is from DB; the frontend labels it "Reportee".

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

// Optional: restrict to admin sessions only
// if (empty($_SESSION['admin'])) { http_response_code(401); echo json_encode([]); exit; }

$result = $conn->query(
    "SELECT id, user, report_type, description, location, status, created_at
     FROM reports
     ORDER BY created_at DESC"
);

$reports = [];
while ($row = $result->fetch_assoc()) {
    $reports[] = $row;
}

header('Content-Type: application/json');
echo json_encode($reports);

$conn->close();
?>
