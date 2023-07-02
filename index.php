<?php
// Check if config.php exists


if (file_exists('config.php')) {
    // Redirect to login.php
    // if user logged in then redirect to dashboard
    session_start();
    if (isset($_SESSION['username'])) {
        // Redirect to the dashboard
        header('Location: dashboard.php');
        exit();
    }
   header('Location: login.php');
} else {
    // Redirect to setup.php
    header('Location: setup.php');
}





exit;
?>