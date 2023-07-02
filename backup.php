<?php
// Include the config file
include 'validate_login.php'; 
$config = include('config.php');

// MySQL database credentials
$dbHost = $config['db_host'];
$dbUser = $config['db_username'];
$dbPass = $config['db_password'];
$dbName = $config['db_name'];

// Path of mysqlDump
$pathOfMysqlDump = 'C:\xampp\mysql\bin\mysqldump.exe';
// $pathOfMysqlDump = 'mysqldump';

// Backup file name and path
$backupFile = 'backup.sql';
$zipFile = 'backup.zip';

// Perform backup
if ($dbPass === '') {
    $command = "$pathOfMysqlDump --single-transaction --host=$dbHost --user=$dbUser $dbName > $backupFile";
} else {
    $command = "mysqldump --single-transaction --host=$dbHost --user=$dbUser --password='$dbPass' $dbName > $backupFile";
}
// echo 'Executing command: ' . $command . PHP_EOL;
$output = [];
$returnVar = 0;
exec($command, $output, $returnVar);

// Check for errors
if ($returnVar !== 0) {
    echo 'mysqldump command failed with error code: ' . $returnVar;
    var_dump($output);
    echo 'Error output: ' . implode(PHP_EOL, $output);
    exit;
}

// Create zip archive
$zip = new ZipArchive();
$zip->open($zipFile, ZipArchive::CREATE);
$zip->addFile($backupFile);
$zip->close();

// Set headers for download
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . basename($zipFile) . '"');
header('Content-Length: ' . filesize($zipFile));

// Output the zip file for download
readfile($zipFile);

// Clean up temporary files
unlink($backupFile);
unlink($zipFile);
?>
