<?php
// Get the path to php.exe

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    function convertDayToValidFormat($day)
{
    $day = strtolower($day);

    switch ($day) {
        case 'monday':
            return 'MON';
        case 'tuesday':
            return 'TUE';
        case 'wednesday':
            return 'WED';
        case 'thursday':
            return 'THU';
        case 'friday':
            return 'FRI';
        case 'saturday':
            return 'SAT';
        case 'sunday':
            return 'SUN';
        default:
            return false;
    }
}
    
function removeScheduledTask($taskName) {
    $command = 'schtasks /delete /tn "' . $taskName . '" /f';
    $output = shell_exec($command);

    if (strpos($output, 'SUCCESS') !== false) {
        return true; // Task removed successfully
    } else {
        return false; // Failed to remove task
    }
}

function convertMonthsToValidFormat(array $months)
{
    $validMonths = array_map(function ($month) {
        $month = strtolower($month);
        switch ($month) {
            case 'january':
                return 'jan';
            case 'february':
                return 'feb';
            case 'march':
                return 'mar';
            case 'april':
                return 'apr';
            case 'may':
                return 'may';
            case 'june':
                return 'jun';
            case 'july':
                return 'jul';
            case 'august':
                return 'aug';
            case 'september':
                return 'sep';
            case 'october':
                return 'oct';
            case 'november':
                return 'nov';
            case 'december':
                return 'dec';
            default:
                return false;
        }
    }, $months);

    return array_filter($validMonths); // Remove any false values from the array
}

function convertDaysToValidFormat(array $days)
{
    $validDays = array_map(function ($day) {
        $day = strtolower($day);
        switch ($day) {
            case 'monday':
                return 'MON';
            case 'tuesday':
                return 'TUE';
            case 'wednesday':
                return 'WED';
            case 'thursday':
                return 'THU';
            case 'friday':
                return 'FRI';
            case 'saturday':
                return 'SAT';
            case 'sunday':
                return 'SUN';
            default:
                return false;
        }
    }, $days);

    return array_filter($validDays); // Remove any false values from the array
}


    $taskName = $_POST['taskName'];

    // Remove the previous task if it exists
    removeScheduledTask($taskName);

$_POST['days']= json_decode($_POST['days']);
$_POST['days'] =  convertDaysToValidFormat($_POST['days']);
$_POST['months']= json_decode($_POST['months']);
 $_POST['months']= convertMonthsToValidFormat($_POST['months']);

// print_r($_POST['months']);

$phpPath = exec("where php");
$phpPath = trim($phpPath);
$cronScriptPath = __DIR__ . "/cron.php"; // Assuming cron.php is in the same folder as this script

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

    $triggerType = $_POST['triggerType'];

    // Check if the task already exists with the same name
    $existingTaskCommand = "\"$phpPath $cronScriptPath\"";
    $command = "$schtasksPath /QUERY /TN \"$taskName\" /FO CSV";

    exec($command, $output, $returnVar);

    if ($returnVar === 0) {
        $existingTaskOutput = implode("\n", $output);
        if (strpos($existingTaskOutput, $existingTaskCommand) !== false) {
            echo "A scheduled task with the same name already exists.";
            exit;
        }
    }

    // Construct the command to create the scheduled task
    switch ($triggerType) {
        case 'hourly':
            $frequency = $_POST['frequencyHours'];
            $command = "$schtasksPath /Create /TN \"$taskName\" /TR \"$phpPath $cronScriptPath\" /SC HOURLY /MO $frequency";
            break;

        case 'daily':
            $startTime = $_POST['startTime'];
            $command = "$schtasksPath /Create /TN \"$taskName\" /TR \"$phpPath $cronScriptPath\" /SC DAILY /ST $startTime";
            break;

        case 'weekly':
            // type of days
            // convert json string to array
            $days = isset($_POST['days']) ? implode(',', $_POST['days']) : '';
            $startTime = $_POST['startTime'];
            $command = "$schtasksPath /Create /TN \"$taskName\" /TR \"$phpPath $cronScriptPath\" /SC WEEKLY /D $days /ST $startTime";
            break;

        case 'monthly':
            $months = isset($_POST['months']) ? implode(',', $_POST['months']) : '';
            $day = isset($_POST['dayMonthly']) ? $_POST['dayMonthly']: '1';
            $startTime = $_POST['startTime'];
            $command = "$schtasksPath /Create /TN \"$taskName\" /TR \"$phpPath $cronScriptPath\" /SC MONTHLY /M \"$months\" /D $day /ST $startTime";
            break;

        case 'startup':
            $command = "$schtasksPath /Create /TN \"$taskName\" /TR \"$phpPath $cronScriptPath\" /SC ONSTART";
            break;

        case 'logon':
            $command = "$schtasksPath /Create /TN \"$taskName\" /TR \"$phpPath $cronScriptPath\" /SC ONLOGON";
            break;
    }

    // Execute the command
    exec($command, $output, $returnVar);

    if ($returnVar === 0) {
        echo "Scheduled task created successfully.";
    } else {
        echo "Failed to create scheduled task.";
        echo "<pre>";
        print_r($output);
        echo $command;
        echo "</pre>";
    }
}
?>