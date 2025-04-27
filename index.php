<?php
// admin/index.php - Password-protected admin interface for viewing email submissions

// Configuration
$data_file = '../data/email_submissions.csv';
$admin_password = 'adminpwd@koreapedia.net'; // Updated password as requested by user

// Check if user is authenticated
session_start();
$authenticated = false;

if (isset($_POST['password']) && $_POST['password'] === $admin_password) {
    $_SESSION['authenticated'] = true;
}

if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    $authenticated = true;
}

// Handle CSV download request
if ($authenticated && isset($_GET['download']) && $_GET['download'] === 'csv') {
    if (file_exists($data_file)) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="koreapedia_email_submissions.csv"');
        readfile($data_file);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koreapedia Admin - Email Submissions</title>
    <style>
        body {
            font-family: 'Open Sans', Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            color: #c62828;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .login-form {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #c62828;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #b71c1c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .actions {
            margin: 20px 0;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!$authenticated): ?>
            <!-- Login Form -->
            <div class="login-form">
                <h2>Admin Login</h2>
                <form method="post">
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit">Login</button>
                </form>
            </div>
        <?php else: ?>
            <!-- Admin Dashboard -->
            <h1>Koreapedia Email Submissions</h1>
            
            <div class="actions">
                <a href="?download=csv"><button>Download as CSV</button></a>
            </div>
            
            <?php
            if (file_exists($data_file)) {
                $submissions = array_map('str_getcsv', file($data_file));
                $header = array_shift($submissions); // Remove header row
                
                // Sort by timestamp (newest first)
                usort($submissions, function($a, $b) {
                    return strtotime($b[2]) - strtotime($a[2]); // Assuming timestamp is at index 2
                });
                
                if (count($submissions) > 0):
            ?>
                <table>
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>IP Address</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $submission): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($submission[0]); ?></td>
                            <td><?php echo htmlspecialchars($submission[1]); ?></td>
                            <td><?php echo htmlspecialchars($submission[2]); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">No submissions yet.</div>
            <?php 
                endif;
            } else {
                echo '<div class="no-data">Data file not found.</div>';
            }
            ?>
            
            <div class="actions" style="margin-top: 20px;">
                <a href="?logout=1"><button>Logout</button></a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
