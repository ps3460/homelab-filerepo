<?php
// This block should be saved in a separate file named "delete.php"

$upload_dir = "uploads/";

// Check if the form was submitted with a filename
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filename'])) {
    
    $filename = $_POST['filename'];
    
    // SECURITY: Use basename() to prevent directory traversal attacks (e.g., ../../etc/passwd)
    $safe_filename = basename($filename);

    // Ensure the filename from the form is the same as the safe filename
    if ($safe_filename === $filename) {
        $filepath = $upload_dir . $safe_filename;

        // Check if the file exists and is actually a file before trying to delete
        if (file_exists($filepath) && is_file($filepath)) {
            // Attempt to delete the file
            if (unlink($filepath)) {
                // Success
            } else {
                // Optional: Handle error if unlink fails (e.g., permissions issue)
                error_log("Failed to delete file: " . $filepath);
            }
        }
    } else {
        // Optional: Log a security warning if a traversal attempt is detected
        error_log("Directory traversal attempt detected: " . $filename);
    }
}

// Redirect back to the main page regardless of outcome
header("Location: index.php");
exit();
?>
