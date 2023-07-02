<?php include 'validate_login.php';
$config = include('config.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="flex flex-col md:flex-row min-h-screen">
    <!-- BackupPro Title -->
    <div class="w-full md:w-1/5 bg-gray-800 text-white flex-shrink-0 p-4">
        <h2 class="text-2xl font-bold mb-4">
            <a href="dashboard.php" class="text-white">BackupPro</a>
        </h2>
        <ul class="space-y-2">
            <li><a href="dashboard.php" class="text-blue-500">Dashboard</a></li>
            <li><a href="schedule.php" class="text-blue-500">Automate Backup</a></li>
            <li><a href="setup_drive.php" class="text-blue-500">Setup Drive</a></li>
            <li><a href="db_settings.php" class="text-blue-500">DB Settings</a></li>
            <li><a href="reset_password.php" class="text-blue-500">Reset Password</a></li>
            <li><a href="reset.php" class="text-blue-500">Reset BackupPro</a></li>
            <li><a href="logout.php" class="text-blue-500">Logout</a></li>
        </ul>
    </div>

    <!-- Content -->
    <div class="w-full md:w-4/5 p-8">
        <h1 class="text-2xl font-bold mb-8">Dashboard</h1>



        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="backup.php" class="block bg-blue-500 hover:bg-blue-600 text-white text-center py-8 rounded">
                <h2 class="text-xl font-bold"> Download Backup ZIP</h2>
            </a>
            <?php if (isset($config['client_secret']) && $config['client_secret'] != "") : ?>
                <a href="backup_drive.php" class="block flex gap-2 justify-center bg-green-500 hover:bg-blue-600 text-white text-center py-8 rounded">
                    <img width="32" alt="Google Drive icon (2020)" src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/Google_Drive_icon_%282020%29.svg/512px-Google_Drive_icon_%282020%29.svg.png">

                    <h2 class="text-xl font-bold">Backup to Google Drive</h2>
                </a>
            <?php else : ?>
                <a href="setup_drive.php" class="block flex gap-2 justify-center bg-green-500 hover:bg-blue-600 text-white text-center py-8 rounded">
                    <img width="32" alt="Google Drive icon (2020)" src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/Google_Drive_icon_%282020%29.svg/512px-Google_Drive_icon_%282020%29.svg.png">

                    <h2 class="text-xl font-bold">Setup Google Drive for Backup</h2>
                </a>
            <?php endif; ?>



        </div>

        <!-- in a muted color display last_backup_timestamp from config in a human readable format show like 7th July 2023 at 12:00 pm -->
        <div class="bg-gray-100 rounded p-4 my-8">
            <h2 class="text-xl font-bold mb-4">Last Backup</h2>
            <p class="text-gray-500">
                <?php if (isset($config['last_backup_timestamp']) && $config['last_backup_timestamp'] != "") : ?>
                    <?php echo date('jS F Y \a\t h:i a', $config['last_backup_timestamp']); ?>
                <?php else : ?>
                    No backup yet
                <?php endif; ?>
            </p>
        </div>

    </div>
</body>

</html>