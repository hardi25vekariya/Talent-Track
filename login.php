 <?php
session_start();
require_once __DIR__ . '/db.php'; // db.php file ko include karein

// Simple helper to sanitize input
function sanitize($data) { 
    return trim(htmlspecialchars($data ?? '')); 
}

// Sirf POST request accept karein
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit();
}

// Form se data lein
$email = sanitize($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']) && $_POST['remember'] === 'on';
$role = sanitize($_POST['role'] ?? 'student');

// Check karein ki email aur password khaali na ho
if (!$email || !$password) {
    $msg = rawurlencode('Email and password are required.');
    header('Location: login.html?error=' . $msg);
    exit();
}

// Database se connect karein
$pdo = getPDO();
if (!$pdo) {
    // Agar connection fail hota hai
    error_log('Database connection failed in login.php');
    $msg = rawurlencode('Database connection error. Please try again later.');
    header('Location: login.html?error=' . $msg);
    exit();
}

try {
    // Students table se query karein
    $stmt = $pdo->prepare('SELECT id, student_id, first_name, last_name, email, password_hash, role FROM students WHERE email = ? AND role = ? LIMIT 1');
    $stmt->execute([$email, $role]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // User check karein aur password verify karein
    if ($user && password_verify($password, $user['password_hash'])) {
        
        // Session variables set karein
        session_regenerate_id(true); // Session fixation attack se bachaav
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['student_id'] = $user['student_id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
        $_SESSION['role'] = $user['role'];

        // 'Remember Me' cookie set karein
        if ($remember) {
            setcookie('remember_email', $email, time() + 60*60*24*30, '/');
        } else {
            if (isset($_COOKIE['remember_email'])) {
                setcookie('remember_email', '', time() - 3600, '/');
            }
        }

        // 'last_login' time ko 'students' table mein update karein
        $upd = $pdo->prepare('UPDATE students SET last_login = NOW() WHERE id = ?');
        $upd->execute([$user['id']]);

        // Role ke hisaab se dashboard par redirect karein
        if ($_SESSION['role'] === 'admin') {
            header('Location: admin_dashboard.php');
            exit();
        } else {
            header('Location: student_dashboard.php');
            exit();
        }

    } else {
        // Agar login fail hota hai
        $msg = rawurlencode('Invalid email, password, or role selection.');
        header('Location: login.html?error=' . $msg);
        exit();
    }

} catch (PDOException $e) {
    // Database query mein error
    error_log('Login DB error: ' . $e->getMessage());
    $msg = rawurlencode('An error occurred during login. Please try again.');
    header('Location: login.html?error=' . $msg);
    exit();
}
?> 
<!-- 
<php
// login.php

session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    redirect_to_dashboard();
}

require_once __DIR__ . '/db.php';

// Simple helper to sanitize input
function sanitize($data) { 
    return trim(htmlspecialchars($data ?? '')); 
}

// Redirect based on role
function redirect_to_dashboard() {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: student_dashboard.php');
    }
    exit();
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Sanitize and validate input
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) && $_POST['remember'] === 'on';
    $role = sanitize($_POST['role'] ?? 'student');

    // Validation
    $errors = [];
    
    if (empty($email) || empty($password)) {
        $errors[] = 'Email and password are required.';
    }
    
    if (!validate_email($email)) {
        $errors[] = 'Invalid email format.';
    }
    
    if (empty($errors)) {
        $pdo = getPDO();
        
        if (!$pdo) {
            $errors[] = 'Database connection error. Please try again later.';
        } else {
            try {
                // Prepare SQL statement
                $stmt = $pdo->prepare('SELECT id, student_id, first_name, last_name, email, password_hash, role FROM students WHERE email = ? AND role = ? LIMIT 1');
                $stmt->execute([$email, $role]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // Verify user and password
                if ($user && verify_password($password, $user['password_hash'])) {
                    
                    // Regenerate session ID for security
                    session_regenerate_id(true);
                    
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['student_id'] = $user['student_id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_name'] = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['login_time'] = time();
                    
                    // Set remember me cookie
                    if ($remember) {
                        $cookie_value = base64_encode($email . ':' . time());
                        setcookie('remember_me', $cookie_value, time() + 60*60*24*30, '/', '', false, true);
                    } else {
                        // Clear remember me cookie if exists
                        if (isset($_COOKIE['remember_me'])) {
                            setcookie('remember_me', '', time() - 3600, '/');
                        }
                    }
                    
                    // Update last login time
                    $update_stmt = $pdo->prepare('UPDATE students SET last_login = NOW() WHERE id = ?');
                    $update_stmt->execute([$user['id']]);
                    
                    // Log login activity
                    error_log("User login: {$user['email']} ({$user['role']})");
                    
                    // Redirect to dashboard
                    redirect_to_dashboard();
                    
                } else {
                    $errors[] = 'Invalid email, password, or role selection.';
                }
                
            } catch (PDOException $e) {
                error_log('Login DB error: ' . $e->getMessage());
                $errors[] = 'An error occurred during login. Please try again.';
            }
        }
    }
    
    // If there are errors, redirect back with error message
    if (!empty($errors)) {
        $error_message = implode(' ', $errors);
        header('Location: login.html?error=' . urlencode($error_message));
        exit();
    }
} else {
    // If not POST request, redirect to login page
    header('Location: login.html');
    exit();
}
?> -->