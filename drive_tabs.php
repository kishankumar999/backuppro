<a href="dashboard.php" class="m-2 block text-blue-500 font-semibold">
    <!-- back long arrow -->
    <svg class="inline-block w-4 h-4 mr-1 -mt-1" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 5.293a1 1 0 0 1 0 1.414L4.414 10H16a1 1 0 1 1 0 2H4.414l2.879 2.293a1 1 0 1 1-1.414 1.414l-4-4a1 1 0 0 1 0-1.414l4-4a1 1 0 0 1 1.414 0z" clip-rule="evenodd"></path>
    </svg>
Back to Dashboard</a>
<div class="max-w-xl mx-auto">

    <div class="flex justify-center mb-3 gap-2 items-center">
        <div>
            <img width="16" height="16" alt="Google Drive icon (2020)" src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/Google_Drive_icon_%282020%29.svg/512px-Google_Drive_icon_%282020%29.svg.png">

        </div>
        <div class="text-bold text-lg text-gray-500">Google Drive Settings</div>
    </div>
    <?php
      // JSON structure containing tab data
      $tabs = '[
          {"label": "Getting Started", "page": "setup_drive.php"},
          {"label": "Google JSON", "page": "upload_secret.php"},
          {"label": "Schedule", "page": "schedule.php"},
          {"label": "Location", "page": "drive_settings.php"}      ]';

          // Decoding the JSON string into an array
      $tabsArray = json_decode($tabs, true);

      // Get the current PHP page filename
      $currentPage = basename($_SERVER['PHP_SELF']);

      // Loop through the tab array and create the tab headers
      echo '<div class="mb-5 text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-500 dark:border-gray-700">';
      echo '<ul class="flex flex-wrap -mb-px justify-center">';
      foreach ($tabsArray as $tab) {
        $isActive = ($currentPage === $tab['page']) ? 'text-blue-600 border-blue-600 active dark:text-blue-500 dark:border-blue-500' : 'hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-500';
        $isDisabled = isset($tab['disabled']) && $tab['disabled'] ? 'text-gray-400 cursor-not-allowed dark:text-gray-500' : '';
        echo '<li class="mr-2">';
        echo '<a href="' . $tab['page'] . '" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg ' . $isActive . ' ' . $isDisabled . '" aria-current="page">' . $tab['label'] . '</a>';
        echo '</li>';
      }
      echo '</ul>';
      echo '</div>';
      ?>
      </div>