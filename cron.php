<?php
// Load the configuration
$config = include 'config.php';

// Check if backup frequency and time are set in the config
if (isset($config['backup_frequency']) && isset($config['backup_time'])) {
    $backupFrequency = strtolower($config['backup_frequency']);
    $backupTime = $config['backup_time'];

    // Calculate the time interval based on the backup frequency
    switch ($backupFrequency) {
        case 'daily':
            $interval = '1 day';
            break;
        case 'weekly':
            $interval = '1 week';
            break;
        case 'monthly':
            $interval = '1 month';
            break;
        default:
            // Invalid backup frequency, do not take backup
            exit;
    }

    // Calculate the last backup timestamp based on the current time and backup time
    $currentTime = time();
    $backupTimestamp = strtotime(date('Y-m-d ') . $backupTime);
    // $no_backupTimestamp =  $backuptimstamp - $interval 
    $no_backupTimestamp = strtotime('-' . $interval, $backupTimestamp);

  
    // Check if it's time for backup and if the last backup timestamp is not already set or older
    if ($currentTime >= $backupTimestamp && (!isset($config['last_backup_timestamp']) || $config['last_backup_timestamp'] < $backupTimestamp)) {

       
        // Calculate the next backup timestamp based on the interval and last backup timestamp
        $lastBackupTimestamp = isset($config['last_backup_timestamp']) ? $config['last_backup_timestamp'] : $no_backupTimestamp;
        $nextBackupTimestamp = strtotime('+' . $interval, $lastBackupTimestamp);

                             // echo dates in human readable format
    echo date('Y-m-d H:i:s', $currentTime) . '<br>';
    echo date('Y-m-d H:i:s', $backupTimestamp) . '<br>';
    echo date('Y-m-d H:i:s', $lastBackupTimestamp) . '<br>';
    echo date('Y-m-d H:i:s', $nextBackupTimestamp) . '<br>';
        // Check if it's time for backup based on the next backup timestamp
        if ($currentTime >= $nextBackupTimestamp) {


    

            // Perform the backup operation here
            include 'backup_drive.php';

            // After successful backup, update the last backup timestamp to the current time
            $config['last_backup_timestamp'] = $currentTime;
            file_put_contents('config.php', '<?php return ' . var_export($config, true) . ';');
        }
    }
}