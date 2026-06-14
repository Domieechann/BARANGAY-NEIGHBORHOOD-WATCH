<?php
// register.php
session_start();
require_once "db_config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ── Get form fields ─────────────────────────────────────────────
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $name     = trim($_POST['name'] ?? '');
    $address  = trim($_POST['address'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $age      = intval($_POST['age'] ?? 0);
    $gender   = trim($_POST['gender'] ?? '');
    $captcha_input = trim($_POST['captcha_input'] ?? '');

    // ── Strict validation: CAPTCHA ───────────────────────────────────
    // Must match the code stored in session by captcha.php, case-insensitive
    if (
        empty($captcha_input) ||
        !isset($_SESSION['captcha_code']) ||
        strcasecmp($captcha_input, $_SESSION['captcha_code']) !== 0
    ) {
        unset($_SESSION['captcha_code']); // force regeneration on next load
        header("Location: registration.html?error=invalid_captcha");
        exit;
    }
    unset($_SESSION['captcha_code']); // one-time use

    // ── HONEYPOT CHECK ────────────────────────────────────────────────
    $honeypot = $_POST['website'] ?? '';
    if ($honeypot !== '') {
        header("Location: registration.html?error=bot");
        exit;
    }

    // ── TIMING CHECK ──────────────────────────────────────────────────
    $elapsed = (int)($_POST['elapsed'] ?? 0);
    if ($elapsed < 1500) {
        header("Location: registration.html?error=bot");
        exit;
    }

    // ── Basic validation ────────────────────────────────────────────
    if (empty($username) || empty($password) || empty($name) || empty($address) ||
        empty($phone) || empty($age) || empty($gender)) {
        header("Location: registration.html?error=missing_fields");
        exit;
    }

    // ── Strict validation: NAME ──────────────────────────────────────
    // Format: "Last Name, First Name Middle Name" — letters, spaces, periods, hyphens, one comma
    if (!preg_match('/^[A-Za-zÀ-ÿñÑ.\-]+(\s+[A-Za-zÀ-ÿñÑ.\-]+)*\s*,\s*[A-Za-zÀ-ÿñÑ.\-]+(\s+[A-Za-zÀ-ÿñÑ.\-]+)*$/u', $name)) {
        header("Location: registration.html?error=invalid_name");
        exit;
    }

    // ── Strict validation: ADDRESS ───────────────────────────────────
    // At least 8 chars, letters/numbers/common punctuation only
    if (strlen($address) < 8 || !preg_match('/^[A-Za-z0-9À-ÿñÑ.,\-#\/\s]+$/u', $address)) {
        header("Location: registration.html?error=invalid_address");
        exit;
    }

    // ── Strict validation: AGE ───────────────────────────────────────
    // Must be a whole number between 13 and 120 (minimum age policy)
    if (!ctype_digit((string)$_POST['age']) || $age < 13 || $age > 120) {
        if ($age >= 1 && $age < 13) {
            header("Location: registration.html?error=underage");
        } else {
            header("Location: registration.html?error=invalid_age");
        }
        exit;
    }

    // ── Strict validation: PHONE ─────────────────────────────────────
    // Philippine mobile format: 09XXXXXXXXX (11 digits, starts with 09)
    if (!preg_match('/^09\d{9}$/', $phone)) {
        header("Location: registration.html?error=invalid_phone");
        exit;
    }

    // ── Handle file upload (verification_id) ───────────────────────
    $verification_id = "";
    if (isset($_FILES['verification_id']) && $_FILES['verification_id']['error'] === UPLOAD_ERR_OK) {

        $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf'];
        $file_tmp    = $_FILES['verification_id']['tmp_name'];
        $file_name   = basename($_FILES['verification_id']['name']);
        $file_ext    = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $file_size   = $_FILES['verification_id']['size'];

        // Validate extension
        if (!in_array($file_ext, $allowed_ext)) {
            header("Location: registration.html?error=invalid_file");
            exit;
        }

        // Validate size (10MB max)
        if ($file_size > 10 * 1024 * 1024) {
            header("Location: registration.html?error=file_too_large");
            exit;
        }

        // Create uploads folder if it doesn't exist
        $upload_dir = "uploads/verification_ids/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Generate unique filename
        $unique_name = "verif_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $file_ext;
        $target_path = $upload_dir . $unique_name;

        if (move_uploaded_file($file_tmp, $target_path)) {
            $verification_id = $target_path;
        } else {
            header("Location: registration.html?error=upload_failed");
            exit;
        }
    } else {
        header("Location: registration.html?error=no_file");
        exit;
    }

    // ── DUPLICATE CHECK: same name + address already registered ──────
    $dupStmt = $conn->prepare("SELECT id FROM users WHERE name = ? AND address = ? LIMIT 1");
    if ($dupStmt) {
        $dupStmt->bind_param("ss", $name, $address);
        $dupStmt->execute();
        $dupStmt->store_result();
        if ($dupStmt->num_rows > 0) {
            $dupStmt->close();
            header("Location: registration.html?error=duplicate_resident");
            exit;
        }
        $dupStmt->close();
    }

    // ── Hash the password ───────────────────────────────────────────
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // ── Default status: Not Verified ────────────────────────────────
    $status = "Not Verified";

    // ── Insert into DB ───────────────────────────────────────────────
    $stmt = $conn->prepare(
        "INSERT INTO users (username, password, name, address, phone, age, gender, verification_id, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    if (!$stmt) {
        header("Location: registration.html?error=db_error");
        exit;
    }

    $stmt->bind_param(
        "sssssisss",
        $username,
        $hashed_password,
        $name,
        $address,
        $phone,
        $age,
        $gender,
        $verification_id,
        $status
    );

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        // ── Redirect to frontpage with success message ────────────
        header("Location: index.html?registered=1");
        exit;
    } else {
        // Likely duplicate username (UNIQUE constraint)
        $stmt->close();
        $conn->close();
        header("Location: registration.html?error=username_taken");
        exit;
    }

} else {
    header("Location: index.html");
    exit;
}
?>