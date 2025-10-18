<?php
// session_check.php

session_start();

/**
 * Check if user is logged in and redirect if not
 */
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        // Store the current URL for redirecting after login
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header('Location: login.html');
        exit();
    }
    
    // Check session expiration (30 minutes)
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > 1800) {
        session_destroy();
        header('Location: login.html?error=Session expired. Please login again.');
        exit();
    }
    
    // Update session time
    $_SESSION['login_time'] = time();
}

/**
 * Check if user has specific role
 */
function require_role($required_role) {
    require_login();
    
    if ($_SESSION['role'] !== $required_role) {
        http_response_code(403);
        die('Access denied. Insufficient permissions.');
    }
}

/**
 * Check if user is admin
 */
function require_admin() {
    require_role('admin');
}

/**
 * Get current user info
 */
function get_current_user() {
    if (isset($_SESSION['user_id'])) {
        return [
            'id' => $_SESSION['user_id'],
            'student_id' => $_SESSION['student_id'],
            'email' => $_SESSION['user_email'],
            'name' => $_SESSION['user_name'],
            'role' => $_SESSION['role']
        ];
    }
    return null;
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function is_admin() {
    return is_logged_in() && $_SESSION['role'] === 'admin';
}
?>