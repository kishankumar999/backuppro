<?php
// Define an empty success message
$successMessage = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the backup frequency and time from the form
    $backupFrequency = $_POST['backup_frequency'];
    $backupTime = $_POST['backup_time'];

    // Save the backup frequency and time to the config file
    $config = include 'config.php';
    $config['backup_frequency'] = $backupFrequency;
    $config['backup_time'] = $backupTime;
    file_put_contents('config.php', '<?php return ' . var_export($config, true) . ';');

    // Set the success message
    $successMessage = 'Backup frequency and time have been saved successfully!';
}

// Include the backup frequency and time from config.php
$config = include 'config.php';
$backupFrequency = isset($config['backup_frequency']) ? $config['backup_frequency'] : '';
$backupTime = isset($config['backup_time']) ? $config['backup_time'] : '';
?>

<!DOCTYPE html>
<html>
<head><?php include("favicon.php"); ?>
    <title>Backup Frequency Setup</title>
     <!-- <link rel="stylesheet" href="css/output.css"> -->
     <!-- include css/output.css -->
     <link rel="stylesheet" href="css/output.css">
</head>
<body class="bg-gray-50">
   
 <!-- include tabs -->
    <?php include 'drive_tabs.php'; ?>

 <?php 
// if windows platform
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    ?>

    <!-- flowbite button -->
    <div class="flex flex-col justify-center max-w-xl mx-auto items-center  gap-5 bg-white p-8 mb-10">
        <!-- Windows Logo  -->
        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5f/Windows_logo_-_2012.svg/1200px-Windows_logo_-_2012.svg.png" alt="Windows Logo" width="50" height="50">
       <!-- muted text tailwind -->
       <div class="flex flex-col justify-center gap-3">

           <p class="text-gray-400 text-sm">We detected your are running BackupPro on Windows Server.</p>
           <a href="windows_scheduler.php" class=" inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
               Set up Automated Backup on Windows. 
            </a>
        </div>
    </div>

    <?php
} 
?>

<?php
// if Linux platform
if (strtoupper(PHP_OS) === 'LINUX') {
    ?>

    <!-- flowbite button -->
    <div class="flex flex-col justify-center max-w-xl mx-auto items-center gap-5 bg-white p-8 mb-10">
        <!-- Linux Logo -->
        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/35/Tux.svg/1200px-Tux.svg.png" alt="Linux Logo" width="50" height="50">
        <!-- muted text tailwind -->
        <div class="flex flex-col justify-center gap-3">
            <p class="text-gray-400 text-sm">We detected you are running BackupPro on a Linux Server.</p>
            <a href="linux_cron.php" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                Set up Automated Backup on Linux.
            </a>
        </div>
    </div>

    <?php
}
?>

</body>
</html>
