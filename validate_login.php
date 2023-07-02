<?php
// Start or resume the session
session_start();

// Check if the session is not set
if (!isset($_SESSION['username'])) {
    // Redirect to the login page
    header('Location: login.php');
    exit();
}

// Continue with the rest of the script for authenticated users
?>