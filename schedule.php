<?php
// if linux
$phpPath = "php";
if (PHP_OS === 'Linux') {
    $phpPath = exec("which php");
}
// Define an empty success message
$successMessage = '';
include("cron_parser.php");

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
        <div class=" max-w-xl mx-auto   gap-5  mb-10">
            <div class="flex flex-col items-center justify-center bg-white p-8">
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
        </div>

    <?php
    }
    ?>

    <?php
    // if Linux platform
    if (strtoupper(PHP_OS) === 'LINUX') {
    ?>

        <!-- flowbite button -->
        <div class=" max-w-xl mx-auto   gap-5  mb-10 bg-white">
            <div class="flex flex-col items-center justify-center  p-8">
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

            <div class="p-8 border-t ">
                <details class="group mb-8">
                    <summary class="flex justify-between items-center font-medium cursor-pointer list-none">
                        <span> Current Schedule</span>
                        <span class="transition group-open:rotate-180">
                            <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24">
                                <path d="M6 9l6 6 6-6"></path>
                            </svg>
                        </span>
                    </summary>
                    <div class="border border-gray-300 rounded   mt-5 p-8 prose ">


                        <?php

                        function hasShellAccess()
                        {
                            return function_exists('shell_exec') && !empty(shell_exec('echo Test'));
                        }
                        $offset = date('P');
                        $original_operation = $offset[0]; // Extract the operation (+ or -)
                        $operation = $offset[0]; // Extract the operation (+ or -)\
                        // flip the operationg
                        if ($operation == "+") {
                            $operation = "-";
                        } else {
                            $operation = "+";
                        }
                        $time_parts = explode(':', substr($offset, 1)); // Split the remaining part (05:30) by :

                        $hours = (int)$time_parts[0]; // Extract hours as an integer
                        $minutes = (int)$time_parts[1]; // Extract minutes as an integer

                        ?>

                        <?php if (hasShellAccess()) { ?>
                            <p class="">
                                <?php
                                $commandToCheck = $phpPath . " " . __DIR__ . DIRECTORY_SEPARATOR . "cron.php";
                                $command_there = hasCronTabForCommand($commandToCheck);
                                if ($command_there) {
                                    //secho "Cron tab is set for the command: $commandToCheck";

                                    // Breaking the set command into Schedule. 

                                    // Split the crontab entry into its components
                                    $parts = preg_split('/\s+/', $command_there);

                                    // Extract schedule and command
                                    $minute = $parts[0];
                                    $hour = $parts[1];
                                    $dayOfMonth = $parts[2];
                                    $month = $parts[3];
                                    $dayOfWeek = $parts[4];

                                    $command_schedule_expression = $minute . " " . $hour . " " . $dayOfMonth . " " . $month . " " . $dayOfWeek;

                                    $command = implode(' ', array_slice($parts, 5));

                                    // Output parsed components
                                    // echo "Minute: $minute\n";
                                    // echo "Hour: $hour\n"; 
                                    // echo "Day of Month: $dayOfMonth\n";
                                    // echo "Month: $month\n";
                                    // echo "Day of Week: $dayOfWeek\n";
                                    // echo "Command: $command\n";

                                    $cronScheduler = new CronScheduler();

                                    // add the offset to the hours and minutes
                                    // $hour = modifyHour($hour, $hours,  $original_operation);
                                    // $minute = modifyMinutes($minute, $minutes,  $original_operation);

                                    $nextTimes = $cronScheduler->generateNextTimes($minute, $hour, $dayOfMonth, $month, $dayOfWeek, 5);

                                ?>
                            <div class="bg-white px-6 pb-8 pt-5 shadow-xl">
                                <p class="mb-3 text-3xl">Current Backup Schedule</p>


                                <p class="mb-3 mt-6 text-lg">5 Next backup dates.</p>
                                <ul class="flex flex-col gap-3">



                                    <?php
                                    foreach ($nextTimes as $nextTime) {
                                    ?>
                                        <li>
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="nz sb axp">
                                                    <path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z" clip-rule="evenodd"></path>
                                                </svg>
                                                <div class="text-lg">
                                                    <?php echo date("l F j, Y, h:i A T", $nextTime->getTimestamp()) . "\n"; ?>
                                                </div>
                                            </div>
                                            <div class="font-bold2 ml-6 text-sm text-gray-600">
                                                <?php echo getRelativeTime($nextTime->format('Y-m-d H:i:s ')) . "\n"; ?>
                                            </div>
                                        </li>
                                    <?php


                                    } ?>
                                </ul>
                            </div>

                            <div class="bg-gray-200 text-gray-500 p-4 text-base text-sm">

                                <?php echo  $command_there; ?>


                            </div>
                        <?php
                                } else {
                                    echo "No cron tab found for the command: <br><code> $commandToCheck </code>";
                                }

                        ?>
                        </p>
                    <?php } ?>
                    <p class="bg-yellow-100 py-1 px-2">
                        <?php


                        if (hasShellAccess()) {
                            echo "✔ This script has shell access.";
                        } else {
                            echo "⚠ This script does not have shell access. <br> You will have to setup the CRON Job manually.";
                        }
                        ?>
                    </p>

                    </div>

                </details>

            </div>

        </div>

    <?php
    }
    ?>

</body>

</html>