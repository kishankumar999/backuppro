<?php
// Start or resume the session
// date_default_timezone_set('America/New_York'); // Change 'America/New_York' to your preferred timezone
$config = include('config.php');

if (isset($config['timezone'])) 
{
    date_default_timezone_set($config['timezone']);
} else {
    date_default_timezone_set('Asia/Kolkata');
}
session_start();

// Check if the session is not set
if (!isset($_SESSION['username'])) {
    // Redirect to the login page
    header('Location: login.php');
    exit();
}

// Continue with the rest of the script for authenticated users
?>