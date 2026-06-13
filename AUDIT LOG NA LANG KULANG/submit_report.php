<?php
// submit_report.php
// Handles POST from all three report forms.
session_start();

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

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

$userId = $_SESSION['user_id']; 

function clean($conn, $val) {
    return $conn->real_escape_string(trim(strip_tags($val)));
}

$report_type = clean($conn, $_POST['report_type'] ?? '');
$description = clean($conn, $_POST['description'] ?? '');
$location    = clean($conn, $_POST['location']    ?? '');

// MAP FORM VALUES (normal, medium, critical) TO DB ALLOWED PRIORITIES (Normal, High, Urgent)
$form_priority = strtolower(clean($conn, $_POST['issue_level'] ?? 'normal'));
$priority = 'Normal'; // Ito ang magiging fallback kapag walang nahanap

if ($form_priority === 'medium') {
    $priority = 'High';
} elseif ($form_priority === 'critical') {
    $priority = 'Urgent';
} elseif ($form_priority === 'normal') {
    $priority = 'Normal';
}

if (!$report_type || !$description) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit;
}

$allowed_types = ['Barangay Issue', 'Kapitbahay', 'Official'];
if (!in_array($report_type, $allowed_types)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid report type.']);
    exit;
}

$status     = 'Pending';
$created_at = date('Y-m-d H:i:s');

$stmt = $conn->prepare(
    "INSERT INTO reports (user, report_type, priority, description, location, status, created_at)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);

$stmt->bind_param('issssss', 
    $userId,
    $report_type,
    $priority,
    $description,
    $location,
    $status,
    $created_at
);

try {
    $stmt->execute();
    echo json_encode(['success' => true, 'message' => 'Report submitted successfully.']);
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>