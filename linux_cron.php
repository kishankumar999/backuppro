<?php
include("cron_parser.php");

function hasShellAccess()
{
    return function_exists('shell_exec') && !empty(shell_exec('echo Test'));
}

function hasCronTabForCommand($commandToCheck)
{
    $existingCronTabs = shell_exec('crontab -l');
    $lines = explode("\n", $existingCronTabs);

    foreach ($lines as $line) {
        // Skip empty lines and comments
        if (trim($line) === '' || strpos(trim($line), '#') === 0) {
            continue;
        }

        // Check if the line contains the command to check
        if (strpos($line, $commandToCheck) !== false) {
            return $line;
        }
    }

    return false;
}

function updateCronTab($newCommand)
{
    // Validate if the command is a valid cron job command and contains "php"
    if (strpos($newCommand, 'php') !== false && preg_match('/^\S+\s+\S+\s+\S+\s+\S+\s+\S+\s+.+$/', $newCommand)) {

        // Read the existing cron tab
        exec('crontab -l', $currentCronTab);

        // print_r($cursrentCronTab);

        $pathOfNewCommand = explode("php", $newCommand, 2)[1]; // Get the path of the new command

        // Filter out existing commands with the same path as the new command
        $filteredCronTab = array_filter($currentCronTab, function ($line) use ($pathOfNewCommand) {
            return strpos($line, $pathOfNewCommand) === false;
        });

        // Add the new command to the filtered cron tab
        $filteredCronTab[] = $newCommand;

        // Update the cron tab
        $newCronTab = implode(PHP_EOL, $filteredCronTab);
        file_put_contents('new_cron_tab', $newCronTab . "\n");
        exec('crontab new_cron_tab');
        //unlink('new_cron_tab');
        return "Cron tab updated successfully.";
    } else {
        return "Invalid Cron Job Command";
    }
}

