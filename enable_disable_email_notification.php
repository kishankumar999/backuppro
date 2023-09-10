<?php
// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $manualBackupNotification = isset($_POST['manual_backup_notification']) ? 'true' : 'false';
    $automatedBackupNotification = isset($_POST['automated_backup_notification']) ? 'true' : 'false';

    // Update the config.php file
    $config = include 'config.php';
    $config['manual_backup_notification'] = $manualBackupNotification;
    $config['automated_backup_notification'] = $automatedBackupNotification;
    file_put_contents('config.php', '<?php return ' . var_export($config, true) . ';');

    // Set success message
    $successMessage = 'Configuration saved successfully.';
}

// Load the existing config.php file or use default values if not present
if (file_exists('config.php')) {
    $config = include 'config.php';
} else {
    $config = array();
}

?>
<!DOCTYPE html>
<html lang="en">
<head><?php include("favicon.php"); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script  src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>

    <title>Enable / Disable Notification</title>
</head>
<body class="bg-gray-50">
    <?php include 'email_notification_tabs.php'; ?>
    <div class="container mx-auto max-w-md bg-white px-10 pt-10 pb-8 shadow-xl my-10 rounded-lg ring-1 ring-gray-900/5 ">
        <h1 class="text-xl font-bold mb-10">Enable / Disable Email Confirmation</h1>

        <?php if (isset($successMessage)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p class="font-bold">Success</p>
                <p><?php echo $successMessage; ?></p>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox h-5 w-5 text-indigo-600" name="manual_backup_notification" <?php if (($config['manual_backup_notification'] ?? 'false') === 'true') echo 'checked'; ?>>
                    <div class="ml-3">

                        <span >On Manual Backup </span>
                        <p class="text-sm text-slate-500">Receive an email confirmation on 1-Click backup from dashboard. </p>
                    </div>
                </label>
            </div>
            
            <div class="mb-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox h-5 w-5 text-indigo-600" name="automated_backup_notification" <?php if (($config['automated_backup_notification'] ?? 'false') === 'true') echo 'checked'; ?>>
                    <div class="ml-3">

<span >On Scheduled Backup </span>
<p class="text-sm text-slate-500">Recieve Email Confirmation of the backup on Schedule you have configured. </p>
</div>
                </label>
            </div>

            <button type="submit" class="bg-indigo-500 w-full mt-7 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded">Save</button>
        </form>
    </div>
</body>
</html>
