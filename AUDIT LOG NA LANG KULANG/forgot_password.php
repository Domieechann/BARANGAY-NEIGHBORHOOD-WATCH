<?php
session_start();
header('Content-Type: application/json');

$conn = mysqli_connect("localhost", "root", "", "barangay_watch");
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'DB connection failed']);
    exit();
}

$username     = trim($_POST['username'] ?? '');
$new_password = $_POST['new_password'] ?? '';

if (!$username || !$new_password) {
    echo json_encode(['success' => false, 'message' => 'Kumpletuhin ang lahat ng fields.']);
    exit();
}

if (strlen($new_password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password too short.']);
    exit();
}

// Hanapin ang user
$stmt = mysqli_prepare($conn, "SELECT id, username FROM users WHERE username = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user) {
    echo json_encode(['success' => false, 'status' => 'not_found']);
    exit();
}

// Tignan kung may pending na request na
$stmt2 = mysqli_prepare($conn, "SELECT id FROM password_reset_requests WHERE user_id = ? AND status = 'Pending' LIMIT 1");
mysqli_stmt_bind_param($stmt2, 'i', $user['id']);
mysqli_stmt_execute($stmt2);
$result2 = mysqli_stmt_get_result($stmt2);
$existing = mysqli_fetch_assoc($result2);
mysqli_stmt_close($stmt2);

if ($existing) {
    echo json_encode(['success' => false, 'status' => 'pending']);
    exit();
}

// I-hash ang bagong password
$hashed = password_hash($new_password, PASSWORD_DEFAULT);

// I-save ang request
$stmt3 = mysqli_prepare($conn, "INSERT INTO password_reset_requests (user_id, username, new_password) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt3, 'iss', $user['id'], $user['username'], $hashed);

if (mysqli_stmt_execute($stmt3)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Hindi na-save ang request.']);
}

mysqli_stmt_close($stmt3);
mysqli_close($conn);
?>
