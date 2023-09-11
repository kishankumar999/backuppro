<?php include 'validate_login.php'; 

require_once 'config.php';

$success = isset($_GET['success']) ? $_GET['success'] : '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $previousPassword = $_POST['previous_password'];
    $newPassword = $_POST['new_password'];

    // Check if the previous password matches the stored password
    if ($previousPassword === $config['dashboard_password']) {
        // Update the password in the configuration array
        $config['dashboard_password'] = $newPassword;

        // Save the updated configuration to config.php
        $configContent = "<?php\n\n\$config = " . var_export($config, true) . ";\n";
        file_put_contents('config.php', $configContent);

        // Set the success message
        $success = 'PasswordReset';
    } else {
        $error = 'Invalid previous password. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head><?php include("favicon.php"); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
     <!-- <link rel="stylesheet" href="css/output.css"> -->
     <!-- include css/output.css -->
     <link rel="stylesheet" href="css/output.css">
</head>

<body class="bg-gray-100" >
<a href="dashboard.php" class="m-2 block text-blue-500 font-semibold">
    <!-- back long arrow -->
    <svg class="inline-block w-4 h-4 mr-1 -mt-1" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 5.293a1 1 0 0 1 0 1.414L4.414 10H16a1 1 0 1 1 0 2H4.414l2.879 2.293a1 1 0 1 1-1.414 1.414l-4-4a1 1 0 0 1 0-1.414l4-4a1 1 0 0 1 1.414 0z" clip-rule="evenodd"></path>
    </svg>
Back to Dashboard</a>
<div class="flex items-center justify-center min-h-[calc(100vh-60px)] ">
    

    <div class="max-w-sm p-6 bg-white rounded shadow-md">
        <h1 class="text-2xl font-semibold mb-6">Reset Password</h1>

        <?php if ($error !== ''): ?>
            <p class="text-red-500 mb-4"><?php echo $error; ?></p>
        <?php endif; ?>

        <?php if ($success === 'PasswordReset'): ?>
            <p class="text-green-500 mb-4">Password reset successfully!</p>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label for="previous_password" class="block mb-2">Previous Password</label>
                <input type="password" id="previous_password" name="previous_password" class="w-full px-4 py-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label for="new_password" class="block mb-2">New Password</label>
                <input type="password" id="new_password" name="new_password" class="w-full px-4 py-2 border rounded" required>
            </div>
            <div class="flex justify-between items-center gap-2">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Reset Password</button>
                <!-- Add a outline button muted button in tailwind css to back to dashboard -->
                <a href="dashboard.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Back to Dashboard</a>
            </div>
        </form>
    </div>
    </div>
</body>

</html>
