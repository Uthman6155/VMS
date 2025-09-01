<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../functions/database.php';

if ($_SERVER['REQUEST_METHOD'] || !isset($_POST['id'])) {
    header('Location: index.php');
    exit();
}

$db = db();
$reportId = $db->escape($_POST['id']);

// First get report to check for file
$report = $db->query("SELECT file_path FROM reports WHERE id = '$reportId'")->fetch_assoc();

if ($report) {
    // Delete report file if exists
    if (!empty($report['file_path'])) {
        @unlink(UPLOAD_PATH . $report['file_path']);
    }
    
    // Delete report
    $db->query("DELETE FROM reports WHERE id = '$reportId'");
}

header('Location: index.php?success=Report deleted successfully');
exit();
?>