<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cron Job Setup Instructions</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #F3F4F6;
        }
    </style>
</head>
<body>
    <div class="container mx-auto p-10 mt-10 bg-white rounded shadow-xl max-w-xl">
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

        <div class="flex items-center justify-between">
            <a class="text-blue-500 hover:text-blue-600 font-bold" href="dashboard.php">Back to Dashboard</a>
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
    </script>
</body>
</html>
