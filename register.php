<?php
// register.php

// Start the session to handle user data across pages
session_start();

// Include your database connection file which contains the getPDO() function
require_once __DIR__ . '/db.php';

// Simple helper to sanitize input
function sanitize($data) {
    return trim(htmlspecialchars($data ?? ''));
}

// 1. Check if the form was submitted using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 2. Get and sanitize form data
    $first_name = sanitize($_POST['name'] ?? '');
    $middle_name = sanitize($_POST['middle_name'] ?? '');
    $last_name = sanitize($_POST['last_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['conform_password'] ?? '';
    $role = sanitize($_POST['role'] ?? 'student');

    // 3. Server-Side Validation
    $errors = [];

    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        $errors[] = 'Please fill in all required fields.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }

    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }

    // If there are errors, redirect back with errors
    if (!empty($errors)) {
        header('Location: register.html?error=' . urlencode(implode(', ', $errors)));
        exit();
    }

    // 4. Connect to the database
    $pdo = getPDO();
    if (!$pdo) {
        // If connection fails, redirect with a generic error
        error_log("Database connection failed in register.php");
        header('Location: register.html?error=' . urlencode('Database error. Please try again later.'));
        exit();
    }

    try {
        // 5. Check if the email already exists
        $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            // User with this email already exists
            header('Location: register.html?error=' . urlencode('An account with this email already exists.'));
            exit();
        }

        // 6. Securely hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // 7. Generate a student ID
        $student_id = 'STU' . date('Y') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // 8. Insert the new user into the database
        $sql = "INSERT INTO students (student_id, first_name, middle_name, last_name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$student_id, $first_name, $middle_name, $last_name, $email, $phone, $password_hash, $role]);

        // 9. Redirect to the login page on successful registration
        header('Location: login.html?success=' . urlencode('Registration successful! Please login.'));
        exit();

    } catch (PDOException $e) {
        // Log the detailed error for the admin and show a generic message to the user
        error_log("Registration failed: " . $e->getMessage());
        header('Location: register.html?error=' . urlencode('An unexpected error occurred. Please try again.'));
        exit();
    }

} else {
    // If the page is accessed directly without POST, redirect to the registration form
    header('Location: register.html');
    exit();
}
?>