<?php

// Show all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include 'validate_login.php';
// if linux
$phpPath = "php";
if (PHP_OS === 'Linux') {
    $phpPath = exec("which php");
}

include("cron_parser.php");

function hasShellAccess()
{
    return function_exists('shell_exec') && !empty(shell_exec('echo Test'));
}


function updateCronTab($newCommand)
{
    global $phpPath;
    // Validate if the command is a valid cron job command and contains "php"
    if (strpos($newCommand, $phpPath) !== false && preg_match('/^\S+\s+\S+\s+\S+\s+\S+\s+\S+\s+.+$/', $newCommand)) {

        // Read the existing cron tab
        exec('crontab -l', $currentCronTab);

        // print_r($cursrentCronTab);

        $pathOfNewCommand = explode($phpPath, $newCommand, 2)[1]; // Get the path of the new command

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
        return "Scheduled Backup Updated Successfully";
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
        $updateResult = updateCronTab($newCommand);
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
    <script src="https://cdn.jsdelivr.net/npm/croner@6/dist/croner.umd.min.js"></script>
    <script src="https://unpkg.com/cronstrue@latest/dist/cronstrue.min.js"></script>
    <link rel="stylesheet" href="css/output.css">
    <title>Backup Scheduler: Linux (Cron Job)</title>
</head>

<body class="">

    <?php //include('drive_tabs.php'); 
    ?>


    <a href="dashboard.php" class="m-2 block text-blue-500 font-semibold">
        <!-- back long arrow -->
        <svg class="inline-block w-4 h-4 mr-1 -mt-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 5.293a1 1 0 0 1 0 1.414L4.414 10H16a1 1 0 1 1 0 2H4.414l2.879 2.293a1 1 0 1 1-1.414 1.414l-4-4a1 1 0 0 1 0-1.414l4-4a1 1 0 0 1 1.414 0z" clip-rule="evenodd"></path>
        </svg>
        Back to Dashboard</a>


        <div class="flex items-center justify-center ">
        <?php

        // if isset $updateResult
        if (isset($updateResult)) {
            echo '<div class="bg-green-100 border max-w-3xl border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">' . $updateResult . '</div>';
        }
        ?>
        </div>

    <div class="flex items-center justify-center min-h-[calc(100vh-60px)] ">
       
        <div class="m-10 max-w-4xl grow rounded-lg shadow-lg md:flex bg-white ring-1 ring-gray-900/5 ">
            <div class="shrink-0 rounded-t-lg bg-gray-100 p-8 md:w-64 md:rounded-l-lg md:rounded-tr-none flex md:block flex-row-reverse gap-5">
                <div class="">

                    <div class="mb-7 text-2xl font-semibold">Set Auto Backup</div>
                    <p class="text-gray-600 mb-8">Set it once, all data will be backed automatically on fixed scheduling.</p>
                </div>
                <div class="shrink-0">

                    <img class="w-28 md:w-full" src="uploads/backup-schedule.png" alt="">
                </div>
            </div>
            <div class="grow px-10">

                <?php
                $commandToCheck = $phpPath . " " . __DIR__ . DIRECTORY_SEPARATOR . "cron.php";
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
                if (hasShellAccess()) {
                    $command_there = hasCronTabForCommand($commandToCheck);
                    if ($command_there) {
                        $parts = preg_split('/\s+/', $command_there);
                        // Extract schedule and command
                        $minute = $parts[0];
                        $hour = $parts[1];
                        $dayOfMonth = $parts[2];
                        $month = $parts[3];
                        $dayOfWeek = $parts[4];

                        $command_schedule_expression = $minute . " " . $hour . " " . $dayOfMonth . " " . $month . " " . $dayOfWeek;

                        $command = implode(' ', array_slice($parts, 5));
                        $cronScheduler = new CronScheduler();
                        $nextTimes = $cronScheduler->generateNextTimes($minute, $hour, $dayOfMonth, $month, $dayOfWeek, 5);
                    }
                } ?>

                <div class="tabs mb-8 flex flex-wrap">
                    <input class=" [&:checked+label]:border-t-5 [&:checked+label]:border-border-y absolute opacity-0 [&:checked+label+.panel]:block 
                    [&:checked+label+.panel]:font-semibold
                    [&:checked+label]:border-b-4 [&:checked+label]:border-indigo-500 [&:checked+label]:bg-white [&:checked+label]:text-black" name="tabs" tabindex="1" type="radio" id="tabone" checked="checked" />
                    <label class="label w-full md:w-1/2 cursor-pointer p-5 text-center text-lg text-gray-500 transition duration-300 hover:bg-slate-100 active:bg-gray-100
    border-b-2 border-gray-200
    " for="tabone">Simple</label>





                    <div class="panel md:order-12 hidden w-full pt-8" tabindex="1">

                        <div class="font-semibold">Backup Frequency</div>

                        <label for="simple_backup_frequency" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select an option</label>

                        <?php
                        if (isset($command_schedule_expression)) {
                            //echo $command_schedule_expression;
                        }

                        ?>

                        <select id="simple_backup_frequency" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                            <optgroup label="Hourly">
                                <option <?php if (isset($command_schedule_expression) &&  $command_schedule_expression == "0 * * * *") {
                                            echo "selected='selected'";
                                        } ?> value="0 * * * *">Backup Every Hour</option>
                                <option <?php if (isset($command_schedule_expression) &&  $command_schedule_expression == "0 */2 * * *") {
                                            echo "selected='selected'";
                                        } ?> value="0 */2 * * *">Backup Every Alternative Hour</option>
                                <option <?php if (isset($command_schedule_expression) &&  $command_schedule_expression == "0 */3 * * *") {
                                            echo "selected='selected'";
                                        } ?> value="0 */3 * * *">Backup Every 3 hours</option>
                                <option <?php if (isset($command_schedule_expression) &&  $command_schedule_expression == "0 */6 * * *") {
                                            echo "selected='selected'";
                                        } ?> value="0 */6 * * *">Backup Every 6 hours</option>
                                <option <?php if (isset($command_schedule_expression) &&  $command_schedule_expression == "0 */8 * * *") {
                                            echo "selected='selected'";
                                        } ?> value="0 */8 * * *">Backup every 8 hours</option>
                                <option <?php if (isset($command_schedule_expression) &&  $command_schedule_expression == "0 0,12 * * *") {
                                            echo "selected='selected'";
                                        } ?> value="0 0,12 * * *">Backup Every 12 hours</option>
                            </optgroup>
                            <optgroup label="Daily">
                                <option <?php if (isset($command_schedule_expression) &&  $command_schedule_expression == "0 0 * * *") {
                                            echo "selected='selected'";
                                        } ?> value="0 0 * * *">Backup Every Day</option>
                                <option <?php if (isset($command_schedule_expression) &&  $command_schedule_expression == "0 0 */2 * *") {
                                            echo "selected='selected'";
                                        } ?> value="0 0 */2 * *">Backup Every Alternative Day</option>
                                <option <?php if (isset($command_schedule_expression) &&  $command_schedule_expression == "0 0 */3 * *") {
                                            echo "selected='selected'";
                                        } ?> value="0 0 */3 * *">Backup Every 3 Days</option>
                                <option <?php if (isset($command_schedule_expression) &&  $command_schedule_expression == "0 0 * * 1-5") {
                                            echo "selected='selected'";
                                        } ?> value="0 0 * * 1-5">Backup Every WeekDay</option>
                                <option <?php if (isset($command_schedule_expression) &&  $command_schedule_expression == "0 0 * * 0,6") {
                                            echo "selected='selected'";
                                        } ?> value="0 0 * * 0,6">Backup Every Weekend Days</option>
                            </optgroup>
                            <optgroup label="Weekly">
                                <option <?php if (isset($command_schedule_expression) &&  $command_schedule_expression == "0 0 * * 1") {
                                            echo "selected='selected'";
                                        } ?> value="0 0 * * 1">Backup Every Next Week</option>
                            </optgroup>
                            <optgroup label="Monthly">
                                <option <?php if (isset($command_schedule_expression) &&  $command_schedule_expression == "0 0 1 * *") {
                                            echo "selected='selected'";
                                        } ?> value="0 0 1 * *">Backup Monthly</option>
                                <option <?php if (isset($command_schedule_expression) &&  $command_schedule_expression == "0 0 1 * *") {
                                            echo "selected='selected'";
                                        } ?> value="0 0 1 * *">Backup on Every Month's First Day</option>
                                <option <?php if (isset($command_schedule_expression) &&  $command_schedule_expression == "0 0 1 1,7 *") {
                                            echo "selected='selected'";
                                        } ?> value="0 0 1 1,7 *">Backup Every 6 Months</option>
                            </optgroup>
                            <optgroup label="Yearly">
                                <option <?php if (isset($command_schedule_expression) &&  $command_schedule_expression == "0 0 1 1 *") {
                                            echo "selected='selected'";
                                        } ?> value="0 0 1 1 *">Backup Every Year</option>
                            </optgroup>
                        </select>

                    </div>













                    <input class=" [&:checked+label]:border-t-5 [&:checked+label]:border-border-y absolute opacity-0 [&:checked+label+.panel]:block  [&:checked+label]:border-b-4 [&:checked+label]:border-black [&:checked+label]:bg-white [&:checked+label]:text-black" tabindex="1" name="tabs" type="radio" id="tabtwo" />
                    <label class="label w-full md:w-1/2 cursor-pointer p-5 text-center text-lg  text-gray-500 transition duration-300 hover:bg-slate-100 active:bg-gray-100  border-b-2 border-gray-200" for="tabtwo">Advance</label>
                    <div class="panel  md:order-12 hidden w-full  pt-8" tabindex="1">
                        <!-- Grid of 2 columsn in tailiwnd -->
                        <div class="grid grid-cols-2 gap-4 mb-4">

                            <label class="block mb-2" for="minute">Minute</label>

                            <?php include 'cron_minutes_select.php'; ?>

                            <label class="block mb-2" for="hour">Hour</label>


                            <?php include 'cron_hour_select.php'; ?>

                            <label class="block mb-2" for="dayOfMonth">Day of Month</label>
                            <?php include 'cron_day_select.php'; ?>


                            <label class="block mb-2" for="month">Month</label>
                            <?php include 'cron_month_select.php'; ?>

                            <label class="block mb-2" for="dayOfWeek">Day of Week</label>
                            <?php include 'cron_weekday_select.php'; ?>

                        </div>
                    </div>

                </div>
                <div id="next3runs" class="hidden">
                    <h3 class="block mt-10 font-semibold">Next 3 runs</h3>
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mb-8 mt-3 ">
    <table class="w-full text-sm text-left text-gray-500 ">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50  ">
            <tr>
                <th scope="col" class="px-6 py-3">
                    Timezone
                </th>
                <th scope="col" class="px-6 py-3">
                    1st Run
                </th>
                <th scope="col" class="px-6 py-3">
                    2nd Run
                </th>
                <th scope="col" class="px-6 py-3">
                    3rd Run
                </th>
                
            </tr>
        </thead>
        <tbody id="nexttimebody">
            <tr class="bg-white border-b  ">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                    <div>Asia/kolkata</div>  
                    <div>(Your Tmezone)</div>
                </th>
                <td class="px-6 py-4">
                 <div class="text-sm">September 26, 2023</div> 
                 <div class="text-lg mt-2"> 1:30:00 AM </div> 
                </td>
                <td class="px-6 py-4">
                 <div class="text-sm">September 26, 2023</div> 
                 <div class="text-lg mt-2"> 1:30:00 AM </div> 
                </td>
                <td class="px-6 py-4">
                 <div class="text-sm">September 26, 2023</div> 
                 <div class="text-lg mt-2"> 1:30:00 AM </div> 
                </td>
            </tr>
            <tr class="bg-white border-b  ">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                    asia/kolkata
                </th>
                <td class="px-6 py-4">
                 <div class="text-sm">September 26, 2023</div> 
                 <div class="text-lg mt-2"> 1:30:00 AM </div> 
                </td>
                <td class="px-6 py-4">
                 <div class="text-sm">September 26, 2023</div> 
                 <div class="text-lg mt-2"> 1:30:00 AM </div> 
                </td>
                <td class="px-6 py-4">
                 <div class="text-sm">September 26, 2023</div> 
                 <div class="text-lg mt-2"> 1:30:00 AM </div> 
                </td>
            </tr>
            
        </tbody>
    </table>
</div>


                    <ul id="nextTimesList" class="list-disc list-inside mt-2 text-gray-600"></ul>
                </div>

                <form method="POST" class="mt-4">

                    <input id="new_command" name="new_command" <?php

                                                                if (hasShellAccess()) {
                                                                    //  echo 'type = "hidden"';
                                                                }
                                                                ?> class="w-full  mb-2 px-2 py-1 border rounded" value="<?php echo $commandToCheck; ?>">
                    <button type="submit" class="bg-blue-500 block w-full hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Set Command Now</button>
                </form>


                <div class="bg-white prose  rounded  my-10">



                    <div class="mt-4" id="explaination_box">
                        <code id="generatedCommand" class="hidden bg-gray-200 p-2 block rounded w-full"></code>
                        <h3>Explaination</h3>
                        <p id="explanation" class="mt-2 text-gray-600"></p>
                    </div>





                </div>

            </div>
        </div>
    </div>


    <script>
        function modifyMinutes(currentMinute, minutesToModify, operation) {
            // Ensure currentMinute is in the range [0-59]
            console.log(currentMinute)
            if (currentMinute == "*") {
                return "*";
            }

            currentMinute = parseInt(currentMinute);
            minutesToModify = parseInt(minutesToModify);

            if (currentMinute < 0 || currentMinute > 59) {
                throw new Error("Current minute must be in the range [0-59]");
            }

            if (operation === '+') {
                // Add minutes
                let newMinute = (currentMinute + minutesToModify) % 60;
                return newMinute;
            } else if (operation === '-') {
                // Subtract minutes
                let newMinute = (currentMinute - minutesToModify + 60) % 60;
                return newMinute;
            } else {
                throw new Error("Invalid operation. Use '+' or '-'");
            }
        }

        function modifyHour(currentHour, hoursToModify, operation) {
            if (currentHour == "*") {
                return "*";
            }

            currentHour = parseInt(currentHour);
            hoursToModify = parseInt(hoursToModify);
            // Ensure currentHour is in the range [0-23]
            if (currentHour < 0 || currentHour > 23) {
                throw new Error("Current hour must be in the range [0-23]");
            }

            if (operation === '+') {
                // Add hours
                let newHour = (currentHour + hoursToModify) % 24;
                return newHour;
            } else if (operation === '-') {
                // Subtract hours
                let newHour = (currentHour - hoursToModify + 24) % 24;
                return newHour;
            } else {
                throw new Error("Invalid operation. Use '+' or '-'");
            }
        }
        document.addEventListener("DOMContentLoaded", function() {
            const commandToRun = '<?php
                                    // escape string for use in javascript


                                    echo  str_replace("\\", "\\\\", " " . $phpPath . " " . __DIR__ . DIRECTORY_SEPARATOR . "cron.php");
                                    ?>'
            const frequencySelect = document.getElementById("simple_backup_frequency");
            const generateBtn = document.getElementById("generateBtn");
            const explanation = document.getElementById("explanation");
            const nextTimesList = document.getElementById("nextTimesList");
            const nexttimeBody = document.getElementById("nexttimebody");
            const minuteInput = document.getElementById("minute");
            const hourInput = document.getElementById("hour");
            const dayOfMonthInput = document.getElementById("dayOfMonth");
            const monthInput = document.getElementById("month");
            const dayOfWeekInput = document.getElementById("dayOfWeek");


            frequencySelect.addEventListener("change", function() {
                const selectedValue = frequencySelect.value;
                const cronParts = selectedValue.split(" ");

                if (cronParts.length === 5) {
                    minuteInput.value = cronParts[0];
                    hourInput.value = cronParts[1];
                    dayOfMonthInput.value = cronParts[2];
                    monthInput.value = cronParts[3];
                    dayOfWeekInput.value = cronParts[4];
                } else {
                    // Handle invalid input or clear the input fields
                    minuteInput.value = "";
                    hourInput.value = "";
                    dayOfMonthInput.value = "";
                    monthInput.value = "";
                    dayOfWeekInput.value = "";
                }
            });



            // set offset on the minutes and hours
            const minutes = <?php echo $minutes; ?>;
            const hours = <?php echo $hours; ?>;
            const operation = "<?php echo $original_operation; ?>";


            // on change of input with class w-full run the function

            document.querySelectorAll(".w-full").forEach((input) => {
                input.addEventListener("change", function() {

                    // modify the minutes and hours

                    var originalminute = minuteInput.value;
                    var originalhour = hourInput.value;


                    // if minuteInput.value or hourInput.value is not numeric then exit 
                    // if (isNaN(minuteInput.value) || isNaN(hourInput.value)||minuteInput.value==""||hourInput.value=="" ) {
                    //     console("Not a number")
                    //     return;
                    // }
                    // else
                    // {
                    //     console.log("Is a number",minuteInput.value,"test",hourInput.value);
                    // }


                    // if minutes 
                    // const minute = "" + modifyMinutes(minuteInput.value, minutes, operation);
                    // const hour = "" + modifyHour(hourInput.value, hours, operation);
                    const minute = minuteInput.value;
                    const hour = hourInput.value;
                    // console.log("minutes: ", minute, modifyMinutes(minuteInput.value, minutes, operation));
                    // console.log("hours: ", hour, modifyHour(hourInput.value, hours, operation));

                    console.log(hour)
                    const dayOfMonth = dayOfMonthInput.value;
                    const month = monthInput.value;
                    const dayOfWeek = dayOfWeekInput.value == 7 ? "0" : dayOfWeekInput.value;
                    console.log(dayOfWeek);


                    if (minute == "" || hour == "" || dayOfMonth == "" || month == "" || dayOfWeek == "") {
                        // add class hidden to #next3runs
                        document.getElementById("next3runs").classList.add("hidden");
                        document.getElementById("explaination_box").classList.add("hidden");
                        return;
                    }

                    if (minute == "--" || hour == "--" || dayOfMonth == "--" || month == "" || dayOfWeek == "--") {
                        document.getElementById("explaination_box").classList.add("hidden");
                        document.getElementById("next3runs").classList.add("hidden");
                        return;
                    }

                    document.getElementById("next3runs").classList.remove("hidden");
                    document.getElementById("explaination_box").classList.remove("hidden");

                    const command = `${originalminute} ${originalhour} ${dayOfMonth} ${month} ${dayOfWeek}`;
                    generatedCommand.textContent = command + commandToRun;

                    // also update it on input with id new_command
                    document.getElementById("new_command").value = command + commandToRun;

                    // const readableExplanation = generateExplanation(minute, hour, dayOfMonth, month, dayOfWeek);
                    (minute + " " + hour + " " + dayOfMonth + " " + month + " " + dayOfWeek);
                    explanation.textContent = readableExplanation;

                    // const nextTimes = generateNextTimes(minute, hour, dayOfMonth, month, dayOfWeek, 3);
                    //  const nextTimes = generateNextTimes(minute, hour, dayOfMonth, month, dayOfWeek, 3);
                    const job = Cron(minute + " " + hour + " " + dayOfMonth + " " + month + " " + dayOfWeek, () => {
                        console.log('This will run every fifth second');
                    });
                    const nextTimes = Cron(minute + " " + hour + " " + dayOfMonth + " " + month + " " + dayOfWeek, {
                        timezone: <?php echo '"' . $original_timezone_b . '"'; ?>
                    }).nextRuns(3);
                    // const nextTimes = Cron(minute +" " +hour +" " + dayOfMonth +" " + month +" " + dayOfWeek,{ timezone: 'America/Los_Angeles' }).nextRuns(3);
                    //renderNextTimes(nextTimes);
                    renderNextTimesTable(nextTimes);
                });
            });

            // trigger input change on page load
            minuteInput.dispatchEvent(new Event("change"));


            function renderNextTimes(nextTimes) {
                nextTimesList.innerHTML = "";
                nextTimes.forEach((time) => {
                    const listItem = document.createElement("li");
                //     listItem.textContent = time.toLocaleString();

                    const options = {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: 'numeric',
                        minute: 'numeric',
                        second: 'numeric',
                        timeZoneName: 'long'
                    };

                    // convert time to local time



                    // USER TIMEZZONE LIKE America/Nassau
                    // const userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;



                    listItem.textContent = time.toLocaleString('en-US', options);
                    nextTimesList.appendChild(listItem);
                });
            }
            function renderNextTimesTable(nextTimes) {
                nexttimeBody.innerHTML = "";
                // create a tr element  
                 const tr = document.createElement("tr");
                 // add class bg-white border-b 
                    tr.classList.add("bg-white", "border-b");
                 // td element 
                    const td = document.createElement("th");
                    td.classList.add("px-6", "py-4", "font-medium", "text-gray-900", "whitespace-nowrap");

                    td.textContent = <?php echo '"' . date_default_timezone_get() . '"'; ?>;
                    tr.appendChild(td);

                nextTimes.forEach((time) => {
                    const listItem = document.createElement("td");
                    // add class px-6 py-4
                    listItem.classList.add("px-6", "py-4");

                    const options = {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: 'numeric',
                        minute: 'numeric',
                        second: 'numeric',
                        timeZoneName: 'long'
                    };

                    options.timeZone = <?php echo '"' . date_default_timezone_get() . '"'; ?>;
                    // convert time to local time
                    // USER TIMEZZONE LIKE America/Nassau
                    // const userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                    
                    // get just timezone
                     const userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                //    alert(userTimeZone);

                    // get the Date as formatted "September 26, 2023" without time
                    //listItem.textContent = time.toLocaleString('en-US', { timeZone: userTimeZone, ...options, hour: undefined, minute: undefined, second: undefined, timeZoneName: undefined });

                    // create a div element with class class="text-sm" and append it to listItem
                    const div = document.createElement("div");
                    div.classList.add("text-sm");
                    div.textContent = time.toLocaleString('en-US', { timeZone: userTimeZone, ...options, hour: undefined, minute: undefined, second: undefined, timeZoneName: undefined });
                    listItem.appendChild(div);
                    
                    // create a div which has class text-lg and mt-2 which has text content as only time and append it to listItem
                    const div2 = document.createElement("div");
                    div2.classList.add("text-lg", "mt-2");
                    div2.textContent = time.toLocaleString('en-US', { timeZone: userTimeZone, ...options, weekday: undefined, year: undefined, month: undefined, day: undefined, timeZoneName: undefined });
                    
                    listItem.appendChild(div2);
                   // listItem.textContent = time.toLocaleString('en-US', options);


                    tr.appendChild(listItem);
                });
                nexttimeBody.appendChild(tr);

                const tr2 = document.createElement("tr");
                 // td element 
                 const td2 = document.createElement("th");
                    td2.textContent = "Asia/Kolkata";
                    td2.classList.add("px-6", "py-4", "font-medium", "text-gray-900", "whitespace-nowrap");

                    td2.textContent = <?php echo '"' . $original_timezone_b . '"'; ?>;
                    tr2.appendChild(td2);

                nextTimes.forEach((time) => {
                    const listItem = document.createElement("td");
                    // add class px-6 py-4
                    listItem.classList.add("px-6", "py-4");

                    const options = {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: 'numeric',
                        minute: 'numeric',
                        second: 'numeric',
                        timeZoneName: 'long'
                    };

                    options.timeZone = <?php echo '"' . $original_timezone_b . '"'; ?>;
                    // convert time to local time
                    // USER TIMEZZONE LIKE America/Nassau
                    // const userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                    
                    // get just timezone
                     const userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                //    alert(userTimeZone);

                    // get the Date as formatted "September 26, 2023" without time
                    //listItem.textContent = time.toLocaleString('en-US', { timeZone: userTimeZone, ...options, hour: undefined, minute: undefined, second: undefined, timeZoneName: undefined });

                    // create a div element with class class="text-sm" and append it to listItem
                    const div = document.createElement("div");
                    div.classList.add("text-sm");
                    div.textContent = time.toLocaleString('en-US', { timeZone: userTimeZone, ...options, hour: undefined, minute: undefined, second: undefined, timeZoneName: undefined });
                    listItem.appendChild(div);
                    
                    // create a div which has class text-lg and mt-2 which has text content as only time and append it to listItem
                    const div2 = document.createElement("div");
                    div2.classList.add("text-lg", "mt-2");
                    div2.textContent = time.toLocaleString('en-US', { timeZone: userTimeZone, ...options, weekday: undefined, year: undefined, month: undefined, day: undefined, timeZoneName: undefined });
                    
                    listItem.appendChild(div2);
                   // listItem.textContent = time.toLocaleString('en-US', options);


                    tr2.appendChild(listItem);
                });
                nexttimeBody.appendChild(tr2);

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