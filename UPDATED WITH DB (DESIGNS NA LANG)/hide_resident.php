<?php
// hide_resident.php
// Soft-delete: hides a resident from the admin dashboard without removing the DB record.
require_once "db_config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id = intval($_POST['id'] ?? 0);

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid ID"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE users SET is_hidden = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);

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