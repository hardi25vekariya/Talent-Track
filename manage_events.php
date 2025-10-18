<?php
// manage_events.php

require_once __DIR__ . '/session_check.php';
require_admin(); // Only admin can access

require_once __DIR__ . '/db.php';

$pdo = getPDO();
$message = '';
$error = '';

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_event':
            $title = sanitize_input($_POST['title'] ?? '');
            $description = sanitize_input($_POST['description'] ?? '');
            $event_date = $_POST['event_date'] ?? '';
            $event_time = $_POST['event_time'] ?? '';
            $location = sanitize_input($_POST['location'] ?? '');
            $status = $_POST['status'] ?? 'open';
            $organizer = sanitize_input($_POST['organizer'] ?? '');
            $max_participants = intval($_POST['max_participants'] ?? 0);
            
            // Validation
            if (empty($title) || empty($event_date) || empty($location)) {
                $error = 'Title, date, and location are required fields.';
            } elseif (!validate_date($event_date)) {
                $error = 'Invalid date format. Use YYYY-MM-DD.';
            } else {
                try {
                    $stmt = $pdo->prepare('
                        INSERT INTO events (title, description, event_date, event_time, location, status, organizer, max_participants, created_by) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ');
                    $stmt->execute([
                        $title, $description, $event_date, $event_time, 
                        $location, $status, $organizer, $max_participants, 
                        $_SESSION['user_id']
                    ]);
                    
                    $message = 'Event added successfully!';
                } catch (PDOException $e) {
                    error_log('Add event error: ' . $e->getMessage());
                    $error = 'Failed to add event. Please try again.';
                }
            }
            break;
            
        case 'update_event':
            $event_id = intval($_POST['event_id'] ?? 0);
            $title = sanitize_input($_POST['title'] ?? '');
            $description = sanitize_input($_POST['description'] ?? '');
            $event_date = $_POST['event_date'] ?? '';
            $event_time = $_POST['event_time'] ?? '';
            $location = sanitize_input($_POST['location'] ?? '');
            $status = $_POST['status'] ?? 'open';
            $organizer = sanitize_input($_POST['organizer'] ?? '');
            $max_participants = intval($_POST['max_participants'] ?? 0);
            
            if ($event_id > 0) {
                try {
                    $stmt = $pdo->prepare('
                        UPDATE events 
                        SET title = ?, description = ?, event_date = ?, event_time = ?, 
                            location = ?, status = ?, organizer = ?, max_participants = ?,
                            updated_at = NOW()
                        WHERE event_id = ?
                    ');
                    $stmt->execute([
                        $title, $description, $event_date, $event_time,
                        $location, $status, $organizer, $max_participants,
                        $event_id
                    ]);
                    
                    $message = 'Event updated successfully!';
                } catch (PDOException $e) {
                    error_log('Update event error: ' . $e->getMessage());
                    $error = 'Failed to update event. Please try again.';
                }
            }
            break;
            
        case 'delete_event':
            $event_id = intval($_POST['event_id'] ?? 0);
            
            if ($event_id > 0) {
                try {
                    $stmt = $pdo->prepare('DELETE FROM events WHERE event_id = ?');
                    $stmt->execute([$event_id]);
                    
                    $message = 'Event deleted successfully!';
                } catch (PDOException $e) {
                    error_log('Delete event error: ' . $e->getMessage());
                    $error = 'Failed to delete event. Please try again.';
                }
            }
            break;
    }
}

