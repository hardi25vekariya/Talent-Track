<?php
// universal_form_handler.php

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Universal Form Handler Class
 * किसी भी form का data automatically text file में save करेगा
 */
class UniversalFormHandler {
    private $baseDir;
    
    public function __construct($baseDir = null) {
        // Set base directory for storing files
        $this->baseDir = $baseDir ?: __DIR__ . '/form_data';
        
        // Create directory if it doesn't exist
        if (!is_dir($this->baseDir)) {
            mkdir($this->baseDir, 0777, true);
        }
    }
    
    /**
     * Generate unique filename based on form name and timestamp
     */
    private function generateFilename($formName = 'general') {
        $timestamp = date('Y-m-d_H-i-s');
        $random = substr(md5(uniqid()), 0, 6);
        $filename = "{$formName}_{$timestamp}_{$random}.txt";
        return $this->baseDir . '/' . $filename;
    }
    
    /**
     * Get form name from POST data or referrer
     */
    private function getFormName() {
        // Try to get form name from hidden field
        if (!empty($_POST['form_name'])) {
            return preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['form_name']);
        }
        
        // Get from referrer URL
        $referrer = $_SERVER['HTTP_REFERER'] ?? '';
        if ($referrer) {
            $path = parse_url($referrer, PHP_URL_PATH);
            $formName = pathinfo($path, PATHINFO_FILENAME);
            return $formName ?: 'unknown_form';
        }
        
