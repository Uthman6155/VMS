<?php
require_once __DIR__ . '/../functions/auth.php';
requireAuth();

// Check if user has permission to access admin pages
$currentPage = basename($_SERVER['PHP_SELF']);
$adminPages = ['create.php', 'edit.php', 'delete.php', 'generate.php'];

if (in_array($currentPage, $adminPages) && !isAdmin()) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit();
}
?>