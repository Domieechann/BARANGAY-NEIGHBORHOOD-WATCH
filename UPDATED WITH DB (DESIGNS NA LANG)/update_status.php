<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['loggedInUser'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "barangay_watch");
if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'DB connection failed']);
    exit();
}

$id     = intval($_POST['id'] ?? 0);
$status = trim($_POST['status'] ?? '');

$allowed = ['Pending', 'In Progress', 'Under Review', 'Resolved'];
if (!$id || !in_array($status, $allowed)) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit();
}

// Kunin ang id ng admin na naka-login
// (parehong logic gaya ng approve_reset.php / update_resident_status.php)
$loggedIn = $_SESSION['loggedInUser'];
$adminId  = null;

if (is_array($loggedIn)) {
    $adminId = $loggedIn['id']
        ?? $loggedIn['admin_id']
        ?? $loggedIn['ID']
        ?? null;

    if (!$adminId && !empty($loggedIn['username'])) {
        $stmtAdmin = mysqli_prepare($conn, "SELECT id FROM admins WHERE username = ? LIMIT 1");
        mysqli_stmt_bind_param($stmtAdmin, 's', $loggedIn['username']);
        mysqli_stmt_execute($stmtAdmin);
        $adminResult = mysqli_stmt_get_result($stmtAdmin);
        $admin = mysqli_fetch_assoc($adminResult);
        mysqli_stmt_close($stmtAdmin);
        $adminId = $admin['id'] ?? null;
    }
} else {
    $stmtAdmin = mysqli_prepare($conn, "SELECT id FROM admins WHERE username = ? LIMIT 1");
    mysqli_stmt_bind_param($stmtAdmin, 's', $loggedIn);
    mysqli_stmt_execute($stmtAdmin);
    $adminResult = mysqli_stmt_get_result($stmtAdmin);
    $admin = mysqli_fetch_assoc($adminResult);
    mysqli_stmt_close($stmtAdmin);
    $adminId = $admin['id'] ?? null;
}

if (!$adminId) {
    echo json_encode(['success' => false, 'error' => 'Admin not recognized']);
    exit();
}

$stmt = mysqli_prepare($conn, "UPDATE reports SET status = ?, handled_by = ?, updated_at = NOW() WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'sii', $status, $adminId, $id);

if (mysqli_stmt_execute($stmt)) {
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'ID not found: ' . $id]);
    }
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>