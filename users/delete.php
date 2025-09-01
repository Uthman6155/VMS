<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireAuth();
require_once __DIR__ . '/../functions/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    header('Location: index.php');
    exit();
}

$db = db();
$userId = $db->escape($_POST['id']);

// Prevent deleting own account
if ($userId == $_SESSION['user_id']) {
    header('Location: index.php?error=You cannot delete your own account');
    exit();
}

// Delete user
$db->query("DELETE FROM users WHERE id = '$userId'");

header('Location: index.php?success=User deleted successfully');
exit();
?>