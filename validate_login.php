<?php
// Start or resume the session
// date_default_timezone_set('America/New_York'); // Change 'America/New_York' to your preferred timezone
$config = include('config.php');

if (isset($config['timezone'])) 
{
    $original_timezone_b= date_default_timezone_get();
    date_default_timezone_set($config['timezone']);
    // echo date_default_timezone_get();
} else {
    date_default_timezone_set('Asia/Kolkata');
}

// set a unique session name base on unique_application_name inside config 
session_name($config['unique_application_name']);
session_start();


// Check if the session is not set
if (!isset($_SESSION['username'])) {
    // Redirect to the login page
    header('Location: login.php');
    exit();
}

// Continue with the rest of the script for authenticated users
?>