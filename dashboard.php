<?php include 'validate_login.php';
$config = include('config.php');
?>
<!DOCTYPE html>
<html lang="en">

<head><?php include("favicon.php"); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script  src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
</head>

<body class="flex flex-col md:flex-row min-h-screen">
    <!-- BackupPro Title -->
    <div class="w-full md:w-1/5 bg-gray-800 text-white flex-shrink-0 p-4">
        <h2 class=" mb-4">
            <a href="dashboard.php" class="text-white">
            <div class="flex gap-1 ">
                <div class="mt-2"><img src="uploads/logo.png" width="35px"  alt=""></div>
                <div class="">
                <div class="div text-2xl font-bold">BackupPro</div>    
                <div class="text-sm text-slate-500">by Webfort</div> </div>
            </div> 
              
            </a>
        </h2>
        <ul class="space-y-2">
            <li><a href="dashboard.php" class="text-blue-500">Dashboard</a></li>
            <li class="pt-5">SETTINGS</li>
            <li><a href="google_drive_setup.php" class="text-blue-500">Drive Settings</a></li>
            <li><a href="db_settings.php" class="text-blue-500">Database Settings</a></li>
            <li><a href="notifications.php" class="text-blue-500">Notifications Settings</a></li>
            <li class="pt-5">ACCOUNT</li>
            <li><a href="reset_password.php" class="text-blue-500">Reset Password</a></li>
            <li><a href="reset.php" class="text-blue-500">Reset BackupPro</a></li>
            <li class="pt-5"><a href="logout.php" class="text-blue-500">Logout</a></li>
        </ul>
    </div>

    <!-- Content -->
    <div class="w-full md:w-4/5 p-8">
        <h1 class="text-2xl font-bold mb-8">Dashboard</h1>
        <?php
    // Check if the installation is complete
    if(isset($_GET['installation_complete']) && $_GET['installation_complete'] === 'true') {
        // Installation is complete, show success message
        ?>
        <div  id="notification"  class="border-2 border-white backdrop-blur-sm fixed inset-0  flex justify-center items-center h-screen bg-stone-100/50">

            <div id="notification-inner" class="border-2 border-white relative max-w-xl shadow-md  rounded-md bg-green-500 p-7 text-white shadow-md">
                <div class="flex items-center">
                <svg id="notificaiton-tick" xmlns="http://www.w3.org/2000/svg" class="hidden mr-2 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <!-- installing svg -->
                <svg id="notification-spin" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                    </path>
                </svg>
                <p class="text-3xl font-semibold " id="notification-in-progress">Installation in Progress</p>
                <p class="text-3xl font-semibold hidden" id="notification-done">Installation Done</p>
            </div>
            <div id="progress" class="w-0 h-2 bg-gradient-to-r from-fuchsia-500 via-red-600 to-orange-400 absolute bottom-0 left-0 rounded-bl-md rounded-r-md"></div>
        </div>
        </div>
        <?php
    }
    ?>



        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="backup.php" class="block bg-blue-500 hover:bg-blue-600 text-white text-center py-8 rounded">
                <h2 class="text-xl font-bold"> Download Backup ZIP</h2>
            </a>
            <?php if (isset($config['client_secret']) && $config['client_secret'] != "") : ?>
                <a href="backup_drive.php" class="block flex gap-2 justify-center bg-green-500 hover:bg-blue-600 text-white text-center py-8 rounded">
                    <img width="32" alt="Google Drive icon (2020)" src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/Google_Drive_icon_%282020%29.svg/512px-Google_Drive_icon_%282020%29.svg.png">

                    <h2 class="text-xl font-bold">Backup to Google Drive</h2>
                </a>
            <?php else : ?>
                <a href="google_drive_setup.php" class="block flex gap-2 justify-center bg-green-500 hover:bg-blue-600 text-white text-center py-8 rounded">
                    <img width="32" alt="Google Drive icon (2020)" src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/Google_Drive_icon_%282020%29.svg/512px-Google_Drive_icon_%282020%29.svg.png">

                    <h2 class="text-xl font-bold">Setup Google Drive for Backup</h2>
                </a>
            <?php endif; ?>



        </div>

        <!-- in a muted color display last_backup_timestamp from config in a human readable format show like 7th July 2023 at 12:00 pm -->
        <div class="bg-gray-100 rounded p-4 my-8">
            <h2 class="text-xl font-bold mb-4">Last Backup</h2>
            <p class="text-gray-500">
                <?php if (isset($config['last_backup_timestamp']) && $config['last_backup_timestamp'] != "") : ?>
                    <?php echo date('jS F Y \a\t h:i a', $config['last_backup_timestamp']); ?>
                <?php else : ?>
                    No backup yet
                <?php endif; ?>
            </p>
        </div>

    </div>
</body>
<?php

if(isset($_GET['installation_complete']) && $_GET['installation_complete'] === 'true') {
    ?>
<script>
        // Show the success notification and progress bar
        const notification = document.getElementById('notification');
        const notificationInner = document.getElementById('notification-inner');
        const progressBar = document.getElementById('progress');

        notification.classList.remove('hidden');
        progressBar.style.width = '0%';

        // Animate the progress bar
        const animationDuration = 5000; // 5 seconds
        const animationStartTime = Date.now();


        setTimeout(() => {
            // Show the tick icon
            document.getElementById('notificaiton-tick').classList.remove('hidden');
            // Hide the spinning icon
            document.getElementById('notification-spin').classList.add('hidden');
            // Show the done text
            document.getElementById('notification-in-progress').classList.add('hidden');
            document.getElementById('notification-done').classList.remove('hidden');
        }, 3000);


        function animateProgressBar() {
            const currentTime = Date.now();
            const elapsed = currentTime - animationStartTime;
            const progress = (elapsed / animationDuration) * 100;

            if (progress <= 100) {
                progressBar.style.width = `${progress}%`;
                requestAnimationFrame(animateProgressBar);
            } else {
               
                // Hide the notification after the animation is complete
                notificationInner.classList.add('flyout');
                setTimeout(() => {
                    notification.style.display = 'none';
                    // redirect to dashboard.php
                    window.location.href = 'dashboard.php';
                }, 1000); // 1 second delay to allow the animation to finish
            
            }
        }

        animateProgressBar();
    </script>


<style>
        /* Define the fly-out animation */
        @keyframes flyout {
            0% {
                opacity: 1;
                transform: translateX(0);
            }
            100% {
                opacity: 0;
                transform: translateX(100%);
            }
        }

        /* Apply the animation to the notification box */
        .flyout {
            animation: flyout 1s ease-in-out forwards;
        }
    </style>
    <?php
}
?>

</html>