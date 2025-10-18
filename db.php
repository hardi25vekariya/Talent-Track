<!-- /*<php
// db.php

// This constant helps other scripts know if the database is configured.
define('DB_CONFIGURED', true);

/**
 * Creates and returns a PDO database connection object.
 * Returns null if the connection fails.
 */
function getPDO() {
    // --- IMPORTANT: Update these details with your database credentials ---
    $host = 'localhost';
    $db   = 'StudentHub'; // Your database name
    $user = 'root';     // Your database username
    $pass = '';         // Your database password
    $charset = 'utf8mb4';

    // Data Source Name (DSN) for the connection string
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

    // Set PDO options for better error handling and fetching
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        // Attempt to create and return the new PDO object
        return new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        // If connection fails, log the error for the administrator
        error_log('Database connection error: ' . $e->getMessage());
        // Return null so the login script knows the connection failed
        return null;
    }
}
?>
*/ -->

<?php
// db.php

// This constant helps other scripts know if the database is configured.
define('DB_CONFIGURED', true);

/**
 * Creates and returns a PDO database connection object.
 * Returns null if the connection fails.
 */
function getPDO() {
    // --- IMPORTANT: Update these details with your database credentials ---
    $host = 'localhost';
    $db   = 'StudentHub'; // Your database name
    $user = 'root';     // Your database username
    $pass = '';         // Your database password
    $charset = 'utf8mb4';

    // Data Source Name (DSN) for the connection string
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

    // Set PDO options for better error handling and fetching
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        // Attempt to create and return the new PDO object
        $pdo = new PDO($dsn, $user, $pass, $options);
        
        // Test connection by running a simple query
        $pdo->query("SELECT 1");
        
        return $pdo;
    } catch (\PDOException $e) {
        // If connection fails, log the error for the administrator
        error_log('Database connection error: ' . $e->getMessage());
        // Return null so the login script knows the connection failed
        return null;
    }
}

/**
 * Sanitize input data
 */
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    return htmlspecialchars(trim(stripslashes($data ?? '')));
}

/**
 * Validate email format
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Hash password
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}
?>
