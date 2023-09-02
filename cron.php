<?php
// Load the configuration
$config = include __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
$dont_check_login = true;
include __DIR__ . DIRECTORY_SEPARATOR . '/backup_drive.php';

$currentTime = time();
// After successful backup, update the last backup timestamp to the current time
$config['last_backup_timestamp'] = $currentTime;
file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'config.php', '<?php return ' . var_export($config, true) . ';');
 