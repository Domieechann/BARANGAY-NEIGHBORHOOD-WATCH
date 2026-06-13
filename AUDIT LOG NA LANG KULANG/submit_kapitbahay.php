<?php
// submit_kapitbahay.php

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

// Auth check
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

$userId = $_SESSION['user_id'];

function clean($conn, $val) {
    return $conn->real_escape_string(trim(strip_tags($val)));
}

// Map ng form fields papunta sa reports table columns
$report_type   = 'Kapitbahay'; // fixed value
$priority      = clean($conn, $_POST['issue_level']    ?? 'Normal');
$reported_name = clean($conn, $_POST['reported_name']  ?? '');
$description   = clean($conn, $_POST['description']    ?? '');
$location      = clean($conn, $_POST['address']        ?? '');

// Validate required fields
if (!$description || !$reported_name) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit;
}

// Normalize priority values mula sa form
$priorityMap = [
    'normal'   => 'Normal',
    'medium'   => 'Medium',
    'critical' => 'Critical',
];
$priority = $priorityMap[$priority] ?? 'Normal';

// I-append ang reported name sa description para hindi mawala ang info
$fullDescription = "Subject: {$reported_name} | {$description}";

$status     = 'Pending';
$created_at = date('Y-m-d H:i:s');

// Handle photo upload (optional)
$photoPath = null;
if (!empty($_FILES['photo']['name'])) {
    $uploadDir = 'uploads/reports/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $ext      = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('report_') . '.' . $ext;
    $target   = $uploadDir . $filename;

    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (in_array(strtolower($ext), $allowed) && $_FILES['photo']['size'] <= 5 * 1024 * 1024) {
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
            $photoPath = $target;
        }
    }
}

$stmt = $conn->prepare(
    "INSERT INTO reports (user, report_type, priority, description, location, status, created_at)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param('issssss',
    $userId,
    $report_type,
    $priority,
    $fullDescription,
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
