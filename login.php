<?php
session_start();
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

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BackupPro Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="max-w-sm p-6 bg-white rounded shadow-md">
        <h1 class="text-2xl font-semibold mb-6">BackupPro Login</h1>

        <?php if ($error === 'InvalidCredentials'): ?>
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
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Login</button>
            </div>
            <!-- Lost password link muted in tailwind css with margin y 5 -->
            <div class="flex justify-between items-center">
                <a href="lost_password.php" class="text-blue-500 hover:text-blue-600">Lost Password?</a>
            </div>

        </form>
    </div>
</body>

</html>
