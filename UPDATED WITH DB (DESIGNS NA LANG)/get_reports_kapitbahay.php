<?php
// get_reports_kapitbahay.php
include 'db_config.php';

$result = mysqli_query($conn,
    "SELECT * FROM reports WHERE report_type = 'Kapitbahay' ORDER BY created_at DESC"
);

$reports = [];
while ($row = mysqli_fetch_assoc($result)) {
    $reports[] = $row;
}

header('Content-Type: application/json');
echo json_encode($reports);
?>
