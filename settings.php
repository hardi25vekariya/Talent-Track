<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

require_once __DIR__ . '/db.php';

$pdo = getPDO();
$user_data = [];
$success_msg = '';
$error_msg = '';

// Get user data
if ($pdo) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM students WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('User data fetch error: ' . $e->getMessage());
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'update_profile':
            $first_name = trim($_POST['first_name'] ?? '');
            $last_name = trim($_POST['last_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $course = trim($_POST['course'] ?? '');
            $year = $_POST['year'] ?? '';
            
            if (empty($first_name) || empty($last_name)) {
                $error_msg = 'First name and last name are required.';
            } else {
                try {
                    $stmt = $pdo->prepare('UPDATE students SET first_name = ?, last_name = ?, phone = ?, course = ?, year = ? WHERE id = ?');
                    $stmt->execute([$first_name, $last_name, $phone, $course, $year, $_SESSION['user_id']]);
                    
                    // Update session
                    $_SESSION['user_name'] = $first_name . ' ' . $last_name;
                    
                    $success_msg = 'Profile updated successfully!';
                    
                    // Refresh user data
                    $stmt = $pdo->prepare('SELECT * FROM students WHERE id = ?');
                    $stmt->execute([$_SESSION['user_id']]);
                    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                } catch (PDOException $e) {
                    $error_msg = 'Error updating profile: ' . $e->getMessage();
                }
            }
            break;
            
        case 'change_password':
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                $error_msg = 'All password fields are required.';
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
            break;
            
        case 'update_preferences':
            $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
            $job_alerts = isset($_POST['job_alerts']) ? 1 : 0;
            $newsletter = isset($_POST['newsletter']) ? 1 : 0;
            $theme = $_POST['theme'] ?? 'light';
            
            // In a real application, you'd store these in a separate preferences table
            $_SESSION['user_preferences'] = [
                'email_notifications' => $email_notifications,
                'job_alerts' => $job_alerts,
                'newsletter' => $newsletter,
                'theme' => $theme
            ];
            
            $success_msg = 'Preferences updated successfully!';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Talent Track</title>
    <link rel="stylesheet" href="index.css">
    <style>
        .settings-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        .settings-tabs {
            display: flex;
            background: white;
            border-radius: 12px;
            padding: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            flex-wrap: wrap;
        }
        .tab-btn {
            padding: 12px 24px;
            border: none;
            background: none;
            cursor: pointer;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            flex: 1;
            min-width: 120px;
        }
        .tab-btn.active {
            background: #667eea;
            color: white;
        }
        .tab-btn:hover:not(.active) {
            background: #f3f4f6;
        }
        .tab-content {
            display: none;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .tab-content.active {
            display: block;
        }
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .preference-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .preference-item:last-child {
            border-bottom: none;
        }
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #667eea;
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        .danger-zone {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 25px;
            margin-top: 30px;
        }
        .account-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }
        .stat-number {
            font-size: 1.8em;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
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
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="admin_dashboard.php">Dashboard</a>
                <?php else: ?>
                    <a href="student_dashboard.php">Dashboard</a>
                    <a href="my_applications.php">My Applications</a>
                <?php endif; ?>
                <a href="jobs.php">Jobs</a>
                <a href="settings.php" class="active">Settings</a>
                <div class="profile-dropdown">
                    <a href="profile.php" style="color: #667eea;">
                        👤 <?php echo htmlspecialchars($_SESSION['user_name']); ?> ▼
                    </a>
                    <div class="dropdown-content">
                        <a href="profile.php">My Profile</a>
                        <a href="resume_builder.php">Resume Builder</a>
                        <a href="settings.php">Settings</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <main class="settings-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1>⚙️ Settings</h1>
            <p>Manage your account preferences and security settings</p>
        </div>

        <!-- Account Statistics -->
        <div class="account-stats">
            <div class="stat-card">
                <div class="stat-number">
                    <?php 
                    if ($_SESSION['role'] === 'student') {
                        echo '0'; // You can add application count logic here
                    } else {
                        echo '12'; // For admin - job posts count
                    }
                    ?>
                </div>
                <div>
                    <?php echo $_SESSION['role'] === 'student' ? 'Applications' : 'Jobs Posted'; ?>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php echo date('M j', strtotime($user_data['created_at'] ?? 'now')); ?>
                </div>
                <div>Member Since</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php echo $user_data['last_login'] ? 'Active' : 'Never'; ?>
                </div>
                <div>Last Login</div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if ($success_msg): ?>
            <div class="message success"><?php echo htmlspecialchars($success_msg); ?></div>
        <?php endif; ?>
        
        <?php if ($error_msg): ?>
            <div class="message error"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>

        <!-- Settings Tabs -->
        <div class="settings-tabs">
            <button class="tab-btn active" onclick="openTab('profile')">👤 Profile</button>
            <button class="tab-btn" onclick="openTab('security')">🔒 Security</button>
            <button class="tab-btn" onclick="openTab('preferences')">🎛️ Preferences</button>
            <button class="tab-btn" onclick="openTab('notifications')">🔔 Notifications</button>
        </div>

        <!-- Profile Tab -->
        <div id="profile" class="tab-content active">
            <h2>Profile Settings</h2>
            <p style="color: #6b7280; margin-bottom: 25px;">Update your personal information and academic details.</p>
            
            <form method="POST">
                <input type="hidden" name="action" value="update_profile">
                
                <div class="form-grid">
                    <div class="glass-field">
                        <label for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" 
                               value="<?php echo htmlspecialchars($user_data['first_name'] ?? ''); ?>" required>
                    </div>
                    <div class="glass-field">
                        <label for="last_name">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" 
                               value="<?php echo htmlspecialchars($user_data['last_name'] ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="glass-field">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>" readonly 
                           style="background: #f3f4f6;" disabled>
                    <small style="color: #6b7280;">Email cannot be changed. Contact support for email updates.</small>
                </div>
                
                <div class="form-grid">
                    <div class="glass-field">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>">
                    </div>
                    <div class="glass-field">
                        <label for="course">Course/Program</label>
                        <input type="text" id="course" name="course" 
                               value="<?php echo htmlspecialchars($user_data['course'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="glass-field">
                    <label for="year">Academic Year</label>
                    <select id="year" name="year">
                        <option value="">Select Year</option>
                        <option value="1" <?php echo ($user_data['year'] ?? '') == 1 ? 'selected' : ''; ?>>1st Year</option>
                        <option value="2" <?php echo ($user_data['year'] ?? '') == 2 ? 'selected' : ''; ?>>2nd Year</option>
                        <option value="3" <?php echo ($user_data['year'] ?? '') == 3 ? 'selected' : ''; ?>>3rd Year</option>
                        <option value="4" <?php echo ($user_data['year'] ?? '') == 4 ? 'selected' : ''; ?>>4th Year</option>
                    </select>
                </div>
                
                <div class="glass-actions">
                    <button type="submit" class="glass-btn primary">Update Profile</button>
                </div>
            </form>
        </div>

        <!-- Security Tab -->
        <div id="security" class="tab-content">
            <h2>Security Settings</h2>
            <p style="color: #6b7280; margin-bottom: 25px;">Manage your password and account security.</p>
            
            <form method="POST">
                <input type="hidden" name="action" value="change_password">
                
                <div class="glass-field">
                    <label for="current_password">Current Password *</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                
                <div class="glass-field">
                    <label for="new_password">New Password *</label>
                    <input type="password" id="new_password" name="new_password" required minlength="6">
                    <small style="color: #6b7280;">Password must be at least 6 characters long.</small>
                </div>
                
                <div class="glass-field">
                    <label for="confirm_password">Confirm New Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="glass-actions">
                    <button type="submit" class="glass-btn primary">Change Password</button>
                </div>
            </form>
            
            <!-- Security Status -->
            <div style="margin-top: 40px;">
                <h3>Security Status</h3>
                <div class="preference-item">
                    <div>
                        <strong>Last Password Change</strong>
                        <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 14px;">
                            <?php echo $user_data['last_login'] ? date('M j, Y', strtotime($user_data['last_login'])) : 'Never'; ?>
                        </p>
                    </div>
                    <span style="color: #10b981; font-weight: 600;">✓ Secure</span>
                </div>
                
                <div class="preference-item">
                    <div>
                        <strong>Two-Factor Authentication</strong>
                        <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 14px;">
                            Add an extra layer of security to your account
                        </p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Preferences Tab -->
        <div id="preferences" class="tab-content">
            <h2>Preferences</h2>
            <p style="color: #6b7280; margin-bottom: 25px;">Customize your experience on Talent Track.</p>
            
            <form method="POST">
                <input type="hidden" name="action" value="update_preferences">
                
                <h3 style="margin-bottom: 20px;">Theme Settings</h3>
                <div class="glass-field">
                    <label for="theme">Theme Preference</label>
                    <select id="theme" name="theme">
                        <option value="light">Light Mode</option>
                        <option value="dark">Dark Mode</option>
                        <option value="auto">Auto (System)</option>
                    </select>
                </div>
                
                <h3 style="margin: 30px 0 20px 0;">Job Preferences</h3>
                <div class="glass-field">
                    <label for="preferred_location">Preferred Job Location</label>
                    <input type="text" id="preferred_location" name="preferred_location" 
                           placeholder="e.g., Bangalore, Remote">
                </div>
                
                <div class="glass-field">
                    <label for="job_types">Preferred Job Types</label>
                    <select id="job_types" name="job_types[]" multiple style="height: 100px;">
                        <option value="full_time">Full Time</option>
                        <option value="part_time">Part Time</option>
                        <option value="internship">Internship</option>
                        <option value="contract">Contract</option>
                        <option value="remote">Remote</option>
                    </select>
                    <small style="color: #6b7280;">Hold Ctrl/Cmd to select multiple options</small>
                </div>
                
                <div class="glass-actions">
                    <button type="submit" class="glass-btn primary">Save Preferences</button>
                </div>
            </form>
        </div>

        <!-- Notifications Tab -->
        <div id="notifications" class="tab-content">
            <h2>Notification Settings</h2>
            <p style="color: #6b7280; margin-bottom: 25px;">Control how and when you receive notifications.</p>
            
            <form method="POST">
                <input type="hidden" name="action" value="update_preferences">
                
                <h3 style="margin-bottom: 20px;">Email Notifications</h3>
                
                <div class="preference-item">
                    <div>
                        <strong>Job Alerts</strong>
                        <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 14px;">
                            Get notified about new job opportunities
                        </p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="job_alerts" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                
                <div class="preference-item">
                    <div>
                        <strong>Application Updates</strong>
                        <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 14px;">
                            Receive updates on your job applications
                        </p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="email_notifications" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                
                <div class="preference-item">
                    <div>
                        <strong>Newsletter</strong>
                        <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 14px;">
                            Weekly career tips and industry insights
                        </p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="newsletter">
                        <span class="slider"></span>
                    </label>
                </div>
                
                <div class="glass-actions">
                    <button type="submit" class="glass-btn primary">Save Notification Settings</button>
                </div>
            </form>
        </div>

        <!-- Danger Zone -->
        <div class="danger-zone">
            <h3 style="color: #dc2626; margin-top: 0;">⚠️ Danger Zone</h3>
            <p style="color: #7f1d1d; margin-bottom: 20px;">
                Once you delete your account, there is no going back. Please be certain.
            </p>
            <button class="glass-btn" style="background: #dc2626; color: white;" 
                    onclick="if(confirm('Are you sure you want to delete your account? This action cannot be undone.')) { alert('Account deletion requested.'); }">
                Delete My Account
            </button>
        </div>
    </main>

    <footer style="background: linear-gradient(90deg, #0b2242, #1b2b55); color: #dbeafe; text-align: center; padding: 40px 0; margin-top: 60px;">
        <div style="max-width: 1200px; margin: 0 auto;">
            <h3 style="margin: 0 0 20px 0; color: #fff;">Talent Track - Career Portal</h3>
            <p style="margin: 20px 0 0 0; color: #cfe8ff; border-top: 1px solid #334; padding-top: 20px;">
                &copy; 2025 Talent Track. All rights reserved.
            </p>
        </div>
    </footer>

    <script>
        // Tab functionality
        function openTab(tabName) {
            // Hide all tab contents
            const tabContents = document.getElementsByClassName('tab-content');
            for (let i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove('active');
            }
            
            // Remove active class from all tab buttons
            const tabButtons = document.getElementsByClassName('tab-btn');
            for (let i = 0; i < tabButtons.length; i++) {
                tabButtons[i].classList.remove('active');
            }
            
            // Show the specific tab content and activate the button
            document.getElementById(tabName).classList.add('active');
            event.currentTarget.classList.add('active');
        }

        // Password strength indicator
        document.getElementById('new_password')?.addEventListener('input', function(e) {
            const password = e.target.value;
            const strength = calculatePasswordStrength(password);
            const indicator = document.getElementById('password-strength') || createPasswordStrengthIndicator();
            
            indicator.textContent = `Password strength: ${strength}`;
            indicator.className = `password-strength ${strength.toLowerCase()}`;
        });

        function calculatePasswordStrength(password) {
            if (password.length === 0) return 'None';
            if (password.length < 6) return 'Weak';
            if (password.length < 8) return 'Fair';
            if (/[A-Z]/.test(password) && /[0-9]/.test(password) && /[^A-Za-z0-9]/.test(password)) {
                return 'Strong';
            }
            return 'Good';
        }

        function createPasswordStrengthIndicator() {
            const indicator = document.createElement('div');
            indicator.id = 'password-strength';
            indicator.style.marginTop = '5px';
            indicator.style.fontSize = '12px';
            document.getElementById('new_password').parentNode.appendChild(indicator);
            return indicator;
        }

        // Auto-save preferences
        let saveTimeout;
        document.querySelectorAll('input, select').forEach(element => {
            element.addEventListener('change', function() {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => {
                    // Auto-save logic can be implemented here
                    console.log('Preferences changed - ready to save');
                }, 1000);
            });
        });
    </script>
</body>
</html>