<?php
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

        $config = array(
            'db_host' => $dbHost,
            'db_name' => $dbName,
            'db_username' => $dbUsername,
            'db_password' => $dbPassword,
            'dashboard_username' => $dashboardUsername,
            'dashboard_password' => $dashboardPassword,
            'setup_name' => $setupName,
            'backup_folder' => 'backups',
            'backup_file_name' => '{database_name}-{date}-{time} '
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
            header('Location: index.php'); // Redirect to the application or next step
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

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 p-10">
    <div class="container max-w-xl bg-white px-10  mx-auto py-10 ">
        <h1 class="text-2xl mb-5">BackupPro Installation</h1>
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

        <form method="post" action="" class="max-w-sm">
            <h2 class="text-xl mb-2 mt-10">Database connection</h2>
            <p class="mb-5">Below you should enter your database connection details. If you're not sure about these, contact your host.</p>

            <div class="mb-5">
                <label for="db_host" class="block mb-2">Database Host:</label>
                <input type="text" id="db_host" name="db_host" required class="border border-gray-300 p-2 w-full">
            </div>
            <div class="mb-5">
                <label for="db_name" class="block mb-2">Database Name:</label>
                <input type="text" id="db_name" name="db_name" required class="border border-gray-300 p-2 w-full">
            </div>
            <div class="mb-5">
                <label for="db_username" class="block mb-2">Database Username:</label>
                <input type="text" id="db_username" name="db_username" required class="border border-gray-300 p-2 w-full">
            </div>
            <div class="mb-5">
                <label for="db_password" class="block mb-2">Database Password:</label>
                <input type="password" id="db_password" name="db_password" class="border border-gray-300 p-2 w-full">
            </div>

            <h2 class="text-xl mb-5 mt-10"> New Admin Credentials</h2>
            <p class="mb-5">Below you should enter your details of your Admin Login 
                that you would use to securely login to your BackupPro dashboard.</p>
            <div class="mb-5">
                <label for="dashboard_username" class="block mb-2"> Username:</label>
                <input type="text" id="dashboard_username" name="dashboard_username" required class="border border-gray-300 p-2 w-full">
            </div>
            <div class="mb-5">
                <label for="dashboard_password" class="block mb-2"> Password:</label>
                <input type="password" id="dashboard_password" name="dashboard_password" required class="border border-gray-300 p-2 w-full">
            </div>
            <div class="mb-5">
                <label for="setup_name" class="block mb-2">Setup Name:</label>
                <input type="text" id="setup_name" name="setup_name" required class="border border-gray-300 p-2 w-full">
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Proceed</button>
        </form>
    </div>
</body>

</html>