<a href="dashboard.php" class="m-2 block text-blue-500 font-semibold">
    <!-- back long arrow -->
    <svg class="inline-block w-4 h-4 mr-1 -mt-1" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 5.293a1 1 0 0 1 0 1.414L4.414 10H16a1 1 0 1 1 0 2H4.414l2.879 2.293a1 1 0 1 1-1.414 1.414l-4-4a1 1 0 0 1 0-1.414l4-4a1 1 0 0 1 1.414 0z" clip-rule="evenodd"></path>
    </svg>
Back to Dashboard</a>
<div class="max-w-xl mx-auto">

    <div class="flex justify-center mb-3 gap-2 items-center">
   
    
<svg class="w-7" viewBox="0 0 87.3 78" xmlns="http://www.w3.org/2000/svg">
	<path d="m6.6 66.85 3.85 6.65c.8 1.4 1.95 2.5 3.3 3.3l13.75-23.8h-27.5c0 1.55.4 3.1 1.2 4.5z" fill="#0066da"/>
	<path d="m43.65 25-13.75-23.8c-1.35.8-2.5 1.9-3.3 3.3l-25.4 44a9.06 9.06 0 0 0 -1.2 4.5h27.5z" fill="#00ac47"/>
	<path d="m73.55 76.8c1.35-.8 2.5-1.9 3.3-3.3l1.6-2.75 7.65-13.25c.8-1.4 1.2-2.95 1.2-4.5h-27.502l5.852 11.5z" fill="#ea4335"/>
	<path d="m43.65 25 13.75-23.8c-1.35-.8-2.9-1.2-4.5-1.2h-18.5c-1.6 0-3.15.45-4.5 1.2z" fill="#00832d"/>
	<path d="m59.8 53h-32.3l-13.75 23.8c1.35.8 2.9 1.2 4.5 1.2h50.8c1.6 0 3.15-.45 4.5-1.2z" fill="#2684fc"/>
	<path d="m73.4 26.5-12.7-22c-.8-1.4-1.95-2.5-3.3-3.3l-13.75 23.8 16.15 28h27.45c0-1.55-.4-3.1-1.2-4.5z" fill="#ffba00"/>
</svg>
          
      
        <div class="text-bold text-2xl text-gray-500">Google Drive Settings</div>
    </div>
    <?php
      // JSON structure containing tab data
      $tabs = '[
    
          {"label": "Setup", "page": "google_drive_setup.php"},
          {"label": "Schedule", "page": "schedule.php"},
          {"label": "Location", "page": "drive_settings.php"}      ]';

          // Decoding the JSON string into an array
      $tabsArray = json_decode($tabs, true);

      // Get the current PHP page filename
      $currentPage = basename($_SERVER['PHP_SELF']);

      // Loop through the tab array and create the tab headers
      echo '<div class=" text-lg font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-500 dark:border-gray-700">';
      echo '<ul class="flex flex-wrap -mb-px justify-center">';
      foreach ($tabsArray as $tab) {
        $isActive = ($currentPage === $tab['page']) ? 'text-blue-600 border-blue-600 active dark:text-blue-500 dark:border-blue-500' : 'hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-500';
        $isDisabled = isset($tab['disabled']) && $tab['disabled'] ? 'text-gray-400 cursor-not-allowed dark:text-gray-500' : '';
        echo '<li class="mr-2">';
        echo '<a href="' . $tab['page'] . '" class="inline-block p-4 border-b-4 border-transparent rounded-t-lg ' . $isActive . ' ' . $isDisabled . '" aria-current="page">' . $tab['label'] . '</a>';
        echo '</li>';
      }
      echo '</ul>';
      echo '</div>';
      ?>
      </div>