<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Applications - Talent Track</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <style>
        body {
            font-size: 16px;
            background: #f9f9f9;
        }
        
        .applications-container {
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
        
        .stat-pending .stat-number { color: #f59e0b; }
        .stat-reviewed .stat-number { color: #3b82f6; }
        .stat-accepted .stat-number { color: #10b981; }
        .stat-rejected .stat-number { color: #ef4444; }
        .stat-total .stat-number { color: #667eea; }
        
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-reviewed { background: #dbeafe; color: #1e40af; }
        .badge-accepted { background: #d1fae5; color: #065f46; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        
        .applications-list {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-top: 30px;
        }
        
        .application-item {
            padding: 25px;
            border-bottom: 1px solid #e5e7eb;
            transition: background-color 0.3s ease;
        }
        
        .application-item:hover {
            background-color: #f8fafc;
        }
        
        .application-item:last-child {
            border-bottom: none;
        }
        
        .application-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .job-title {
            font-size: 1.3em;
            color: #1f2937;
            margin: 0 0 8px 0;
        }
        
        .company-info {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .job-meta {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .application-meta {
            display: flex;
            gap: 20px;
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .cover-letter-preview {
            color: #4b5563;
            line-height: 1.5;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .application-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .no-applications {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }
        
        .filters-section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }
        
        .filter-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
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
        
        .filter-btn:hover:not(.active) {
            border-color: #667eea;
            color: #667eea;
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 10px;
        }
        
        .progress-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        
        .progress-pending { background: #f59e0b; }
        .progress-reviewed { background: #3b82f6; }
        .progress-accepted { background: #10b981; }
        .progress-rejected { background: #ef4444; }
        
        .application-details {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin-top: 15px;
            display: none;
        }
        
        .application-details.show {
            display: block;
        }
        
        .detail-section {
            margin-bottom: 15px;
        }
        
        .detail-section:last-child {
            margin-bottom: 0;
        }
        
        .detail-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }
        
        .withdraw-btn {
            background: #ef4444;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        
        .withdraw-btn:hover {
            background: #dc2626;
        }
        
        .empty-state {
            text-align: center;
            padding: 80px 20px;
        }
        
        .empty-state-icon {
            font-size: 4em;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .btn {
            padding: 12px 24px;
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
        
        .btn-primary:hover {
            background: #5a6fd8;
        }
        
        .btn-outline {
            background: white;
            color: #667eea;
            border: 1px solid #667eea;
        }
        
        .btn-outline:hover {
            background: #f7fafc;
        }
        
        .glass-btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .glass-btn.primary {
            background: #667eea;
            color: white;
        }
        
        .glass-btn.secondary {
            background: white;
            color: #667eea;
            border: 1px solid #667eea;
        }
        
        /* Updated Navbar Styles - Dark Blue Gradient */
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
        
        .searchbar {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 8px 15px;
            margin-left: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .searchbar input {
            border: none;
            background: transparent;
            margin-left: 8px;
            outline: none;
            width: 200px;
            color: white;
        }
        
        .searchbar input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: auto;
        }
        
        .nav-item a {
            text-decoration: none;
            color: #dbeafe;
            font-weight: 500;
            transition: all 0.3s ease;
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
        
        .profile-dropdown {
            position: relative;
        }
        
        .profile-dropdown > a {
            color: white !important;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            background: white;
            min-width: 160px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-radius: 8px;
            z-index: 1;
            right: 0;
            top: 100%;
        }
        
        .dropdown-content a {
            color: #64748b;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .dropdown-content a:last-child {
            border-bottom: none;
        }
        
        .dropdown-content a:hover {
            background: #f7fafc;
            color: #667eea;
        }
        
        .profile-dropdown:hover .dropdown-content {
            display: block;
        }
    </style>
</head>
<body>
    <?php
    // PHP code starts here
    //session_start();
    
    // If session is not set, use demo data
    $is_demo = true;
    $user_name = "Demo User";
    
    if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'student') {
        $is_demo = false;
        $user_name = $_SESSION['user_name'];
    }
    
    // Process form data if submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $application_data = [
            'id' => uniqid(),
            'title' => $_POST['job_title'] ?? 'Frontend Developer (React)',
            'company' => $_POST['company'] ?? 'Tata Consultancy Services',
            'location' => $_POST['location'] ?? 'Bangalore',
            'salary_range' => $_POST['salary_range'] ?? '₹6-10 LPA',
            'job_type' => $_POST['job_type'] ?? 'full_time',
            'applied_at' => date('Y-m-d H:i:s'),
            'status' => 'pending',
            'cover_letter' => $_POST['cover_letter'] ?? '',
            'full_name' => $_POST['full_name'] ?? 'John Doe',
            'email' => $_POST['email'] ?? 'john.doe@example.com',
            'phone' => $_POST['phone'] ?? '+91 9876543210',
            'degree' => $_POST['degree'] ?? 'B.Tech / B.E.',
            'specialization' => $_POST['specialization'] ?? 'Computer Science',
            'college' => $_POST['college'] ?? 'ABC Engineering College',
            'graduation_year' => $_POST['graduation_year'] ?? '2024'
        ];
        
        // Store in session
        if (!isset($_SESSION['applications'])) {
            $_SESSION['applications'] = [];
        }
        $_SESSION['applications'][] = $application_data;
    }
    
    // Get applications from session or use demo data
    if (isset($_SESSION['applications']) && !empty($_SESSION['applications'])) {
        $applications = $_SESSION['applications'];
    } else {
        // Demo data
        $applications = [
            [
                'id' => 1,
                'title' => 'Frontend Developer',
                'company' => 'Infosys',
                'location' => 'Bangalore',
                'salary_range' => '₹6-10 LPA',
                'job_type' => 'full_time',
                'applied_at' => '2025-07-05 10:30:00',
                'status' => 'reviewed',
                'cover_letter' => 'I am excited to apply for the Frontend Developer position...',
                'full_name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'phone' => '+91 9876543210',
                'degree' => 'B.Tech / B.E.',
                'specialization' => 'Computer Science',
                'college' => 'ABC Engineering College',
                'graduation_year' => '2024'
            ],
            [
                'id' => 2,
                'title' => 'Product Intern',
                'company' => 'Flipkart',
                'location' => 'Bangalore',
                'salary_range' => '₹3-5 LPA',
                'job_type' => 'internship',
                'applied_at' => '2025-06-28 14:20:00',
                'status' => 'pending',
                'cover_letter' => 'I am writing to apply for the Product Intern position...',
                'full_name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'phone' => '+91 9876543210',
                'degree' => 'B.Tech / B.E.',
                'specialization' => 'Computer Science',
                'college' => 'ABC Engineering College',
                'graduation_year' => '2024'
            ]
        ];
    }
    
    // Calculate statistics
    $stats = [
        'total' => count($applications),
        'pending' => count(array_filter($applications, fn($app) => $app['status'] === 'pending')),
        'reviewed' => count(array_filter($applications, fn($app) => $app['status'] === 'reviewed')),
        'accepted' => count(array_filter($applications, fn($app) => $app['status'] === 'accepted')),
        'rejected' => count(array_filter($applications, fn($app) => $app['status'] === 'rejected'))
    ];
    
    // Function to get status badge
    function getStatusBadge($status) {
        $badges = [
            'pending' => ['class' => 'badge-pending', 'text' => '⏳ Pending', 'icon' => '⏳'],
            'reviewed' => ['class' => 'badge-reviewed', 'text' => '👀 Under Review', 'icon' => '👀'],
            'accepted' => ['class' => 'badge-accepted', 'text' => '✅ Accepted', 'icon' => '✅'],
            'rejected' => ['class' => 'badge-rejected', 'text' => '❌ Rejected', 'icon' => '❌']
        ];
        
        $badge = $badges[$status] ?? $badges['pending'];
        return '<span class="status-badge ' . $badge['class'] . '">' . $badge['icon'] . ' ' . $badge['text'] . '</span>';
    }
    
    // Function to get status percentage
    function getStatusPercentage($count, $total) {
        return $total > 0 ? round(($count / $total) * 100) : 0;
    }
    ?>
    
    <header>
        <div class="navbar">
            <div class="nav-logo">
                <img src="logo.jpg" alt="logo">
            </div>
            <div class="site-title">Talent Track</div>
            <div style="margin-left:12px;">
                <div class="searchbar">
                    <img src="logo.jpg" alt="search" style="height: 20px; filter: brightness(0) invert(1);">
                    <input aria-label="Search applications" placeholder="Search applications" id="searchApplications">
                </div>
            </div>
            <nav class="nav-item">
                <a href="student_dashboard.html">Dashboard</a>
                <a href="job_listings.html">Job Listings</a>
                <a href="#" class="active">My Applications</a>
                <div class="profile-dropdown">
                    <a href="profile.php">
                        👤 <?php echo htmlspecialchars($user_name); ?> ▼
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

    <main class="applications-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1>📋 My Job Applications</h1>
            <p>Track and manage all your job applications in one place</p>
            <?php if ($is_demo): ?>
                <p style="opacity: 0.8; font-size: 0.9em; margin-top: 10px;">
                    Demo Mode - Data will reset on page refresh
                </p>
            <?php endif; ?>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card stat-total">
                <div class="stat-number"><?php echo $stats['total']; ?></div>
                <div>Total Applications</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 100%; background: #667eea;"></div>
                </div>
            </div>
            <div class="stat-card stat-pending">
                <div class="stat-number"><?php echo $stats['pending']; ?></div>
                <div>Pending Review</div>
                <div class="progress-bar">
                    <div class="progress-fill progress-pending" style="width: <?php echo getStatusPercentage($stats['pending'], $stats['total']); ?>%"></div>
                </div>
            </div>
            <div class="stat-card stat-reviewed">
                <div class="stat-number"><?php echo $stats['reviewed']; ?></div>
                <div>Under Review</div>
                <div class="progress-bar">
                    <div class="progress-fill progress-reviewed" style="width: <?php echo getStatusPercentage($stats['reviewed'], $stats['total']); ?>%"></div>
                </div>
            </div>
            <div class="stat-card stat-accepted">
                <div class="stat-number"><?php echo $stats['accepted']; ?></div>
                <div>Accepted</div>
                <div class="progress-bar">
                    <div class="progress-fill progress-accepted" style="width: <?php echo getStatusPercentage($stats['accepted'], $stats['total']); ?>%"></div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-section">
            <h3 style="margin-top: 0; margin-bottom: 15px;">Filter Applications</h3>
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="all">All (<?php echo $stats['total']; ?>)</button>
                <button class="filter-btn" data-filter="pending">Pending (<?php echo $stats['pending']; ?>)</button>
                <button class="filter-btn" data-filter="reviewed">Under Review (<?php echo $stats['reviewed']; ?>)</button>
                <button class="filter-btn" data-filter="accepted">Accepted (<?php echo $stats['accepted']; ?>)</button>
                <button class="filter-btn" data-filter="rejected">Rejected (<?php echo $stats['rejected']; ?>)</button>
            </div>
        </div>

        <!-- Applications List -->
        <?php if (empty($applications)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <h2>No Applications Yet</h2>
                <p style="color: #6b7280; margin-bottom: 30px; max-width: 400px; margin-left: auto; margin-right: auto;">
                    You haven't applied to any jobs yet. Start exploring opportunities and apply to your dream jobs!
                </p>
                <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                    <a href="job_listings.html" class="glass-btn primary">Browse Jobs</a>
                    <a href="resume_builder.php" class="glass-btn secondary">Build Resume</a>
                </div>
            </div>
        <?php else: ?>
            <div class="applications-list">
                <?php foreach ($applications as $application): ?>
                    <div class="application-item" data-status="<?php echo $application['status']; ?>">
                        <div class="application-header">
                            <div style="flex: 1;">
                                <h3 class="job-title"><?php echo htmlspecialchars($application['title']); ?></h3>
                                <div class="company-info">
                                    <?php echo htmlspecialchars($application['company']); ?> • 
                                    <?php echo htmlspecialchars($application['location']); ?>
                                </div>
                                <div class="job-meta">
                                    💰 <?php echo htmlspecialchars($application['salary_range']); ?> • 
                                    ⏱️ <?php echo ucfirst(str_replace('_', ' ', $application['job_type'])); ?>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <?php echo getStatusBadge($application['status']); ?>
                            </div>
                        </div>

                        <div class="application-meta">
                            <div>
                                <strong>Applied On:</strong> 
                                <?php echo date('M j, Y', strtotime($application['applied_at'])); ?>
                            </div>
                            <div>
                                <strong>Application ID:</strong> 
                                APP-<?php echo str_pad($application['id'], 6, '0', STR_PAD_LEFT); ?>
                            </div>
                        </div>

                        <?php if (!empty($application['cover_letter'])): ?>
                            <div class="cover-letter-preview">
                                <?php echo htmlspecialchars(substr($application['cover_letter'], 0, 150)); ?>...
                            </div>
                        <?php endif; ?>

                        <div class="application-actions">
                            <button class="btn btn-outline view-details-btn" 
                                    data-application-id="<?php echo $application['id']; ?>">
                                📄 View Details
                            </button>
                            <a href="job_details.php?job_id=<?php echo $application['id']; ?>" 
                               class="btn btn-outline">
                                👁️ View Job
                            </a>
                            <?php if ($application['status'] === 'pending'): ?>
                                <button class="withdraw-btn" 
                                        onclick="withdrawApplication('<?php echo $application['id']; ?>')">
                                    🗑️ Withdraw
                                </button>
                            <?php endif; ?>
                            <?php if ($application['status'] === 'accepted'): ?>
                                <button class="btn btn-primary">
                                    🎉 Congratulations!
                                </button>
                            <?php endif; ?>
                        </div>

                        <!-- Application Details (Hidden by default) -->
                        <div class="application-details" id="details-<?php echo $application['id']; ?>">
                            <div class="detail-section">
                                <div class="detail-label">Personal Information:</div>
                                <div>
                                    <strong>Name:</strong> <?php echo htmlspecialchars($application['full_name']); ?><br>
                                    <strong>Email:</strong> <?php echo htmlspecialchars($application['email']); ?><br>
                                    <strong>Phone:</strong> <?php echo htmlspecialchars($application['phone']); ?>
                                </div>
                            </div>
                            
                            <div class="detail-section">
                                <div class="detail-label">Education:</div>
                                <div>
                                    <strong>Degree:</strong> <?php echo htmlspecialchars($application['degree']); ?><br>
                                    <strong>Specialization:</strong> <?php echo htmlspecialchars($application['specialization']); ?><br>
                                    <strong>College:</strong> <?php echo htmlspecialchars($application['college']); ?><br>
                                    <strong>Graduation Year:</strong> <?php echo htmlspecialchars($application['graduation_year']); ?>
                                </div>
                            </div>
                            
                            <div class="detail-section">
                                <div class="detail-label">Cover Letter:</div>
                                <div style="white-space: pre-wrap; line-height: 1.6;">
                                    <?php echo htmlspecialchars($application['cover_letter']); ?>
                                </div>
                            </div>
                            
                            <div class="detail-section">
                                <div class="detail-label">Application Timeline:</div>
                                <div>
                                    ✅ Applied: <?php echo date('M j, Y g:i A', strtotime($application['applied_at'])); ?><br>
                                    <?php if ($application['status'] === 'reviewed'): ?>
                                        👀 Under Review: <?php echo date('M j, Y', strtotime($application['applied_at'] . ' +2 days')); ?>
                                    <?php elseif ($application['status'] === 'accepted'): ?>
                                        🎉 Accepted: <?php echo date('M j, Y', strtotime($application['applied_at'] . ' +5 days')); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Applications Summary -->
            <div style="text-align: center; margin-top: 30px; color: #6b7280;">
                <p>
                    Showing <?php echo count($applications); ?> application<?php echo count($applications) !== 1 ? 's' : ''; ?> • 
                    Last updated: <?php echo date('g:i A'); ?>
                </p>
            </div>
        <?php endif; ?>
    </main>

    <footer style="background: linear-gradient(90deg, #0b2242, #1b2b55); color: #dbeafe; text-align: center; padding: 40px 0; margin-top: 60px;">
        <div style="max-width: 1200px; margin: 0 auto;">
            <h3 style="margin: 0 0 20px 0; color: #fff;">Talent Track - Career Portal</h3>
            <p style="margin: 8px 0; color: #cfe8ff;">Track your job applications and career progress</p>
            <p style="margin: 20px 0 0 0; color: #cfe8ff; border-top: 1px solid #334; padding-top: 20px;">
                &copy; 2025 Talent Track. All rights reserved.
            </p>
        </div>
    </footer>

    <script>
        // Filter applications
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
                
                const filter = this.dataset.filter;
                const applications = document.querySelectorAll('.application-item');
                
                applications.forEach(app => {
                    if (filter === 'all' || app.dataset.status === filter) {
                        app.style.display = 'block';
                    } else {
                        app.style.display = 'none';
                    }
                });
            });
        });

        // View details toggle
        document.querySelectorAll('.view-details-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const applicationId = this.dataset.applicationId;
                const detailsDiv = document.getElementById('details-' + applicationId);
                
                if (detailsDiv.classList.contains('show')) {
                    detailsDiv.classList.remove('show');
                    this.innerHTML = '📄 View Details';
                } else {
                    detailsDiv.classList.add('show');
                    this.innerHTML = '📄 Hide Details';
                }
            });
        });

        // Search functionality
        document.getElementById('searchApplications').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const applications = document.querySelectorAll('.application-item');
            
            applications.forEach(app => {
                const title = app.querySelector('.job-title').textContent.toLowerCase();
                const company = app.querySelector('.company-info').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || company.includes(searchTerm)) {
                    app.style.display = 'block';
                } else {
                    app.style.display = 'none';
                }
            });
        });

        // Withdraw application
        function withdrawApplication(applicationId) {
            if (confirm('Are you sure you want to withdraw this application? This action cannot be undone.')) {
                // Simulate API call
                const btn = event.target;
                btn.innerHTML = '⏳ Withdrawing...';
                btn.disabled = true;
                
                setTimeout(() => {
                    btn.innerHTML = '✅ Withdrawn';
                    btn.style.background = '#10b981';
                    btn.style.color = 'white';
                    
                    // Update status badge
                    const statusBadge = btn.closest('.application-item').querySelector('.status-badge');
                    statusBadge.innerHTML = '❌ Withdrawn';
                    statusBadge.className = 'status-badge badge-rejected';
                    
                    // Update statistics
                    updateStatistics();
                    
                }, 1000);
            }
        }

        // Update statistics (simulated)
        function updateStatistics() {
            // In a real application, you would make an API call to update statistics
            console.log('Statistics updated');
        }

        // Auto-refresh every 5 minutes
        setInterval(() => {
            // In a real application, you would fetch updated application statuses
            console.log('Auto-refreshing applications...');
        }, 300000);

        // Print applications
        function printApplications() {
            window.print();
        }

        // Export applications (simulated)
        function exportApplications() {
            alert('Export feature would download a CSV file of your applications in a real implementation.');
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'f') {
                e.preventDefault();
                document.getElementById('searchApplications').focus();
            }
        });
    </script>
</body>
</html>