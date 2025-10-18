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
$resume_data = [];

// Get resume data
if ($pdo) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM students WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $resume_data = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Resume data fetch error: ' . $e->getMessage());
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $skills = trim($_POST['skills'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    $education = trim($_POST['education'] ?? '');
    $projects = trim($_POST['projects'] ?? '');
    $certifications = trim($_POST['certifications'] ?? '');

    // In a real application, you would store this in a separate resume table
    $_SESSION['resume_skills'] = $skills;
    $_SESSION['resume_experience'] = $experience;
    $_SESSION['resume_education'] = $education;
    $_SESSION['resume_projects'] = $projects;
    $_SESSION['resume_certifications'] = $certifications;

    $success_msg = 'Resume updated successfully!';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resume Builder - Talent Track</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <style>
        .resume-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: center;
        }
        .resume-builder {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        .resume-form {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .resume-preview {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 2px solid #667eea;
        }
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .preview-section {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .preview-section h4 {
            color: #667eea;
            margin-bottom: 10px;
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
                    <img src="logo.jpg" alt="logo" style="height: 20px;">
                    <input aria-label="Search jobs or companies" placeholder="Search jobs or companies">
                </div>
            </div>
            <nav class="nav-item">
                <a href="student_dashboard.php">Dashboard</a>
                <a href="job_listings.html">Job Listings</a>
                <a href="my_applications.php">My Applications</a>
                <a href="resume_builder.php" class="active">Resume Builder</a>
                <div class="profile-dropdown">
                    <a href="profile.php" style="color: #667eea;">
                        👤 <?php echo htmlspecialchars($_SESSION['user_name']); ?> ▼
                    </a>
                    <div class="dropdown-content">
                        <a href="profile.php">My Profile</a>
                        <a href="academic_profile.php">Academic Profile</a>
                       
                        <a href="change_password.php">Change Password</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <main class="resume-container">
        <div class="page-header">
            <h1>Professional Resume Builder 📄</h1>
            <p>Create an impressive resume to stand out to employers</p>
        </div>

        <?php if ($success_msg): ?>
            <div class="message success"><?php echo htmlspecialchars($success_msg); ?></div>
        <?php endif; ?>

        <div class="resume-builder">
            <div class="resume-form">
                <h2>Build Your Resume</h2>
                <form method="POST">
                    <div class="glass-field">
                        <label for="skills">Technical Skills *</label>
                        <textarea id="skills" name="skills" rows="4" required placeholder="List your technical skills (e.g., Java, Python, React, SQL)..."><?php echo htmlspecialchars($_SESSION['resume_skills'] ?? ''); ?></textarea>
                    </div>

                    <div class="glass-field">
                        <label for="experience">Work Experience</label>
                        <textarea id="experience" name="experience" rows="4" placeholder="Describe your work experience, internships, or relevant projects..."><?php echo htmlspecialchars($_SESSION['resume_experience'] ?? ''); ?></textarea>
                    </div>

                    <div class="glass-field">
                        <label for="education">Education</label>
                        <textarea id="education" name="education" rows="3" placeholder="Your educational background..."><?php echo htmlspecialchars($_SESSION['resume_education'] ?? ($resume_data['course'] ?? '') . ' - Year ' . ($resume_data['year'] ?? '')); ?></textarea>
                    </div>

                    <div class="glass-field">
                        <label for="projects">Projects</label>
                        <textarea id="projects" name="projects" rows="4" placeholder="Describe your academic or personal projects..."><?php echo htmlspecialchars($_SESSION['resume_projects'] ?? ''); ?></textarea>
                    </div>

                    <div class="glass-field">
                        <label for="certifications">Certifications & Achievements</label>
                        <textarea id="certifications" name="certifications" rows="3" placeholder="List your certifications, awards, or achievements..."><?php echo htmlspecialchars($_SESSION['resume_certifications'] ?? ''); ?></textarea>
                    </div>

                    <div class="glass-actions">
                        <button type="submit" class="glass-btn primary">Save Resume</button>
                        <button type="button" class="glass-btn secondary" onclick="printResume()">Print Resume</button>
                    </div>
                </form>
            </div>

            <div class="resume-preview">
                <h2>Resume Preview</h2>
                <div class="preview-section">
                    <h4>Personal Information</h4>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($resume_data['phone'] ?? 'Not provided'); ?></p>
                </div>

                <div class="preview-section">
                    <h4>Skills</h4>
                    <p><?php echo nl2br(htmlspecialchars($_SESSION['resume_skills'] ?? 'Not specified')); ?></p>
                </div>

                <div class="preview-section">
                    <h4>Experience</h4>
                    <p><?php echo nl2br(htmlspecialchars($_SESSION['resume_experience'] ?? 'Not specified')); ?></p>
                </div>

                <div class="preview-section">
                    <h4>Education</h4>
                    <p><?php echo nl2br(htmlspecialchars($_SESSION['resume_education'] ?? ($resume_data['course'] ?? 'Not specified') . ($resume_data['year'] ? ' - Year ' . $resume_data['year'] : ''))); ?></p>
                </div>

                <div class="preview-section">
                    <h4>Projects</h4>
                    <p><?php echo nl2br(htmlspecialchars($_SESSION['resume_projects'] ?? 'Not specified')); ?></p>
                </div>

                <div class="preview-section">
                    <h4>Certifications</h4>
                    <p><?php echo nl2br(htmlspecialchars($_SESSION['resume_certifications'] ?? 'Not specified')); ?></p>
                </div>
            </div>
        </div>
    </main>

    <footer style="background: linear-gradient(90deg, #0b2242, #1b2b55); color: #dbeafe; text-align: center; padding: 30px 0; margin-top: 50px;">
        <div style="max-width: 1200px; margin: 0 auto;">
            <h3 style="margin: 0 0 15px 0; color: #fff;">Talent Track - Career Portal</h3>
            <p style="margin: 8px 0; color: #cfe8ff;">&copy; 2025 Talent Track. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function printResume() {
            const previewContent = document.querySelector('.resume-preview').innerHTML;
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Resume - <?php echo htmlspecialchars($_SESSION['user_name']); ?></title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .preview-section { margin-bottom: 20px; }
                        h4 { color: #667eea; margin-bottom: 10px; }
                    </style>
                </head>
                <body>
                    <h1>Resume - <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
                    ${previewContent}
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }
    </script>
</body>
</html>