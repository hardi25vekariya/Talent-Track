<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

require_once __DIR__ . '/db.php';

$pdo = getPDO();
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_msg = 'All fields are required.';
    } elseif ($new_password !== $confirm_password) {
        $error_msg = 'New passwords do not match.';
    } elseif (strlen($new_password) < 6) {
        $error_msg = 'New password must be at least 6 characters long.';
    } else {
        try {
            // Verify current password
            $stmt = $pdo->prepare('SELECT password_hash FROM students WHERE id = ?');
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($current_password, $user['password_hash'])) {
                // Update password
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE students SET password_hash = ? WHERE id = ?');
                $stmt->execute([$new_password_hash, $_SESSION['user_id']]);
                
                $success_msg = 'Password changed successfully!';
            } else {
                $error_msg = 'Current password is incorrect.';
            }
        } catch (PDOException $e) {
            $error_msg = 'Error changing password: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Talent Track</title>
    <link rel="stylesheet" href="index.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <header>
        <div class="navbar">
            <div class="nav-logo">
                <img src="logo.jpg" alt="logo" style="height: 40px;">
            </div>
            <div class="site-title">Talent Track</div>
            <nav class="nav-item">
                <a href="profile.php">← Back to Profile</a>
                <a href="logout.php">Logout</a>
                <span style="color: #667eea; margin-left: 15px;">
                    Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                </span>
            </nav>
        </div>
    </header>
    
    <main class="form-container">
        <div class="glass-form-wrapper">
            <form class="glass-form" method="POST">
                <h2>Change Password</h2>
                
                <?php if ($success_msg): ?>
                    <div class="message success"><?php echo htmlspecialchars($success_msg); ?></div>
                <?php endif; ?>
                
                <?php if ($error_msg): ?>
                    <div class="message error"><?php echo htmlspecialchars($error_msg); ?></div>
                <?php endif; ?>

                <div class="glass-field">
                    <label for="current_password">Current Password *</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>

                <div class="glass-field">
                    <label for="new_password">New Password *</label>
                    <input type="password" id="new_password" name="new_password" required minlength="6">
                    <small style="color: #666; font-size: 12px;">Must be at least 6 characters long</small>
                </div>

                <div class="glass-field">
                    <label for="confirm_password">Confirm New Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <div class="glass-actions">
                    <button type="submit" class="glass-btn primary">Change Password</button>
                    <a href="profile.php" class="glass-btn secondary">Cancel</a>
                </div>
            </form>
        </div>
    </main>

    <footer style="background: linear-gradient(90deg, #0b2242, #1b2b55); color: #dbeafe; text-align: center; padding: 30px 0; margin-top: 50px;">
        <div style="max-width: 1200px; margin: 0 auto;">
            <h3 style="margin: 0 0 15px 0; color: #fff;">Talent Track - Career Portal</h3>
            <p style="margin: 8px 0; color: #cfe8ff;">&copy; 2025 Talent Track. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>