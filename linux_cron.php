<?php

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
            return true;
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
        $filteredCronTab = array_filter($currentCronTab, function($line) use ($pathOfNewCommand) {
            return strpos($line, $pathOfNewCommand) === false;
        });

        // Add the new command to the filtered cron tab
        $filteredCronTab[] = $newCommand;
         
        // Update the cron tab
        $newCronTab = implode(PHP_EOL, $filteredCronTab);
        file_put_contents('new_cron_tab', $newCronTab."\n");
        exec('crontab new_cron_tab');
        //unlink('new_cron_tab');
        return "Cron tab updated successfully.";
    }
    else
    {
        return "Invalid Cron Job Command";
    }
}

// // Example usage
// $newCronJob = '0 10 * * * php /path/to/cron.php';
// updateCronTab($newCronJob);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newCommand = $_POST["new_command"];
    if(hasShellAccess())
    {
        echo $updateResult = updateCronTab($newCommand);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <title>Cron Job Generator</title>
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

            <h2>Are there any Previous Schedule Set?</h2>




            <?php if (hasShellAccess()) { ?>
                <p class="bg-yellow-100 py-1 px-2">
                    <?php



                    $commandToCheck = "php " . __DIR__ . DIRECTORY_SEPARATOR . "cron.php";

                    if (hasCronTabForCommand($commandToCheck)) {
                        echo "Cron tab is set for the command: $commandToCheck";
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
                        echo "<option value=\"$i\">$i</option>";
                    }
                    ?>
                </select>

                <label class="block mb-2" for="hour">Hour</label>
                <select id="hour" class="w-full  mb-2 px-2 py-1 border rounded">
                    <option value="*">*</option>
                    <?php
                    for ($i = 0; $i <= 23; $i++) {
                        echo "<option value=\"$i\">$i</option>";
                    }
                    ?>
                </select>

                <label class="block mb-2" for="dayOfMonth">Day of Month</label>
                <select id="dayOfMonth" class="w-full  mb-2 px-2 py-1 border rounded">
                    <option value="*">*</option>
                    <?php
                    for ($i = 1; $i <= 31; $i++) {
                        echo "<option value=\"$i\">$i</option>";
                    }
                    ?>
                </select>

                <label class="block mb-2" for="month">Month</label>
                <select id="month" class="w-full  mb-2 px-2 py-1 border rounded">
                    <option value="*">*</option>
                    <?php
                    for ($i = 1; $i <= 12; $i++) {
                        echo "<option value=\"$i\">$i</option>";
                    }
                    ?>
                </select>

                <label class="block mb-2" for="dayOfWeek">Day of Week</label>
                <select id="dayOfWeek" class="w-full  mb-2 px-2 py-1 border rounded">
                    <option value="*">*</option>
                    <?php
                    for ($i = 0; $i <= 7; $i++) {
                        echo "<option value=\"$i\">$i</option>";
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
                <button type="submit" class="bg-blue-500 block w-full hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Set Command Nows</button>
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
                    const dayOfWeek = dayOfWeekInput.value;

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

                    listItem.textContent  = time.toLocaleString('en-US', options);
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