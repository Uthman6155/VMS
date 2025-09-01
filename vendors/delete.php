<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireAuth();
require_once __DIR__ . '/../functions/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    header('Location: index.php');
    exit();
}

$db = db();
$vendorId = $db->escape($_POST['id']);

// First get vendor to check for certifications file
$vendor = $db->query("SELECT certifications_path FROM vendors WHERE id = '$vendorId'")->fetch_assoc();

if ($vendor) {
    // Delete certifications file if exists
    if (!empty($vendor['certifications_path'])) {
        @unlink(UPLOAD_PATH . $vendor['certifications_path']);
    }
    
    // Delete vendor
    $db->query("DELETE FROM vendors WHERE id = '$vendorId'");
}

header('Location: index.php?success=Vendor deleted successfully');
exit();