// Get all events
$events = [];
if ($pdo) {
    try {
        $stmt = $pdo->query('
            SELECT e.*, s.first_name, s.last_name 
            FROM events e 
            LEFT JOIN students s ON e.created_by = s.id 
            ORDER BY e.event_date DESC, e.event_time DESC
        ');
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Fetch events error: ' . $e->getMessage());
        $error = 'Failed to load events.';
    }
}

// Helper function to validate date
function validate_date($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

// Helper function to format date for display
function format_display_date($date) {
    return date('M j, Y', strtotime($date));
}

// Helper function to get status badge
function get_status_badge($status) {
    $badges = [
        'open' => '<span class="badge badge-success">Open</span>',
        'closed' => '<span class="badge badge-danger">Closed</span>',
        'cancelled' => '<span class="badge badge-secondary">Cancelled</span>'
    ];
    return $badges[$status] ?? $badges['open'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events - Talent Track</title>
    <link rel="stylesheet" href="index.css">
    <style>
        .events-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        .event-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
        }
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .event-title {
            color: #2d3748;
            margin: 0 0 10px 0;
            font-size: 1.3em;
        }
        .event-meta {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        .event-description {
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .event-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-right: 8px;
        }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-secondary { background: #e5e7eb; color: #374151; }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: black;
        }
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 25px;
        }
        .add-event-btn {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <header>
        <div class="navbar">
            <div class="nav-logo">
                <img src="logo.jpg" alt="logo" style="height: 40px;">
            </div>
            <div class="site-title">Talent Track - Manage Events</div>
            <nav class="nav-item">
                <a href="admin_dashboard.php">Dashboard</a>
                <a href="manage_events.php" class="active">Manage Events</a>
                <a href="manage_students.php">Manage Students</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <main class="events-container">
        <div class="page-header">
            <h1>📅 Manage Events</h1>
            <p>Create, view, update, and delete events</p>
        </div>

        <?php if ($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <button class="add-event-btn" onclick="openModal('addEventModal')">
            ➕ Add New Event
        </button>

        <div class="events-grid">
            <?php if (empty($events)): ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #6b7280;">
                    <h3>No Events Found</h3>
                    <p>Start by adding your first event!</p>
                </div>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                    <div class="event-card">
                        <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                        <div class="event-meta">
                            <strong>📅 Date:</strong> <?php echo format_display_date($event['event_date']); ?><br>
                            <strong>⏰ Time:</strong> <?php echo $event['event_time'] ? date('g:i A', strtotime($event['event_time'])) : 'Not specified'; ?><br>
                            <strong>📍 Location:</strong> <?php echo htmlspecialchars($event['location']); ?><br>
                            <strong>👨‍💼 Organizer:</strong> <?php echo htmlspecialchars($event['organizer']); ?><br>
                            <strong>👥 Max Participants:</strong> <?php echo $event['max_participants'] ?: 'Unlimited'; ?>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <?php echo get_status_badge($event['status']); ?>
                        </div>
                        <?php if (!empty($event['description'])): ?>
                            <div class="event-description">
                                <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                            </div>
                        <?php endif; ?>
                        <div class="event-actions">
                            <button class="btn btn-outline" 
                                    onclick="editEvent(<?php echo htmlspecialchars(json_encode($event)); ?>)">
                                ✏️ Edit
                            </button>
                            <button class="btn btn-danger" 
                                    onclick="deleteEvent(<?php echo $event['event_id']; ?>, '<?php echo htmlspecialchars($event['title']); ?>')">
                                🗑️ Delete
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- Add Event Modal -->
    <div id="addEventModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addEventModal')">&times;</span>
            <h2>Add New Event</h2>
            <form method="POST" id="addEventForm">
                <input type="hidden" name="action" value="add_event">
                
                <div class="glass-field">
                    <label for="title">Event Title *</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="glass-field">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4"></textarea>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="glass-field">
                        <label for="event_date">Event Date *</label>
                        <input type="date" id="event_date" name="event_date" required>
                    </div>
                    <div class="glass-field">
                        <label for="event_time">Event Time</label>
                        <input type="time" id="event_time" name="event_time">
                    </div>
                </div>
                
                <div class="glass-field">
                    <label for="location">Location *</label>
                    <input type="text" id="location" name="location" required>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="glass-field">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="open">Open</option>
                            <option value="closed">Closed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="glass-field">
                        <label for="max_participants">Max Participants</label>
                        <input type="number" id="max_participants" name="max_participants" min="0">
                    </div>
                </div>
                
                <div class="glass-field">
                    <label for="organizer">Organizer</label>
                    <input type="text" id="organizer" name="organizer">
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeModal('addEventModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Event</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Event Modal -->
    <div id="editEventModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editEventModal')">&times;</span>
            <h2>Edit Event</h2>
            <form method="POST" id="editEventForm">
                <input type="hidden" name="action" value="update_event">
                <input type="hidden" id="edit_event_id" name="event_id">
                
                <div class="glass-field">
                    <label for="edit_title">Event Title *</label>
                    <input type="text" id="edit_title" name="title" required>
                </div>
                
                <div class="glass-field">
                    <label for="edit_description">Description</label>
                    <textarea id="edit_description" name="description" rows="4"></textarea>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="glass-field">
                        <label for="edit_event_date">Event Date *</label>
                        <input type="date" id="edit_event_date" name="event_date" required>
                    </div>
                    <div class="glass-field">
                        <label for="edit_event_time">Event Time</label>
                        <input type="time" id="edit_event_time" name="event_time">
                    </div>
                </div>
                
                <div class="glass-field">
                    <label for="edit_location">Location *</label>
                    <input type="text" id="edit_location" name="location" required>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="glass-field">
                        <label for="edit_status">Status</label>
                        <select id="edit_status" name="status">
                            <option value="open">Open</option>
                            <option value="closed">Closed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="glass-field">
                        <label for="edit_max_participants">Max Participants</label>
                        <input type="number" id="edit_max_participants" name="max_participants" min="0">
                    </div>
                </div>
                
                <div class="glass-field">
                    <label for="edit_organizer">Organizer</label>
                    <input type="text" id="edit_organizer" name="organizer">
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeModal('editEventModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Event</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteEventModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('deleteEventModal')">&times;</span>
            <h2>Confirm Delete</h2>
            <p>Are you sure you want to delete the event "<span id="deleteEventTitle"></span>"?</p>
            <p style="color: #ef4444; font-weight: 600;">This action cannot be undone.</p>
            <form method="POST" id="deleteEventForm">
                <input type="hidden" name="action" value="delete_event">
                <input type="hidden" id="delete_event_id" name="event_id">
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeModal('deleteEventModal')">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Event</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal functions
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }

        // Edit event
        function editEvent(event) {
            document.getElementById('edit_event_id').value = event.event_id;
            document.getElementById('edit_title').value = event.title;
            document.getElementById('edit_description').value = event.description || '';
            document.getElementById('edit_event_date').value = event.event_date;
            document.getElementById('edit_event_time').value = event.event_time || '';
            document.getElementById('edit_location').value = event.location;
            document.getElementById('edit_status').value = event.status;
            document.getElementById('edit_organizer').value = event.organizer || '';
            document.getElementById('edit_max_participants').value = event.max_participants || '';
            
            openModal('editEventModal');
        }

        // Delete event
        function deleteEvent(eventId, eventTitle) {
            document.getElementById('delete_event_id').value = eventId;
            document.getElementById('deleteEventTitle').textContent = eventTitle;
            openModal('deleteEventModal');
        }

        // Set minimum date to today for event date
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('event_date').min = today;
            document.getElementById('edit_event_date').min = today;
        });
    </script>
</body>
</html>