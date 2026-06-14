<?php
// get_my_reports.php
// Returns all reports of the currently logged-in user

session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

$host = 'localhost';
$db   = 'barangay_watch';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare(
    "SELECT id, report_type, priority, description, status, created_at 
     FROM reports 
     WHERE user = ? 
     ORDER BY created_at DESC"
);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

$reports = [];
while ($row = $result->fetch_assoc()) {
    $reports[] = $row;
}

echo json_encode(['success' => true, 'reports' => $reports]);

$stmt->close();
$conn->close();
?>
