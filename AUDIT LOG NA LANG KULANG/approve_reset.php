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
$action = trim($_POST['action'] ?? '');

if (!$id || !in_array($action, ['approve', 'reject'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit();
}

if ($action === 'approve') {
    // Kunin ang new_password at user_id ng request
    $stmt = mysqli_prepare($conn, "SELECT user_id, new_password FROM password_reset_requests WHERE id = ? AND status = 'Pending' LIMIT 1");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $req = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$req) {
        echo json_encode(['success' => false, 'error' => 'Request not found or already processed']);
        exit();
    }

    // I-update ang password ng user
    $stmt2 = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt2, 'si', $req['new_password'], $req['user_id']);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);

    // I-mark ang request as Approved
    $stmt3 = mysqli_prepare($conn, "UPDATE password_reset_requests SET status = 'Approved' WHERE id = ?");
    mysqli_stmt_bind_param($stmt3, 'i', $id);
    mysqli_stmt_execute($stmt3);
    mysqli_stmt_close($stmt3);

    echo json_encode(['success' => true, 'action' => 'approved']);

} else {
    // I-mark lang as Rejected
    $stmt = mysqli_prepare($conn, "UPDATE password_reset_requests SET status = 'Rejected' WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo json_encode(['success' => true, 'action' => 'rejected']);
}

mysqli_close($conn);
?>
