<?php
require_once __DIR__ . '/functions/auth.php';

// Log the user out
logout();

// Redirect to login page
header('Location: ' . BASE_URL . '/login.php');
exit();