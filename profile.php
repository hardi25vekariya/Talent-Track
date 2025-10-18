<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

require_once __DIR__ . '/db.php';

$pdo = getPDO();
$user_details = [];

if ($pdo) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM students WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user_details = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Profile data fetch error: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Talent Track</title>
    <link rel="stylesheet" href="index.css">
    <style>
        .profile-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        .profile-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .profile-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        .info-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .action-btn {
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-secondary {
            background: #f8f9fa;
            color: #333;
            border: 2px solid #667eea;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
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
            <div style="margin-left:12px;">
                <div class="searchbar">
                    <img src="logo.jpg" alt="search" style="height: 20px;">
                    <input aria-label="Search jobs or companies" placeholder="Search jobs or companies">
                </div>
            </div>
            <nav class="nav-item">
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="admin_dashboard.php">Dashboard</a>
                <?php else: ?>
                    <a href="student_dashboard.php">Dashboard</a>
                <?php endif; ?>
                <a href="index.php">Home</a>
                <a href="events.php">Events</a>
                <a href="profile.php" style="color: #667eea;">Profile</a>
                <a href="logout.php">Logout</a>
                <span style="color: #667eea; margin-left: 15px;">
                    Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                </span>
            </nav>
        </div>
    </header>
    
    <main class="profile-container">
        <div class="profile-header">
            <h1>My Profile 👤</h1>
            <p>Manage your personal information and career profile</p>
        </div>

        <div class="profile-card">
            <h2>Personal Information</h2>
            <div class="profile-info-grid">
                <div class="info-item">
                    <div class="info-label">Full Name</div>
                    <div><?php echo htmlspecialchars($user_details['first_name'] . ' ' . ($user_details['middle_name'] ? $user_details['middle_name'] . ' ' : '') . $user_details['last_name']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Student ID</div>
                    <div><?php echo htmlspecialchars($user_details['student_id'] ?? 'N/A'); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div><?php echo htmlspecialchars($user_details['email']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Phone</div>
                    <div><?php echo htmlspecialchars($user_details['phone'] ?? 'Not provided'); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Role</div>
                    <div><?php echo htmlspecialchars(ucfirst($user_details['role'])); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Course</div>
                    <div><?php echo htmlspecialchars($user_details['course'] ?? 'Not specified'); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Year</div>
                    <div><?php echo htmlspecialchars($user_details['year'] ?? 'Not specified'); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Last Login</div>
                    <div><?php echo htmlspecialchars($user_details['last_login'] ?? 'Never'); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Member Since</div>
                    <div><?php echo htmlspecialchars($user_details['created_at'] ?? 'N/A'); ?></div>
                </div>
            </div>
        </div>

        <div class="profile-card">
            <h2>Quick Actions</h2>
            <div class="action-buttons">
                <a href="edit_profile.php" class="action-btn btn-primary">✏️ Edit Profile</a>
                <a href="change_password.php" class="action-btn btn-secondary">🔒 Change Password</a>
                <?php if ($_SESSION['role'] === 'student'): ?>
                    <a href="academic_profile.php" class="action-btn btn-success">🎓 Academic Info</a>
                    <a href="my_applications.php" class="action-btn btn-secondary">📋 My Applications</a>
                    <a href="resume_builder.php" class="action-btn btn-primary">📄 Build Resume</a>
                <?php endif; ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="manage_students.php" class="action-btn btn-success">👥 Manage Students</a>
                    <a href="post_job.php" class="action-btn btn-primary">➕ Post Job</a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer style="background: linear-gradient(90deg, #0b2242, #1b2b55); color: #dbeafe; text-align: center; padding: 30px 0; margin-top: 50px;">
        <div style="max-width: 1200px; margin: 0 auto;">
            <h3 style="margin: 0 0 15px 0; color: #fff;">Talent Track - Career Portal</h3>
            <p style="margin: 8px 0; color: #cfe8ff;">Connecting Students with Dream Careers</p>
            <p style="margin: 8px 0; color: #cfe8ff;">Email: support@talenttrack.com • Phone: +91-9313993111</p>
            <p style="margin: 8px 0; color: #cfe8ff;">&copy; 2025 Talent Track. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>