<?php
session_start();

// 1. Siguraduhing naka-login ang user
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Kumonekta sa iyong database
$conn = new mysqli("localhost", "root", "", "barangay_watch");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$success_submit = false;
$error_msg = "";

// 3. PROSESO NG PAG-UPDATE SA DATABASE KAPAG HINANDLE NA NG FORM SUBMISSION
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $age = intval($_POST['age']); 
    $birthday = !empty($_POST['dob']) ? $_POST['dob'] : null; 
    $gender = trim($_POST['gender']);
    $civil_status = trim($_POST['civil_status']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $occupation = trim($_POST['occupation']);

    // SQL Update Query
    $update_query = "UPDATE users SET name = ?, age = ?, birthday = ?, gender = ?, civil_status = ?, address = ?, phone = ?, email = ?, occupation = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    
    $stmt->bind_param("sisssssssi", $name, $age, $birthday, $gender, $civil_status, $address, $phone, $email, $occupation, $user_id);
    
    if ($stmt->execute()) {
        $success_submit = true;
    } else {
        $error_msg = "Database Error: " . $conn->error;
    }
}

// 4. KUNIN ANG DATA NG USER PARA MAG-AUTOPOPULATE SA MGA INPUT FIELDS
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

if (!$user_data) {
    $user_data = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <title>Bantay Barangay | Update Personal Info</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background: linear-gradient(145deg, #eef2f7 0%, #d9e2ec 100%);
      font-family: 'Segoe UI', Roboto, 'Helvetica Neue', system-ui, -apple-system, sans-serif;
      line-height: 1.5;
      padding: 2rem 1.5rem;
      min-height: 100vh;
    }

    .portal-container {
      max-width: 880px;
      margin: 0 auto;
      background: #ffffff;
      border-radius: 2rem;
      box-shadow: 0 25px 45px -12px rgba(0, 0, 0, 0.25), 0 4px 12px rgba(0, 0, 0, 0.05);
      overflow: hidden;
    }

    .portal-header {
      background: #020353;
      padding: 1.5rem 2rem;
      border-bottom: 6px solid #f4b942;
    }

    .back-link-wrapper {
      margin-bottom: 0.75rem;
    }

    .back-link {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      color: #f9f3e2;
      text-decoration: none;
      font-weight: 500;
      font-size: 0.9rem;
      background: rgba(0, 0, 0, 0.25);
      padding: 0.4rem 1rem;
      border-radius: 40px;
      transition: all 0.2s ease;
    }

    .back-link:hover {
      background: rgba(0, 0, 0, 0.4);
      transform: translateX(-3px);
      color: white;
    }

    .header-title-section {
      text-align: center;
    }

    .portal-header h2 {
      font-size: 1.9rem;
      font-weight: 700;
      color: white;
    }

    .header-sub {
      font-size: 1rem;
      color: #f9f3e2;
      background: rgba(0, 0, 0, 0.2);
      display: inline-block;
      padding: 0.25rem 1.2rem;
      border-radius: 40px;
      margin-top: 0.6rem;
    }

    .elegant-divider {
      height: 4px;
      background: linear-gradient(90deg, #f4b942, #d9a13b, #f4b942);
    }

    .form-container {
      padding: 2rem;
    }

    .info-form {
      display: flex;
      flex-direction: column;
      gap: 1.4rem;
    }

    .form-row {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .form-group {
      flex: 1;
      min-width: 180px;
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    .form-group-full {
      width: 100%;
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    .form-label {
      font-weight: 700;
      color: #1a2e28;
      font-size: 0.85rem;
    }

    input[type="text"], 
    input[type="number"],
    input[type="tel"],
    input[type="email"],
    input[type="date"],
    select {
      width: 100%;
      padding: 0.8rem 1rem;
      border: 1.5px solid #e2e8e6;
      border-radius: 1rem;
      font-size: 0.9rem;
      background: #fefef7;
      color: #1e2f29;
    }

    input:focus, select:focus {
      outline: none;
      border-color: #f4b942;
      box-shadow: 0 0 0 3px rgba(244, 185, 66, 0.2);
      background: #ffffff;
    }

    /* Locked overlay styling protection */
    input[readonly] {
      background-color: #eef2f5 !important;
      color: #64748b !important;
      border-color: #cbd5e1 !important;
      cursor: not-allowed !important;
    }

    .form-actions {
      display: flex;
      gap: 1rem;
      margin-top: 0.5rem;
    }

    .btn-submit {
      background: #0b3b2f;
      color: white;
      border: none;
      padding: 0.8rem 2rem;
      font-size: 1rem;
      font-weight: 600;
      border-radius: 3rem;
      cursor: pointer;
    }

    .btn-submit:hover {
      background: #1f5e4b;
    }

    .btn-cancel {
      background: transparent;
      border: 1.5px solid #cbdcd5;
      padding: 0.75rem 1.8rem;
      border-radius: 3rem;
      color: #476b5f;
      text-decoration: none;
      text-align: center;
    }

    .portal-footer {
      background: #f8fafc;
      padding: 1.2rem 2rem;
      text-align: center;
      border-top: 1px solid #e2e8f0;
      font-size: 0.8rem;
    }

    /* ===== Modal Overlay ===== */
    .modal-overlay {
      display: none;
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(2, 3, 83, 0.55);
      z-index: 1000;
      align-items: center;
      justify-content: center;
    }

    .modal-box {
      background: #ffffff;
      border-radius: 1.5rem;
      max-width: 460px;
      width: 100%;
      padding: 2.5rem 2rem 2rem;
      text-align: center;
      box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4);
    }

    .modal-icon {
      width: 90px;
      height: 90px;
      margin: 0 auto 1.2rem;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .modal-icon.success-icon {
      border: 5px solid #2c5a4a;
      background: #eef2e7;
    }

    .modal-icon.error-icon {
      border: 5px solid #d93838;
      background: #fde8e8;
    }

    .modal-status-badge {
      display: inline-flex;
      padding: 0.35rem 1rem;
      border-radius: 2rem;
      font-weight: 700;
      font-size: 0.75rem;
      margin-bottom: 1rem;
    }

    .modal-status-badge.success-badge {
      border: 1.5px solid #2c5a4a;
      color: #2c5a4a;
      background: #eef2e7;
    }

    .modal-status-badge.error-badge {
      border: 1.5px solid #d93838;
      color: #d93838;
      background: #fde8e8;
    }

    .modal-title {
      font-size: 1.4rem;
      font-weight: 700;
      margin-bottom: 0.75rem;
    }

    .modal-btn {
      margin-top: 1.5rem;
      color: white;
      border: none;
      padding: 0.75rem 2.5rem;
      font-size: 1rem;
      font-weight: 600;
      border-radius: 3rem;
      cursor: pointer;
    }

    .modal-btn.success-btn { background: #0b3b2f; }
    .modal-btn.alert-btn { background: #d93838; }
  </style>
</head>
<body>

<div class="portal-container">
  <div class="portal-header">
    <div class="back-link-wrapper">
      <a href="homepage.php" class="back-link">← Go Back / Bumalik</a>
    </div>
    <div class="header-title-section">
      <h2>UPDATE PERSONAL INFO</h2>
      <div class="header-sub">Restricted registration fields are permanently locked.</div>
    </div>
  </div>
  <div class="elegant-divider"></div>

  <div class="form-container">
    <form id="updateForm" action="" method="POST" class="info-form">

      <div class="form-group-full">
        <div class="form-label">FULL NAME / BUONG PANGALAN (Locked):</div>
        <input type="text" id="name" name="name" value="<?php echo isset($user_data['name']) ? htmlspecialchars($user_data['name']) : ''; ?>" readonly>
      </div>

      <div class="form-row">
        <div class="form-group">
          <div class="form-label">DATE OF BIRTH / PETSA NG KAPANGANAKAN:</div>
          <input type="date" id="dob" name="dob" max="<?php echo date('Y-m-d'); ?>" value="<?php echo isset($user_data['birthday']) ? htmlspecialchars($user_data['birthday']) : ''; ?>">
        </div>
        <div class="form-group">
          <div class="form-label">AGE / EDAD (Locked from Registration):</div>
          <input type="number" id="age" name="age" value="<?php echo isset($user_data['age']) ? htmlspecialchars($user_data['age']) : ''; ?>" readonly>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <div class="form-label">GENDER / KASARIAN (Locked):</div>
          <input type="text" id="gender_display" value="<?php
            $g = isset($user_data['gender']) ? $user_data['gender'] : '';
            echo htmlspecialchars($g === 'male' ? 'Male / Lalaki' : ($g === 'female' ? 'Female / Babae' : ''));
          ?>" readonly>
          <input type="hidden" name="gender" value="<?php echo htmlspecialchars(isset($user_data['gender']) ? $user_data['gender'] : ''); ?>">
        </div>
        <div class="form-group">
          <div class="form-label">CIVIL STATUS / KATAYUANG SIBIL:</div>
          <select id="civil_status" name="civil_status">
            <option value="">Select / Piliin</option>
            <option value="single" <?php echo (isset($user_data['civil_status']) && $user_data['civil_status'] == 'single') ? 'selected' : ''; ?>>Single</option>
            <option value="married" <?php echo (isset($user_data['civil_status']) && $user_data['civil_status'] == 'married') ? 'selected' : ''; ?>>Married</option>
            <option value="widowed" <?php echo (isset($user_data['civil_status']) && $user_data['civil_status'] == 'widowed') ? 'selected' : ''; ?>>Widowed</option>
            <option value="separated" <?php echo (isset($user_data['civil_status']) && $user_data['civil_status'] == 'separated') ? 'selected' : ''; ?>>Separated</option>
          </select>
        </div>
      </div>

      <div class="form-group-full">
        <div class="form-label">HOME ADDRESS / TIRAHAN (Locked):</div>
        <input type="text" id="address" name="address" value="<?php echo isset($user_data['address']) ? htmlspecialchars($user_data['address']) : ''; ?>" readonly>
      </div>

      <div class="form-row">
        <div class="form-group">
          <div class="form-label">PHONE NUMBER / NUMERO NG TELEPONO:</div>
          <input type="tel" id="phone" name="phone" placeholder="e.g. 09XXXXXXXXX" value="<?php echo isset($user_data['phone']) ? htmlspecialchars($user_data['phone']) : ''; ?>">
        </div>
        <div class="form-group">
          <div class="form-label">EMAIL ADDRESS:</div>
          <input type="email" id="email" name="email" placeholder="youremail@example.com" value="<?php echo isset($user_data['email']) ? htmlspecialchars($user_data['email']) : ''; ?>">
        </div>
      </div>

      <div class="form-group-full">
        <div class="form-label">OCCUPATION / TRABAHO:</div>
        <input type="text" id="occupation" name="occupation" placeholder="Occupation" value="<?php echo isset($user_data['occupation']) ? htmlspecialchars($user_data['occupation']) : ''; ?>">
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-submit">Save Changes</button>
        <a href="homepage.php" class="btn-cancel">Cancel</a>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="customAlertModal">
  <div class="modal-box">
    <div class="modal-icon" id="alertIcon"></div>
    <div class="modal-status-badge" id="alertBadge"></div>
    <div class="modal-title" id="alertTitle"></div>
    <p id="alertTextMsgEN" style="color: #1a2e28; font-size: 1rem; margin-bottom: 0.4rem;"></p>
    <p id="alertTextMsgTL" style="font-size: 0.85rem; color: #6b7c74;"></p>
    <button class="modal-btn" id="alertGotItBtn">OK / Sige</button>
  </div>
</div>

<script>
  const form = document.getElementById('updateForm');
  const alertModal = document.getElementById('customAlertModal');
  const alertGotItBtn = document.getElementById('alertGotItBtn');
  
  const alertIcon = document.getElementById('alertIcon');
  const alertBadge = document.getElementById('alertBadge');
  const alertTitle = document.getElementById('alertTitle');
  const alertTextMsgEN = document.getElementById('alertTextMsgEN');
  const alertTextMsgTL = document.getElementById('alertTextMsgTL');

  const dobInput = document.getElementById('dob');
  const ageInput = document.getElementById('age');
  
  // Kunin ang static value ng nakasave na rehistradong Edad
  const originalRegisteredAge = parseInt(ageInput.value, 10);

  let activeFocusInput = null;
  let isSuccessRedirect = false;

  function showCardAlert(title, msgEN, msgTL, isSuccess = false, targetInput = null) {
    activeFocusInput = targetInput;
    isSuccessRedirect = isSuccess;

    if (isSuccess) {
      alertIcon.className = "modal-icon success-icon";
      alertBadge.className = "modal-status-badge success-badge";
      alertGotItBtn.className = "modal-btn success-btn";
      alertBadge.innerText = "✅ SUCCESS";
      alertIcon.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width:40px; height:40px; stroke:#2c5a4a;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`;
    } else {
      alertIcon.className = "modal-icon error-icon";
      alertBadge.className = "modal-status-badge error-badge";
      alertGotItBtn.className = "modal-btn alert-btn";
      alertBadge.innerText = "⚠️ INVALID INPUT";
      alertIcon.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width:40px; height:40px; stroke:#d93838;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>`;
    }

    alertTitle.innerText = title;
    alertTextMsgEN.innerText = msgEN;
    alertTextMsgTL.innerText = msgTL;
    alertModal.style.display = 'flex';
  }

  alertGotItBtn.addEventListener('click', function () {
    alertModal.style.display = 'none';
    if (isSuccessRedirect) {
      window.location.href = 'homepage.php';
    } else if (activeFocusInput) {
      activeFocusInput.focus();
    }
  });

  // Form Validation Handler
  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const phoneInput = document.getElementById('phone');
    const emailInput = document.getElementById('email');
    const occupationInput = document.getElementById('occupation');

    // 1. Patunayan kung may piniling kaarawan
    if (!dobInput.value) {
      showCardAlert('Missing Birthday', 'Please select your date of birth.', 'Mangyaring piliin ang iyong petsa ng kapanganakan.', false, dobInput);
      return;
    }

    const birthDate = new Date(dobInput.value);
    const today = new Date();
    today.setHours(0,0,0,0);

    if (birthDate >= today) {
      showCardAlert('Invalid Birthday', 'Birth dates cannot be today or in the future.', 'Bawal ang petsa ngayon o sa hinaharap para sa kapanganakan.', false, dobInput);
      return;
    }

    // 2. SMART BIRTHDAY VS AGE MISMATCH CHECK
    let calculatedAge = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
      calculatedAge--;
    }

    // May allowance na +/- 1 taon para sumakto sa birthday month logic ng user
    const ageDifference = Math.abs(calculatedAge - originalRegisteredAge);
    if (ageDifference > 1) {
      showCardAlert(
        'Age & Birthday Mismatch', 
        `The birthday you selected corresponds to ${calculatedAge} years old, which does not match your registered age (${originalRegisteredAge}).`, 
        `Hindi tumutugma ang pinili mong kaarawan sa rehistradong edad mo na (${originalRegisteredAge} taong gulang).`, 
        false, 
        dobInput
      );
      return;
    }

    // 3. Iba pang mga basic validation checks
    const phoneRegex = /^09\d{9}$/;
    if (!phoneRegex.test(phoneInput.value.trim())) {
      showCardAlert('Invalid Phone', 'Please enter a valid 11-digit phone number starting with 09.', 'Mangyaring ilagay ang wastong numero ng mobile (hal. 09123456789).', false, phoneInput);
      return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailInput.value.trim() || !emailRegex.test(emailInput.value.trim())) {
      showCardAlert('Invalid Email', 'Please enter a valid email format.', 'Mangyaring maglagay ng tamang email format (hal. name@example.com).', false, emailInput);
      return;
    }

    if (!occupationInput.value.trim()) {
      showCardAlert('Missing Occupation', 'Please enter your occupation.', 'Mangyaring ilagay ang iyong trabaho o hanapbuhay.', false, occupationInput);
      return;
    }

    // Ipadala sa PHP kapag malinis ang data
    this.submit();
  });

  // PHP Hooks para sa feedback popup cards pagkarefresh
  <?php if ($success_submit): ?>
    showCardAlert('Changes Saved!', 'Your profile information has been successfully updated.', 'Matagumpay na nai-save ang iyong mga bagong impormasyon!', true);
  <?php endif; ?>

  <?php if (!empty($error_msg)): ?>
    showCardAlert('Database Error', '<?php echo addslashes($error_msg); ?>', 'Nagkaroon ng problema sa pagsave sa database.', false);
  <?php endif; ?>
</script>

</body>
</html>