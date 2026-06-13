<?php
// submit_officials.php
// Form fields: barangay_official, sk_official, tanod, complaint_type, description, incident_date, evidence

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

// Form fields
$barangay_official = clean($conn, $_POST['barangay_official'] ?? '');
$sk_official       = clean($conn, $_POST['sk_official']       ?? '');
$tanod             = clean($conn, $_POST['tanod']             ?? '');
$complaint_type    = clean($conn, $_POST['complaint_type']    ?? '');
$description       = clean($conn, $_POST['description']       ?? '');
$incident_date     = clean($conn, $_POST['incident_date']     ?? '');

// At least one official must be selected
if (!$barangay_official && !$sk_official && !$tanod) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please select at least one official.']);
    exit;
}

// Required fields
if (!$complaint_type || !$description) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit;
}

// Build the reported officials string
$officials = [];
if ($barangay_official) $officials[] = 'Barangay Official: ' . $barangay_official;
if ($sk_official)       $officials[] = 'SK Official: ' . $sk_official;
if ($tanod)             $officials[] = 'Tanod: ' . $tanod;
$officialsStr = implode(', ', $officials);

// Build full description with all details
$fullDescription = "Officials: {$officialsStr} | Complaint: {$complaint_type} | Incident Date: {$incident_date} | Details: {$description}";

// Fixed values for reports table
$report_type = 'Official';
$priority    = 'Normal';
$location    = '';
$status      = 'Pending';
$created_at  = date('Y-m-d H:i:s');

// Handle file upload (optional)
if (!empty($_FILES['evidence']['name'])) {
    $uploadDir = 'uploads/reports/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $ext      = strtolower(pathinfo($_FILES['evidence']['name'], PATHINFO_EXTENSION));
    $filename = uniqid('official_') . '.' . $ext;
    $target   = $uploadDir . $filename;

    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
    if (in_array($ext, $allowed) && $_FILES['evidence']['size'] <= 10 * 1024 * 1024) {
        move_uploaded_file($_FILES['evidence']['tmp_name'], $target);
    }
}

// Insert into reports table
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
