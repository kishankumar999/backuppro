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
<head>
    <title>Backup Frequency Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
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
// if (strtoupper(PHP_OS) === 'LINUX') {
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
// }
?>


    <!-- Content -->
    <div class="max-w-xl mx-auto   bg-white p-8">
        <h2 class="text-2xl font-bold mb-4">Backup Frequency Setup</h2>
        <?php if ($successMessage !== ''): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?= $successMessage ?></span>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-4">
                <label for="backup_frequency" class="block font-bold">Select Backup Frequency:</label>
                <select id="backup_frequency" name="backup_frequency" class="border rounded-md py-2 px-3 w-full">
                    <option value="daily" <?php if ($backupFrequency === 'daily') echo 'selected'; ?>>Daily</option>
                    <option value="weekly" <?php if ($backupFrequency === 'weekly') echo 'selected'; ?>>Weekly</option>
                    <option value="monthly" <?php if ($backupFrequency === 'monthly') echo 'selected'; ?>>Monthly</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="backup_time" class="block font-bold">Select Backup Time:</label>
                <input type="time" id="backup_time" name="backup_time" value="<?= $backupTime ?>" class="border rounded-md py-2 px-3 w-full">
            </div>
            <div class="flex justify-between items-center">
                <button type="submit" class="bg-green-500 w-full text-white py-2 px-4 rounded">Save</button>
            </div>
        </form>
    </div>
    <div class="max-w-xl mx-auto   bg-white p-8 my-10">
        <h1 class="text-2xl font-bold mb-6">Cron Job Setup Instructions</h1>
        <div class="mb-10">
            <label class="block font-bold mb-2" for="platform">Platform:</label>
            <select class="border rounded w-full py-2 px-3" id="platform" name="platform">
                <option value="cpanel">cPanel</option>
                <option value="dedicated_server">Dedicated Server</option>
                <option value="windows">Windows</option>
            </select>
        </div>

        <div class="mb-10">
            <h2 class="font-bold mb-4">Instructions:</h2>
            <div id="cpanelInstructions" class="hidden">
                <ol class="list-decimal ml-6">
                    <li>Log in to your cPanel account.</li>
                    <li>Navigate to the "Cron Jobs" section.</li>
                    <li>Add a new cron job with the following command:</li>
                    <code class="bg-gray-200 px-4 py-2 rounded"><?php echo "0 * * * * php " . realpath("cron.php"); ?></code>
                    <li>Set the desired schedule for the cron job (e.g., every hour).</li>
                    <li>Save the cron job.</li>
                </ol>
            </div>

            <div id="dedicatedServerInstructions" class="hidden">
                <ol class="list-decimal ml-6">
                    <li>Connect to your server using SSH.</li>
                    <li>Open the crontab file by running the command:</li>
                    <code class="bg-gray-200 px-4 py-2 rounded">crontab -e</code>
                    <li>Add a new cron job with the following command:</li>
                    <code class="bg-gray-200 px-4 py-2 rounded"><?php echo "0 * * * * php " . realpath("cron.php"); ?></code>
                    <li>Set the desired schedule for the cron job (e.g., every hour).</li>
                    <li>Save and exit the crontab file.</li>
                </ol>
            </div>

            <div id="windowsInstructions" class="hidden">
                <ol class="list-decimal ml-6">
                    <li>Open the Task Scheduler.</li>
                    <li>Click on "Create Basic Task" or "Create Task" in the sidebar.</li>
                    <li>Set a name and description for the task.</li>
                    <li>In the "Action" tab, add a new action with the following settings:</li>
                    <ul class="list-disc ml-8">
                        <li>Program/script: <code>php.exe</code></li>
                        <li>Arguments: <code><?php echo realpath("cron.php"); ?></code></li>
                    </ul>
                    <li>Set the desired schedule for the task (e.g., every hour).</li>
                    <li>Save the task.</li>
                </ol>
            </div>
        </div>

        
    </div>

    <script>
        const platformSelect = document.getElementById('platform');
        const instructionsDivs = {
            'cpanel': document.getElementById('cpanelInstructions'),
            'dedicated_server': document.getElementById('dedicatedServerInstructions'),
            'windows': document.getElementById('windowsInstructions')
        };

        platformSelect.addEventListener('change', function() {
            const selectedPlatform = this.value;
            Object.keys(instructionsDivs).forEach(function(platform) {
                if (platform === selectedPlatform) {
                    instructionsDivs[platform].classList.remove('hidden');
                } else {
                    instructionsDivs[platform].classList.add('hidden');
                }
            });
        });

        // on page load 
        addEventListener('load', function() {
           // trigger 
              platformSelect.dispatchEvent(new Event('change'));
        });

        
    </script>
</body>
</html>
