<?php include 'validate_login.php'; ?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Database Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full sm:max-w-md p-6 bg-white rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Update Database Settings</h1>

        <?php
        $config = include('config.php');

        $db_host = $config['db_host'];
        $db_name = $config['db_name'];
        $db_username = $config['db_username'];
        $db_password = $config['db_password'];

        $error = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get the new settings from the form
            $db_host = $_POST['db_host'];
            $db_name = $_POST['db_name'];
            $db_username = $_POST['db_username'];
            $db_password = $_POST['db_password'];

            // Step 1: Validate the form data
            if (empty($db_host) || empty($db_name) || empty($db_username)) {
                $error = true;
                echo '<p class="text-red-500">Please provide all the required fields.</p>';
            }

            // Step 2: Check if the database credentials are valid
            try {
                $connection = @mysqli_connect($db_host, $db_username, $db_password, $db_name);
                if (!$connection) {
                    $error = true;
                    echo '<p class="text-red-500">Invalid database credentials. Please check your settings.</p>';
                }
            } catch (Exception $e) {
                $error = true;
                echo '<p class="text-red-500">An error occurred while connecting to the database.</p>';
            }

            // Step 3: Update the config array
            if (!$error) {
                $config['db_host'] = $db_host;
                $config['db_name'] = $db_name;
                $config['db_username'] = $db_username;
                $config['db_password'] = $db_password;

                // Write the updated config array to the config.php file
                $config_content = "<?php\n\nreturn " . var_export($config, true) . ";\n";
                file_put_contents('config.php', $config_content);

                // Show success message
                echo '<p class="text-green-500">Settings updated successfully!</p>';
            }
        }
        ?>

        <form action="" method="POST">
            <div class="mb-4">
                <label for="db_host" class="block mb-1 font-semibold">Database Host</label>
                <input type="text" id="db_host" name="db_host" class="w-full px-4 py-2 border rounded" value="<?php echo $db_host; ?>" required>
            </div>

            <div class="mb-4">
                <label for="db_name" class="block mb-1 font-semibold">Database Name</label>
                <input type="text" id="db_name" name="db_name" class="w-full px-4 py-2 border rounded" value="<?php echo $db_name; ?>" required>
            </div>

            <div class="mb-4">
                <label for="db_username" class="block mb-1 font-semibold">Database Username</label>
                <input type="text" id="db_username" name="db_username" class="w-full px-4 py-2 border rounded" value="<?php echo $db_username; ?>" required>
            </div>

            <div class="mb-4">
                <label for="db_password" class="block mb-1 font-semibold">Database Password</label>
                <input type="password" id="db_password" name="db_password" class="w-full px-4 py-2 border rounded" value="<?php echo $db_password; ?>">
            </div>

            <div class="flex justify-between items-center mb-4">
                <a href="dashboard.php" class="text-blue-500">Back to Dashboard</a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Update Settings</button>
            </div>
        </form>
    </div>
</body>

</html>
