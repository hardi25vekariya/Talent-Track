<?php
// submit_form_advanced.php

class FormHandler {
    private $filename;
    private $logfile;
    
    public function __construct($data_file = "form_data", $log_file = "form_log.txt") {
        $this->filename = $data_file;
        $this->logfile = $log_file;
    }
    
    /**
     * Sanitize input data
     */
    public function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(trim(stripslashes($data ?? '')));
    }
    
    /**
     * Validate form data
     */
    public function validate($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors['name'] = "Name is required.";
        } elseif (strlen($data['name']) < 2) {
            $errors['name'] = "Name must be at least 2 characters.";
        }
        
        if (empty($data['email'])) {
            $errors['email'] = "Email is required.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email format.";
        }
        
        if (empty($data['message'])) {
            $errors['message'] = "Message is required.";
        } elseif (strlen($data['message']) < 10) {
            $errors['message'] = "Message must be at least 10 characters.";
        }
        
        return $errors;
    }
    
    /**
     * Save data to text file
     */
    public function saveToTextFile($data) {
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        
        $content = "=== FORM SUBMISSION ===" . PHP_EOL;
        $content .= "Timestamp: $timestamp" . PHP_EOL;
        $content .= "IP: $ip" . PHP_EOL;
        
        foreach ($data as $key => $value) {
            $content .= ucfirst($key) . ": $value" . PHP_EOL;
        }
        
        $content .= "======================" . PHP_EOL . PHP_EOL;
        
        return file_put_contents($this->filename . ".txt", $content, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Save data to CSV file
     */
    public function saveToCSV($data) {
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        
        // Add metadata
        $data['timestamp'] = $timestamp;
        $data['ip_address'] = $ip;
        
        $filename = $this->filename . ".csv";
        
        // Check if file exists to write headers
        $write_headers = !file_exists($filename) || filesize($filename) == 0;
        
        $file = fopen($filename, "a");
        
        if ($write_headers) {
            fputcsv($file, array_keys($data));
        }
        
        $result = fputcsv($file, $data);
        fclose($file);
        
        return $result !== false;
    }
    
    /**
     * Save data to JSON file
     */
    public function saveToJSON($data) {
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        
        // Add metadata
        $data['timestamp'] = $timestamp;
        $data['ip_address'] = $ip;
        
        $filename = $this->filename . ".json";
        
        // Read existing data
        $existing_data = [];
        if (file_exists($filename)) {
            $existing_content = file_get_contents($filename);
            $existing_data = json_decode($existing_content, true) ?? [];
        }
        
        // Add new data
        $existing_data[] = $data;
        
        // Save back to file
        return file_put_contents($filename, json_encode($existing_data, JSON_PRETTY_PRINT));
    }
    
    /**
     * Log activity
     */
    public function logActivity($message, $type = "INFO") {
        $timestamp = date('Y-m-d H:i:s');
        $log_entry = "[$timestamp] [$type] $message" . PHP_EOL;
        
        return file_put_contents($this->logfile, $log_entry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Process form submission
     */
    public function processForm($post_data) {
        // Sanitize data
        $clean_data = $this->sanitize($post_data);
        
        // Validate data
        $errors = $this->validate($clean_data);
        
        if (!empty($errors)) {
            $this->logActivity("Form validation failed: " . implode(", ", $errors), "ERROR");
            return ['success' => false, 'errors' => $errors];
        }
        
        // Save to multiple formats
        $text_success = $this->saveToTextFile($clean_data);
        $csv_success = $this->saveToCSV($clean_data);
        $json_success = $this->saveToJSON($clean_data);
        
        if ($text_success && $csv_success && $json_success) {
            $this->logActivity("Form submitted successfully by: " . $clean_data['email']);
            return ['success' => true, 'data' => $clean_data];
        } else {
            $this->logActivity("Failed to save form data for: " . $clean_data['email'], "ERROR");
            return ['success' => false, 'errors' => ['Failed to save form data. Please try again.']];
        }
    }
}

// Process the form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formHandler = new FormHandler();
    $result = $formHandler->processForm($_POST);
    
    $success = $result['success'];
    $data = $result['data'] ?? [];
    $errors = $result['errors'] ?? [];
} else {
    header("Location: contact_form.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Submission - Talent Track</title>
    <link rel="stylesheet" href="index.css">
    <style>
        .result-container { max-width: 700px; margin: 50px auto; padding: 20px; }
        .message { padding: 25px; border-radius: 10px; margin-bottom: 25px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .file-info { background: #e8f4fd; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .file-info h4 { margin-top: 0; color: #0c5460; }
    </style>
</head>
<body>
    <header>
        <div class="navbar">
            <div class="nav-logo"><img src="logo.jpg" alt="logo" style="height: 40px;"></div>
            <div class="site-title">Talent Track</div>
            <nav class="nav-item">
                <a href="index.html">Home</a>
                <a href="contact_form.html">Contact</a>
            </nav>
        </div>
    </header>

    <main class="result-container">
        <div class="glass-form-wrapper">
            <div class="glass-form">
                <h2>Form Submission Result</h2>
                
                <?php if ($success): ?>
                    <div class="message success">
                        <h3 style="margin-top: 0;">✅ Success!</h3>
                        <p>Your form has been submitted successfully and saved in multiple formats.</p>
                    </div>
                    
                    <div class="file-info">
                        <h4>Data Saved In:</h4>
                        <ul>
                            <li><strong>Text File:</strong> form_data.txt</li>
                            <li><strong>CSV File:</strong> form_data.csv</li>
                            <li><strong>JSON File:</strong> form_data.json</li>
                            <li><strong>Log File:</strong> form_log.txt</li>
                        </ul>
                    </div>
                    
                    <div style="text-align: center; margin-top: 25px;">
                        <a href="contact_form.html" class="glass-btn primary">Submit Another Form</a>
                        <a href="index.html" class="glass-btn secondary">Go Home</a>
                    </div>
                    
                <?php else: ?>
                    <div class="message error">
                        <h3 style="margin-top: 0;">❌ Submission Failed</h3>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <div style="text-align: center; margin-top: 25px;">
                        <a href="contact_form.html" class="glass-btn primary">Try Again</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer style="background: linear-gradient(90deg, #0b2242, #1b2b55); color: #dbeafe; text-align: center; padding: 30px 0;">
        <p>&copy; 2025 Talent Track. All rights reserved.</p>
    </footer>
</body>
</html>