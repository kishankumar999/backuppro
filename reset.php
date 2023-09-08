<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['confirm']) && $_POST['confirm'] === 'true') {
    // Empty all .json files from the upload folder
    $uploadPath = 'uploads';
    $files = glob($uploadPath . '/*.json');
    foreach ($files as $file) {
      if (is_file($file)) {
        unlink($file);
      }
    }

    // Delete token.json from the root folder
    $tokenFile = 'token.json';
    if (is_file($tokenFile)) {
      unlink($tokenFile);
    }

    // Delete config.php
    $configFile = 'config.php';
    if (is_file($configFile)) {
      unlink($configFile);
    }

    // Destroy session
    session_start();
    session_destroy();

    // Redirect to index.php
    header('Location: index.php');
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head><?php include("favicon.php"); ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Page</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
  <div class="container mx-auto max-w-xl py-10">
    <div class="bg-white p-6 rounded">
      <h1 class="text-2xl mb-4">Reset to Default</h1>
      <p class="mb-6">All settings will be lost and you will be redirected to the Setup wizard.</p>
      <form method="post" action="">
        <input type="hidden" name="confirm" value="true">
        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Reset to Default</button>
      </form>
    </div>
  </div>
</body>

</html>
