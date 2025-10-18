<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.html');
    exit();
}

require_once __DIR__ . '/db.php';

$pdo = getPDO();
$success_msg = '';
$error_msg = '';
$academic_info = [];

// Get academic info
if ($pdo) {
    try {
        $stmt = $pdo->prepare('SELECT course, year, created_at FROM students WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $academic_info = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Academic data fetch error: ' . $e->getMessage());
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course = trim($_POST['course'] ?? '');
    $year = trim($_POST['year'] ?? '');
    $skills = trim($_POST['skills'] ?? '');
    $projects = trim($_POST['projects'] ?? '');
    $achievements = trim($_POST['achievements'] ?? '');

    try {
        $stmt = $pdo->prepare('UPDATE students SET course = ?, year = ? WHERE id = ?');
        $stmt->execute([$course, $year, $_SESSION['user_id']]);
        
        // Store additional info in session or separate table (simplified for demo)
        $_SESSION['academic_skills'] = $skills;
        $_SESSION['academic_projects'] = $projects;
        $_SESSION['academic_achievements'] = $achievements;
        
        $success_msg = 'Academic profile updated successfully!';
        
        // Refresh academic info
        $stmt = $pdo->prepare('SELECT course, year FROM students WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $academic_info = $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        $error_msg = 'Error updating academic profile: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Profile - Talent Track</title>
    <link rel="stylesheet" href="index.css">
    <style>
        .profile-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        .academic-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .info-grid {
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
                <a href="student_dashboard.php">Dashboard</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
                <span style="color: #667eea; margin-left: 15px;">
                    Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                </span>
            </nav>
        </div>
    </header>
    
    <main class="profile-container">
        <div class="academic-card">
            <h1>🎓 Academic Profile</h1>
            <p>Manage your academic information and showcase your skills to employers</p>
            
            <?php if ($success_msg): ?>
                <div class="message success"><?php echo htmlspecialchars($success_msg); ?></div>
            <?php endif; ?>
            
            <?php if ($error_msg): ?>
                <div class="message error"><?php echo htmlspecialchars($error_msg); ?></div>
            <?php endif; ?>
        </div>

        <div class="academic-card">
            <h2>Current Academic Information</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Course/Program</div>
                    <div><?php echo htmlspecialchars($academic_info['course'] ?? 'Not specified'); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Academic Year</div>
                    <div><?php echo htmlspecialchars($academic_info['year'] ? $academic_info['year'] . ' Year' : 'Not specified'); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Member Since</div>
                    <div><?php echo htmlspecialchars($academic_info['created_at'] ?? 'N/A'); ?></div>
                </div>
            </div>
        </div>

        <div class="academic-card">
            <h2>Update Academic Information</h2>
            <form method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="glass-field">
                        <label for="course">Course/Program *</label>
                        <input type="text" id="course" name="course" value="<?php echo htmlspecialchars($academic_info['course'] ?? ''); ?>" required placeholder="e.g., B.Tech Computer Science">
                    </div>

                    <div class="glass-field">
                        <label for="year">Academic Year *</label>
                        <select id="year" name="year" required>
                            <option value="">Select Year</option>
                            <option value="1" <?php echo ($academic_info['year'] ?? '') == 1 ? 'selected' : ''; ?>>1st Year</option>
                            <option value="2" <?php echo ($academic_info['year'] ?? '') == 2 ? 'selected' : ''; ?>>2nd Year</option>
                            <option value="3" <?php echo ($academic_info['year'] ?? '') == 3 ? 'selected' : ''; ?>>3rd Year</option>
                            <option value="4" <?php echo ($academic_info['year'] ?? '') == 4 ? 'selected' : ''; ?>>4th Year</option>
                        </select>
                    </div>
                </div>

                <div class="glass-field">
                    <label for="skills">Technical Skills</label>
                    <textarea id="skills" name="skills" rows="3" placeholder="e.g., Java, Python, Web Development, Data Analysis..."><?php echo htmlspecialchars($_SESSION['academic_skills'] ?? ''); ?></textarea>
                </div>

                <div class="glass-field">
                    <label for="projects">Projects & Experience</label>
                    <textarea id="projects" name="projects" rows="4" placeholder="Describe your academic projects, internships, or work experience..."><?php echo htmlspecialchars($_SESSION['academic_projects'] ?? ''); ?></textarea>
                </div>

                <div class="glass-field">
                    <label for="achievements">Achievements & Certifications</label>
                    <textarea id="achievements" name="achievements" rows="3" placeholder="List your achievements, certifications, awards..."><?php echo htmlspecialchars($_SESSION['academic_achievements'] ?? ''); ?></textarea>
                </div>

                <div class="glass-actions">
                    <button type="submit" class="glass-btn primary">Update Academic Profile</button>
                    <a href="profile.php" class="glass-btn secondary">Back to Profile</a>
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