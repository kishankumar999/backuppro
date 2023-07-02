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
        <h2 class="title text-2xl text-bold mb-5">Getting Started</h2>

        <p>To allow your visitors to log in with their Google account, first you must create a Google App. The following guide will help you through the Google App creation process. </p>

        <p><strong><u>Warning</u></strong>: Providers change the App setup process quite often, which means some steps below might not be accurate.</p>

        <h2 class="title">Create Google App</h2>

        <ol class="list-decimal pl-8">
            <li class="mb-4">Navigate to <a href="https://console.developers.google.com/apis/" target="_blank">https://console.developers.google.com/apis/</a></li>
            <li class="mb-4">Log in with your Google credentials if you are not logged in.</li>
            <li class="mb-4">If you don't have a project yet, you'll need to create one. You can do this by clicking on the blue "<b>Create Project</b>" text on the right side! ( If you already have a project, then in the top bar, click on the name of your project instead, which will bring up a modal, and click <b>"New Project"</b>. )</li>
            <!-- enable google drive api -->
            <li class="mb-4">Click on the "<b>Enable APIs and Services</b>" button.</li>
            <li class="mb-4">Search for "<b>Google Drive API</b>" and click on it.</li>
            <li class="mb-4">Click on the "<b>Enable</b>" button.</li>
            
            <li class="mb-4">Name your project and then click on the "<b>Create</b>" button again!</li>
            <li class="mb-4">Once you have a project, you'll end up on the dashboard. ( If earlier you have already had a Project, then make sure you select the created project in the top bar! )</li>
            <li class="mb-4">Click the “<b>OAuth consent screen</b>” button on the left-hand side.</li>
            <li class="mb-4">Choose a <b>User Type</b> according to your needs and press "<b>Create</b>". 
            Mostly it is  "External" option!
                <ul class="list-disc pl-8 mt-2">
                    <li><b>Note:</b> We don't use sensitive or restricted scopes either. But if you will use this App for other purposes too, then you may need to go through an <a href="https://support.google.com/cloud/answer/9110914" target="_blank">Independent security review</a>!</li>
                </ul>
            </li>
            <li class="mb-4">Enter a name for your App in the "<b>App name</b>" field, which will appear as the name of the app asking for consent.</li>
            <li class="mb-4">For the "<b>User support email</b>" field, select an email address that users can use to contact you with questions about their consent.</li>
            <li class="mb-4">Under the "<b>Authorized domains</b>" section, press the "<b>Add Domain</b>" button and enter your domain name, probably: <b>
                 <?php
                  $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                 echo $protocol.'://' . $_SERVER['HTTP_HOST']  ?>
            </b> without subdomains!</li>
            <li class="mb-4">At the "<b>Developer contact information</b>" section, enter an email address that Google can use to notify you about any changes to your project.</li>
            <li class="mb-4">Press "<b>Save and Continue</b>", then press it again on the "Scopes" and "Test users" pages, too!</li>
            <li class="mb-4">On the left side, click on the "<b>Credentials</b>" menu point, then click the "<b>+ Create Credentials</b>" button in the top bar.</li>
            <li class="mb-4">Choose the "<b>OAuth client ID</b>" option.</li>
            <li class="mb-4">Select the "<b>Web application</b>" under Application type.</li>
            <li class="mb-4">Enter a "<b>Name</b>" for your OAuth client ID.</li>
            <li class="mb-4">Under the "<b>Authorized redirect URIs</b>" section, click "<b>Add URI</b>" and add the following URL:
                <ul class="list-disc pl-8 mt-2">
                    <?php
                    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                    // path till the  folder 
                    $path = $_SERVER['REQUEST_URI'];
                    // remove the current page name
                    $path = str_replace("setup_drive.php", "", $path);
                    ?>
                    <li><strong><?php echo $protocol.'://' . $_SERVER['HTTP_HOST'] .$path.'backup_drive.php'; ?></strong>
                        <!-- add copy to clipboard button change text to copied and return back once copy is done -->
                        <script>
                            function copyToClipboard(text) {
                                var dummy = document.createElement("textarea");
                                document.body.appendChild(dummy);
                                dummy.value = text;
                                dummy.select();
                                document.execCommand("copy");
                                document.body.removeChild(dummy);
                                var btn = document.getElementById("copy");
                                btn.innerHTML = "Copied";
                                setTimeout(function() {
                                    btn.innerHTML = "Copy";
                                }, 3000);
                            }
                        </script>
                        <button class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800" id="copy" onclick="copyToClipboard('<?php echo $protocol.'://' . $_SERVER['HTTP_HOST'] .$path.'backup_drive.php'; ?>')">Copy</button>
                </li>
                </ul>
            </li>
            <li class="mb-4">Click on the "<b>Create</b>" button</li>
            <li class="mb-4">A modal should pop up with your credentials. If that doesn't happen, go to the Credentials in the left-hand menu 
                and select your app by clicking on its name, and you would be able to<strong> download JSON File from there. </strong> </li>
            <li class="mb-4">Currently, your App is in Testing mode, so only a limited number of people can use it. To allow this App for any user with a Google Account, click on the "<b>OAuth consent screen</b>" option on the left side, then click the "<b>PUBLISH APP</b>" button under the "<b>Publishing status</b>" section, and press the "<b>Confirm</b>" button. </li>
        </ol>

            <a href="upload_secret.php" class="block bg-blue-500 hover:bg-blue-600 text-white text-center py-5 rounded not-prose">
                <h2 class="text-xl font-bold not-prose "> I am done setting up my Google App</h2>
            </a>
          
    </div>
</body>

</html>