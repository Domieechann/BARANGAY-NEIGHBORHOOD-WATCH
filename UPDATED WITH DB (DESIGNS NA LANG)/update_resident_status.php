<?php
// update_resident_status.php
session_start();
require_once "db_config.php";

if (!isset($_SESSION['loggedInUser'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id     = intval($_POST['id'] ?? 0);
    $status = trim($_POST['status'] ?? '');

    $allowed_status = ['Not Verified', 'Verified', 'Rejected'];

    if ($id <= 0 || !in_array($status, $allowed_status)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid input"]);
        exit;
    }

    // Kunin ang id ng admin na naka-login
    // (parehong logic gaya ng approve_reset.php)
    $loggedIn = $_SESSION['loggedInUser'];
    $adminId  = null;

    if (is_array($loggedIn)) {
        $adminId = $loggedIn['id']
            ?? $loggedIn['admin_id']
            ?? $loggedIn['ID']
            ?? null;

        if (!$adminId && !empty($loggedIn['username'])) {
            $stmtAdmin = $conn->prepare("SELECT id FROM admins WHERE username = ? LIMIT 1");
            $stmtAdmin->bind_param('s', $loggedIn['username']);
            $stmtAdmin->execute();
            $adminResult = $stmtAdmin->get_result();
            $admin = $adminResult->fetch_assoc();
            $stmtAdmin->close();
            $adminId = $admin['id'] ?? null;
        }
    } else {
        $stmtAdmin = $conn->prepare("SELECT id FROM admins WHERE username = ? LIMIT 1");
        $stmtAdmin->bind_param('s', $loggedIn);
        $stmtAdmin->execute();
        $adminResult = $stmtAdmin->get_result();
        $admin = $adminResult->fetch_assoc();
        $stmtAdmin->close();
        $adminId = $admin['id'] ?? null;
    }

    if (!$adminId) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Hindi makilala ang admin na naka-login"]);
        exit;
    }

    // Kung "Not Verified" ulit ibinalik, i-clear ang verified_by / verified_at
    if ($status === 'Not Verified') {
        $stmt = $conn->prepare("UPDATE users SET status = ?, verified_by = NULL, verified_at = NULL WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
    } else {
        // Verified o Rejected - itala kung sino at kailan
        $stmt = $conn->prepare("UPDATE users SET status = ?, verified_by = ?, verified_at = NOW() WHERE id = ?");
        $stmt->bind_param("sii", $status, $adminId, $id);
    }

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Update failed"]);
    }

    $stmt->close();
    $conn->close();

} else {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
}
?>