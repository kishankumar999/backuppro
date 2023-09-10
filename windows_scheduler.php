<!DOCTYPE html>
<html lang="en">

<head><?php include("favicon.php"); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Windows Task Scheduler</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
  
    <style>
 
        .alert-box {
            position: fixed;
            top: 1rem;
            right: 1rem;
            padding: 0.5rem 1rem;
            border: 1px solid #e2e8f0;
            background-color: #fff;
            color: #4a4a4a;
            font-size: 0.875rem;
            border-radius: 0.25rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 9999;
        }
    </style>
</head>


<body class="bg-gray-50">
<a href="dashboard.php" class="m-2 block text-blue-500 font-semibold">
    <!-- back long arrow -->
    <svg class="inline-block w-4 h-4 mr-1 -mt-1" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 5.293a1 1 0 0 1 0 1.414L4.414 10H16a1 1 0 1 1 0 2H4.414l2.879 2.293a1 1 0 1 1-1.414 1.414l-4-4a1 1 0 0 1 0-1.414l4-4a1 1 0 0 1 1.414 0z" clip-rule="evenodd"></path>
    </svg>
Back to Dashboard</a>
    <div class="container mx-auto max-w-xl bg-white p-6 mt-10 rounded shadow">
        <h1 class="text-2xl font-bold mb-6">Windows Task Scheduler</h1>
        <div id="alertBox" class="hidden alert-box"></div>

        <?php
$taskName = 'BackupPro'; // Replace 'YourTaskName' with the actual task name

// Execute the command to get the scheduled task details
$command = 'schtasks /query /tn "'.$taskName.'" /v /fo list';
$output = shell_exec($command);

