<?php
ini_set('memory_limit', '1000M');
ini_set('max_execution_time', 60 * 10); //10 minutes
if (!isset($dont_check_login) || $dont_check_login != true) {
    include __DIR__ . DIRECTORY_SEPARATOR . 'validate_login.php';
    $dont_check_login = false; // reset to false
}


// Enable implicit flush
ob_implicit_flush(true);

// Enable error reporting
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Include the necessary Google API files
// include your composer dependencies
require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor/autoload.php';
// Include the config file
$config = include(__DIR__ . DIRECTORY_SEPARATOR . 'config.php');

// Set up the Google API client
$client = new Google_Client();
$client->setApplicationName('BackupPro');
$client->setScopes(Google_Service_Drive::DRIVE);
$client->addScope(Google_Service_Gmail::GMAIL_SEND);
// add scope for people api
$client->addScope(Google_Service_PeopleService::USERINFO_EMAIL);
// add scope for profile api
$client->addScope(Google_Service_PeopleService::USERINFO_PROFILE);

$client->setAuthConfig(__DIR__ . DIRECTORY_SEPARATOR . $config['client_secret']);
$client->setAccessType('offline');
$client->setPrompt('select_account consent');

$client->setRedirectUri($config['redirect_url']);


if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);
    file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'token.json', json_encode($client->getAccessToken()));
}

// Authorize the client
if (!$client->isAccessTokenExpired()) {
    // Save the access token for future use
    file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'token.json', json_encode($client->getAccessToken()));
}

// Check if token already exists
if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'token.json')) {
    $accessToken = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'token.json'), true);
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
$backupFile = __DIR__ . DIRECTORY_SEPARATOR . 'backup.sql';
$zipFile = __DIR__ . DIRECTORY_SEPARATOR . 'backup.zip';
?>

<!DOCTYPE html>
<html lang="en">

<head><?php include("favicon.php"); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Progress</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 flex flex-col items-center justify-center h-screen">
    <div class="flex gap-2 my-5">
        <img width="32" alt="Google Drive icon (2020)" src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/Google_Drive_icon_%282020%29.svg/512px-Google_Drive_icon_%282020%29.svg.png">
        <div class="text-bold">Google Drive backup</div>
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


function generateBackupFileName($template)
{
    $currentDate = date('Y-m-d');
    // current time in 12 hour format with AM/PM separated by - 
    $currentTime = date('h-i-sA');
    // getting datbase name from config.php
    $config = include(__DIR__ . DIRECTORY_SEPARATOR . 'config.php');
    $databaseName = $config['db_name'];

    // Replace placeholders in the template with actual values
    $backupFileName = str_replace('{date}', $currentDate, $template);
    $backupFileName = str_replace('{time}', $currentTime, $backupFileName);
    $backupFileName = str_replace('{database_name}', $databaseName, $backupFileName);

    return $backupFileName . '.zip'; // Add .zip extension to the file name
}

// Example usage:
// get template from config
$template = $config['backup_file_name'];
$backupFileName = generateBackupFileName($template);

// Create a file metadata


// Set the parent folder ID if necessary

// find folder id from name in root if exists in drive else create new folder

// if isset folder name and not blank
if (isset($config['folder_name']) && !empty($config['folder_name'])) {
    $folderName = $config['folder_name'];
    $folderId = null;
    $optParams = array(
        'q' => "mimeType='application/vnd.google-apps.folder' and name='$folderName' and trashed=false",
        'fields' => 'files(id, name)'
    );
    $results = $service->files->listFiles($optParams);

    if (count($results->getFiles()) == 0) {
        // create folder directly under the root directory (no parent folder)
        $fileMetadata = new Google_Service_Drive_DriveFile(array(
            'name' => $folderName,
            'mimeType' => 'application/vnd.google-apps.folder'
        ));
        $file = $service->files->create($fileMetadata, array(
            'fields' => 'id'
        ));
        $folderId = $file->id;
    } else {
        $folderId = $results->getFiles()[0]->getId();
    }
}

$fileMetadata = new Google_Service_Drive_DriveFile(array(
    'name' => $backupFileName
));
if (isset($folderId) && !empty($folderId)) {
    $fileMetadata->setParents(array($folderId));
}







// Create a file metadata


// Set the parent folder ID if necessary
// if isset folder name and not blank then Pick it from the Config. 
if (isset($config['folder_name']) && !empty($config['folder_name'])) {
    $folderName = $config['folder_name'];
    $folderId = null;
    $optParams = array(
        'q' => "mimeType='application/vnd.google-apps.folder' and name='$folderName' and trashed=false",
        'fields' => 'files(id, name)'
    );
    $results = $service->files->listFiles($optParams);

    // find folder id from name in root if exists in drive else create new folder
    if (count($results->getFiles()) == 0) {
        // create folder directly under the root directory (no parent folder)
        $fileMetadata = new Google_Service_Drive_DriveFile(array(
            'name' => $folderName,
            'mimeType' => 'application/vnd.google-apps.folder'
        ));
        $file = $service->files->create($fileMetadata, array(
            'fields' => 'id'
        ));
        $folderId = $file->id;
    } else {
        $folderId = $results->getFiles()[0]->getId();
    }
}

