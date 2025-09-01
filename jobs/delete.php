<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireAuth();

$db = db();

// Check if ID is provided and valid
if (!isset($_POST['id']) || empty($_POST['id'])) {
    $_SESSION['error_message'] = "No job ID specified for deletion";
    header("Location: index.php");
    exit;
}

$job_id = $db->escape($_POST['id']);

// First, get the job details to log them or use for any other purpose
$jobResult = $db->query("SELECT job_id, title FROM jobs WHERE id = '$job_id'");

if (!$jobResult || $jobResult->num_rows === 0) {
    $_SESSION['error_message'] = "Job not found";
    header("Location: index.php");
    exit;
}

$job = $jobResult->fetch_assoc();
$job_reference = $job['job_id'] . " - " . $job['title'];

// Perform the deletion
$sql = "DELETE FROM jobs WHERE id = '$job_id'";

if ($db->query($sql)) {
    // Optional: Log the deletion action
    // logAction("Deleted job: $job_reference");
    
    $_SESSION['success_message'] = "Job '$job_reference' has been successfully deleted";
} else {
    $_SESSION['error_message'] = "Error deleting job: " . $db->error;
}

// Redirect back to the jobs listing page
header("Location: index.php");
exit;
?>