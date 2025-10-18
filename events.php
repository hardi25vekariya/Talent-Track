<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

require_once __DIR__ . '/db.php';

$pdo = getPDO();
$events = [];

if ($pdo) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date, event_time');
        $stmt->execute();
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Events fetch error: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - Talent Track</title>
    <link rel="stylesheet" href="index.css">
    <style>
        .events-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .events-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        .event-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            border-left: 5px solid #667eea;
            transition: transform 0.3s ease;
        }
        .event-card:hover {
            transform: translateY(-5px);
        }
        .event-date {
            background: #667eea;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 15px;
        }
        .event-title {
            color: #333;
            margin: 0 0 10px 0;
            font-size: 1.3em;
        }
        .event-meta {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .event-description {
            color: #555;
            line-height: 1.6;
        }
        .no-events {
            text-align: center;
            padding: 40px;
            color: #666;
            background: #f8f9fa;
            border-radius: 10px;
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
                    <input aria-label="Search events" placeholder="Search events">
                </div>
            </div>
            <nav class="nav-item">
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="admin_dashboard.php">Dashboard</a>
                    <a href="manage_events.php">Manage Events</a>
                <?php else: ?>
                    <a href="student_dashboard.php">Dashboard</a>
                <?php endif; ?>
                <a href="index.php">Home</a>
                <a href="events.php" style="color: #667eea;">Events</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
                <span style="color: #667eea; margin-left: 15px;">
                    Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                </span>
            </nav>
        </div>
    </header>
    
    <main class="events-container">
        <div class="events-header">
            <h1>📅 Upcoming Events & Workshops</h1>
            <p>Stay updated with career fairs, workshops, and networking events</p>
        </div>

        <?php if (empty($events)): ?>
            <div class="no-events">
                <h3>No Upcoming Events</h3>
                <p>Check back later for new events and workshops.</p>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="manage_events.php" class="glass-btn primary" style="margin-top: 15px;">Add New Event</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="events-grid">
                <?php foreach ($events as $event): ?>
                    <div class="event-card">
                        <div class="event-date">
                            📅 <?php echo date('M j, Y', strtotime($event['event_date'])); ?> 
                            at <?php echo date('g:i A', strtotime($event['event_time'])); ?>
                        </div>
                        <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                        <div class="event-meta">
                            <strong>📍 Venue:</strong> <?php echo htmlspecialchars($event['venue']); ?><br>
                            <strong>👨‍💼 Organizer:</strong> <?php echo htmlspecialchars($event['organizer']); ?>
                        </div>
                        <div class="event-description">
                            <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($_SESSION['role'] === 'admin'): ?>
            <div style="text-align: center; margin-top: 40px;">
                <a href="manage_events.php" class="glass-btn primary">➕ Manage Events</a>
            </div>
        <?php endif; ?>
    </main>

    <footer style="background: linear-gradient(90deg, #0b2242, #1b2b55); color: #dbeafe; text-align: center; padding: 30px 0; margin-top: 50px;">
        <div style="max-width: 1200px; margin: 0 auto;">
            <h3 style="margin: 0 0 15px 0; color: #fff;">Talent Track - Career Portal</h3>
            <p style="margin: 8px 0; color: #cfe8ff;">Stay connected with the latest career opportunities and events</p>
            <p style="margin: 8px 0; color: #cfe8ff;">&copy; 2025 Talent Track. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>