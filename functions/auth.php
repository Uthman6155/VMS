<?php
// functions/auth.php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/database.php';

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Login user
 * @param string $username
 * @param string $password
 * @return bool
 */
function login($username, $password) {
    $db = db();
    
    // Prevent SQL injection
    $username = $db->escape($username);
    
    // Get user from database
    $result = $db->query("SELECT id, username, password, role FROM users WHERE username = '$username' LIMIT 1");
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify password with password_verify
        if (password_verify($password, $user['password'])) {
            // Set session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Update last login time
            $db->query("UPDATE users SET last_login = NOW() WHERE id = {$user['id']}");
            
            return true;
        }
    }
    
    return false;
}

/**
 * Log out user
 */
function logout() {
    // Unset all session variables
    $_SESSION = array();
    
    // Delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
}

/**
 * Require authentication
 */
function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit();
    }
}

/**
 * Check if user is admin
 * @return bool
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}