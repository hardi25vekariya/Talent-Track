<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Achievements - Talent Track</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <style>
        .achievements-container {
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
        
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
        
        .stat-badges .stat-number { color: #f59e0b; }
        .stat-points .stat-number { color: #10b981; }
        .stat-level .stat-number { color: #667eea; }
        .stat-streak .stat-number { color: #ef4444; }
        
        .achievements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin: 30px 0;
        }
        
        .achievement-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .achievement-card.locked {
            opacity: 0.6;
            background: #f8fafc;
        }
        
        .achievement-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .achievement-icon {
            font-size: 3em;
            margin-bottom: 15px;
        }
        
        .achievement-badge {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 2em;
        }
        
        .badge-gold { background: linear-gradient(45deg, #FFD700, #FFA500); }
        .badge-silver { background: linear-gradient(45deg, #C0C0C0, #A0A0A0); }
        .badge-bronze { background: linear-gradient(45deg, #CD7F32, #A0522D); }
        .badge-special { background: linear-gradient(45deg, #667eea, #764ba2); }
        
        .progress-ring {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 40px;
            height: 40px;
        }
        
        .progress-ring-circle {
            fill: none;
            stroke: #10b981;
            stroke-width: 3;
            stroke-linecap: round;
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
        }
        
        .level-system {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .level-bar {
            width: 100%;
            height: 20px;
            background: #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            margin: 20px 0;
            position: relative;
        }
        
        .level-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 10px;
            transition: width 0.5s ease;
        }
        
        .level-milestones {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            color: #6b7280;
            font-size: 14px;
        }
        
        .recent-activity {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .activity-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #667eea;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.2em;
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
        
        .filter-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        
        .filter-btn {
            padding: 8px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 20px;
            background: white;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .filter-btn.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
    </style>
</head>
<body>
    <?php
    //session_start();
    
    // Achievements data
    $achievement_stats = [
        'total_badges' => 12,
        'total_points' => 1850,
        'current_level' => 7,
        'login_streak' => 15
    ];
    
    // Level progress
    $level_data = [
        'current_points' => 1850,
        'next_level_points' => 2000,
        'progress_percentage' => 75
    ];
    
    // Achievements list
    $achievements = [
        [
            'id' => 1,
            'title' => 'First Application',
            'description' => 'Submit your first job application',
            'icon' => '📝',
            'points' => 50,
            'tier' => 'bronze',
            'unlocked' => true,
            'progress' => 100,
            'date_unlocked' => '2025-01-15'
        ],
        [
            'id' => 2,
            'title' => 'Interview Pro',
            'description' => 'Complete 5 interviews',
            'icon' => '💼',
            'points' => 200,
            'tier' => 'silver',
            'unlocked' => true,
            'progress' => 100,
            'date_unlocked' => '2025-03-20'
        ],
        [
            'id' => 3,
            'title' => 'Job Hunter',
            'description' => 'Apply to 20 different jobs',
            'icon' => '🎯',
            'points' => 150,
            'tier' => 'silver',
            'unlocked' => true,
            'progress' => 100,
            'date_unlocked' => '2025-04-10'
        ],
        [
            'id' => 4,
            'title' => 'Skill Master',
            'description' => 'Complete 10 skill assessments with 90%+ score',
            'icon' => '⭐',
            'points' => 300,
            'tier' => 'gold',
            'unlocked' => false,
            'progress' => 60,
            'date_unlocked' => null
        ],
        [
            'id' => 5,
            'title' => 'Network Builder',
            'description' => 'Connect with 50 professionals',
            'icon' => '🤝',
            'points' => 250,
            'tier' => 'silver',
            'unlocked' => false,
            'progress' => 30,
            'date_unlocked' => null
        ],
        [
            'id' => 6,
            'title' => 'Early Bird',
            'description' => 'Login for 30 consecutive days',
            'icon' => '🌅',
            'points' => 100,
            'tier' => 'bronze',
            'unlocked' => false,
            'progress' => 50,
            'date_unlocked' => null
        ],
        [
            'id' => 7,
            'title' => 'Resume Guru',
            'description' => 'Get your resume viewed 100 times',
            'icon' => '📄',
            'points' => 400,
            'tier' => 'gold',
            'unlocked' => false,
            'progress' => 25,
            'date_unlocked' => null
        ],
        [
            'id' => 8,
            'title' => 'Offer Accepted',
            'description' => 'Accept your first job offer',
            'icon' => '🎉',
            'points' => 500,
            'tier' => 'special',
            'unlocked' => false,
            'progress' => 0,
            'date_unlocked' => null
        ]
    ];
    
    // Recent activity
    $recent_activity = [
        [
            'type' => 'badge',
            'message' => 'Earned "Interview Pro" badge',
            'points' => 200,
            'timestamp' => '2 hours ago',
            'icon' => '💼'
        ],
        [
            'type' => 'progress',
            'message' => 'Completed skill assessment for React',
            'points' => 50,
            'timestamp' => '1 day ago',
            'icon' => '⭐'
        ],
        [
            'type' => 'application',
            'message' => 'Applied to Senior Frontend Developer at TechCorp',
            'points' => 10,
            'timestamp' => '2 days ago',
            'icon' => '📝'
        ],
        [
            'type' => 'login',
            'message' => '15-day login streak maintained',
            'points' => 15,
            'timestamp' => '3 days ago',
            'icon' => '🔥'
        ]
    ];
    
    // Calculate unlocked achievements
    $unlocked_achievements = array_filter($achievements, fn($a) => $a['unlocked']);
    $locked_achievements = array_filter($achievements, fn($a) => !$a['unlocked']);
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
                <a href="career.php">Career Progress</a>
                <a href="achievements.php" class="active">Achievements</a>
                <a href="profile.php">Profile</a>
            </nav>
        </div>
    </header>

    <main class="achievements-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1>🏆 My Achievements</h1>
            <p>Celebrate your career milestones and track your progress</p>
        </div>

        <!-- Achievement Statistics -->
        <div class="stats-overview">
            <div class="stat-card stat-badges">
                <div class="stat-number"><?php echo $achievement_stats['total_badges']; ?></div>
                <div>Badges Earned</div>
            </div>
            <div class="stat-card stat-points">
                <div class="stat-number"><?php echo number_format($achievement_stats['total_points']); ?></div>
                <div>Total Points</div>
            </div>
            <div class="stat-card stat-level">
                <div class="stat-number"><?php echo $achievement_stats['current_level']; ?></div>
                <div>Current Level</div>
            </div>
            <div class="stat-card stat-streak">
                <div class="stat-number"><?php echo $achievement_stats['login_streak']; ?></div>
                <div>Day Streak</div>
            </div>
        </div>

        <!-- Level Progress -->
        <div class="level-system">
            <h2>📈 Level Progress</h2>
            <div style="display: flex; justify-content: between; margin-bottom: 10px;">
                <span>Level <?php echo $achievement_stats['current_level']; ?></span>
                <span><?php echo number_format($level_data['current_points']); ?>/<?php echo number_format($level_data['next_level_points']); ?> XP</span>
                <span>Level <?php echo $achievement_stats['current_level'] + 1; ?></span>
            </div>
            <div class="level-bar">
                <div class="level-fill" style="width: <?php echo $level_data['progress_percentage']; ?>%;"></div>
            </div>
            <div class="level-milestones">
                <span>0 XP</span>
                <span>Level Up in <?php echo number_format($level_data['next_level_points'] - $level_data['current_points']); ?> XP</span>
                <span><?php echo number_format($level_data['next_level_points']); ?> XP</span>
            </div>
        </div>

        <!-- Achievement Filters -->
        <div class="filter-buttons">
            <button class="filter-btn active" data-filter="all">All Achievements</button>
            <button class="filter-btn" data-filter="unlocked">Unlocked (<?php echo count($unlocked_achievements); ?>)</button>
            <button class="filter-btn" data-filter="locked">In Progress (<?php echo count($locked_achievements); ?>)</button>
            <button class="filter-btn" data-filter="bronze">Bronze</button>
            <button class="filter-btn" data-filter="silver">Silver</button>
            <button class="filter-btn" data-filter="gold">Gold</button>
        </div>

        <!-- Achievements Grid -->
        <div class="achievements-grid">
            <?php foreach ($achievements as $achievement): ?>
                <div class="achievement-card <?php echo $achievement['unlocked'] ? '' : 'locked'; ?>" 
                     data-tier="<?php echo $achievement['tier']; ?>"
                     data-status="<?php echo $achievement['unlocked'] ? 'unlocked' : 'locked'; ?>">
                    
                    <div class="achievement-badge badge-<?php echo $achievement['tier']; ?>">
                        <?php echo $achievement['icon']; ?>
                    </div>
                    
                    <?php if (!$achievement['unlocked']): ?>
                        <div class="progress-ring">
                            <svg width="40" height="40">
                                <circle class="progress-ring-circle" 
                                        stroke-width="3" 
                                        fill="transparent" 
                                        r="17" 
                                        cx="20" 
                                        cy="20"
                                        stroke-dasharray="106.8"
                                        stroke-dashoffset="<?php echo 106.8 - (106.8 * $achievement['progress'] / 100); ?>">
                                </circle>
                            </svg>
                        </div>
                    <?php endif; ?>
                    
                    <h3 style="margin: 0 0 10px 0;"><?php echo $achievement['title']; ?></h3>
                    <p style="color: #6b7280; margin: 0 0 15px 0;"><?php echo $achievement['description']; ?></p>
                    
                    <div style="display: flex; justify-content: center; align-items: center; gap: 10px; margin-bottom: 15px;">
                        <span style="background: #f59e0b; color: white; padding: 2px 8px; border-radius: 10px; font-size: 12px;">
                            ⭐ <?php echo $achievement['points']; ?> pts
                        </span>
                        <span style="background: #667eea; color: white; padding: 2px 8px; border-radius: 10px; font-size: 12px; text-transform: capitalize;">
                            <?php echo $achievement['tier']; ?>
                        </span>
                    </div>
                    
                    <?php if ($achievement['unlocked']): ?>
                        <div style="color: #10b981; font-weight: 500;">
                            ✅ Unlocked on <?php echo date('M j, Y', strtotime($achievement['date_unlocked'])); ?>
                        </div>
                    <?php else: ?>
                        <div style="color: #6b7280;">
                            🔒 <?php echo $achievement['progress']; ?>% Complete
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Recent Activity -->
        <div class="recent-activity">
            <h2>📋 Recent Activity</h2>
            <?php foreach ($recent_activity as $activity): ?>
                <div class="activity-item">
                    <div class="activity-icon">
                        <?php echo $activity['icon']; ?>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500;"><?php echo $activity['message']; ?></div>
                        <div style="color: #6b7280; font-size: 14px;"><?php echo $activity['timestamp']; ?></div>
                    </div>
                    <div style="color: #10b981; font-weight: bold;">
                        +<?php echo $activity['points']; ?> pts
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Share Achievements -->
        <div style="text-align: center; margin-top: 40px;">
            <button class="btn btn-primary" onclick="shareAchievements()">
                📤 Share My Achievements
            </button>
        </div>
    </main>

    <footer style="background: linear-gradient(90deg, #0b2242, #1b2b55); color: #dbeafe; text-align: center; padding: 40px 0; margin-top: 60px;">
        <div style="max-width: 1200px; margin: 0 auto;">
            <h3 style="margin: 0 0 20px 0; color: #fff;">Talent Track - Career Portal</h3>
            <p style="margin: 8px 0; color: #cfe8ff;">Celebrate your achievements and career milestones</p>
            <p style="margin: 20px 0 0 0; color: #cfe8ff; border-top: 1px solid #334; padding-top: 20px;">
                &copy; 2025 Talent Track. All rights reserved.
            </p>
        </div>
    </footer>

    <script>
        // Filter achievements
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
                
                const filter = this.dataset.filter;
                const achievements = document.querySelectorAll('.achievement-card');
                
                achievements.forEach(achievement => {
                    const tier = achievement.dataset.tier;
                    const status = achievement.dataset.status;
                    
                    if (filter === 'all' || 
                        (filter === 'unlocked' && status === 'unlocked') ||
                        (filter === 'locked' && status === 'locked') ||
                        (filter === tier)) {
                        achievement.style.display = 'block';
                    } else {
                        achievement.style.display = 'none';
                    }
                });
            });
        });

        // Share achievements
        function shareAchievements() {
            const achievementsCount = <?php echo count($unlocked_achievements); ?>;
            const totalPoints = <?php echo $achievement_stats['total_points']; ?>;
            const currentLevel = <?php echo $achievement_stats['current_level']; ?>;
            
            const shareText = `I've unlocked ${achievementsCount} achievements with ${totalPoints} points and reached Level ${currentLevel} on Talent Track! 🏆🎉`;
            
            if (navigator.share) {
                navigator.share({
                    title: 'My Career Achievements',
                    text: shareText,
                    url: window.location.href
                });
            } else {
                // Fallback for browsers that don't support Web Share API
                navigator.clipboard.writeText(shareText).then(() => {
                    alert('Achievements copied to clipboard! 📋\n\n' + shareText);
                });
            }
        }

        // Animate progress rings
        function animateProgressRings() {
            const rings = document.querySelectorAll('.progress-ring-circle');
            rings.forEach(ring => {
                const circumference = 2 * Math.PI * 17;
                const offset = circumference - (circumference * parseFloat(ring.getAttribute('stroke-dashoffset')) / 106.8);
                ring.style.strokeDashoffset = circumference;
                
                setTimeout(() => {
                    ring.style.transition = 'stroke-dashoffset 1s ease-in-out';
                    ring.style.strokeDashoffset = offset;
                }, 100);
            });
        }

        // Initialize animations
        document.addEventListener('DOMContentLoaded', function() {
            animateProgressRings();
        });

        // Print achievements
        function printAchievements() {
            window.print();
        }
    </script>
</body>
</html>