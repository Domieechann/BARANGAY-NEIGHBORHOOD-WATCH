<?php
session_start();
header('Content-Type: application/json');

$host    = 'localhost';
$dbname  = 'barangay_watch';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

$dsn     = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    echo json_encode(['success' => false, 'status' => 'invalid']);
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'status' => 'error']);
    exit;
}

// ── CHECK USERS TABLE ──────────────────────────────────────────────────────
try {
    $stmt = $pdo->prepare('SELECT id, username, password, status FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $userRow = $stmt->fetch();
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'status' => 'error']);
    exit;
}

if (!$userRow) {
    echo json_encode(['success' => false, 'status' => 'invalid']);
    exit;
}

if (!password_verify($password, $userRow['password'])) {
    echo json_encode(['success' => false, 'status' => 'invalid']);
    exit;
}

$status = strtolower(trim($userRow['status']));

if ($status === 'not verified') {
    echo json_encode(['success' => false, 'status' => 'pending']);
    exit;
}

if ($status === 'rejected' || $status === 'denied') {
    echo json_encode(['success' => false, 'status' => 'rejected']);
    exit;
}

if ($status === 'verified') {
    $_SESSION['user_id']  = $userRow['id'];
    $_SESSION['username'] = $userRow['username'];
    session_regenerate_id(true);
    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['success' => false, 'status' => 'invalid']);
exit;
?>