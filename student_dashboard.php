<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.html');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Talent Track</title>
    <link rel="stylesheet" href="index.css">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .welcome-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            border-left: 4px solid #667eea;
        }
        .profile-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .nav-tabs {
            display: flex;
            background: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .nav-tabs a {
            padding: 10px 20px;
            margin: 0 10px;
            text-decoration: none;
            color: #333;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .nav-tabs a:hover {
            background: #667eea;
            color: white;
        }
        .nav-tabs a.active {
            background: #667eea;
            color: white;
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
                <a href="student_dashboard.php">Dashboard</a>
                <a href="index.html">Home</a>
                <a href="events.php">Events</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
                <span style="color: #667eea; margin-left: 15px;">
                    Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                </span>
            </nav>
        </div>
    </header>
    
    <main class="dashboard-container">
        <div class="welcome-section">
            <h1>Welcome to Your Dashboard, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! 🎓</h1>
            <p>Manage your academic journey and career opportunities</p>
        </div>

        <div class="nav-tabs">
            <a href="student_dashboard.php" class="active">Dashboard</a>
            <a href="profile.php">My Profile</a>
            <a href="events.php">Events & Workshops</a>
            <a href="jobs.php">Job Opportunities</a>
            <a href="settings.php">Settings</a>
        </div>

        <div class="profile-info">
            <h3>Student Information</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px;">
                <div>
                    <strong>Student ID:</strong> <?php echo htmlspecialchars($_SESSION['student_id']); ?>
                </div>
                <div>
                    <strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?>
                </div>
                <div>
                    <strong>Role:</strong> <?php echo htmlspecialchars(ucfirst($_SESSION['role'])); ?>
                </div>
                <div>
                    <strong>Login Time:</strong> <?php echo date('Y-m-d H:i:s'); ?>
                </div>
            </div>
        </div>

        <div class="dashboard-cards">
            <div class="card">
                <h3>📚 Academic Profile</h3>
                <p>View and update your academic information, courses, and achievements.</p>
                <a href="academic_profile.php" style="color: #667eea; text-decoration: none;">View Profile →</a>
            </div>
            
            <div class="card">
                <h3>🎯 Career Opportunities</h3>
                <p>Explore internships, job openings, and career development programs.</p>
                <a href="career.php" style="color: #667eea; text-decoration: none;">Explore →</a>
            </div>
            
            <div class="card">
                <h3>📅 Upcoming Events</h3>
                <p>Check out workshops, seminars, and campus events.</p>
                <a href="events.php" style="color: #667eea; text-decoration: none;">View Events →</a>
            </div>
            
            <div class="card">
                <h3>🏆 Achievements</h3>
                <p>Track your certificates, awards, and accomplishments.</p>
                <a href="achievements.php" style="color: #667eea; text-decoration: none;">View Achievements →</a>
            </div>
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