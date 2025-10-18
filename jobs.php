<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

require_once __DIR__ . '/db.php';

$pdo = getPDO();
$jobs = [];
$categories = [];
$search_results = [];

// Get all job categories
if ($pdo) {
    try {
        // Get distinct categories from jobs
        $stmt = $pdo->prepare('SELECT DISTINCT job_type FROM jobs WHERE job_type IS NOT NULL');
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Handle search
        $search_query = $_GET['search'] ?? '';
        $category_filter = $_GET['category'] ?? '';
        $location_filter = $_GET['location'] ?? '';
        
        // Build query based on filters
        $sql = "SELECT * FROM jobs WHERE 1=1";
        $params = [];
        
        if (!empty($search_query)) {
            $sql .= " AND (title LIKE ? OR company LIKE ? OR description LIKE ?)";
            $search_term = "%$search_query%";
            $params[] = $search_term;
            $params[] = $search_term;
            $params[] = $search_term;
        }
        
        if (!empty($category_filter)) {
            $sql .= " AND job_type = ?";
            $params[] = $category_filter;
        }
        
        if (!empty($location_filter)) {
            $sql .= " AND location LIKE ?";
            $params[] = "%$location_filter%";
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log('Jobs fetch error: ' . $e->getMessage());
    }
}

// Sample jobs data if database is empty
if (empty($jobs)) {
    $jobs = [
        [
            'id' => 1,
            'title' => 'Frontend Developer',
            'company' => 'Tech Solutions Inc.',
            'location' => 'Bangalore',
            'salary_range' => '₹6-10 LPA',
            'job_type' => 'full_time',
            'description' => 'We are looking for a skilled Frontend Developer to join our team...',
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'id' => 2,
            'title' => 'Data Analyst',
            'company' => 'Data Insights Ltd.',
            'location' => 'Mumbai',
            'salary_range' => '₹5-8 LPA',
            'job_type' => 'full_time',
            'description' => 'Join our data team to analyze and interpret complex datasets...',
            'created_at' => date('Y-m-d H:i:s')
        ]
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Opportunities - Talent Track</title>
    <link rel="stylesheet" href="index.css">
    <style>
        .jobs-container {
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
        .filters-section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .jobs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }
        .job-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
            position: relative;
        }
        .job-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .job-card.featured {
            border-left-color: #fbbf24;
            background: linear-gradient(135deg, #fff, #fefce8);
        }
        .featured-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #fbbf24;
            color: #78350f;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .urgent-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #ef4444;
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .job-title {
            color: #1f2937;
            margin: 0 0 10px 0;
            font-size: 1.3em;
        }
        .company-name {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .job-meta {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        .job-description {
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .job-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        .no-jobs {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 40px;
        }
        .page-btn {
            padding: 8px 16px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .page-btn.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        .page-btn:hover:not(.active) {
            background: #f3f4f6;
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
                    <input aria-label="Search jobs" placeholder="Search jobs" id="globalSearch">
                </div>
            </div>
            <nav class="nav-item">
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="admin_dashboard.php">Dashboard</a>
                    <a href="post_job.php">Post Job</a>
                <?php else: ?>
                    <a href="student_dashboard.php">Dashboard</a>
                    <a href="my_applications.php">My Applications</a>
                <?php endif; ?>
                <a href="jobs.php" class="active">Jobs</a>
                <a href="events.php">Events</a>
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

    <main class="jobs-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1>🚀 Job Opportunities</h1>
            <p>Discover your next career move from top companies</p>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($jobs); ?></div>
                <div>Total Jobs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count(array_filter($jobs, fn($job) => $job['job_type'] === 'full_time')); ?></div>
                <div>Full-time</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count(array_filter($jobs, fn($job) => $job['job_type'] === 'internship')); ?></div>
                <div>Internships</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">24h</div>
                <div>New Today</div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <h3 style="margin-top: 0; margin-bottom: 20px; color: #1f2937;">🔍 Find Your Perfect Job</h3>
            <form method="GET" action="jobs.php" id="filterForm">
                <div class="filter-grid">
                    <div class="glass-field">
                        <label for="search">Job Title or Company</label>
                        <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="e.g., Frontend Developer">
                    </div>
                    <div class="glass-field">
                        <label for="category">Job Type</label>
                        <select id="category" name="category">
                            <option value="">All Types</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category); ?>" 
                                    <?php echo (($_GET['category'] ?? '') === $category) ? 'selected' : ''; ?>>
                                    <?php echo ucfirst(str_replace('_', ' ', $category)); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="glass-field">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($_GET['location'] ?? ''); ?>" placeholder="e.g., Bangalore">
                    </div>
                    <div class="glass-field" style="display: flex; align-items: flex-end;">
                        <button type="submit" class="glass-btn primary" style="width: 100%;">Apply Filters</button>
                    </div>
                </div>
                <?php if (!empty($_GET['search']) || !empty($_GET['category']) || !empty($_GET['location'])): ?>
                    <div style="text-align: center; margin-top: 15px;">
                        <a href="jobs.php" class="glass-btn secondary" style="padding: 8px 16px; font-size: 14px;">
                            Clear All Filters
                        </a>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- Job Listings -->
        <?php if (empty($jobs)): ?>
            <div class="no-jobs">
                <h3>No Jobs Found</h3>
                <p>Try adjusting your search filters or check back later for new opportunities.</p>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="post_job.php" class="glass-btn primary" style="margin-top: 15px;">Post a Job</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3>Available Positions (<?php echo count($jobs); ?>)</h3>
                <div style="color: #6b7280; font-size: 14px;">
                    Sorted by: Newest First
                </div>
            </div>

            <div class="jobs-grid">
                <?php foreach ($jobs as $index => $job): ?>
                    <div class="job-card <?php echo $index % 5 == 0 ? 'featured' : ''; ?>">
                        <?php if ($index % 5 == 0): ?>
                            <div class="featured-badge">⭐ Featured</div>
                        <?php elseif ($index % 7 == 0): ?>
                            <div class="urgent-badge">🔥 Urgent</div>
                        <?php endif; ?>
                        
                        <h3 class="job-title"><?php echo htmlspecialchars($job['title']); ?></h3>
                        <div class="company-name"><?php echo htmlspecialchars($job['company']); ?></div>
                        <div class="job-meta">
                            📍 <?php echo htmlspecialchars($job['location']); ?> • 
                            💰 <?php echo htmlspecialchars($job['salary_range'] ?? 'Salary not disclosed'); ?> • 
                            ⏱️ <?php echo ucfirst(str_replace('_', ' ', $job['job_type'] ?? 'Full-time')); ?>
                        </div>
                        <div class="job-description">
                            <?php echo htmlspecialchars($job['description']); ?>
                        </div>
                        <div class="job-actions">
                            <a href="job_details.php?job_id=<?php echo $job['id'] ?? $index; ?>" class="btn btn-outline">
                                View Details
                            </a>
                            <?php if ($_SESSION['role'] === 'student'): ?>
                                <a href="apply_job.php?job_id=<?php echo $job['id'] ?? $index; ?>" class="btn btn-primary">
                                    Apply Now
                                </a>
                            <?php endif; ?>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <a href="edit_job.php?job_id=<?php echo $job['id'] ?? $index; ?>" class="btn btn-outline" style="background: #f3f4f6;">
                                    Edit
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <button class="page-btn active">1</button>
                <button class="page-btn">2</button>
                <button class="page-btn">3</button>
                <button class="page-btn">Next →</button>
            </div>
        <?php endif; ?>
    </main>

    <footer style="background: linear-gradient(90deg, #0b2242, #1b2b55); color: #dbeafe; text-align: center; padding: 40px 0; margin-top: 60px;">
        <div style="max-width: 1200px; margin: 0 auto;">
            <h3 style="margin: 0 0 20px 0; color: #fff;">Talent Track - Career Portal</h3>
            <p style="margin: 8px 0; color: #cfe8ff;">Connecting Talent with Opportunities</p>
            <p style="margin: 20px 0 0 0; color: #cfe8ff; border-top: 1px solid #334; padding-top: 20px;">
                &copy; 2025 Talent Track. All rights reserved.
            </p>
        </div>
    </footer>

    <script>
        // Real-time search functionality
        document.getElementById('globalSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const jobCards = document.querySelectorAll('.job-card');
            
            jobCards.forEach(card => {
                const title = card.querySelector('.job-title').textContent.toLowerCase();
                const company = card.querySelector('.company-name').textContent.toLowerCase();
                const description = card.querySelector('.job-description').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || company.includes(searchTerm) || description.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Auto-submit form when filters change
        document.getElementById('category').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        // Save search preferences
        document.addEventListener('DOMContentLoaded', function() {
            const savedSearch = localStorage.getItem('jobSearch');
            if (savedSearch) {
                document.getElementById('globalSearch').value = savedSearch;
            }
        });

        document.getElementById('globalSearch').addEventListener('input', function() {
            localStorage.setItem('jobSearch', this.value);
        });
    </script>
</body>
</html>