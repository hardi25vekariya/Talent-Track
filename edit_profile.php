<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

require_once __DIR__ . '/db.php';

$pdo = getPDO();
$user_details = [];
$success_msg = '';
$error_msg = '';

// Get current user data
if ($pdo) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM students WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user_details = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Profile data fetch error: ' . $e->getMessage());
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $year = trim($_POST['year'] ?? '');

    if (empty($first_name) || empty($last_name)) {
        $error_msg = 'First name and last name are required.';
    } else {
        try {
            $stmt = $pdo->prepare('UPDATE students SET first_name = ?, middle_name = ?, last_name = ?, phone = ?, course = ?, year = ? WHERE id = ?');
            $stmt->execute([$first_name, $middle_name, $last_name, $phone, $course, $year, $_SESSION['user_id']]);
            
            // Update session name
            $_SESSION['user_name'] = trim($first_name . ' ' . ($middle_name ? $middle_name . ' ' : '') . $last_name);
            
            $success_msg = 'Profile updated successfully!';
            
            // Refresh user details
            $stmt = $pdo->prepare('SELECT * FROM students WHERE id = ?');
            $stmt->execute([$_SESSION['user_id']]);
            $user_details = $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $error_msg = 'Error updating profile: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Talent Track</title>
    <link rel="stylesheet" href="index.css">
    <style>
        .form-container {
            max-width: 800px;
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
                <h2>Edit Profile</h2>
                
                <?php if ($success_msg): ?>
                    <div class="message success"><?php echo htmlspecialchars($success_msg); ?></div>
                <?php endif; ?>
                
                <?php if ($error_msg): ?>
                    <div class="message error"><?php echo htmlspecialchars($error_msg); ?></div>
                <?php endif; ?>

                <div class="glass-field">
                    <label for="first_name">First Name *</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user_details['first_name'] ?? ''); ?>" required>
                </div>

                <div class="glass-field">
                    <label for="middle_name">Middle Name</label>
                    <input type="text" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($user_details['middle_name'] ?? ''); ?>">
                </div>

                <div class="glass-field">
                    <label for="last_name">Last Name *</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user_details['last_name'] ?? ''); ?>" required>
                </div>

                <div class="glass-field">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user_details['phone'] ?? ''); ?>">
                </div>

                <div class="glass-field">
                    <label for="course">Course/Program</label>
                    <input type="text" id="course" name="course" value="<?php echo htmlspecialchars($user_details['course'] ?? ''); ?>" placeholder="e.g., B.Tech Computer Science">
                </div>

                <div class="glass-field">
                    <label for="year">Academic Year</label>
                    <select id="year" name="year">
                        <option value="">Select Year</option>
                        <option value="1" <?php echo ($user_details['year'] ?? '') == 1 ? 'selected' : ''; ?>>1st Year</option>
                        <option value="2" <?php echo ($user_details['year'] ?? '') == 2 ? 'selected' : ''; ?>>2nd Year</option>
                        <option value="3" <?php echo ($user_details['year'] ?? '') == 3 ? 'selected' : ''; ?>>3rd Year</option>
                        <option value="4" <?php echo ($user_details['year'] ?? '') == 4 ? 'selected' : ''; ?>>4th Year</option>
                    </select>
                </div>

                <div class="glass-actions">
                    <button type="submit" class="glass-btn primary">Update Profile</button>
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