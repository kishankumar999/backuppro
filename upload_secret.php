<?php include 'validate_login.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>



</head>

<body class="flex flex-col md:flex-row min-h-screen">
    <!-- BackupPro Title -->
    <div class="w-full md:w-1/5 bg-gray-800 text-white flex-shrink-0 p-4">
        <h2 class="text-2xl font-bold mb-4">
            <a href="dashboard.php" class="text-white">BackupPro</a>
        </h2>
        <ul class="space-y-2">
            <li><a href="dashboard.php" class="text-blue-500">Dashboard</a></li>
            <li><a href="reset_password.php" class="text-blue-500">Reset Password</a></li>
            <li><a href="logout.php" class="text-blue-500">Logout</a></li>
            <li><a href="db_settings.php" class="text-blue-500">DB Settings</a></li>
            <li><a href="setup_drive.php" class="text-blue-500">Setup Drive</a></li>
        </ul>
    </div>

    <!-- Content -->
    <div class="w-full md:w-4/5 p-8 prose">
    <?php
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['credentials'])) {
    $uploadDir = __DIR__ . '/uploads/';
    $uploadedFile = $uploadDir . basename($_FILES['credentials']['name']);
    $uploadSuccess = move_uploaded_file($_FILES['credentials']['tmp_name'], $uploadedFile);

    if ($uploadSuccess) {
        // Update the config.php file with the uploaded file path
        $configFile = __DIR__ . '/config.php';
        $configData = include $configFile;
        // get relative url. 
        $configData['client_secret'] = 'uploads/' . basename($_FILES['credentials']['name']);

        $configContent = "<?php\n\nreturn " . var_export($configData, true) . ";\n";
        file_put_contents($configFile, $configContent);
        echo '<p class="text-green-600">File uploaded successfully! Config file updated.</p>';
    } else {
        echo '<p class="text-red-600">Failed to upload the file.</p>';
    }
}
?>


<div class="container mx-auto p-6">
    <h1 class="text-3xl font-semibold mb-4">Setup Google Drive</h1>

    <div class="bg-white rounded shadow p-4">
        <h2 class="text-xl font-semibold mb-2">Upload JSON Credentials</h2>
        <p class="mb-4">Upload the JSON credentials file you downloaded from the Google App here:</p>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
            <div class="mb-4">
                <input type="file" name="credentials" accept=".json" required>
            </div>
            <div>
                <input type="submit" value="Upload"
                       class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 cursor-pointer">
            </div>
        </form>
    </div>
</div>


    </div>
</body>

</html>