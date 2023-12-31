<?php
ini_set('memory_limit', '-1');
// Include the config file
include 'validate_login.php'; 
$config = include('config.php');

// MySQL database credentials
$dbHost = $config['db_host'];
$dbUser = $config['db_username'];
$dbPass = $config['db_password'];
$dbName = $config['db_name'];

// Path of mysqlDump
// $pathOfMysqlDump = 'C:\xampp\mysql\bin\mysqldump.exe';
// $pathOfMysqlDump = 'mysqldump';
$pathOfMysqlDump = $config['mysqldump_path'];
// Backup file name and path
$backupFile = 'backup.sql';
$zipFile = 'backup.zip';

// Perform backup
if ($dbPass === '') {
    $command = "$pathOfMysqlDump --single-transaction --host=$dbHost --user=$dbUser $dbName > $backupFile";
} else {
    $command = "$pathOfMysqlDump  --single-transaction --host=$dbHost --user=$dbUser --password='$dbPass' $dbName > $backupFile";
}
// echo 'Executing command: ' . $command . PHP_EOL;
$output = [];
$returnVar = 0;
exec($command, $output, $returnVar);

// Print the captured output and return status
// echo "Command Output:\n";
// echo implode("\n", $output);
// echo "\nReturn Status: $returnVar\n;";


// Check for errors
if ($returnVar !== 0) {
    echo $command."<br>"; 
    echo 'mysqldump command failed with error code: ' . $returnVar;
    var_dump($output);
    var_dump($returnVar);
    echo 'Error output: ' . implode(PHP_EOL, $output);
    exit;
}

// Create zip archive
$zip = new ZipArchive();
$zip->open($zipFile, ZipArchive::CREATE);
$zip->addFile($backupFile);
$zip->close();

include("generate_backup_file_name.php");


// Example usage:
// get template from config
$template = $config['backup_file_name'];
$backupFileName = generateBackupFileName($template);
// echo $backupFileName;


// Set headers for download
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $backupFileName . '"');
header('Content-Length: ' . filesize($zipFile));

// Output the zip file for download
readfile($zipFile);

// Clean up temporary files
unlink($backupFile);
unlink($zipFile);   
?>