$fileMetadata = new Google_Service_Drive_DriveFile(array(
    'name' => $backupFileName
));
if (isset($folderId) && !empty($folderId)) {
    $fileMetadata->setParents(array($folderId));
}




// $fileMetadata->setParents(array('folderId'));

// Specify the MIME type of the file
$fileMimeType = 'application/zip';

// Set the file content
//$fileContent = file_get_contents( $zipFile);

// echo "Shishir";


// NEW cODE. 
// ==========================

function readVideoChunk($handle, $chunkSize)
{
    $byteCount = 0;
    $giantChunk = "";
    while (!feof($handle)) {
        // fread will never return more than 8192 bytes if the stream is read
        // buffered and it does not represent a plain file
        $chunk = fread($handle, 8192);
        $byteCount += strlen($chunk);
        $giantChunk .= $chunk;
        if ($byteCount >= $chunkSize) {
            return $giantChunk;
        }
    }
    return $giantChunk;
}

$chunkSizeBytes = 1 * 1024 * 1024; // Set your desired chunk size

// Your Google Drive service setup here (e.g., $service = new Google_Service_Drive($client))

// Specify the MIME type of the file
$fileMimeType = 'application/zip';

// Create the file metadata
// $fileMetadata = new Google_Service_Drive_DriveFile();
// $fileMetadata->name = "Large File.zip";

// Call the API with the media upload, defer so it doesn't immediately return.
$client->setDefer(true);
$request = $service->files->create($fileMetadata);

// Create a media file upload to represent our upload process.
$media = new Google_Http_MediaFileUpload(
    $client,
    $request,
    $fileMimeType,
    null,
    true,
    $chunkSizeBytes
);
$media->setFileSize(filesize($zipFile));

// Upload the various chunks. $status will be false until the process is complete.
$status = false;
$handle = fopen($zipFile, "rb");
while (!$status && !feof($handle)) {
    $chunk = readVideoChunk($handle, $chunkSizeBytes);
    $status = $media->nextChunk($chunk);
}

// The final value of $status will be the data from the API for the object that has been uploaded.
$result = false;
if ($status != false) {
    $result = $status;
}

fclose($handle);

// Get the file ID
$fileId = $result->id;


// ===============================================
// NEW CODE ENDS. 
// ===============================================

// Generate the download URL
$downloadUrl = 'https://drive.google.com/uc?export=download&id=' . $fileId;



// Checking if the config automated_backup_notification is true 

    // Read subscribers from subscriber.json file
    $subscribers = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'subscribers.json'), true);