// Check if the task was found or not
if (strpos($output, 'ERROR: The system cannot find the file specified.') !== false) {
    echo '<p class="text-red-500">Task not found</p>';
} else {
    // Convert the output to an array of lines
    $lines = explode("\n", $output);

    // Create a table to display the task details
    echo '<details class="my-5">';
    echo '<summary class="bg-gray-200 py-2 px-4 cursor-pointer">Current Task Set for BackupPro </summary>';
    echo '<table class="table-auto">';
    foreach ($lines as $line) {
        // Split each line into key-value pairs
        $parts = explode(':', $line, 2);
        $key = trim($parts[0]);
        // if array ke is defined
        $value = '';
        if (isset($parts[1]))
        {
            $value = trim($parts[1]);
        }

        // Display key-value pairs in table rows
        echo '<tr>';
        echo '<td class="border px-4 py-2 font-semibold">' . $key . '</td>';
        echo '<td class="border px-4 py-2">' . $value . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '</details>';
}
?>

        <form id="taskForm" method="POST">
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2" for="taskName">Task Name:</label>
                <input class="form-input" id="taskName" name="taskName" type="text" placeholder="Enter Task Name" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2" for="triggerType">Trigger:</label>
                <select class="form-input" id="triggerType" name="triggerType" >
                    <option value="hourly">Hourly</option>
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="startup">When Computer Starts</option>
                    <option value="logon">When I Log On</option>
                </select>
            </div>



            <div id="daysMonthlyContainer" class="mb-4 hidden2 option-container" >
            <div class="flex flex-wrap">
            <?php for ($i = 1; $i <= 31; $i++) : ?>
                <label class="inline-flex items-center mr-6 mb-3">
                <input type="radio" class="form-radio" name="dayMonthly" value="<?= $i ?>" style="margin-right: 0.5rem;">
                <span><?= $i ?></span>
                </label>
            <?php endfor; ?>
            </div>

            </div>
            <div id="frequencyContainer" class="mb-4 hidden option-container" >
                <label class="block text-sm font-bold mb-2" for="frequency">Frequency (in minutes):</label>
                <input class="form-input" id="frequency" name="frequency" type="number" min="1" >
            </div>
            <div id="frequencyContainerHours" class="mb-4 hidden option-container" >
                <label class="block text-sm font-bold mb-2" for="frequencyhours">Frequency (in hours):</label>
                <input class="form-input" id="frequencyHours" name="frequencyhours" type="number" min="1" max="23">
            </div>
            <div id="startTimeContainer" class="mb-4 hidden option-container">
                <label class="block text-sm font-bold mb-2" for="startTime">Start Time:</label>
                <input class="form-input" id="startTime" name="startTime" type="time" >
            </div>
            <div id="daysContainer" class="mb-4 hidden  option-container">
                <label class="block text-sm font-bold mb-2">Days of the Week:</label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox" name="days[]" value="monday">
                    <span class="ml-2">Monday</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox" name="days[]" value="tuesday">
                    <span class="ml-2">Tuesday</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox" name="days[]" value="wednesday">
                    <span class="ml-2">Wednesday</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox" name="days[]" value="thursday">
                    <span class="ml-2">Thursday</span>
                </label>
                <label class="inline-flex items-center">
                   
<input type="checkbox" class="form-checkbox" name="days[]" value="friday">
                    <span class="ml-2">Friday</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox" name="days[]" value="saturday">
                    <span class="ml-2">Saturday</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox" name="days[]" value="sunday">
                    <span class="ml-2">Sunday</span>
                </label>
            </div>
            <div id="monthsContainer" class="mb-4 hidden option-container">
                <label class="block text-sm font-bold mb-2">Months:</label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox" name="months[]" value="january">
                    <span class="ml-2">January</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox" name="months[]" value="february">
                    <span class="ml-2">February</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox" name="months[]" value="march">
                    <span class="ml-2">March</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox" name="months[]" value="april">
                    <span class="ml-2">April</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox" name="months[]" value="may">
                    <span class="ml-2">May</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox" name="months[]" value="june">
                    <span class="ml-2">June</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox" name="months[]" value="july">
                    <span class="ml-2">July</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox" name="months[]" value="august">
                    <span class="ml-2">August</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox" name="months[]" value="september">
                    <span class="ml-2">September</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox" name="months[]" value="october">
                    <span class="ml-2">October</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox" name="months[]" value="november">
                    <span class="ml-2">November</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox" name="months[]" value="december">
                    <span class="ml-2">December</span>
                </label>
            </div>
            <div class="mb-4">
                <button type="submit" name="submit"  class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                    Create Task</button>
            </div>
        </form>
     
    </div>

    <script>
        const taskForm = document.getElementById('taskForm');
    
        const alertBox = document.getElementById('alertBox');

        // Function to display an alert message
        function showAlert(message, type) {
            alertBox.textContent = message;
            alertBox.classList.remove('hidden');
            alertBox.classList.add(type);

            setTimeout(() => {
                alertBox.classList.add('hidden');
                alertBox.classList.remove(type);
            }, 3000);
        }

   

        
       function onTriggerElementChange()
       {

            const triggerType = document.getElementById('triggerType').value;
      

            // Hide all container elements
            const containers = document.querySelectorAll('.option-container');
            containers.forEach(container => {
                container.classList.add('hidden');
            });

            // Show the relevant container based on the selected trigger type
            switch (triggerType) {
                case 'hourly':
                  //  document.getElementById('frequencyContainer').classList.remove('hidden');
                    document.getElementById('frequencyContainerHours').classList.remove('hidden');
                    break;

                case 'daily':
                    document.getElementById('startTimeContainer').classList.remove('hidden');
                    break;
                case 'weekly':
                    document.getElementById('daysContainer').classList.remove('hidden');
                    document.getElementById('startTimeContainer').classList.remove('hidden');
                    break;

                case 'monthly':
                    document.getElementById('monthsContainer').classList.remove('hidden');
                    document.getElementById('daysMonthlyContainer').classList.remove('hidden');
                    document.getElementById('startTimeContainer').classList.remove('hidden');
                    break;
            }

       }

       // on input trigger type change  
         document.getElementById('triggerType').addEventListener('change', onTriggerElementChange);

        // Function to handle form submission
        function handleFormSubmit(event) {
            event.preventDefault();

            const taskName = document.getElementById('taskName').value;
            const triggerType = document.getElementById('triggerType').value;
            const frequency = document.getElementById('frequency').value;
            const frequencyHours = document.getElementById('frequencyHours').value;
            const startTime = document.getElementById('startTime').value;
            const days = Array.from(document.querySelectorAll('input[name="days[]"]:checked')).map(el => el.value);
            const months = Array.from(document.querySelectorAll('input[name="months[]"]:checked')).map(el => el.value);
   

            // Adding Validation based on trigger type
            if (triggerType === 'hourly') {
                if (frequencyHours === '') {
                    showAlert('Please enter the frequency in hours.', 'text-red-500');
                    return;
                }
            } else if (triggerType === 'daily' || triggerType === 'weekly' || triggerType === 'monthly') {
                if (startTime === '') {
                    showAlert('Please enter the start time.', 'text-red-500');
                    return;
                }
            }

            // if trigger type weekly 
            if (triggerType === 'weekly') {
                if (days.length === 0) {
                    showAlert('Please select at least one day.', 'text-red-500');
                    return;
                }
            }

            // if trigger type monthly
            var dayMonthly = "";
            if (triggerType === 'monthly') {
                 dayMonthly =document.querySelector('input[name="dayMonthly"]:checked').value;
                if (dayMonthly === "") {
                    showAlert('Please select a day.', 'text-red-500');
                    return;
                }

                if (months.length === 0) {
                    showAlert('Please select at least one month.', 'text-red-500');
                    return;
                }
            }


           
            // Make an AJAX request to the PHP script for creating the scheduled task
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'create-task.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    const response = xhr.responseText;
                    showAlert(response, 'text-green-500');
                    // addTaskToList(taskName);
                    taskForm.reset();
                    // refresh the page. 
                    location.reload(); 
                } else if (xhr.readyState === XMLHttpRequest.DONE && xhr.status !== 200) {
                    showAlert('Failed to create scheduled task.', 'text-red-500');
                }
            };

       
            // if days or months are undefined
            if (typeof days === 'undefined') {
                days = [];
            }

            if (typeof months === 'undefined') {
                months = [];
            }
           

            // Send the data to the PHP script
            
            const data = `taskName=${encodeURIComponent(taskName)}&dayMonthly=${encodeURIComponent(dayMonthly)}&triggerType=${encodeURIComponent(triggerType)}&frequencyHours=${encodeURIComponent(frequencyHours)}&frequency=${encodeURIComponent(frequency)}&startTime=${encodeURIComponent(startTime)}&days=${encodeURIComponent(JSON.stringify(days))}&months=${encodeURIComponent(JSON.stringify(months))}`;
           console.log(data);
           console.log(taskName);
           alert(data);
           xhr.send(data);
        }

        taskForm.addEventListener('submit', handleFormSubmit);

        // set on page load and on page load run onTriggerElementChange
        window.onload = onTriggerElementChange();
    </script>
</body>

</html>