<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Career Progress - Talent Track</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <style>
        .career-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .stat-applications .stat-number { color: #667eea; }
        .stat-interviews .stat-number { color: #f59e0b; }
        .stat-offers .stat-number { color: #10b981; }
        .stat-skills .stat-number { color: #ef4444; }
        
        .progress-section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .progress-bar {
            width: 100%;
            height: 12px;
            background: #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
            margin: 15px 0;
        }
        
        .progress-fill {
            height: 100%;
            border-radius: 6px;
            transition: width 0.5s ease;
        }
        
        .timeline {
            position: relative;
            max-width: 800px;
            margin: 40px auto;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #667eea;
            transform: translateX(-50%);
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 50px;
            width: 45%;
        }
        
        .timeline-item:nth-child(odd) {
            left: 0;
        }
        
        .timeline-item:nth-child(even) {
            left: 55%;
        }
        
        .timeline-content {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            position: relative;
        }
        
        .timeline-content::after {
            content: '';
            position: absolute;
            top: 20px;
            width: 20px;
            height: 20px;
            background: #667eea;
            border-radius: 50%;
        }
        
        .timeline-item:nth-child(odd) .timeline-content::after {
            right: -50px;
        }
        
        .timeline-item:nth-child(even) .timeline-content::after {
            left: -50px;
        }
        
        .skill-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }
        
        .skill-tag {
            background: #667eea;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .goals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .goal-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }
        
        .goal-progress {
            margin-top: 15px;
        }
        
        .goal-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            color: #6b7280;
            font-size: 14px;
        }
        
        /* Updated Navbar Styles */
        .navbar {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            background: linear-gradient(135deg, #0b2242 0%, #1b2b55 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .nav-logo img {
            height: 40px;
        }
        
        .site-title {
            font-size: 1.5em;
            font-weight: bold;
            color: white;
            margin-left: 10px;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-left: auto;
        }
        
        .nav-item a {
            text-decoration: none;
            color: #dbeafe;
            font-weight: 500;
            transition: color 0.3s ease;
            padding: 8px 16px;
            border-radius: 6px;
        }
        
        .nav-item a.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .nav-item a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-outline {
            background: white;
            color: #667eea;
            border: 1px solid #667eea;
        }
    </style>
</head>
<body>
    <?php
    //session_start();
    
    // Career data
    $career_data = [
        'total_applications' => 24,
        'interviews' => 8,
        'job_offers' => 3,
        'skills_learned' => 15,
        'completion_rate' => 65,
        'profile_strength' => 85,
        'resume_views' => 47
    ];
    
    // Career timeline
    $timeline = [
        [
            'date' => 'Jan 2025',
            'title' => 'Started Job Search',
            'description' => 'Began actively applying for frontend developer positions',
            'type' => 'start'
        ],
        [
            'date' => 'Mar 2025',
            'title' => 'First Interview',
            'description' => 'First technical interview with Tech Solutions Inc.',
            'type' => 'interview'
        ],
        [
            'date' => 'Apr 2025',
            'title' => 'Skills Upgrade',
            'description' => 'Completed React and Node.js certification courses',
            'type' => 'learning'
        ],
        [
            'date' => 'Jun 2025',
            'title' => 'First Job Offer',
            'description' => 'Received offer from Digital Innovations Ltd.',
            'type' => 'offer'
        ]
    ];
    
    // Career goals
    $goals = [
        [
            'title' => 'Master React Framework',
            'progress' => 75,
            'target' => 'Complete by Aug 2025',
            'tasks_completed' => 15,
            'total_tasks' => 20
        ],
        [
            'title' => 'Build 5 Projects',
            'progress' => 40,
            'target' => 'Complete by Sep 2025',
            'tasks_completed' => 2,
            'total_tasks' => 5
        ],
        [
            'title' => 'Network with 50 Professionals',
            'progress' => 60,
            'target' => 'Complete by Dec 2025',
            'tasks_completed' => 30,
            'total_tasks' => 50
        ]
    ];
    
    // Skills progress
    $skills = [
        ['name' => 'HTML/CSS', 'progress' => 90],
        ['name' => 'JavaScript', 'progress' => 85],
        ['name' => 'React', 'progress' => 75],
        ['name' => 'Node.js', 'progress' => 60],
        ['name' => 'MongoDB', 'progress' => 50],
        ['name' => 'Git', 'progress' => 80]
    ];
    ?>
    
    <header>
        <div class="navbar">
            <div class="nav-logo">
                <img src="logo.jpg" alt="logo">
            </div>
            <div class="site-title">Talent Track</div>
            <nav class="nav-item">
                <a href="student_dashboard.php">Dashboard</a>
                <a href="job_listings.php">Job Listings</a>
                <a href="my_applications.php">Applications</a>
                <a href="career.php" class="active">Career Progress</a>
                <a href="achievements.php">Achievements</a>
                <a href="profile.php">Profile</a>
            </nav>
        </div>
    </header>

    <main class="career-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1>🚀 My Career Progress</h1>
            <p>Track your journey and milestones towards your dream career</p>
        </div>

        <!-- Career Statistics -->
        <div class="stats-grid">
            <div class="stat-card stat-applications">
                <div class="stat-number"><?php echo $career_data['total_applications']; ?></div>
                <div>Total Applications</div>
            </div>
            <div class="stat-card stat-interviews">
                <div class="stat-number"><?php echo $career_data['interviews']; ?></div>
                <div>Interviews</div>
            </div>
            <div class="stat-card stat-offers">
                <div class="stat-number"><?php echo $career_data['job_offers']; ?></div>
                <div>Job Offers</div>
            </div>
            <div class="stat-card stat-skills">
                <div class="stat-number"><?php echo $career_data['skills_learned']; ?></div>
                <div>Skills Learned</div>
            </div>
        </div>

        <!-- Profile Strength -->
        <div class="progress-section">
            <h2>📊 Profile Strength</h2>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo $career_data['profile_strength']; ?>%; background: #667eea;"></div>
            </div>
            <div style="display: flex; justify-content: space-between; color: #6b7280;">
                <span>Basic Profile</span>
                <span><?php echo $career_data['profile_strength']; ?>% Complete</span>
                <span>All-Star Profile</span>
            </div>
        </div>

        <!-- Skills Progress -->
        <div class="progress-section">
            <h2>💻 Technical Skills Progress</h2>
            <?php foreach ($skills as $skill): ?>
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: between; margin-bottom: 8px;">
                        <span><?php echo $skill['name']; ?></span>
                        <span><?php echo $skill['progress']; ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $skill['progress']; ?>%; background: #10b981;"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Career Timeline -->
        <div class="progress-section">
            <h2>📅 Career Journey Timeline</h2>
            <div class="timeline">
                <?php foreach ($timeline as $index => $event): ?>
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div style="font-weight: bold; color: #667eea; margin-bottom: 5px;">
                                <?php echo $event['date']; ?>
                            </div>
                            <h3 style="margin: 0 0 10px 0;"><?php echo $event['title']; ?></h3>
                            <p style="margin: 0; color: #6b7280;"><?php echo $event['description']; ?></p>
                            <div style="margin-top: 10px;">
                                <?php 
                                $badge_color = [
                                    'start' => '#667eea',
                                    'interview' => '#f59e0b', 
                                    'learning' => '#10b981',
                                    'offer' => '#ef4444'
                                ];
                                ?>
                                <span style="background: <?php echo $badge_color[$event['type']]; ?>; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px;">
                                    <?php echo ucfirst($event['type']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Career Goals -->
        <div class="progress-section">
            <h2>🎯 Career Goals</h2>
            <div class="goals-grid">
                <?php foreach ($goals as $goal): ?>
                    <div class="goal-card">
                        <h3 style="margin: 0 0 15px 0;"><?php echo $goal['title']; ?></h3>
                        <div class="goal-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $goal['progress']; ?>%; background: #f59e0b;"></div>
                            </div>
                            <div class="goal-stats">
                                <span><?php echo $goal['progress']; ?>% Complete</span>
                                <span><?php echo $goal['tasks_completed']; ?>/<?php echo $goal['total_tasks']; ?> Tasks</span>
                            </div>
                        </div>
                        <div style="margin-top: 15px; color: #667eea; font-size: 14px;">
                            🎯 <?php echo $goal['target']; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Action Buttons -->
        <div style="text-align: center; margin-top: 40px;">
            <a href="job_listings.html" class="btn btn-primary" style="margin-right: 15px;">
                🔍 Explore New Jobs
            </a>
            <a href="resume_builder.php" class="btn btn-outline">
                📝 Update Resume
            </a>
        </div>
    </main>

    <footer style="background: linear-gradient(90deg, #0b2242, #1b2b55); color: #dbeafe; text-align: center; padding: 40px 0; margin-top: 60px;">
        <div style="max-width: 1200px; margin: 0 auto;">
            <h3 style="margin: 0 0 20px 0; color: #fff;">Talent Track - Career Portal</h3>
            <p style="margin: 8px 0; color: #cfe8ff;">Track your career progress and achieve your goals</p>
            <p style="margin: 20px 0 0 0; color: #cfe8ff; border-top: 1px solid #334; padding-top: 20px;">
                &copy; 2025 Talent Track. All rights reserved.
            </p>
        </div>
    </footer>

    <script>
        // Animate progress bars on scroll
        function animateProgressBars() {
            const progressBars = document.querySelectorAll('.progress-fill');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 100);
            });
        }

        // Initialize animations when page loads
        document.addEventListener('DOMContentLoaded', function() {
            animateProgressBars();
        });

        // Print career progress
        function printCareerProgress() {
            window.print();
        }

        // Export career data
        function exportCareerData() {
            alert('Career progress data exported successfully!');
        }
    </script>
</body>
</html>