        return 'general_form';
    }
    
    /**
     * Format data for text file
     */
    private function formatData($data) {
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        $output = "=== FORM SUBMISSION ===\n";
        $output .= "Submission Time: $timestamp\n";
        $output .= "IP Address: $ip\n";
        $output .= "User Agent: $userAgent\n";
        $output .= "Form Name: " . $this->getFormName() . "\n";
        $output .= "------------------------\n";
        $output .= "FORM DATA:\n";
        $output .= "------------------------\n";
        
        foreach ($data as $key => $value) {
            if ($key !== 'form_name') { // Skip form_name field
                if (is_array($value)) {
                    $output .= "$key: " . implode(', ', $value) . "\n";
                } else {
                    $output .= "$key: $value\n";
                }
            }
        }
        
        $output .= "========================\n\n";
        return $output;
    }
    
    /**
     * Save form data to text file
     */
    public function saveFormData($data) {
        try {
            $formName = $this->getFormName();
            $filename = $this->generateFilename($formName);
            
            $formattedData = $this->formatData($data);
            
            // Save to individual file
            $result = file_put_contents($filename, $formattedData, LOCK_EX);
            
            if ($result === false) {
                throw new Exception("Failed to write to file: $filename");
            }
            
            // Also append to master log file
            $masterFile = $this->baseDir . '/all_submissions.txt';
            file_put_contents($masterFile, $formattedData, FILE_APPEND | LOCK_EX);
            
            return [
                'success' => true,
                'filename' => basename($filename),
                'form_name' => $formName,
                'bytes_written' => $result
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get list of all saved form submissions
     */
    public function getSubmissionsList() {
        $files = glob($this->baseDir . '/*.txt');
        $submissions = [];
        
        foreach ($files as $file) {
            if (basename($file) !== 'all_submissions.txt') {
                $submissions[] = [
                    'filename' => basename($file),
                    'size' => filesize($file),
                    'modified' => date('Y-m-d H:i:s', filemtime($file))
                ];
            }
        }
        
        return array_reverse($submissions); // Newest first
    }
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formHandler = new UniversalFormHandler();
    $result = $formHandler->saveFormData($_POST);
    
    // Show result to user
    showResultPage($result, $formHandler);
} else {
    // If accessed directly, show submission list
    showSubmissionList();
}

/**
 * Display result page after form submission
 */
function showResultPage($result, $formHandler) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Form Submitted - Talent Track</title>
        <link rel="stylesheet" href="index.css">
        <style>
            .result-container { max-width: 800px; margin: 50px auto; padding: 20px; }
            .success-box { background: #d4edda; color: #155724; padding: 25px; border-radius: 10px; margin-bottom: 20px; }
            .error-box { background: #f8d7da; color: #721c24; padding: 25px; border-radius: 10px; margin-bottom: 20px; }
            .file-info { background: #e8f4fd; padding: 20px; border-radius: 8px; margin: 20px 0; }
            .submission-list { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 30px; }
            .submission-item { padding: 10px; border-bottom: 1px solid #ddd; }
            .submission-item:last-child { border-bottom: none; }
        </style>
    </head>
    <body>
        <header>
            <div class="navbar">
                <div class="nav-logo"><img src="logo.jpg" alt="logo" style="height: 40px;"></div>
                <div class="site-title">Talent Track</div>
                <nav class="nav-item">
                    <a href="index.html">Home</a>
                    <a href="universal_form_handler.php">View Submissions</a>
                </nav>
            </div>
        </header>

        <main class="result-container">
            <?php if ($result['success']): ?>
                <div class="success-box">
                    <h2>✅ Form Submitted Successfully!</h2>
                    <p>Your form data has been saved to our system.</p>
                </div>
                
                <div class="file-info">
                    <h3>Submission Details:</h3>
                    <p><strong>Form Name:</strong> <?php echo htmlspecialchars($result['form_name']); ?></p>
                    <p><strong>File Created:</strong> <?php echo htmlspecialchars($result['filename']); ?></p>
                    <p><strong>Data Size:</strong> <?php echo $result['bytes_written']; ?> bytes</p>
                    <p><strong>Timestamp:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
                </div>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="javascript:history.back()" class="glass-btn primary">← Go Back</a>
                    <a href="index.html" class="glass-btn secondary">Homepage</a>
                    <a href="universal_form_handler.php" class="glass-btn primary">View All Submissions</a>
                </div>
                
            <?php else: ?>
                <div class="error-box">
                    <h2>❌ Submission Failed</h2>
                    <p>Error: <?php echo htmlspecialchars($result['error']); ?></p>
                </div>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="javascript:history.back()" class="glass-btn primary">Try Again</a>
                </div>
            <?php endif; ?>
            
            <!-- Show recent submissions -->
            <div class="submission-list">
                <h3>📋 Recent Form Submissions</h3>
                <?php
                $submissions = $formHandler->getSubmissionsList();
                if (empty($submissions)): ?>
                    <p>No submissions yet.</p>
                <?php else: ?>
                    <?php foreach (array_slice($submissions, 0, 5) as $submission): ?>
                        <div class="submission-item">
                            <strong><?php echo htmlspecialchars($submission['filename']); ?></strong>
                            <br>
                            <small>Size: <?php echo $submission['size']; ?> bytes • Modified: <?php echo $submission['modified']; ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </body>
    </html>
    <?php
}

/**
 * Show list of all submissions when accessed directly
 */
function showSubmissionList() {
    $formHandler = new UniversalFormHandler();
    $submissions = $formHandler->getSubmissionsList();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Form Submissions - Talent Track</title>
        <link rel="stylesheet" href="index.css">
        <style>
            .submissions-container { max-width: 1000px; margin: 50px auto; padding: 20px; }
            .submission-card { background: white; padding: 20px; margin: 15px 0; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .file-content { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 10px; font-family: monospace; white-space: pre-wrap; }
        </style>
    </head>
    <body>
        <header>
            <div class="navbar">
                <div class="nav-logo"><img src="logo.jpg" alt="logo" style="height: 40px;"></div>
                <div class="site-title">Talent Track - Form Submissions</div>
                <nav class="nav-item">
                    <a href="index.html">Home</a>
                    <a href="contact_form.html">Contact Form</a>
                </nav>
            </div>
        </header>

        <main class="submissions-container">
            <h1>📄 All Form Submissions</h1>
            <p>Total submissions: <?php echo count($submissions); ?></p>
            
            <?php if (empty($submissions)): ?>
                <div style="text-align: center; padding: 50px;">
                    <h3>No form submissions yet.</h3>
                    <p>Submit a form to see data here.</p>
                    <a href="contact_form.html" class="glass-btn primary">Go to Contact Form</a>
                </div>
            <?php else: ?>
                <?php foreach ($submissions as $submission): ?>
                    <div class="submission-card">
                        <h3><?php echo htmlspecialchars($submission['filename']); ?></h3>
                        <p>
                            <strong>Size:</strong> <?php echo $submission['size']; ?> bytes • 
                            <strong>Last Modified:</strong> <?php echo $submission['modified']; ?>
                        </p>
                        
                        <?php
                        $filePath = 'form_data/' . $submission['filename'];
                        if (file_exists($filePath)) {
                            $content = file_get_contents($filePath);
                            echo '<div class="file-content">' . htmlspecialchars($content) . '</div>';
                        }
                        ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>
    </body>
    </html>
    <?php
}
?>