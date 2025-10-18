<?php
// admin_dashboard.php

require_once __DIR__ . '/session_check.php';
require_admin();

require_once __DIR__ . '/db.php';

$pdo = getPDO();

// Get statistics
$stats = [
    'total_students' => 0,
    'total_events' => 0,
    'pending_applications' => 0,
    'active_jobs' => 0
];

if ($pdo) {
    try {
        // Total students
        $stmt = $pdo->query('SELECT COUNT(*) as count FROM students');
        $stats['total_students'] = $stmt->fetch()['count'];
        
        // Total events
        $stmt = $pdo->query('SELECT COUNT(*) as count FROM events');
        $stats['total_events'] = $stmt->fetch()['count'];
        
        // Pending applications (assuming you have job_applications table)
        $stmt = $pdo->query('SELECT COUNT(*) as count FROM job_applications WHERE status = "pending"');
        $stats['pending_applications'] = $stmt->fetch()['count'];
        
        // Active jobs (assuming you have jobs table)
        $stmt = $pdo->query('SELECT COUNT(*) as count FROM jobs WHERE status = "active"');
        $stats['active_jobs'] = $stmt->fetch()['count'];
        
    } catch (PDOException $e) {
        error_log('Dashboard stats error: ' . $e->getMessage());
    }
}

// Get recent activities
$recent_activities = [];
if ($pdo) {
    try {
        // Get recent events
        $stmt = $pdo->query('SELECT title, event_date, status FROM events ORDER BY created_at DESC LIMIT 5');
        $recent_events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get recent students
        $stmt = $pdo->query('SELECT first_name, last_name, email, created_at FROM students ORDER BY created_at DESC LIMIT 5');
        $recent_students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $recent_activities = [
            'events' => $recent_events,
            'students' => $recent_students
        ];
        
    } catch (PDOException $e) {
        error_log('Recent activities error: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Talent Track</title>
    <link rel="stylesheet" href="index.css">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .welcome-section {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .card.students { border-left: 4px solid #667eea; }
        .card.events { border-left: 4px solid #ff6b6b; }
        .card.reports { border-left: 4px solid #feca57; }
        .card.settings { border-left: 4px solid #48dbfb; }
    </style>
</head>
<body>
    <header>
        <div class="navbar">
            <div class="nav-logo">
                <img src="logo.jpg" alt="logo" style="height: 40px;">
            </div>
            <div class="site-title">Talent Track - Admin Panel</div>
            <div style="margin-left:12px;">
                <div class="searchbar">
                    <img src="logo.jpg" alt="search" style="height: 20px;">
                    <input aria-label="Search students or events" placeholder="Search students or events">
                </div>
            </div>
            <nav class="nav-item">
                <a href="admin_dashboard.php">Dashboard</a>
                <a href="index.html">Home</a>
                <a href="manage_students.php">Manage Students</a>
                <a href="manage_events.php">Manage Events</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
                <span style="color: #ff6b6b; margin-left: 15px;">
                    Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                </span>
            </nav>
        </div>
    </header>
    
    <main class="dashboard-container">
        <div class="welcome-section">
            <h1>Admin Dashboard 👨‍💼</h1>
            <p>Manage students, events, and platform settings</p>
        </div>

        <div class="profile-info" style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
            <h3>Administrator Information</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px;">
                <div><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
                <div><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></div>
                <div><strong>Role:</strong> <?php echo htmlspecialchars(ucfirst($_SESSION['role'])); ?></div>
                <div><strong>Login Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></div>
            </div>
        </div>

        <div class="dashboard-cards">
            <div class="card students">
                <h3>👥 Manage Students</h3>
                <p>View, edit, and manage student accounts and profiles.</p>
                <a href="manage_students.php" style="color: #667eea; text-decoration: none;">Manage →</a>
            </div>
            
            <div class="card events">
                <h3>📅 Manage Events</h3>
                <p>Create and manage events, workshops, and seminars.</p>
                <a href="manage_events.php" style="color: #ff6b6b; text-decoration: none;">Manage →</a>
            </div>
            
            <div class="card reports">
                <h3>📊 Reports & Analytics</h3>
                <p>View platform statistics and generate reports.</p>
                <a href="reports.php" style="color: #feca57; text-decoration: none;">View Reports →</a>
            </div>
            
            <div class="card settings">
                <h3>⚙️ System Settings</h3>
                <p>Configure platform settings and preferences.</p>
                <a href="admin_settings.php" style="color: #48dbfb; text-decoration: none;">Settings →</a>
            </div>
        </div>
    </main>

    <footer style="text-align: center; padding: 20px; margin-top: 50px; background: #f8f9fa;">
        <p>&copy; 2025 Talent Track. All rights reserved.</p>
    </footer>
</body>
</html> 

