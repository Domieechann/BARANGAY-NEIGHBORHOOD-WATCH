<?php
// submit_report.php
// Handles POST from all three report forms:
//   report_barangay_issue.html  → report_type = 'Barangay Issue'
//   report_kapitbahay.html      → report_type = 'Kapitbahay'
//   report_official.html        → report_type = 'Official'
//
// DB columns: id | user | report_type | description | location | status | created_at

session_start();

// ── DB connection ─────────────────────────────────────────────────────────────
$host = 'localhost';
$db   = 'barangay_watch';
$user = 'root';       // change to your MySQL user
$pass = '';           // change to your MySQL password

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

// ── Auth check ────────────────────────────────────────────────────────────────
// Expects the resident's username / user_id stored in session after login.
// Adjust the session key to match your login system (e.g. $_SESSION['username']).
if (empty($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

$sessionUser = $_SESSION['username']; // stored in 'user' column in reports table

// ── Sanitize inputs ───────────────────────────────────────────────────────────
function clean($conn, $val) {
    return $conn->real_escape_string(trim(strip_tags($val)));
}

$report_type = clean($conn, $_POST['report_type'] ?? '');
$description = clean($conn, $_POST['description'] ?? '');
$location    = clean($conn, $_POST['location']    ?? '');

// Validate required fields
if (!$report_type || !$description) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit;
}

// Allowed report types
$allowed_types = ['Barangay Issue', 'Kapitbahay', 'Official'];
if (!in_array($report_type, $allowed_types)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid report type.']);
    exit;
}

// Default status on submission
$status     = 'Pending';
$created_at = date('Y-m-d H:i:s');

// ── Insert into reports ───────────────────────────────────────────────────────
$stmt = $conn->prepare(
    "INSERT INTO reports (user, report_type, description, location, status, created_at)
     VALUES (?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param('ssssss',
    $sessionUser,
    $report_type,
    $description,
    $location,
    $status,
    $created_at
);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Report submitted successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save report.']);
}

$stmt->close();
$conn->close();
?>
