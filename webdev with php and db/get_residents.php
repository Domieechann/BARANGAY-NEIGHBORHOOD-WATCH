<?php
// get_users.php
require_once "db_config.php";
header('Content-Type: application/json');

// (Optional) ensure only logged-in admins can access this.
// Uncomment if you have session-based admin auth set up:
//
// session_start();
// if (!isset($_SESSION['admin_logged_in'])) {
//     http_response_code(403);
//     echo json_encode(["error" => "Unauthorized"]);
//     exit;
// }

$sql = "SELECT id, name AS full_name, address, phone, age, gender, verification_id, status, created_at FROM users WHERE is_hidden = 0 ORDER BY created_at DESC";
$result = $conn->query($sql);

$users = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

echo json_encode($users);

$conn->close();
?>