<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talent Track - Career Portal</title>
    <link rel="stylesheet" href="index.css">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 20px;
            text-align: center;
        }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            padding: 50px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .stats {
            background: linear-gradient(135deg, #0b2242 0%, #1b2b55 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .stat-item h3 {
            font-size: 2.5em;
            margin: 0;
            color: #667eea;
        }
        .cta-buttons {
            margin-top: 30px;
        }
        .cta-btn {
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            margin: 0 10px;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .cta-primary {
            background: white;
            color: #667eea;
        }
        .cta-secondary {
            border: 2px solid white;
            color: white;
        }
        .cta-btn:hover {
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
                <a href="index.php" style="color: #667eea;">Home</a>
                <a href="about.html">About</a>
                <a href="events.php">Events</a>
                <a href="job_listings.html">Jobs</a>
                <a href="contact_form.html">Contact</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="admin_dashboard.php">Dashboard</a>
                    <?php else: ?>
                        <a href="student_dashboard.php">Dashboard</a>
                    <?php endif; ?>
                    <a href="profile.php">Profile</a>
                    <a href="logout.php">Logout</a>
                    <span style="color: #667eea; margin-left: 15px;">
                        Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                    </span>
                <?php else: ?>
                    <a href="login.html">Login</a>
                    <a href="register.html">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero-section">
            <h1 style="font-size: 3em; margin-bottom: 20px;">Welcome to Talent Track 🎓</h1>
            <p style="font-size: 1.2em; margin-bottom: 30px;">Your gateway to academic excellence and career opportunities</p>
            <div class="cta-buttons">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="register.html" class="cta-btn cta-primary">Get Started Free</a>
                    <a href="login.html" class="cta-btn cta-secondary">Student Login</a>
                <?php else: ?>
                    <a href="<?php echo $_SESSION['role'] === 'admin' ? 'admin_dashboard.php' : 'student_dashboard.php'; ?>" class="cta-btn cta-primary">Go to Dashboard</a>
                    <a href="job_listings.html" class="cta-btn cta-secondary">Browse Jobs</a>
                <?php endif; ?>
            </div>
        </section>

        <section class="stats">
            <div class="stats-grid">
                <div class="stat-item">
                    <h3>500+</h3>
                    <p>Job Opportunities</p>
                </div>
                <div class="stat-item">
                    <h3>2000+</h3>
                    <p>Registered Students</p>
                </div>
                <div class="stat-item">
                    <h3>50+</h3>
                    <p>Partner Companies</p>
                </div>
                <div class="stat-item">
                    <h3>100+</h3>
                    <p>Successful Placements</p>
                </div>
            </div>
        </section>

        <section class="features">
            <div class="feature-card">
                <h3>🎯 Career Guidance</h3>
                <p>Get personalized career advice and job opportunities tailored to your skills and interests.</p>
            </div>
            <div class="feature-card">
                <h3>📚 Academic Resources</h3>
                <p>Access study materials, course information, and academic support from industry experts.</p>
            </div>
            <div class="feature-card">
                <h3>📅 Events & Workshops</h3>
                <p>Participate in seminars, workshops, career fairs, and networking events with top companies.</p>
            </div>
            <div class="feature-card">
                <h3>💼 Internship Opportunities</h3>
                <p>Find the perfect internship to gain practical experience and boost your career prospects.</p>
            </div>
            <div class="feature-card">
                <h3>🏆 Skill Development</h3>
                <p>Enhance your skills with our curated learning resources and certification programs.</p>
            </div>
            <div class="feature-card">
                <h3>🤝 Industry Connect</h3>
                <p>Connect directly with recruiters and industry professionals for mentorship and guidance.</p>
            </div>
        </section>
    </main>

    <footer style="background: linear-gradient(90deg, #0b2242, #1b2b55); color: #dbeafe; text-align: center; padding: 40px 0;">
        <div style="max-width: 1200px; margin: 0 auto;">
            <h3 style="margin: 0 0 20px 0; color: #fff; font-size: 1.8em;">Talent Track - Career Portal</h3>
            <p style="margin: 8px 0; color: #cfe8ff; font-size: 1.1em;">Connecting Students with Dream Careers</p>
            <div style="display: flex; justify-content: center; gap: 30px; margin: 20px 0; flex-wrap: wrap;">
                <div>
                    <h4 style="color: #fff; margin-bottom: 10px;">Quick Links</h4>
                    <a href="job_listings.html" style="color: #cfe8ff; display: block; margin: 5px 0;">Browse Jobs</a>
                    <a href="events.php" style="color: #cfe8ff; display: block; margin: 5px 0;">Events</a>
                    <a href="about.html" style="color: #cfe8ff; display: block; margin: 5px 0;">About Us</a>
                </div>
                <div>
                    <h4 style="color: #fff; margin-bottom: 10px;">Support</h4>
                    <a href="contact.html" style="color: #cfe8ff; display: block; margin: 5px 0;">Contact</a>
                    <a href="faq.html" style="color: #cfe8ff; display: block; margin: 5px 0;">FAQ</a>
                    <a href="privacy.html" style="color: #cfe8ff; display: block; margin: 5px 0;">Privacy Policy</a>
                </div>
                <div>
                    <h4 style="color: #fff; margin-bottom: 10px;">Contact Info</h4>
                    <p style="color: #cfe8ff; margin: 5px 0;">📧 support@talenttrack.com</p>
                    <p style="color: #cfe8ff; margin: 5px 0;">📞 +91-9313993111</p>
                    <p style="color: #cfe8ff; margin: 5px 0;">📍 University Campus</p>
                </div>
            </div>
            <p style="margin: 20px 0 0 0; color: #cfe8ff; border-top: 1px solid #334; padding-top: 20px;">
                &copy; 2025 Talent Track. All rights reserved. | Designed for Student Success
            </p>
        </div>
    </footer>
</body>
</html>