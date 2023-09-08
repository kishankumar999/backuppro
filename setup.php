<?php
session_start();
$error = false;
$error_not_writable = false;
$configFilePath = 'config.php';

$currentFolder = __DIR__;
$writable = is_writable($currentFolder);
$platform = PHP_OS;

if (!$writable) {
    if (strpos($platform, 'WIN') === 0) {
        // Windows platform
        $instructions = "To make the current folder writable, open Command Prompt or PowerShell as an administrator and run: \n\n";
        $instructions .= "<div class='my-2'><code>icacls \"$currentFolder\" /grant Users:(F)</code></div>";
    } else {
        // Non-Windows platform (e.g., Linux, macOS)
        $instructions = "To make the current folder writable, open a terminal and run: \n\n";
        $instructions .= "<div class='my-2'><code>chmod -R 777 \"$currentFolder\"</code></div>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Step 1: Process the form submission
    $dbHost = $_POST['db_host'];
    $dbName = $_POST['db_name'];
    $dbUsername = $_POST['db_username'];
    $dbPassword = $_POST['db_password'];
    $dashboardUsername = $_POST['dashboard_username'];
    $dashboardPassword = $_POST['dashboard_password'];
    $setupName = $_POST['setup_name'];

    // Step 2: Check if the database credentials are valid
    try {

        $connection = @mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbName);
    } catch (Exception $e) {
        // display exception
        echo $e->getMessage();
        echo $dbHost;
        echo $dbName;
        echo $dbUsername;
        echo $dbPassword;
        $error = true;
    }

    // Step 3: Generate the config file content
    if (!$error) {


        $currentURL = 'http';
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $currentURL .= 's';
        }
        $currentURL .= '://' . $_SERVER['HTTP_HOST'];

        // Parse the URL and remove the query parameters
        $urlParts = parse_url($_SERVER['REQUEST_URI']);
        $path = $urlParts['path'];
        $query = isset($urlParts['query']) ? '' : '';

        // replace current file name with 
        $path = str_replace('setup.php', 'backup_drive.php', $path);

        // Rebuild the URL without the query parameters
        $currentURL .= $path;

        $config = array(
            'db_host' => $dbHost,
            'db_name' => $dbName,
            'db_username' => $dbUsername,
            'db_password' => $dbPassword,
            'dashboard_username' => $dashboardUsername,
            'dashboard_password' => $dashboardPassword,
            'setup_name' => $setupName,
            'backup_folder' => 'backups',
            'backup_file_name' => '{database_name}-{date}-{time}',
            'redirect_url' => $currentURL
        );

        // Step 3.5: Find mysqldump path
        $mysqldumpPath = '';

        // Check common paths for mysqldump
        $commonPaths = array(
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            '/usr/mysql/bin/mysqldump',
            'C:/xampp/mysql/bin/mysqldump.exe',
            'C:/Program Files/MySQL/MySQL Server/bin/mysqldump.exe'
        );

        foreach ($commonPaths as $path) {
            if (is_executable($path)) {
                $mysqldumpPath = $path;
                break;
            }
        }

        // Add mysqldump path to the config
        $config['mysqldump_path'] = $mysqldumpPath;


        $configContent = "<?php\n\nreturn " . var_export($config, true) . ";\n";


        // Step 4: Write the config file to disk
        if (file_put_contents($configFilePath, $configContent) !== false) {
            // Step 5: Installation successful
                 // Valid credentials, create session
                $_SESSION['username'] = $dashboardUsername;
                $_SESSION['setup_name'] ="";
            header('Location: dashboard.php?installation_complete=true'); // Redirect to the application or next step
            exit();
        } else {
            // Handle the error case
            $error_not_writable = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head><?php include("favicon.php"); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
</head>

<body class="bg-gray-100 md:p-4">
    <div class="grid md:grid-cols-3 gap-3 max-w-xl md:max-w-full grid bg-white p-5 md:p-10  mx-auto  ">
        <div>
            <div class="flex gap-1 ">
                <div class="mt-2"><img src="uploads/logo.png" width="35px" alt=""></div>
                <div class="">
                    <div class="div text-2xl font-bold">BackupPro</div>
                    <div class="text-sm text-slate-500">by Webfort</div>
                </div>
            </div>

            <h1 class="text-2xl font-bold my-5">Installation</h1>
            <?php if (!$writable) : ?>
                <div class="bg-yellow-300 text-black my-5 px-4 py-2">
                    <p class="text-lg my-2">Make BackupPro folder writeable </p>
                    <p><?php echo $instructions; ?></p>
                </div>
            <?php endif; ?>
            <?php if ($error) : ?>
                <div class="bg-red-200 text-red-800 p-3 my-5">
                    Error occurred during installation. Please check your database credentials and try again.
                </div>
            <?php endif; ?>
            <?php if ($error_not_writable) : ?>
                <div class="bg-red-200 text-red-800 p-3 my-5">
                    Could not write the config file. Please make sure the current folder is writable.
                </div>
            <?php endif; ?>
            <p>
                Welcome to 2 minutes BackupPro installation process! Just fill in the details below and you'll be on your way to using the most powerful backup tool for your database.
            </p>
            <h2 class="text-xl mb-2 mt-10">
                Information needed
            </h2>
            <p>
                Please provide the following information. Don't worry, you can always change these settings later.
            </p>
        </div>

        <form method="post" class="col-span-2" action="">
            <div class="md:grid  grid-cols-2 gap-4">
                <div class="p-5 bg-blue-100 mt-5 md:mt-0">

                    <h2 class="text-xl mb-2 ">MySQL Database connection</h2>
                    <p class="mb-5">Below you should enter your database connection details. If you're not sure about these, contact your host.</p>

                    <div class="mb-5">
                        <label for="db_host" class="block mb-2">Database Host:</label>
                        <input autocomplete="off" type="text" id="db_host" name="db_host" required class="border border-gray-300 p-2 w-full">
                    </div>
                    <div class="mb-5">
                        <label for="db_name" class="block mb-2">Database Name:</label>
                        <input autocomplete="off" type="text" id="db_name" name="db_name" required class="border border-gray-300 p-2 w-full">
                    </div>
                    <div class="mb-5">
                        <label for="db_username" class="block mb-2">Database Username:</label>
                        <input autocomplete="off" type="text" id="db_username" name="db_username" required class="border border-gray-300 p-2 w-full">
                    </div>
                    <div class="mb-5">
                        <label for="db_password" class="block mb-2">Database Password:</label>
                        <input  autocomplete="false" type="password" id="db_password" name="db_password" class="border border-gray-300 p-2 w-full">
                    </div>
                </div>
                <div class="p-5 bg-green-100 mt-7 md:mt-0 ">
                    <h2 class="text-xl mb-5 ">BackupPro New Admin Credentials</h2>
                    <p class="mb-5">Please enter your Admin Login details for secure BackupPro dashboard access.</p>
                    <div class="mb-5">
                        <label for="dashboard_username" class="block mb-2"> Username:</label>
                        <input autocomplete="off" type="text" id="dashboard_username" name="dashboard_username" required class="border border-gray-300 p-2 w-full">
                    </div>
                    <div class="mb-5">
                        <label for="dashboard_password" class="block mb-2"> Password:</label>
                        <input  autocomplete="new-password" type="password" id="dashboard_password" name="dashboard_password" required class="border border-gray-300 p-2 w-full">
                    </div>
                    <div class="mb-5 hidden   ">
                        <label for="setup_name" class="block mb-2">Setup Name:</label>
                        <input autocomplete="off" type="hidden" id="setup_name" name="setup_name"  class="border border-gray-300 p-2 w-full">
                    </div>
                </div>
            </div>
            <div class="text-end">

                <button type="submit" class="bg-blue-500 text-white text-lg px-10 py-2 rounded mt-5 w-full md:w-auto">Install Now</button>
            </div>
        </form>
    </div>
</body>

</html>