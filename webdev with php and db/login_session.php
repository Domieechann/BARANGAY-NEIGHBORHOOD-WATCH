<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['user_id'], $_SESSION['role'])) {
    echo json_encode([
        'loggedIn' => true,
        'role'     => $_SESSION['role'],
        'username' => $_SESSION['username'] ?? ''
    ]);
} else {
    echo json_encode(['loggedIn' => false]);
}
?>