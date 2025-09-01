<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/functions/auth.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/dashboard.php');
} else {
    header('Location: ' . BASE_URL . '/login.php');
}
exit();