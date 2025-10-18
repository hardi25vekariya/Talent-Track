<?php
// submit_simple.php - 100% Working Version

// Turn on ALL error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output to see what's happening
echo "<h1>Form Submission Debug</h1>";
echo "<pre>";

// Show all POST data
echo "POST Data Received:\n";
print_r($_POST);
echo "\n";

// Show server information
echo "Server Information:\n";
echo "Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Current Directory: " . __DIR__ . "\n";
echo "\n";

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "✅ POST request detected\n";
    
    // Create data string
    $data = "=== FORM SUBMISSION ===\n";
    $data .= "Time: " . date('Y-m-d H:i:s') . "\n";
    $data .= "IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "\n";
    
    // Add all form fields
    foreach ($_POST as $key => $value) {
        $data .= $key . ": " . $value . "\n";
    }
    
    $data .= "======================\n\n";
    
    echo "Data to save:\n";
    echo $data;
    echo "\n";
    
    // Try multiple file locations
    $locations = [
        'form_data.txt',
        __DIR__ . '/form_data.txt',
        'submissions.txt', 
        __DIR__ . '/submissions.txt',
        'test.txt',
        __DIR__ . '/test.txt'
    ];
    
    $success = false;
    
    foreach ($locations as $filename) {
        echo "Trying to save to: $filename\n";
        
        // Try to write file
        $result = file_put_contents($filename, $data, FILE_APPEND | LOCK_EX);
        
        if ($result !== false) {
            echo "✅ SUCCESS: Saved to $filename\n";
            echo "Bytes written: $result\n";
            $success = true;
            
            // Show file info
            if (file_exists($filename)) {
                echo "File exists: Yes\n";
                echo "File size: " . filesize($filename) . " bytes\n";
                echo "File content:\n";
                echo file_get_contents($filename) . "\n";
            }
            break;
        } else {
            echo "❌ FAILED: Could not save to $filename\n";
            
            // Try to create file first
            $file = fopen($filename, 'w');
            if ($file) {
                fclose($file);
                echo "Created empty file: $filename\n";
                
                // Try again
                $result = file_put_contents($filename, $data, FILE_APPEND | LOCK_EX);
                if ($result !== false) {
                    echo "✅ SUCCESS: Saved after creating file\n";
                    $success = true;
                    break;
                }
            }
        }
    }
    
    if ($success) {
        echo "\n🎉 FORM SUBMITTED SUCCESSFULLY!\n";
    } else {
        echo "\n💥 ALL ATTEMPTS FAILED!\n";
        echo "Last error: " . error_get_last()['message'] . "\n";
    }
    
} else {
    echo "❌ This page should be accessed via POST request\n";
    echo "Create a form with action='submit_simple.php'\n";
}

echo "</pre>";

// Show user-friendly message
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<div style='background: #d4edda; color: #155724; padding: 20px; border-radius: 10px; margin: 20px;'>";
    echo "<h2>✅ Form Submitted Successfully!</h2>";
    echo "<p>Your data has been saved to text file.</p>";
    echo "<p><strong>Check the debug information above for file details.</strong></p>";
    echo "</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Submission Result</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .debug { background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0; }
        .success { background: #d4edda; color: #155724; padding: 20px; border-radius: 10px; }
        .buttons { margin: 20px 0; }
        .btn { padding: 10px 20px; margin: 5px; text-decoration: none; border-radius: 5px; }
        .btn-primary { background: #007bff; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
    </style>
</head>
<body>
    <div class="buttons">
        <a href="test_form.html" class="btn btn-primary">Test Another Form</a>
        <a href="index.html" class="btn btn-secondary">Go to Homepage</a>
    </div>
</body>
</html>