// Note $dont_check_login means it is running from the cron.php file.
if ((isset($config['automated_backup_notification']) && $config['automated_backup_notification'] == 'true' && $dont_check_login == true) ||
    (isset($config['manual_backup_notification']) && $config['manual_backup_notification'] == 'true' && $dont_check_login != true)
) {
    // update status sending notification email
    echo '<script>
        updateProgressBar(100);
        updateStatusText("Uploaded on Google Drive now Sending notication email...  ");
    </script>';
    ob_flush(); // Flush the output buffer
    flush(); // Send the HTML content to the browser immediately

    $client->setDefer(false);

    // // get google accounts email id from google people api
    // // Create a Google People API service instance
    // $peopleService = new Google_Service_PeopleService($client);
    // // get user getPrimaryEmail() from google people api



    // // Fetch the user's email addresses
    // $person = $peopleService->people->get('people/me', array('personFields' => 'emailAddresses'));
    // $email = $peopleService->getMetadata()->

    // // Access the primary email address
    // $emailAddresses = $person->getEmailAddresses();
    // foreach ($emailAddresses as $emailAddress) {
    //     if ($emailAddress->metadata->primary) {
    //         $primaryEmail = $emailAddress->value;
    //         break;
    //     }
    // }

    // if (isset($primaryEmail)) {
    //     echo "Primary Email Address: " . $primaryEmail . "\n";
    // } else {
    //   //  echo "Primary email address not found.\n";
    // }

    $service = new Google_Service_Gmail($client);



    // Check if subscribers exist
    if (!empty($subscribers)) {

          // Create a Google People API service instance
          $peopleService = new Google_Service_PeopleService($client);


          // Fetch the user's email addresses
          $person = $peopleService->people->get('people/me', array('personFields' => 'emailAddresses'));

          // Access the primary email address
          $emailAddresses = $person->getEmailAddresses();
          foreach ($emailAddresses as $emailAddress) {
              if ($emailAddress->metadata->primary) {
                  $primaryEmail = $emailAddress->value;
                  break;
              }
          }

          if (isset($primaryEmail)) {
              //   echo "Primary Email Address: " . $primaryEmail . "\n";
          } else {
              echo "Primary email address not found.\n";
          }

          ?>
          <div class="max-w-96 rounded-lg bg-white p-6 shadow-lg mt-10">
  <div class="flex items-center gap-1">
      




<svg version="1.1" class="w-6" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="-112 114 24 24" enable-background="new -112 114 24 24" xml:space="preserve">
<path fill="#0084FF" d="M-92,118h-16c-1.1,0-2,0.9-2,2l0,12c0,1.1,0.9,2,2,2h16c1.1,0,2-0.9,2-2v-12C-90,118.9-90.9,118-92,118z
	 M-92,122l-8,5l-8-5v-2l8,5l8-5V122z"></path>
<path fill="none" d="M-112,114h24v24h-24V114z"></path>
</svg>

 
      <div class=" text-xl font-bold text-black">Email Confirmations</div>
    </div>
  
    <p class="text-sm text-slate-500 mb-3 mt-1">Confirmation email sent to following <a class="text-blue-500 font-semibold " title="Manage Subscribers" href="subscribers.php">subscribers</a></p>

    


          <?php


        // Loop through subscribers
        foreach ($subscribers as $subscriber) {
            $name = $subscriber['name'];
            $email = $subscriber['email'];

            // Create a new message
            $message = new Google_Service_Gmail_Message();


            // fetch email id from Google Service Gmail client
          



            $rawMessage = "From: $primaryEmail \r\n";

            $rawMessage .= "To: $email\r\n";

            // Pick email template from email.html file and replace the placeholders with actual values {name},{email},{backup-time},{backup-location},{backup-size}, and extract subject and body
            $emailTemplate = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'email.html');
            // Get subject in subject tag
            $subject = '';
            if (preg_match('/<subject>(.*?)<\/subject>/s', $emailTemplate, $matches)) {
                $subject = $matches[1];
            }
            // Remove subject; the rest of the email template is the body
            $emailTemplate = preg_replace('/<subject>(.*?)<\/subject>/s', '', $emailTemplate);
            // Replace placeholders with actual values
            $backupSize = round(filesize($zipFile) / 1024 / 1024, 2) . ' MB';

            // Backup location is folder name and file name
            if (isset($folderName) && !empty($folderName)) {
                $backupLocation = $folderName . '/' . $backupFileName;
            } else {
                $backupLocation = $backupFileName;
            }

            $emailTemplate = str_replace('{name}', $name, $emailTemplate);
            $emailTemplate = str_replace('{email}', $email, $emailTemplate);
            $emailTemplate = str_replace('{backup-time}', date('Y-m-d h:i:s A'), $emailTemplate);
            $emailTemplate = str_replace('{backup-location}', $backupLocation, $emailTemplate);
            $emailTemplate = str_replace('{backup-size}', $backupSize, $emailTemplate);

            // Replace body in email template
            $body = $emailTemplate;

            // Add subject and body to raw message
            $rawMessage .= "Subject: =?utf-8?B?" . base64_encode($subject) . "?=\r\n";
            $rawMessage .= "MIME-Version: 1.0\r\n";
            $rawMessage .= "Content-Type: text/html; charset=utf-8\r\n";
            $rawMessage .= "Content-Transfer-Encoding: base64\r\n\r\n";
            $rawMessage .= chunk_split(base64_encode($body));

            // Encode the message
            $encodedMessage = rtrim(strtr(base64_encode($rawMessage), '+/', '-_'), '=');
            $message->setRaw($encodedMessage);

            try {
                // Send the message
                $service->users_messages->send('me', $message);
                ?>
                   <div class=" flex items-center gap-4 border p-4 rounded-xl shadow mt-3">
                  <div class="">
                    <svg class="w-12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" enable-background="new 0 0 64 64">
                    <path d="M32,2C15.431,2,2,15.432,2,32c0,16.568,13.432,30,30,30c16.568,0,30-13.432,30-30C62,15.432,48.568,2,32,2z M25.025,50
                l-0.02-0.02L24.988,50L11,35.6l7.029-7.164l6.977,7.184l21-21.619L53,21.199L25.025,50z" fill="#43a047"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-lg font-semibold"><?php echo ucfirst($name); ?></div>
                    <div class="text-sm font-semibold text-slate-500"><?php echo $email; ?></div>
                </div>
                </div>
                <?php
                // echo "Notification sent to $email successfully.<br>";
            } catch (Google_Service_Exception $e) {
                echo "Error sending notification to $email: " . $e->getMessage() . "<br>";
            } catch (Google_Exception $e) {
                echo "Error sending notification to $email: " . $e->getMessage() . "<br>";
            }
        }

        ?>
         
          </div>
        <?php
    } else {
        echo "No subscribers found.";
    }
}

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