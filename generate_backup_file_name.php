<?php
function generateBackupFileName($template) {
    $currentDate = date('j-F-Y-l');
    // current time in 12 hour format with AM/PM separated by - 
    $currentTime = date('h-ia-T');
    // getting datbase name from config.php
    $config = include('config.php');
    $databaseName = $config['db_name'];

    // Replace placeholders in the template with actual values
    $backupFileName = str_replace('{date}', $currentDate, $template);
    $backupFileName = str_replace('{time}', $currentTime, $backupFileName);
    $backupFileName = str_replace('{database_name}', $databaseName, $backupFileName);

    return $backupFileName . '.zip'; // Add .zip extension to the file name
}


