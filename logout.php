<!-- <php
session_start();
$_SESSION = array();
session_destroy();
header("location: index.html");
exit;
?> -->

<?php
// logout.php

session_start();

// Log logout activity
if (isset($_SESSION['user_email'])) {
    error_log("User logout: {$_SESSION['user_email']}");
}

// Destroy all session data
$_SESSION = array();

// Delete session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy session
session_destroy();

// Clear remember me cookie
if (isset($_COOKIE['remember_me'])) {
    setcookie('remember_me', '', time() - 3600, '/');
}

// Redirect to login page
header('Location: login.html?success=You have been logged out successfully.');
exit();
?>