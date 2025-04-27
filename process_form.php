<?php
// process_form.php - Handles email form submissions for Koreapedia
// Stores submissions in a CSV file with email, IP address, and timestamp

// Configuration
$data_file = 'data/email_submissions.csv';
$redirect_success = 'thank-you.html';
$redirect_error = 'error.html';

// Create data directory if it doesn't exist
if (!file_exists(dirname($data_file))) {
    mkdir(dirname($data_file), 0755, true);
}

// Initialize or create the CSV file if it doesn't exist
if (!file_exists($data_file)) {
    $header = "email,ip_address,timestamp\n";
    file_put_contents($data_file, $header);
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate email
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    
    if ($email) {
        // Get IP address
        $ip_address = $_SERVER['REMOTE_ADDR'];
        
        // Get current timestamp
        $timestamp = date('Y-m-d H:i:s');
        
        // Format data for CSV
        $data = sprintf("%s,%s,%s\n", 
            str_replace(',', '', $email), // Remove commas from email to prevent CSV issues
            $ip_address,
            $timestamp
        );
        
        // Append to CSV file
        if (file_put_contents($data_file, $data, FILE_APPEND)) {
            // Redirect to thank you page
            header("Location: $redirect_success");
            exit;
        }
    }
    
    // If we get here, something went wrong
    header("Location: $redirect_error");
    exit;
}

// If accessed directly without POST data, redirect to homepage
header("Location: index.html");
exit;
?>
