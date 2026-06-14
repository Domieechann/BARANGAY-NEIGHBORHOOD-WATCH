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

$id = intval($_POST['id'] ?? 0);

if (!$id) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit();
}

// Kunin ang id ng admin na naka-login, kung ano man ang format
// ng $_SESSION['loggedInUser'] (array na may 'id', o plain username string)
$loggedIn = $_SESSION['loggedInUser'];
$adminId  = null;

if (is_array($loggedIn)) {
    // Subukan ang iba't ibang possible keys, depende kung paano
    // na-set ang session sa login.php
    $adminId = $loggedIn['id']
        ?? $loggedIn['admin_id']
        ?? $loggedIn['ID']
        ?? null;

    // Kung hindi pa rin nakuha ang id pero meron tayong username,
    // i-lookup sa admins table
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
    // Plain string (username) ang naka-store sa session
    $stmtAdmin = mysqli_prepare($conn, "SELECT id FROM admins WHERE username = ? LIMIT 1");
    mysqli_stmt_bind_param($stmtAdmin, 's', $loggedIn);
    mysqli_stmt_execute($stmtAdmin);
    $adminResult = mysqli_stmt_get_result($stmtAdmin);
    $admin = mysqli_fetch_assoc($adminResult);
    mysqli_stmt_close($stmtAdmin);
    $adminId = $admin['id'] ?? null;
}

// Debug log - tingnan sa PHP error log kung kinakailangan
error_log('DEBUG loggedInUser: ' . print_r($loggedIn, true));
error_log('DEBUG resolved adminId: ' . print_r($adminId, true));

if (!$adminId) {
    echo json_encode(['success' => false, 'error' => 'Hindi makilala ang admin na naka-login (adminId null). Check error log.']);
    exit();
}

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

$stmt2 = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt2, 'si', $req['new_password'], $req['user_id']);
mysqli_stmt_execute($stmt2);
mysqli_stmt_close($stmt2);

$stmt3 = mysqli_prepare($conn, "UPDATE password_reset_requests SET status = 'Approved', approved_by = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt3, 'ii', $adminId, $id);
mysqli_stmt_execute($stmt3);
mysqli_stmt_close($stmt3);

echo json_encode(['success' => true, 'action' => 'approved']);

mysqli_close($conn);
?>