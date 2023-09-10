<?php include 'validate_login.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head><?php include("favicon.php"); ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Google Drive Setup</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>



</head>

<body class="bg-gray-50">

  <!-- include tabs -->
  <?php include 'drive_tabs.php'; ?>
  <!-- Content -->

  

  <div class="bg-white items-center justify-center flex-col flex ">




  
    <div class="mb-4 mt-10 max-w-full gap-2 px-5 md:max-w-2xl">
      <div class="text-bold text-2xl font-semibold">Setup Google Drive</div>

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
     // echo '<p class="text-green-600">File uploaded successfully! Config file updated.</p>';

  ?>
  <div class="my-10">
      <div class="mb-4 border-l-4 border-green-500 bg-green-100 p-4 text-green-700" role="alert">
        <div class="flex items-center gap-2 font-bold">
          <svg class="h-6 w-6 flex-none fill-sky-100 stroke-green-500 stroke-2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="11"></circle>
            <path d="m8 13 2.165 2.165a1 1 0 0 0 1.521-.126L16 9" fill="none"></path>
          </svg>
          <div>Credentials Uploaded Successfully!</div>
        </div>
      </div>

      <div class="mb-4 border-l-4 border-green-400 bg-green-50 p-4 text-green-700" role="alert">
        <div class="pb-3">
          <p class="my-4 text-lg font-bold">Ready for Next Action</p>
          <p class="mb-10 mt-5">Now, it's time to take your initial backup to ensure that everything is in order. Please click on the button below to initiate the backup process.</p>

          <a href="backup_drive.php" class="dark:bg-green-00 mb-2 mr-2 mt-10 rounded-lg bg-green-600 px-7 py-2.5 text-lg font-medium text-white hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 dark:hover:bg-green-700 dark:focus:ring-green-800">Take Backup</a>
        </div>
      </div>
      </div>
  <?php
    } else {
      echo '<p class="text-red-600">Failed to upload the file.</p>';
    }
  }
  ?>

      <p class="mt-3 text-sm">The following guide will help you through the Google App creation process.</p>

      <!-- STEP  -->
      <div class="mt-10 flex min-w-full gap-4 rounded-xl border bg-white shadow">
        <div class="text-cent w-20 rounded-l-md bg-slate-400 p-5 text-3xl font-black text-slate-600">
          <div class="">
            <small class="text-sm font-bold text-slate-600">STEP</small>
            1
          </div>
        </div>
        <div class="flex-1 p-4">
          <div class="mb-5 text-xl font-semibold">Create a Google Project</div>
          <div class="font-semibold3  text-slate-800">
            <p class="mb-5">Creating a Google Cloud Project is a foundational step for using the Google Drive API and other Google APIs. Let's begin.</p>
            <ul class="ml-4 flex list-disc flex-col gap-2">
              <li>
                Navigate to
                <a class="text-sky-600 font-bold underline" href="https://console.developers.google.com/apis/" target="_blank">https://console.developers.google.com/apis/</a>
                <a class="text-sky-600 font-bold" href="https://console.developers.google.com/apis/" target="_blank">→</a>
              </li>
              <li>Log in with your Google credentials if you are not logged in.</li>
              <li>
                <p class="mb-2"><strong>If you don't have a project yet</strong>, you'll need to create one. You can do this by clicking on the blue "Create Project" text on the right side!.</p>
                <p class="mb-5">Name your project and then click on the "Create"</p>
                <p class="mb-5">( <strong>If you already have a project</strong>, then in the top bar, click on the name of your project instead, which will bring up a modal, and click "New Project". )</p>
              </li>
            </ul>
            <p class="mb-5">Once you have a project, you'll end up on the dashboard. ( If earlier you have already had a Project, then make sure you select the created project in the top bar! )</p>
          </div>
        </div>
      </div>
      <!-- STEP 2 -->
      <div class="mt-10 flex w-full gap-4 rounded-xl border bg-white shadow">
        <div class="text-center2 w-20 rounded-l-md bg-slate-400 p-5 text-3xl font-black text-slate-600">
          <div class="">
            <small class="text-sm font-bold text-slate-600">STEP</small>
            2
          </div>
        </div>
        <div class="flex-1 p-4">
          <div class="mb-5 text-xl font-semibold">Enable APIs and Services</div>
          <div class="font-semibold3  text-slate-800">
            <p class="mb-4">We <strong>need 3 Google API's for BackupPro,</strong></p>
            <ol class="mb-4 ml-4 flex list-decimal flex-col gap-2">
              <li><strong>Google Drive</strong> <br />to store backup,</li>
              <li><strong>Gmail</strong> <br />to Send Notification Emails</li>
              <li><strong>People API </strong> <br />to know from which Email ID the notifications should be sent.</li>
            </ol>
            <p class="my-5 text-lg">Let's do it.</p>
            <ol class="ml-4 flex list-decimal flex-col gap-3">
              <li>Click on the <strong>"Enable APIs and Services"</strong> button.</li>
              <li>
                Search for <strong>"Google Drive API"</strong> and click on it. <br />
                Then click on "Enable" button.
              </li>
              <li>
                Search for <strong>"Gmail API"</strong> and click on it. <br />
                Then click on "Enable" button.
              </li>
              <li>
                Search for <strong>"People API"</strong> and click on it. <br />
                Then click on "Enable" button.
              </li>
            </ol>
          </div>
        </div>
      </div>

      <!-- STEP 3 -->
      <div class="mt-10 flex w-full gap-4 rounded-xl border bg-white shadow">
        <div class="text-center2 w-20 rounded-l-md bg-slate-400 p-5 text-3xl font-black text-slate-600">
          <div class="">
            <small class="text-sm font-bold text-slate-600">STEP</small>
            3
          </div>
        </div>
        <div class="flex-1 p-4">
          <div class="mb-5 text-xl font-semibold">Setup Google consent screen</div>
          <div class="font-semibold3  text-slate-800">
            <p class="mb-4">When you use OAuth 2.0 for authorisation, your app requests authorisations for one or more scopes of access from a Google Account.</p>
            <p class="mb-4">Google displays a consent screen to the user that includes a summary of your project and its policies and the requested scopes of access.</p>

            <p class="mt-5 text-lg">Let's do it.</p>
            <ol class="mb-5 ml-4 mt-4 flex list-decimal flex-col gap-6">
              <li>Click the <strong> “OAuth consent screen”</strong> button on the left-hand side.</li>
              <li>
                <strong>Choose a User Type</strong> according to your needs and press "Create". Mostly it is "External" option!

                <div class="mt-2 rounded bg-slate-200 p-2"><strong>Note:</strong> We don't use sensitive or restricted scopes either. But if you will use this App for other purposes too, then you may need to go through an Independent security review!</div>
              </li>
              <li><strong>Enter a name for your App</strong> in the "App name" field, which will appear as the <strong>name of the app asking for consent.</strong></li>
              <li>For the <strong>"User support email"</strong> field, select an <strong>email address that users can use to contact you</strong> with questions about their consent.</li>
              <li>At the <strong>"Developer contact information"</strong> section, enter an <strong>email address that Google can use to notify you</strong> about any changes to your project.</li>
              <li>Press <strong>"Save and Continue"</strong>, then press it again on the <strong>"Scopes"</strong> and <strong>"Test users"</strong> pages, too!</li>
            </ol>
          </div>
        </div>
      </div>

      <!-- STEP 4 -->
      <div class="mt-10 flex w-full gap-4 rounded-xl border bg-white shadow">
        <div class="text-center2 w-20 rounded-l-md bg-slate-400 p-5 text-3xl font-black text-slate-600">
          <div class="">
            <small class="text-sm font-bold text-slate-600">STEP</small>
            4
          </div>
        </div>
        <div class="flex-1 p-4">
          <div class="mb-5 text-xl font-semibold">Create & Download credentials to access your enabled APIs.</div>
          <div class="font-semibold3  text-slate-800">
            <p class="my-5 text-lg">Let's do it.</p>

            <ol class="ml-4 flex list-decimal flex-col gap-4">
              <li class="">On the left side, click on the "<b>Credentials</b>" menu point, then click the "<b>+ Create Credentials</b>" button in the top bar.</li>
              <li class="">Choose the "<b>OAuth client ID</b>" option.</li>
              <li class="">Select the "<b>Web application</b>" under Application type.</li>
              <li class="">Enter a "<b>Name</b>" for your OAuth client ID.</li>
              <li class="">
                Under the "<b>Authorized redirect URIs</b>" section, click "<b>Add URI</b>" and add the following URL:
                <?php
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                // path till the  folder 
                $path = $_SERVER['REQUEST_URI'];
                // remove the current page name
                $path = str_replace("google_drive_setup.php", "", $path);
                $copyurl =   $protocol . '://' . $_SERVER['HTTP_HOST'] . $path . 'backup_drive.php';
                ?>

                <div class="mt-3 flex flex-wrap gap-2 rounded bg-blue-100 p-3">
                  <div class="border-1 whitespace-pre-wrap rounded border border-blue-500 bg-white p-1 text-sm"><?php echo $copyurl; ?></div>
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
                  <button class="mb-2 mr-2 rounded-lg bg-blue-700 px-4 py-1 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" id="copy" onclick="copyToClipboard('<?php echo $copyurl; ?>')">Copy URL</button>
                </div>
              </li>

              <li>Click on the "<b>Create</b>" button</li>
              <li>A modal should pop up with your credentials. If that doesn't happen, go to the Credentials in the left-hand menu and select your app by clicking on its name, and you would be able to<strong> download JSON File from there. </strong></li>
            </ol>
            <div class="mt-5 flex flex-col gap-3 bg-blue-100 p-4">
              <p>Currently, your App is in Testing mode, so only a limited number of people can use it. <br /></p>
              <p>To allow this App for any user with a Google Account, click on the "<b>OAuth consent screen</b>" option on the left side,</p>
              <p>Then click the "<b>PUBLISH APP</b>" button under the "<b>Publishing status</b>" section, and press the "<b>Confirm</b>" button.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- STEP 5 -->
      <div class="mt-10 flex w-full gap-4 rounded-xl border bg-white shadow">
        <div class="text-center2 w-20 rounded-l-md bg-slate-400 p-5 text-3xl font-black text-slate-600">
          <div class="text-4xl">🏁</div>
          <div class="">
            <small class="text-sm font-bold text-slate-600">STEP</small>
            5
          </div>
        </div>
        <div class="flex-1 p-4">
          <div class="mb-5 text-xl font-semibold">Upload the JSON credentials file you downloaded from the previous step here</div>
          <div class="font-semibold3  text-slate-800 my-10">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
              <div class="mb-4">
                <input type="file" name="credentials" accept=".json" required>
              </div>
              <div>
                <input type="submit" value="Upload" class="px-4 w-full py-2 bg-blue-500 text-white rounded hover:bg-blue-600 cursor-pointer">
              </div>
            </form>


          </div>
        </div>
      </div>
    </div>
  </div>







</body>

</html>