// // Example usage
// $newCronJob = '0 10 * * * php /path/to/cron.php';
// updateCronTab($newCronJob);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newCommand = $_POST["new_command"];
    if (hasShellAccess()) {
        echo $updateResult = updateCronTab($newCommand);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head><?php include("favicon.php"); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
     <!-- <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script> -->
     <!-- include css/output.css -->
    <link rel="stylesheet" href="css/output.css">
    <title>Backup Scheduler: Linux (Cron Job)</title>
</head>

<body>

    <?php include('tabs_linux_schedule.php'); ?>
    <div class="bg-gray-100 min-h-screen flex flex-col items-center justify-center">

        <div class="bg-white p-6 rounded shadow md:w-1/2 mt-10 prose ">
            <h2>What do we do here?</h2>
            <p>
                Here on this tab we will be creating a CRON Command which will be used to schedule the backup.
                CRON command is basically a command which is used to schedule a task on a particular time and day and month also
                how they will be repeted on a particular time and day and month.
            </p>
            <p>
                The following <strong> CRON Job Generator that will help you to generate a CRON Command </strong> for your backup.
                It will also expain what the command is doing and when it will be executed. And also next 5 times when it will be executed.
            </p>

           
            <?php if (hasShellAccess()) { ?>
                <p class="">
                    <?php
                    $commandToCheck = "php " . __DIR__ . DIRECTORY_SEPARATOR . "cron.php";
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
                        $command = implode(' ', array_slice($parts, 5));

                        // Output parsed components
                        // echo "Minute: $minute\n";
                        // echo "Hour: $hour\n";
                        // echo "Day of Month: $dayOfMonth\n";
                        // echo "Month: $month\n";
                        // echo "Day of Week: $dayOfWeek\n";
                        // echo "Command: $command\n";

                        $cronScheduler = new CronScheduler();
$nextTimes = $cronScheduler->generateNextTimes($minute, $hour, $dayOfMonth, $month, $dayOfWeek, 5);

?>
<div class="bg-white px-6 pb-8 pt-5 shadow-xl">
  <p class="mb-3 text-3xl">Current Backup Schedule</p>
    

  <p class="mb-3 mt-6 text-lg">5 Next backup dates.</p>
  <ul class="flex flex-col gap-3">
   
 
  
<?php
foreach($nextTimes as $nextTime) {
    ?>
     <li>
      <div class="flex items-center gap-2">
        <svg class="w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="nz sb axp"><path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z" clip-rule="evenodd"></path></svg>
        <div class="text-lg">
            <?php echo date("l F j, Y, h:i A", $nextTime->getTimestamp()) . "\n"; ?>
        </div>
      </div>
      <div class="font-bold2 ml-6 text-sm text-gray-600">
        <?php echo getRelativeTime($nextTime->format('Y-m-d H:i:s')) . "\n"; ?>    
    </div>
    </li>
    <?php
    
    
}?>
</ul>
</div>

<div class="bg-gray-200 text-gray-500 p-4 text-base text-sm">

    <?php echo  $command_there ; ?>
 

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
        <div class="bg-white prose p-6 rounded shadow md:w-1/2 my-10">
            <h2>Cron Job Command Generator</h2>
            <p class="mute">Specify the time and frequency of the automated backups to generate command to be used. </p>

            <!-- Grid of 2 columsn in tailiwnd -->
            <div class="grid grid-cols-2 gap-4 mb-4">

                <label class="block mb-2" for="minute">Minute</label>
                <select id="minute" class="w-full  mb-2 px-2 py-1 border rounded">
                    <option value="*">*</option>
                    <?php
                    for ($i = 0; $i <= 59; $i++) {
                        if(isset($minute) && $minute == $i) {
                            $selected = "selected=\"selected\"";
                        } else {
                            $selected = "";
                        }

                
                        echo "<option   $selected  value=\"$i\">$i</option>";
                    }
                    ?>
                </select>

                <label class="block mb-2" for="hour">Hour</label>
                <select id="hour" class="w-full  mb-2 px-2 py-1 border rounded">
                    <option value="*">*</option>
                    <?php
                    for ($i = 0; $i <= 23; $i++) {
                        if(isset($hour) && $hour == $i) {
                            $selected = "selected=\"selected\"";
                        } else {
                            $selected = "";
                        }
                        echo "<option  $selected value=\"$i\">$i</option>";
                    }
                    ?>
                </select>

                <label class="block mb-2" for="dayOfMonth">Day of Month</label>
                <select id="dayOfMonth" class="w-full  mb-2 px-2 py-1 border rounded">
                    <option value="*">*</option>
                    <?php
                    for ($i = 1; $i <= 31; $i++) {
                        if(isset($dayOfMonth) && $dayOfMonth == $i) {
                            $selected = "selected=\"selected\"";
                        } else {
                            $selected = "";
                        }
                        echo "<option $selected value=\"$i\">$i</option>";
                    }
                    ?>
                </select>

                <label class="block mb-2" for="month">Month</label>
                <select id="month" class="w-full  mb-2 px-2 py-1 border rounded">
                    <option value="*">*</option>
                    <?php
                    if (isset($month) && $month == $i) {
                        $selected = "selected=\"selected\"";
                    } else {
                        $selected = "";
                    }
                    for ($i = 1; $i <= 12; $i++) {
                        echo "<option $selected value=\"$i\">$i</option>";
                    }
                    ?>
                </select>

                <label class="block mb-2" for="dayOfWeek">Day of Week</label>
                <select id="dayOfWeek" class="w-full  mb-2 px-2 py-1 border rounded">
                    <option value="*">*</option>
                    <?php
                    if(isset($dayOfWeek) && $dayOfWeek == $i) {
                        $selected = "selected=\"selected\"";
                    } else {
                        $selected = "";
                    }
                    for ($i = 0; $i <= 7; $i++) {
                        echo "<option  $selected  value=\"$i\">$i</option>";
                    }
                    ?>
                </select>

            </div>

            <div class="mt-4">
                <h2 class="block mb-2">Generated Command</h2>
                <code id="generatedCommand" class="bg-gray-200 p-2 block rounded w-full"></code>
                <h3>Explaination</h3>
                <p id="explanation" class="mt-2 text-gray-600"></p>
            </div>

            <div class="mt-4">
                <h3 class="block mb-2">Next Scheduled Times</h3>
                <ul id="nextTimesList" class="list-disc list-inside mt-2 text-gray-600"></ul>
            </div>

            <h2>Add command to CRON Jobs on platform</h2>
            <form method="POST" class="mt-4">
                <label class="block mb-2" for="new_command">New Command</label>
                <input id="new_command" name="new_command" class="w-full  mb-2 px-2 py-1 border rounded" value="<?php echo $commandToCheck; ?>">
                <button type="submit" class="bg-blue-500 block w-full hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Set Command Now</button>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const commandToRun = '<?php
                                    // escape string for use in javascript


                                    echo  str_replace("\\", "\\\\", " php " . __DIR__ . DIRECTORY_SEPARATOR . "cron.php");
                                    ?>'
            const generateBtn = document.getElementById("generateBtn");
            const explanation = document.getElementById("explanation");
            const nextTimesList = document.getElementById("nextTimesList");
            const minuteInput = document.getElementById("minute");
            const hourInput = document.getElementById("hour");
            const dayOfMonthInput = document.getElementById("dayOfMonth");
            const monthInput = document.getElementById("month");
            const dayOfWeekInput = document.getElementById("dayOfWeek");

            // on change of input with class w-full run the function

            document.querySelectorAll(".w-full").forEach((input) => {
                input.addEventListener("change", function() {
                    const minute = minuteInput.value;
                    const hour = hourInput.value;
                    const dayOfMonth = dayOfMonthInput.value;
                    const month = monthInput.value;
                    const dayOfWeek = dayOfWeekInput.value==7?"0":dayOfWeekInput.value;
                    console.log(dayOfWeek);
                    

                    const command = `${minute} ${hour} ${dayOfMonth} ${month} ${dayOfWeek}`;
                    generatedCommand.textContent = command + commandToRun;

                    // also update it on input with id new_command
                    document.getElementById("new_command").value = command + commandToRun;

                    const readableExplanation = generateExplanation(minute, hour, dayOfMonth, month, dayOfWeek);
                    explanation.textContent = readableExplanation;

                    const nextTimes = generateNextTimes(minute, hour, dayOfMonth, month, dayOfWeek, 5);
                    renderNextTimes(nextTimes);
                });
            });

            // trigger input change on page load
            minuteInput.dispatchEvent(new Event("change"));

            function generateNextTimes(minute, hour, dayOfMonth, month, dayOfWeek, count) {
                const currentDate = new Date();

                const minutes = parseCronComponent(minute, 59);
                const hours = parseCronComponent(hour, 23);
                const daysOfMonth = parseCronComponent(dayOfMonth, 31);
                const months = parseCronComponent(month, 12);
                const daysOfWeek = parseCronComponent(dayOfWeek, 7);

                const nextTimes = [];
                let nextTimeCandidate = new Date(currentDate);

                while (nextTimes.length < count) {
                    nextTimeCandidate.setSeconds(0);

                    while (
                        !minutes.includes(nextTimeCandidate.getMinutes()) ||
                        !hours.includes(nextTimeCandidate.getHours()) ||
                        !daysOfMonth.includes(nextTimeCandidate.getDate()) ||
                        !months.includes(nextTimeCandidate.getMonth() + 1) ||
                        !daysOfWeek.includes(nextTimeCandidate.getDay())
                    ) {
                        nextTimeCandidate.setMinutes(nextTimeCandidate.getMinutes() + 1);
                    }

                    nextTimes.push(new Date(nextTimeCandidate));
                    nextTimeCandidate.setMinutes(nextTimeCandidate.getMinutes() + 1);
                }

                return nextTimes;
            }

            function renderNextTimes(nextTimes) {
                nextTimesList.innerHTML = "";
                nextTimes.forEach((time) => {
                    const listItem = document.createElement("li");
                    //listItem.textContent = time.toLocaleString();

                    const options = {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: 'numeric',
                        minute: 'numeric',
                        second: 'numeric',
                        timeZoneName: 'short'
                    };

                    listItem.textContent = time.toLocaleString('en-US', options);
                    nextTimesList.appendChild(listItem);
                });
            }

            function parseCronComponent(component, maxValue) {
                if (component === "*") {
                    return Array.from({
                        length: maxValue + 1
                    }, (_, i) => i);
                }
                return component.split(",").map(item => parseInt(item));
            }

            function generateExplanation(minute, hour, dayOfMonth, month, dayOfWeek) {
                const daysOfWeek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

                const minuteExplanation = minute === "*" ?
                    "Every minute" :
                    `Every ${minute === "*" ? "minute" : `at minute ${minute}`}`;

                const hourExplanation = hour === "*" ?
                    "every hour" :
                    `every hour at ${hour}:00`;

                const dayOfMonthExplanation = dayOfMonth === "*" ?
                    "on every day of the month" :
                    `on the ${ordinalSuffix(dayOfMonth)} day of the month`;

                const monthExplanation = month === "*" ?
                    "in every month" :
                    `in ${monthToName(month)}`;

                const dayOfWeekExplanation = dayOfWeek === "*" ?
                    "on every day of the week" :
                    `on ${daysOfWeek[dayOfWeek]} (${dayOfWeek === "0" ? "Sunday" : "Day " + dayOfWeek})`;

                return `${minuteExplanation}, ${hourExplanation}, ${dayOfMonthExplanation}, ${monthExplanation}, ${dayOfWeekExplanation}.`;
            }

            function ordinalSuffix(day) {
                if (day === "1") return "1st";
                if (day === "2") return "2nd";
                if (day === "3") return "3rd";
                return day + "th";
            }

            function monthToName(month) {
                const monthNames = [
                    "January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ];
                return monthNames[parseInt(month) - 1];
            }
        });
    </script>
</body>

</html>