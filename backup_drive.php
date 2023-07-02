<?php
include 'validate_login.php'; 
// header('Content-Length: ' . ob_get_length());
// Send the HTTP headers for chunked encoding

// // Disable output buffering
// ini_set('output_buffering', 'off');
// ini_set('zlib.output_compression', 'off');

// Enable implicit flush
ob_implicit_flush(true);

// Set an appropriate timeout value
// set_time_limit(0);
// header('Content-Type: text/html; charset=utf-8');
// header('Transfer-Encoding: chunked');

//apache_setenv('no-gzip', '1');


// Enable error reporting
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// Include the necessary Google API files
// include your composer dependencies
require_once 'vendor/autoload.php';
// Include the config file
$config = include('config.php');

// Set up the Google API client
$client = new Google_Client();
$client->setApplicationName('BackupPro');
$client->setScopes(Google_Service_Drive::DRIVE);
$client->setAuthConfig($config['client_secret']);
$client->setAccessType('offline');
$client->setPrompt('select_account consent');


$currentURL = 'http';
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    $currentURL .= 's';
}
$currentURL .= '://' . $_SERVER['HTTP_HOST'];

// Parse the URL and remove the query parameters
$urlParts = parse_url($_SERVER['REQUEST_URI']);
$path = $urlParts['path'];
$query = isset($urlParts['query']) ? '' : '';

// Rebuild the URL without the query parameters
$currentURL .= $path;

//echo $currentURL; 
  //  exit; 
$client->setRedirectUri($currentURL);


if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);
    file_put_contents('token.json', json_encode($client->getAccessToken()));
}


// Authorize the client
if (!$client->isAccessTokenExpired()) {
    // Save the access token for future use
    file_put_contents('token.json', json_encode($client->getAccessToken()));
}

// Check if token already exists
if (file_exists('token.json')) {
    $accessToken = json_decode(file_get_contents('token.json'), true);
    $client->setAccessToken($accessToken);
} else {
    // Redirect the user to the authorization URL
    $authUrl = $client->createAuthUrl();
    header('Location: ' . $authUrl);
    exit();
}

// MySQL database credentials
$dbHost = $config['db_host'];
$dbUser = $config['db_username'];
$dbPass = $config['db_password'];
$dbName = $config['db_name'];

// Path of mysqldumps
$pathOfMysqlDump = $config['mysqldump_path'];
// Backup file name and path
$backupFile = 'backup.sql';
$zipFile = 'backup.zip';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Progress</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center h-screen">
    <div class="flex gap-2 my-5">
        <img width="32" alt="Google Drive icon (2020)" src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/Google_Drive_icon_%282020%29.svg/512px-Google_Drive_icon_%282020%29.svg.png">
        <div class="text-bold">Google Drive  backup</div>
    </div>
    <div class="bg-white shadow-lg rounded-lg p-6 w-96">
        <h1 class="text-2xl font-bold mb-6">Backup Progress</h1>
        <div class="mb-4">
            <div id="progressBar" class="bg-gray-300 h-2 w-full rounded-full">
                <div id="progressBarFill" class="bg-green-500 h-2 rounded-full" style="width: 0;"></div>
            </div>
        </div>
        <p id="statusText" class="text-gray-500 mb-4">Backing up database...</p>
        
        <p id="downloadLink" class="text-gray-500 hidden flex flex-col gap-2">
    <a id="copyLinkButton" href="#" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Copy Backup Link</a>
    <a id="openLinkButton" href="#" target="_blank" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Open Backup Link</a>
    <a id="goBackButton" href="dashboard.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Go to Dashboard</a>
</p>
    </div>
    <script>
        let countdownTimeout;

        // Function to copy text to clipboard
        function copyToClipboard(text) {
            const tempInput = document.createElement('input');
            tempInput.value = text;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
        }

        function updateProgressBar(percent) {
            const progressBarFill = document.getElementById('progressBarFill');
            progressBarFill.style.width = percent + '%';
        }

        function updateStatusText(status, link) {
            const statusText = document.getElementById('statusText');
            statusText.innerHTML = status;
            if (link) {
                const downloadLink = document.getElementById('downloadLink');
                downloadLink.classList.remove('hidden');

                const copyLinkButton = document.getElementById('copyLinkButton');
                copyLinkButton.addEventListener('click', function() {
                    copyToClipboard(link);
                    alert('Backup link copied to clipboard!');
                });

                const openLinkButton = document.getElementById('openLinkButton');
                openLinkButton.href = link;
            }
        }
    </script>
</body>
</html>

<?php
ob_flush(); // Flush the output buffer
flush(); // Send the HTML content to the browser immediately

// Perform backup
if ($dbPass === '') {
    $command = "$pathOfMysqlDump --single-transaction --host=$dbHost --user=$dbUser $dbName > $backupFile";
} else {
    $command = "$pathOfMysqlDump --single-transaction --host=$dbHost --user=$dbUser --password='$dbPass' $dbName > $backupFile";
}

$output = [];
$returnVar = 0;
exec($command, $output, $returnVar);

// Check for errors
if ($returnVar !== 0) {
    echo $command;
    echo '<script>
            updateProgressBar(0);
            updateStatusText("Backup failed. Please check the database credentials and try again.");
        </script>';
    exit;
}

echo '<script>
        updateProgressBar(33);
        updateStatusText("Database backup completed. Creating zip archive...");
    </script>';
ob_flush(); // Flush the output buffer
flush(); // Send the HTML content to the browser immediately


// Create zip archive
$zip = new ZipArchive();
$zip->open($zipFile, ZipArchive::CREATE);
$zip->addFile($backupFile);
$zip->close();

echo '<script>
        updateProgressBar(66);
        updateStatusText("Zip archive created. Uploading to Google Drive...");
    </script>';
ob_flush(); // Flush the output buffer
flush(); // Send the HTML content to the browser immediately


// Upload zip file to Google Drive
$service = new Google_Service_Drive($client);

// Create a file metadata
$fileMetadata = new Google_Service_Drive_DriveFile(array(
    'name' => 'backup.zip'
));

// Set the parent folder ID if necessary
// $fileMetadata->setParents(array('folderId'));

// Specify the MIME type of the file
$fileMimeType = 'application/zip';

// Set the file content
$fileContent = file_get_contents($zipFile);

// Create the file
$file = $service->files->create($fileMetadata, array(
    'data' => $fileContent,
    'mimeType' => $fileMimeType,
    'uploadType' => 'multipart',
    'fields' => 'id'
));

// Get the file ID
$fileId = $file->id;

// Generate the download URL
$downloadUrl = 'https://drive.google.com/uc?export=download&id=' . $fileId;

// remvoing files 
unlink($backupFile);
unlink($zipFile);

echo '<script>
        updateProgressBar(100);
        updateStatusText("Backup uploaded successfully!", "' . $downloadUrl . '");
    </script>';
ob_flush(); // Flush the output buffer
flush(); // Send the HTML content to the browser immediately
?>