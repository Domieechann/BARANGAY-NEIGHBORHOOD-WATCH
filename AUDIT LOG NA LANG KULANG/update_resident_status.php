<?php
// update_resident_status.php
require_once "db_config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id     = intval($_POST['id'] ?? 0);
    $status = trim($_POST['status'] ?? '');

    $allowed_status = ['Not Verified', 'Verified', 'Rejected'];

    if ($id <= 0 || !in_array($status, $allowed_status)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid input"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);

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