<?php
$taskName = "MyScheduledTask";
$cronScriptPath = __DIR__ . "/cron.php"; // Assuming cron.php is in the same folder as this script
$cronSchedule = "*/5 * * * *"; // Schedule to run every 5 minutes

// Get the path to php.exe
$phpPath = exec("where php");
$phpPath = trim($phpPath);

if (empty($phpPath)) {
    echo "Unable to find PHP executable (php.exe). Please ensure PHP is installed and in the system's PATH.";
    exit;
}

// Get the path to schtasks
$schtasksPath = exec("where schtasks");
$schtasksPath = trim($schtasksPath);

if (empty($schtasksPath)) {
    echo "Unable to find schtasks executable. Please ensure the Task Scheduler is available on your system.";
    exit;
}

// Check if the task already exists with the same command
$existingTaskCommand = "\"$phpPath $cronScriptPath\"";
$command = "\"$schtasksPath\" /QUERY /TN \"$taskName\" /FO CSV";

exec($command, $output, $returnVar);

if ($returnVar === 0) {
    $existingTaskOutput = implode("\n", $output);
    if (strpos($existingTaskOutput, $existingTaskCommand) !== false) {
        echo "A scheduled task with the same command already exists.";
        exit;
    }
}

// Construct the command to create the scheduled task
$command = "\"$schtasksPath\" /Create /TN \"$taskName\" /TR \"$phpPath $cronScriptPath\" /SC MINUTE /MO 5";

// Execute the command
exec($command, $output, $returnVar);

if ($returnVar === 0) {
    echo "Scheduled task created successfully.";
} else {
    echo "Failed to create scheduled task.";
}
?>
