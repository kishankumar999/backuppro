<?php
if (isset($_SESSION['username'])) {
    // Redirect to the dashboard
    header('Location: dashboard.php');
    exit();
}

$error = isset($_GET['error']) ? $_GET['error'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $config = include('config.php');

    // print_r($config);
    // exit;

    if ($username === $config['dashboard_username'] && $password === $config['dashboard_password']) {
        // Valid credentials, create session
        
session_name($config['unique_application_name']);
session_start();
        $_SESSION['username'] = $username;
        $_SESSION['setup_name'] = $config['setup_name'];

        // Redirect to dashboard.php
        header('Location: dashboard.php');
        exit();
    } else {
        // Invalid credentials, redirect back to login page with error message
        header('Location: login.php?error=InvalidCredentials');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head><?php include("favicon.php"); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BackupPro Login</title>
   
     <!-- <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script> -->
     <!-- include css/output.css -->
    <link rel="stylesheet" href="css/output.css">
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div>

    <?php
    // Check if the installation is complete
    if(isset($_GET['installation_complete']) && $_GET['installation_complete'] === 'true') {
        // Installation is complete, show success message
        ?>
        <div class="w-80 my-5 mx-auto rounded-md bg-green-500 p-4 text-white shadow-md">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="text-lg font-semibold">Installation Successful.</p>
            </div>
            <p class="mt-2">Now you can proceed to login.</p>
        </div>
        <?php
    }
    ?>
        <div class="w-80 p-6 bg-white rounded shadow-md">
            <h1 class="text-2xl font-semibold mb-6">BackupPro Login</h1>

            <?php if ($error === 'InvalidCredentials') : ?>
                <p class="text-red-500 mb-4">Invalid username or password. Please try again.</p>
            <?php endif; ?>



            <form action="login.php" method="POST">
                <div class="mb-4">
                    <label for="username" class="block mb-2">Username</label>
                    <input type="text" id="username" name="username" class="w-full px-4 py-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block mb-2">Password</label>
                    <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded" required>
                </div>
                <div class="flex justify-between items-center">
                    <button type="submit" class="bg-blue-500 w-full my-6 hover:bg-blue-600 text-white px-4 py-2 rounded">Login</button>
                </div>
                <!-- Lost password link muted in tailwind css with margin y 5 -->
                <div class="flex justify-between items-center mt-5">
                    <a href="lost_password.php" class="text-blue-500 hover:text-blue-600">Lost Password?</a>
                </div>

            </form>
        </div>
    </div>
</body>

</html>